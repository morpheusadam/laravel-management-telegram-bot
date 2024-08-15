@extends('layouts.guest')
@section('title',__('Confirm Password'))
@section('content')
    <div class="text-center mb-5">
        <a href="{{url('/')}}"><img src="{{ config('app.favicon') }}" height="80" class='mb-4'></a>
        <h3>{{config('app.name')}} - {{ __('Confirm Password') }}</h3>
        {{ __('Please confirm your password to continue.') }}
    </div>
    <form method="POST" id="login-form" action="{{ route('password.confirm') }}">
        @csrf
        <div class="form-group">
            <label for="">{{ __("Password") }} </label>
            <div class="input-group">
                <span class="input-group-text"><i data-feather="key"></i></span>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                <button id="form-submit-button" class="btn btn-primary float-end">{{ __('Confirm Password') }}</button>
            </div>
            @if ($errors->has('password'))
                <span class="text-danger">
                    {{ $errors->first('password') }}
                </span>
            @endif
        </div>
    </form>
@endsection
