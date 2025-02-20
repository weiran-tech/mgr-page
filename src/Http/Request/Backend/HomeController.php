<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Backend;

use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Weiran\Core\Classes\Traits\CoreTrait;
use Weiran\Core\Exceptions\PermissionException;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Classes\Traits\WeiranTrait;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\Framework\Helper\EnvHelper;
use Weiran\Framework\Helper\StrHelper;
use Weiran\Framework\Helper\UtilHelper;
use Weiran\MgrPage\Classes\Setting\SettingView;
use Weiran\MgrPage\Http\MgrPage\FormPassword;
use Weiran\System\Action\Pam;
use Weiran\System\Classes\PySystemDef;
use Weiran\System\Classes\Traits\UserSettingTrait;
use Weiran\System\Events\BePamLogoutEvent;
use Weiran\System\Http\Request\Web\Validation\AuthLoginRequest;
use Weiran\System\Models\PamAccount;
use Weiran\System\Models\PamRole;

/**
 * 主页控制器
 */
class HomeController extends BackendController
{
    use WeiranTrait, CoreTrait, UserSettingTrait;

    /**
     * 主页
     * @return View
     * @throws PermissionException
     */
    public function index(): View
    {
        $isFullPermission = $this->pam->hasRole(PamRole::BE_ROOT);
        $this->pyView()->share([
            '_menus' => $this->coreModule()->menus()->withPermission(PamAccount::TYPE_BACKEND, $isFullPermission, $this->pam),
        ]);
        $host = StrHelper::formatId(EnvHelper::host()) . '-backend';
        $name = sys_setting('wr-system::site.name');
        $logo = sys_setting('wr-system::site.logo');
        $main = route('weiran-mgr-page:backend.home.cp', [], false);
        return view('weiran-mgr-page::backend.home.index', [
            'host' => $host,
            'logo' => $logo,
            'name' => $name,
            'main' => $main,
        ]);
    }

    /**
     * 登录
     * @throws ApplicationException
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function login(Request $req)
    {
        $auth = $this->auth();
        $req->merge([
            'os' => PamAccount::REG_PLATFORM_MGR,
        ]);

        if (is_post()) {
            /** @var AuthLoginRequest $request */
            $request      = app(AuthLoginRequest::class, [$req]);
            $reqPassport  = $request->scene('passport')->validated();
            $isMobile     = UtilHelper::isMobile($reqPassport['passport']);
            $Pam          = new Pam();
            $loginSuccess = false;
            if (!$isMobile) {
                $reqPwd = $request->scene('password')->validated();
                if ($Pam->loginCheck($reqPwd['passport'], $reqPwd['password'], PamAccount::GUARD_BACKEND)) {
                    $auth->login($Pam->getPam(), $this->isRemember());
                    $loginSuccess = true;
                }
            }
            if ($isMobile) {
                $reqCaptcha = $request->scene('captcha')->validated();
                if ($Pam->beCaptchaLogin($reqCaptcha['passport'], $reqCaptcha['captcha'])) {
                    $auth->login($Pam->getPam(), $this->isRemember());
                    $loginSuccess = true;
                }
            }
            if ($loginSuccess) {
                $this->setSessionLifetime($Pam->getPam());
                $this->setRememberTokenExpired();
                return Resp::success('登录成功', '_location|' . route('weiran-mgr-page:backend.home.index'));
            }
            return Resp::error($Pam->getError());
        }

        if ($auth->check()) {
            return Resp::success('登录成功', '_location|' . route('weiran-mgr-page:backend.home.index'));
        }

        return view('weiran-mgr-page::backend.home.login');
    }

    /**
     * 修改本账户密码
     */
    public function password()
    {
        $form = new FormPassword();
        $form->setPam($this->pam);
        return $form->render();
    }

    public function clearCache()
    {
        $this->pyConsole()->call('weiran:optimize');
        return Resp::success('已清空缓存');
    }

    /**
     * 登出
     * @return JsonResponse|Response|RedirectResponse
     */
    public function logout()
    {
        $guard = Auth::guard(PamAccount::GUARD_BACKEND);

        $accountId = $guard->id();

        $guard->logout();

        event(new BePamLogoutEvent((int) $accountId));

        // todo 退出后台清空 session 会导致其他用户失效
        app('session.store')->flush();

        return Resp::success('退出登录', '_location|' . route('weiran-mgr-page:backend.home.login'));
    }

    /**
     * 控制面板
     * @return View
     */
    public function cp()
    {
        return view('weiran-mgr-page::backend.home.cp');
    }

    /**
     * Setting
     * @param string     $path 地址
     * @param int|string $index
     */
    public function setting(string $path = 'weiran.mgr-page', $index = 0)
    {
        return (new SettingView())->render($path, $index);
    }

    /**
     * tools
     * @param string $type 类型
     * @return Factory|View
     */
    public function easyWeb(string $type)
    {
        $host = StrHelper::formatId(EnvHelper::host());
        return view('weiran-mgr-page::backend.home.easyweb.' . $type, [
            'host' => $host,
        ]);
    }

    /**
     * 获取后台的Auth
     * @return Guard|SessionGuard
     */
    private function auth()
    {
        return Auth::guard(PamAccount::GUARD_BACKEND);
    }


    /**
     * 用户自定义的 Session 生命周期
     * @param PamAccount $pam
     */
    private function setSessionLifetime(PamAccount $pam): void
    {
        $defaultLoginHours = sys_setting('wr-system::pam.lifetime') ?: 12;

        // 获取用户设定
        $setting  = $this->userSettingGet($pam->id, PySystemDef::uskAccount());
        $lifetime = ($setting['expired_hour'] ?? $defaultLoginHours) * 60;
        config(['session.lifetime' => $lifetime]);
    }

    /**
     * 是否记住了自动登录
     * @return bool
     */
    private function isRemember(): bool
    {
        return (bool) sys_setting('wr-system::pam.is_remember');
    }

    /**
     * 设置记录登录时长的有效期
     * @return void
     */
    private function setRememberTokenExpired(): void
    {
        if (!$this->isRemember()) {
            return;
        }

        $auth        = $this->auth();
        $cookieJar   = $auth->getCookieJar();
        $cookieValue = $cookieJar->queued($auth->getRecallerName())->getValue();

        // reset expired value
        $rememberTokenExpireMinutes = ((int) sys_setting('wr-system::pam.remember_hour', 60) ?: 60) * 24 * 60;
        $cookieJar->queue($auth->getRecallerName(), $cookieValue, $rememberTokenExpireMinutes);
    }
}