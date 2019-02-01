<?php
/* @var \Manadev\Framework\Views\Views\Page $view */
?>
<!DOCTYPE html>
<html @if ($view->html_modifier) class="{{ $view->html_modifier }}" @endif>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $view->title }}</title>
    <link rel="stylesheet" href="{{ m_asset('styles.css') }}">
    <script src="{{ m_asset('critical.js') }}"></script>
    @foreach($view->head_end as $child)
        @include($child)
    @endforeach
</head>
<body class="{{ $view->modifier }}">

@if ($view->header)
    @include($view->header)
@endif
@include($view->content)
@if ($view->footer)
    @include($view->footer)
@endif
@foreach($view->body_end as $child)
    @include($child)
@endforeach

<script>Manadev_Framework_Js.vars.config.merge({!! json_encode($view->model->toObject(), JSON_PRETTY_PRINT) !!});</script>
<script src="{{ m_asset('scripts.js') }}"></script>
</body>

</html>