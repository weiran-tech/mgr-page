@extends('py-mgr-page::tpl.default')
@section('head-css')
    @include('py-mgr-page::tpl._js_css', [
        '_type' => ['layui']
    ])
@endsection
@section('body-main')
    @include('py-mgr-page::tpl._toastr')
    @yield('dialog-main')
@endsection