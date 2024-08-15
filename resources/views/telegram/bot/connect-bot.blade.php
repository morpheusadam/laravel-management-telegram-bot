@extends('layouts.auth')
@section('title',__('Connect Telegram'))
@section('content')
<div class="main-content container">
    @include('shared.limit-check-error')
    @if (session('connect_bot_status')=='1')
        <div class="mb-4 alert alert-light-success alert-dismissible fade show p-4 border-success border-dashed"  role="alert">
            <h4 class="alert-heading text-success mb-0">
                <i class="fas fa-check-circle fs-1 float-start mt-1 me-3"></i>
                {{__('Connected')}}
            </h4>
            <p class="mt-1">{{ __('Bot has been connected successfully.') }}</a></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('connect_bot_status')=='0')
        <div class="mb-4 alert alert-light-danger alert-dismissible fade show p-4 border-danger border-dashed"  role="alert">
            <h4 class="alert-heading text-danger mb-0">
                <i class="far fa-times-circle fs-1 float-start mt-1 me-3"></i>
                {{__('Failed to connect')}}
            </h4>
            <p class="mt-1"><b>{{ session('api_error_message') }}</b> {{ __('Something went wrong.') }} {{session('connect_bot_error_message')}}</a></p>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="page-title">
        <div class="row">
            <div class="col-12 text-center">
                <h3>{{ __('Connect Telegram Bot') }}</h3>
                <p class="text-subtitle text-muted">{{ __('Connect your Telegram bot') }}</p>
            </div>
        </div>
    </div>

    <section id="basic-horizontal-layouts">
        <div class="row match-height">
            <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-12">
                <div class="card box-shadow card-icon-bg">
                    <div class="card-header px-4 pt-4 pb-2 px-md-5 pt-md-5">
                        <h4 class="card-title d-flex align-items-start flex-column">
                            <span class="card-label text-primary">{{ __('Telegram Bot') }}</span>
                            <small class="text-muted mt-2 fw-normal"><i class="fas fa-circle text-success"></i> {{ __('One click bot connection') }}</small>
                        </h4>
                    </div>

                    <form class="form form-horizontal" id="" method="POST" action="{{ route('connect-bot-action') }}">
                        @csrf
                        <div class="card-body py-0">
                            <div class="card-icon-container">
                                <img src="{{asset('assets/images/flaticon/robot.png')}}" width="150">
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="ms-md-3 me-5 d-none d-lg-flex">
                                    <span class="text-primary icon-mega">
                                        <i class="fab fa-telegram"></i>
                                    </span>
                                </div>
                                <div class="m-0">
                                    <h4 class="fs-6 mb-2">{{ __('Telegram Bot Token') }}</h4>
                                    <div class="d-flex d-grid gap-5">
                                        <div class="">
                                            <div class="form-body">
                                                <input type="text" autofocus id="bot_token" class="form-control mb-2" name="bot_token" placeholder="123456789:xxxxxxxx__xxxxxxxxxxxxxxxxxxxxx">
                                                @if ($errors->has('bot_token'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('bot_token') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 bg-light-purple border-dashed border-primary">
                            <div class="mb-0">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-link"></i> {{ __('Connect') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="h-60px"></div>

    @if (count($telegram_bots)>0)
        <div class="page-title mt-4">
            <div class="row">
                <div class="col-12">
                    <h3>{{ __('Connected Bots') }}</h3>
                    <p class="text-subtitle text-muted">{{ __('Bots you have already connected with us') }}</p>
                </div>
            </div>
        </div>
        <section id="bg-variants">
            <div class="row">
                @php($sl=0)
                @foreach($telegram_bots as $bot)
                    @php($sl++)
                    <?php $bg_color = "bg-white" ?>
                    <?php $txt_color = "" ?>
                    <?php $checked = ($bot->status=='1') ? "checked" : "" ?>
                    <?php $color = ($bot->status=='1') ? "text-success" : "text-muted" ?>
                    <div class="col-12 col-md-6 col-xl-4 bot-item">
                        <div id="" class="card card-icon-bg box-shadow">
                            <div class="card-header">
                                <h4 class="card-title d-flex align-items-start flex-column">
                                    <span class="card-label"><i class="fas fa-circle me-1 {{$color}}"></i> {{$bot->first_name}} {{$bot->last_name}}</span>
                                </h4>
                            </div>
                            <div class="card-body pt-0 pb-2 min-height-50px">
                                <div class="card-icon-container">
                                    <i class="fab fa-telegram text-primary"></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-4 me-md-5">
                                        <span class="text-primary icon-mid">
                                             <img src="{{asset('assets/images/flaticon/robot.png')}}" width="60">
                                        </span>
                                    </div>
                                    <div class="m-0">
                                        <h4 class="fs-6 mb-3">
                                            <a class="text-dark" href="https://t.me/{{$bot->username}}" target="_BLANK">{{"@".$bot->username}}</a>
                                        </h4>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input update-status" type="checkbox" data-url="{{route('update-bot-status')}}" data-id="{{$bot->id}}" {{$checked}}>
                                            <label class="form-check-label" for="active_inactive_bot">{{__("Active")}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p-2 bg-light-primary border-dashed border-primary">
                                <div class="">
                                    <a href="" class="px-2 py-1 btn btn-sm btn-primary sync_bot" data-id="{{$bot->id}}"><i class="far fa-check-circle"></i> {{__("Sync")}}</a>
                                    <a href="" class="px-2 py-1 btn btn-sm bg-dark text-white delete_bot ms-1" data-id="{{$bot->id}}"><i class="fas fa-unlink"></i> {{__("Disconnect")}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection

@push('styles-footer')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/telegram/bot.connect-bot.css') }}">
@endpush

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/telegram/bot.connect-bot.js') }}"></script>
@endpush
