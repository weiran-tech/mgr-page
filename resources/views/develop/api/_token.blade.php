@if (data_get($definition, 'sign_token', true))
    <p class="layui-form-item mt10">
        {!! Form::label('token', '当前 Token:') !!}
        <a href="{!! route_url('py-mgr-page:develop.api.field', [$guard, 'token']) !!}"
            data-title="设置 Token {!! $guard !!}"
            class="J_iframe pull-right layui-btn layui-btn-sm">设置 Token</a>
        <a href="{!! route_url('py-mgr-page:develop.api.login', null, ['guard'=> $guard]) !!}"
            data-title="登录 {!! $guard !!}"
            class="J_iframe pull-right layui-btn layui-btn-sm mr10">登录</a>
        {!! Form::text('token',$data['token']??'', [
            'class' => 'layui-input J_calc mt3 layui-input-sm',
            'readonly'=> true,
        ]) !!}
    </p>
@endif