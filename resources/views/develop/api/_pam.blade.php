@if($data['token'] && !data_get($pam??[], 'id'))
    <div class="layui-elem-quote mt10 layui-quote-nm pd8">
        token 存在, 但是获取数据不正确
    </div>
@endif
@if (data_get($pam??[], 'id'))
    <div class="layui-elem-quote mt10 layui-quote-nm pd8">
        <i class="layui-icon layui-icon-face-smile mr8" style="font-size: 30px; color: #1E9FFF;"></i>
        {!! Form::label('id', 'id : '.$pam['id']) !!}

        @if($pam['mobile']??'')
            <i class="layui-icon layui-icon-template ml20 mr8" style="font-size: 30px; color: #1E9FFF;"></i>
            {!! Form::label('mobile', 'mobile : '.$pam['mobile']??'') !!}
        @endif
        @if($pam['username']??'')
            <i class="layui-icon layui-icon-template ml20 mr8" style="font-size: 30px; color: #1E9FFF;"></i>
            {!! Form::label('username', 'username : '.$pam['username']??'') !!}
        @endif
        @if($pam['email']??'')
            <i class="layui-icon layui-icon-template ml20 mr8" style="font-size: 30px; color: #1E9FFF;"></i>
            {!! Form::label('email', 'email : '.$pam['email']??'') !!}
        @endif
        <i class="layui-icon layui-icon-friends ml20 mr8" style="font-size: 30px; color: #1E9FFF;"></i>
        {!! Form::label('username', 'ua : '.$pam['username']) !!}
    </div>
@endif