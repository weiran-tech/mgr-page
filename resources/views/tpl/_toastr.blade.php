@if (Session::has('end.message'))
    <script>
	setTimeout(function() {
        @if (Session::get('end.level') === 0 )
		layer.msg('{!! Session::get('end.message') !!}', {icon : 1});
        @endif
        @if (Session::get('end.level') !== 0)
		layer.msg('{!! Session::get('end.message') !!}', {icon : 2});
        @endif
	}, 1300);
    </script>
@endif