<div class="{{$viewClass['form-group']}}">
	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('weiran-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			{!! app('weiran.mgr-page.form')->text($name, $value, [
				'readonly' => 'readonly',
				'class' => 'layui-input',
				'id' => $id,
			] + $attributes) !!}
		</div>
		@include('weiran-mgr-page::tpl.form.help-block')
	</div>
</div>