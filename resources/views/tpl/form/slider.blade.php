<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('weiran-mgr-page::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">

		<input type="text" class="{{$class}}" name="{{$name}}" data-from="{{ old($column, $value) }}" {!! $attributes !!} />
		@include('weiran-mgr-page::tpl.form.help-block')
		@include('weiran-mgr-page::tpl.form.error')
	</div>
</div>
