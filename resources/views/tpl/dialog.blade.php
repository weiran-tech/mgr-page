@extends('weiran-mgr-page::tpl.default')
@section('head-css')
    @include('weiran-mgr-page::tpl._js_css', [
        '_type' => ['layui']
    ])
@endsection
@section('body-main')
    @include('weiran-mgr-page::tpl._toastr')
    @yield('dialog-main')
@endsection