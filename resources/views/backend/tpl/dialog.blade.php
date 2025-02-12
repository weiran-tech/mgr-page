@extends('weiran-mgr-page::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-content')
    @include('weiran-mgr-page::tpl._js_css', [
        '_type' => ['layui']
    ])
@endsection
@section('body-main')
    @include('weiran-mgr-page::tpl._toastr')
    <main class="backend--main pd10" style="background: #fff">
        @yield('backend-main')
    </main>
    <script>
	layui.form.render();
    </script>
@endsection