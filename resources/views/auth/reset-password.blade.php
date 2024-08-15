@extends('layouts.guest')
@section('title',__('Reset Password'))
@section('content')
<div class="text-center mb-5">
    <a href="{{url('/')}}"><img src="{{ config('app.favicon') }}" height="80" class='mb-4'></a>
    <h3>{{config('app.name')}} - {{ __('Reset Password') }}</h3>
</div>
<form method="POST" id="login-form" action="{{ !empty($agent_user_email) ? route('password.update').'?email='.$agent_user_email : route('password.update')}}">
    @csrf
       <!-- Password Reset Token -->

    <div class="form-group position-relative has-icon-left d-none">
        <label for="token">{{ __('Input token') }}</label>
        <div class="position-relative">
            <input type="hidden" id="token" name="token" value="{{ $request->route('token') }}">
            <div class="form-control-icon">
                <i data-feather="user"></i>
            </div>
        </div>
    </div>
    <div class="form-group position-relative has-icon-left">
        <label for="email">{{ __('Email') }}</label>
        <div class="position-relative">
            <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $request->email) }}">
            <div class="form-control-icon">
                <i data-feather="user"></i>
            </div>
        </div>
        @if ($errors->has('email'))
            <span class="text-danger">
                {{ $errors->first('email') }}
            </span>
        @endif
    </div>
    <div class="form-group position-relative has-icon-left">
        <div class="clearfix">
            <label for="password">{{ __('Password') }}</label>
        </div>
        <div class="position-relative">
            <input type="password" name="password" class="form-control" id="password">
            <div class="form-control-icon">
                <i data-feather="lock"></i>
            </div>
        </div>
        @if ($errors->has('password'))
            <span class="text-danger">
                {{ $errors->first('password') }}
            </span>
        @endif
    </div>
    <div class="form-group position-relative has-icon-left">
        <div class="clearfix">
            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
        </div>
        <div class="position-relative">
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
            <div class="form-control-icon">
                <i data-feather="lock"></i>
            </div>
        </div>
        @if ($errors->has('password_confirmation'))
        <span class="text-danger">
            {{ $errors->first('password_confirmation') }}
        </span>
        @endif
    </div>
    <div class="clearfix">
        <button id="form-submit-button" class="btn btn-primary btn-lg w-100"><i class="fas fa-key"></i> {{ __('Reset Password') }}</button>
    </div>
</form>
@endsection