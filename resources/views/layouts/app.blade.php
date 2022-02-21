<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{asset('uploads/images/logo.png')}}" type="image/x-icon">
    <title>{{config('app.name')}}::@yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link href="{{asset('css/app.css?time=20220221')}}" rel="stylesheet">

</head>
<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    @yield('contents')
    @yield('scripts')
    <script src="{{asset('js/app.js?time=20220221')}}"></script>
</body>
</html>
