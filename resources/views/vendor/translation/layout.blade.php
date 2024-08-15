<!DOCTYPE html>
<html lang="{{ get_current_lang() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    
    <link href="{{asset('assets/cdn/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/language.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/translation/css/main.css') }}">
    <link rel="stylesheet" href="{{asset('assets/cdn/css/all.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/cdn/css/sweetalert2.css')}}" />
    <script src="{{asset('assets/cdn/js/jquery-3.6.0.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/cdn/css/select2.css')}}" />
</head>
<body>
    
    <div id="app">
        
        @include('translation::nav')
        @include('translation::notifications')
        
        @yield('body')
        
    </div>

    @include('vendor.translation.js.layout')
    <script src="{{asset('assets/cdn/js/bootstrap.bundle.min.js')}}" ></script>
    <script src="{{ asset('assets/translation/js/app.js') }}"></script>
    <script src="{{asset('assets/cdn/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/cdn/js/sweetalert2.min.js')}}"></script>
    <script src="{{ asset('assets/js/pages/language.js') }}"></script>
</body>
</html>
