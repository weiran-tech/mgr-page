@if($help && !$showHelp)
    {!! app('weiran.mgr-page.form')->tip(data_get($help, 'text'), data_get($help, 'icon')) !!}
@endif