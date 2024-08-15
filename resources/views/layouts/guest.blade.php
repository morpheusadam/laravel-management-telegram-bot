<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{config('app.localeDirection')}}">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="csrf_token()">
        {!!url_make_canonical()!!}
        <title>{{ config('app.name') }} - @yield('title')</title>
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="shortcut icon" href="{{ asset(config('app.favicon')) }}" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        <link rel="stylesheet" href="{{asset('assets/cdn/css/sweetalert2.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/cdn/css/toastr.min.css')}}" />
        <link rel="stylesheet" href="{{ asset('assets/css/pages/layouts/guest.css') }}">

        @stack('styles-header')
        @stack('scripts-header')

    </head>

    <body>
        <div id="auth">

            <div class="container">
                <div class="row d-flex">
                    <div class="col-md-8 col-lg-6 col-xl-5 col-sm-12 mx-auto">
                        <div class="card pt-4">
                            <div class="card-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <script>
            "use strict";
            var agent_user_id = '1';
            var csrf_token = '{{ csrf_token() }}';
            var url_login = '{{ route('login') }}'
            var url_register = '{{ route('register') }}';
            if(agent_user_id) url_register = url_register+"?at="+agent_user_id;
            var url_dashboard = '{{ route('dashboard') }}';
            var success = '{{ __('success') }}';
            var warning = '{{ __('warning') }}';
            var error = '{{ __('error') }}';
        </script>

        <script src="{{ asset('assets/cdn/js/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('assets/js/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>

        <script src="{{asset('assets/cdn/js/sweetalert2.min.js')}}"></script>
        <script src="{{asset('assets/cdn/js/toastr.min.js')}}"></script>
        <script src="{{ asset('assets/js/common/common.js') }}"></script>

        @include('shared.guest-variables')
        @stack('scripts-footer')
        @stack('styles-footer')

    </body>

</html>
