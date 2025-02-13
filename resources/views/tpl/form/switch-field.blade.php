<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

    <div class="{{$viewClass['label']}}">
        <label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
            @include('weiran-mgr-page::tpl.form.help-tip')
            {{$label}}
        </label>
    </div>
    <div class="{{$viewClass['field']}} layui-field-radio-ctr">
        @foreach($options as $option => $label)
            <div class="layui-field-radio-item">
                {!! app('weiran.mgr-page.form')->radio($name, $option, ($option == old($column, $value)) || ($value === null && in_array($label, $checked, false)),
                        array_merge($attributes , [
                            'class' => 'layui-field-radio',
                            'id' => $column.'-'.$option,
                            'lay-ignore',
                        ])
                    )
                !!}
                {!! app('weiran.mgr-page.form')->label($column.'-'.$option, $label, [
                    'class' => 'layui-field-radio-label'
                ]) !!}
            </div>
        @endforeach
        @include('weiran-mgr-page::tpl.form.help-block')
        @include('weiran-mgr-page::tpl.form.error')
    </div>
</div>