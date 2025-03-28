<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('weiran-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			{!! app('weiran.mgr-page.form')->editor($name, old($column, $value), array_merge([
				'placeholder' => $placeholder,
			], $options)) !!}
		</div>
		@include('weiran-mgr-page::tpl.form.help-block')
		@include('weiran-mgr-page::tpl.form.error')
	</div>
</div>
