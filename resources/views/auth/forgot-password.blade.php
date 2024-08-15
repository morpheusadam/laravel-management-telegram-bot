@extends('layouts.guest')
@section('title',__('Reset Password'))
@section('content')
<div class="text-center mb-5">
    <a href="{{url('/')}}"><img src="{{ config('app.favicon') }}" height="80" class='mb-4'></a>
     <h3>{{config('app.name')}} - {{ __('Reset Password') }}</h3>
    <p>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
    <p class="text-success">{{session('status')}}</p>
</div>
<form method="POST" id="login-form" action="{{ !empty($agent_user_id) ? route('password.email').'?at='.$agent_user_id : route('password.email')}}">
    @csrf
    <div class="form-group">
        <label for="">{{ __("Email") }} </label>
        <div class="input-group">
            <span class="input-group-text"><i data-feather="user"></i></span>
            <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
            <button id="form-submit-button" class="btn btn-primary float-end"><i class="fas fa-paper-plane"></i> {{ __('Send Password Reset Link') }}</button>
        </div>
        @if ($errors->has('email'))
            <span class="text-danger">
                {{ $errors->first('email') }}
            </span>
        @endif
    </div>
</form>
@endsection
