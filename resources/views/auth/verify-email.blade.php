@extends('layouts.guest')
@section('title',__('Email Verification'))
@section('content')
<div class="text-center mb-5">
    <a href="{{url('/')}}"><img src="{{ config('app.favicon') }}" height="80" class='mb-4'></a>
    <h3>{{config('app.name')}} - {{ __('Email Verification') }}</h3>
    <p>  {{ __('Clicking the `Send Verification Email` button below will send an email with verification link to your registered email address.') }}</p>
</div>
@if (session('status') == 'verification-link-sent')
<div class="mb-4 alert alert-success text-center">
    {{ __("An email verification link has been sent to your email. Please verify your email address by clicking on the link we just emailed to you. We will gladly send you another email if you did not receive the first one, but please first check your spam folder.") }}
</div>
@endif

<form method="POST" id="login-form" action="{{ route('verification.send') }}">
    @csrf
    <div class="clearfix pt-3">
        <a href="{{route('logout')}}" class="btn btn-outline-dark btn-lg float-end">
            <i class="fas fa-sign-out-alt"></i>  {{ __('Log Out') }}
        </a>
        <button id="form-submit-button" class="btn btn-primary btn-lg float-start"><i class="fas fa-paper-plane"></i> {{ __('Send Verification Email') }}</button>
    </div>
</form>
@endsection
