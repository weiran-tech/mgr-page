{{-- 给 Form/Content 使用 --}}
@extends('weiran-mgr-page::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-meta')
    {!! Html::favicon('assets/images/default/favicon.png') !!}
@endsection
@section('head-content')
    @include('weiran-mgr-page::tpl._js_css', [
       '_type' => [ 'layui'],
    ])
@endsection
@section('body-class')
    {!! input('_iframe') === 'weiran' ? 'layui-iframe' : '' !!}
@endsection
@section('body-main')
    @include('weiran-mgr-page::tpl._toastr')
    <div class="layui-fluid pd15" data-pjax pjax-ctr="#main" id="main">
        @if ($title ?? '')
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>
                    {!! $title !!}
                    @if ($description ?? '')
                        <small>{!! $description !!}</small>
                    @endif
                </legend>
            </fieldset>
        @endif
        {!! $content !!}
    </div>
@endsection