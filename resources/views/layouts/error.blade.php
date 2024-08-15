<!DOCTYPE html>
<html lang="{{ get_current_lang() }}">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="csrf_token()">
        <title>{{config('app.name')}} | @yield('title')</title>

        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
        <link rel="shortcut icon" href="{{ config('app.favicon') }}" />
    </head>

    <body class="bg-light">
        <div id="error">
            <div class="container text-center pt-32">
                <h1 class='error-title'><big>@yield('error_code')</big></h1>
                <h3 class=''> @yield('error_details')</h3>
                <a href="{{URL::to('/')}}" class='btn btn-dark btn-lg mt-5'>{{__('Back to Home')}}</a>
            </div>
            <div class="footer pt-32">
                <p class="text-center"><?php echo date("Y")?> &copy; {{ config('app.name') }}</p>
            </div>
        </div>
    </body>

</html>
