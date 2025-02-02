<form {!! $attributes !!}>
    @foreach($fields as $field)
        {!! $field->setWidth($width['field'], $width['label'])->render() !!}
    @endforeach

    @if ($method !== 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif

    @if(count($buttons) > 0)
        <div class="layui-row">
            <div class="layui-col-sm{{$width['label']}} layui-col-xs12">&nbsp;</div>
            <div class="layui-col-sm{{$width['field']}} layui-col-xs12">
                <div class="layui-form-auto-field">
                    @if(in_array('reset', $buttons, true))
                        <button type="reset" class="layui-btn layui-btn-primary">{{ trans('py-system::form.reset') }}</button>
                    @endif
                    @if(in_array('submit', $buttons, true))
                        @if ($ajax)
                            <button class="layui-btn layui-btn-normal">{{ trans('py-system::form.submit') }}</button>
                        @else
                            <button type="submit" class="layui-btn layui-btn-normal">{{ trans('py-system::form.submit') }}</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif
</form>
{!! Poppy\MgrPage\Classes\Widgets\FormWidget::assetsStrAppendBody('builder') !!}
<script>
$(function () {
    layui.form.render();
    $('#{!! $id !!}').validate(Util.validateConfig({!! $validation !!}, true));
})
</script>