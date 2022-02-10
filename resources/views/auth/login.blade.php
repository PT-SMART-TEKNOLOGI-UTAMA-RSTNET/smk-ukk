<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{asset('uploads/images/logo.png')}}" type="image/x-icon">
    <title>Otorisasi :: {{config('app.name')}}</title>
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <style>
        .login-wrap{
            background:url({{asset('uploads/images/bg-01.jpg')}}) no-repeat center;
        }
    </style>
</head>
<body>
    <div class="login-wrap" id="login-page"></div>
    <script src="{{asset('js/index.js')}}"></script>
</body>
</html>
