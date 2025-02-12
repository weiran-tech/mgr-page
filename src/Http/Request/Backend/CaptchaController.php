<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Validation\Rule;
use Weiran\System\Action\Verification;
use Weiran\System\Events\CaptchaSendEvent;
use Weiran\System\Models\PamAccount;
use Throwable;
use Validator;

/**
 * 后台登录发送验证码
 */
class CaptchaController extends BackendController
{

    /**
     * 发送后台通行证的验证码
     * @return JsonResponse|RedirectResponse|Response
     * @throws ValidationException
     */
    public function send()
    {
        $validator = Validator::make(input(), [
            'passport' => [Rule::required(), Rule::mobile(),],
            'captcha'  => [Rule::required(), 'captcha'],
        ], [], [
            'passport' => '手机号',
            'captcha'  => '验证码',
        ]);

        $valid    = $validator->validated();
        $passport = $valid['passport'];

        $beMobile = PamAccount::beMobile($passport);
        if (!PamAccount::where('type', PamAccount::TYPE_BACKEND)->where('mobile', $beMobile)->exists()) {
            return Resp::error('用户不存在');
        }

        $Verification = new Verification();
        $expired      = (int) sys_setting('wr-system::pam.captcha_expired') ?: 5;
        if (!$Verification->isPassThrottle($passport)) {
            return Resp::error($Verification->getError());
        }
        if ($Verification->genCaptcha($passport, $expired)) {
            $captcha = $Verification->getCaptcha();
            try {
                event(new CaptchaSendEvent($passport, $captcha));
                return Resp::success('验证码发送成功' . (!is_production() ? ', 验证码:' . $captcha : ''));
            } catch (Throwable $e) {
                return Resp::error($e);
            }
        }
        else {
            return Resp::error($Verification->getError());
        }
    }
}