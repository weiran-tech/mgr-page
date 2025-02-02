@extends('py-mgr-page::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-content')
    @include('py-mgr-page::tpl._js_css', [
       '_type' => [ 'layui', 'easy-web']
    ])
@endsection
@section('body-main')
    @yield('backend-fe')
@endsection