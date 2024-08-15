@extends('layouts.guest')
@section('title',__('Sign Up'))
@section('content')
<div class="text-center mb-5">
    <a href="{{url('/')}}"><img src="{{ config('app.favicon') }}" height="80" class='mb-4'></a>
    <h3>{{config('app.name')}} - {{ __('Sign Up') }}</h3>
    <?php $url_email = request()->email ?? '';?>
</div>
<form method="POST" id="register-form">
    <div class="row mb-2">
        <div class="col-12">
            <div class="form-group position-relative has-icon-left">
                <div class="clearfix">
                    <label for="name">{{ __('Name') }}</label>
                </div>
                <div class="position-relative">
                    <input type="text" id="name" autofocus name="name" class="form-control" value="{{ old('name') }}">
                    <div class="form-control-icon">
                        <i data-feather="user"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group position-relative has-icon-left">
                <div class="clearfix">
                    <label for="email">{{ __('Email') }}</label>
                </div>
                <div class="position-relative">
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email',$url_email) }}">
                    <div class="form-control-icon">
                        <i data-feather="at-sign"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group position-relative has-icon-left">
                <div class="clearfix">
                    <label for="password">{{ __('Password') }}</label>
                </div>
                <div class="position-relative">
                    <input type="password" id="password" name="password" class="form-control" value="{{ old('password') }}">
                    <div class="form-control-icon">
                        <i data-feather="lock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group position-relative has-icon-left">
                <div class="clearfix">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                </div>
                <div class="position-relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
                    <div class="form-control-icon">
                        <i data-feather="lock"></i>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($tos_url) && !empty($privacy_url))
        <div class="col-12">
            <div class="form-check form-switch mt-2">
              <input class="form-check-input" name="terms" type="checkbox" id="terms" value="1">
              <label class="form-check-label" for="terms">{{__('I agree to the')}} <a href="{{$tos_url}}">{{__('Terms of Service')}}</a> & <a href="{{$privacy_url}}">{{__('Privacy Policy')}}</a></label>
            </div>
        </div>
        @else
             <input class="d-none" name="terms" type="checkbox" id="terms" checked value="1">
        @endif
    </diV>

    <button type="submit" id="form-submit-button" class="btn btn-lg btn-primary w-100"><i class="fas fa-user-circle"></i> {{ __('Sign Up') }}</button>
    <a class="float-start mt-3 text-dark" href="{{ !empty($agent_user_id) ? route('login').'?at='.$agent_user_id : route('login')}}"><i class="fas fa-user-circle"></i> {{ __('Login') }}</a>
</form>
@endsection

@push('scripts-footer')
<script src="{{ asset('assets/js/pages/auth/auth.register.js') }}"></script>
@endpush
