<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Weiran\System\Classes\PySystemDef;
use Weiran\System\Classes\Traits\UserSettingTrait;
use Weiran\System\Models\PamAccount;

/**
 * 修改登录凭证的有效期
 */
class InterruptLifetime
{
    use UserSettingTrait;

    /**
     * Middleware handler.
     * @param Request $request request
     * @param Closure $next    next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            /** @var PamAccount $user */
            $defaultLoginHours = sys_setting('py-system::pam.lifetime') ?: 12;
            // user setting
            $setting  = $this->userSettingGet($user->id, PySystemDef::uskAccount());
            $lifetime = ($setting['expired_hour'] ?? $defaultLoginHours) * 60;
            config(['session.lifetime' => $lifetime]);
        }
        return $next($request);
    }
}