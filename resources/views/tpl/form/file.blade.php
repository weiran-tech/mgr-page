<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

    <div class="{{$viewClass['label']}}">
        <label for="{{$id}}" class="layui-form-auto-label  {{$viewClass['label_element']}}">
            @include('weiran-mgr-page::tpl.form.help-tip')
            {{$label}}
        </label>
    </div>

    <div class="{{$viewClass['field']}}">
        {!! app('weiran.mgr-page.form')->upload($name, $value, array_merge($attributes, $options)) !!}
        @include('weiran-mgr-page::tpl.form.help-block')
        @include('weiran-mgr-page::tpl.form.error')
    </div>
</div>
