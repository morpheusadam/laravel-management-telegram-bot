<!DOCTYPE html>
<html lang="{{ get_current_lang() }}">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="csrf_token()">
        <title>{{__('No Telegram bot found')}} - {{ config('app.name') }}</title>

        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="shortcut icon" href="{{ config('app.favicon') }}" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    </head>

    <body>
        <div class="card no-shadow">
            <div class="card-body">
              <div class="empty-state text-center">
                <img class="img-fluid w-100 h-300px" src="{{ asset('assets/images/drawkit/drawkit-nature-man-colour.svg') }}" alt="image">
                 <h2 class="mt-0">{{ __("No bot found.") }}</h2>
                <p class="lead">{{ __("We could not find any connected bot. Please connect a bot first.") }}</p>
                <div> <a href="{{route('connect-bot')}}" title="{{ __('Connect Bot') }}" data-toggle="tooltip" class="btn btn-primary" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> {{ __("Connect Bot") }}</a></div><br>
              </div>
            </div>
        </div>
    </body>

</html>
