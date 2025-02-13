<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('weiran-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			@if($type === 'text')
				{!! app('weiran.mgr-page.form')->text($name, $value, $attributes) !!}
			@endif
			@if($type === 'number')
				{!! app('weiran.mgr-page.form')->number($name, $value, $attributes) !!}
			@endif
			@if($type === 'password')
				{!! app('weiran.mgr-page.form')->password($name, $attributes) !!}
			@endif

		</div>
		@include('weiran-mgr-page::tpl.form.help-block')
		@include('weiran-mgr-page::tpl.form.error')
	</div>
</div>