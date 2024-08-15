@extends('layouts.auth')
@section('title',__('Account'))
@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 <?php echo $is_admin || $is_manager ? 'text-center' : ''?>">
                <h3>{{ __('Account') }}</h3>
                <p class="text-subtitle text-muted">{{ __('Account Information') }}</p>
            </div>
        </div>
    </div>
    @if (session('save_user_profile')=='1')
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Account Updated')}}</h4>
            <p> {{ __('Account has been updated successfully.') }}</p>
        </div>
    @endif
	<section id="basic-horizontal-layouts">
        @if(($is_member || $is_agent) && !$is_manager)
            @include('member.payment.usage-log.stat')
        @endif
        <div class="row match-height">
            <div class="<?php echo $is_admin || $is_manager ? 'col-lg-6 offset-lg-3 col-md-12 col-12' : 'col-lg-5 col-md-12 col-12'?>">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <h4>{{__('Update Account')}}</h4>
                        </div>
                        <div class="card-body">
                            <form class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('account-action') }}">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    @php
                                                        $profile_pic  = !empty($data->profile_pic) ? $data->profile_pic : asset('assets/images/avatar/avatar-1.png');
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-4 col-md-2"><img src="{{ $profile_pic }}" width="70" height="70" class="border rounded-circle" alt="">
                                                        </div>
                                                        <div class="col-8 col-md-10">
                                                            <div class="form-group mt-1">
                                                                <div class="position-relative">
                                                                    <input type="file" id="profile_pic" class="form-control" name="profile_pic" >
                                                                    @if ($errors->has('profile_pic'))
                                                                        <span class="text-danger"> {{ $errors->first('profile_pic') }} </span>
                                                                    @else
                                                                        <span class="text-primary"> 200KB, png/jpg/webp, {{__('Square Image')}} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group has-icon-left">
                                                        <label>{{ __('Name') }}* </label>
                                                        <div class="position-relative">
                                                            <input type="text" id="first-name" class="form-control" name="name" value="{{old('name', $data->name)}}">
                                                            <div class="form-control-icon">
                                                                <i class="far fa-user"></i>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger"> {{ $errors->first('name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group has-icon-left">
                                                        <label>{{ __('Email') }}*</label>
                                                        <div class="position-relative">
                                                            <input type="email" id="email-id" class="form-control" name="email" value="{{old('email', $data->email)}}">
                                                            <div class="form-control-icon">
                                                                <i class="far fa-envelope"></i>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('email'))
                                                            <span class="text-danger"> {{ $errors->first('email') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group has-icon-left">
                                                        <label>{{ __('Mobile') }}</label>
                                                        <div class="position-relative">
                                                            <input type="text" id="contact-info" class="form-control" name="mobile" value="{{old('mobile', $data->mobile)}}">
                                                            <div class="form-control-icon">
                                                                <i class="fas fa-mobile-alt"></i>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('mobile'))
                                                            <span class="text-danger"> {{ $errors->first('mobile') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group has-icon-left">
                                                        <label>{{ __('Password') }}</label>
                                                        <div class="position-relative">
                                                            <input type="password" id="password" class="form-control" name="password" placeholder="******">
                                                            <div class="form-control-icon">
                                                                <i data-feather="lock"></i>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('password'))
                                                            <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group has-icon-left">
                                                        <label>{{ __('Confirm Password') }}</label>
                                                        <div class="position-relative">
                                                            <input type="password" id="password" class="form-control" name="password_confirmation" placeholder="******">
                                                            <div class="form-control-icon">
                                                                <i data-feather="lock"></i>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('password_confirmation'))
                                                            <span class="text-danger"> {{ $errors->first('mobile') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Address') }}</label>
                                                        <textarea id="address"  class="form-control" name="address">{{old('address', $data->address)}}</textarea>
                                                        @if ($errors->has('address'))
                                                            <span class="text-danger">
                                                            {{ $errors->first('address') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Timezone') }}</label>
                                                        @php
                                                            $select_timezone = !empty($data->timezone) ? $data->timezone : config('app.userTimezone');
                                                            $selected = old('timezone', $select_timezone);
                                                            if($selected=='UTC') $selected='Europe/Dublin';
                                                            $timezone_list = get_timezone_list();
                                                            echo Form::select('timezone',$timezone_list,$selected,array('class'=>'form-control select2'));
                                                        @endphp
                                                        @if ($errors->has('timezone'))
                                                            <span class="text-danger">
                                                                {{ $errors->first('timezone') }}
                                                            </span>
                                                        @endif

                                                    </div>
                                                </div>

                                                <div class="col-12 mt-4">
                                                    <button type="submit" class="btn btn-primary me-1 mb-1"><i class="fas fa-edit"></i> {{ __('Update') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if(($is_member || $is_agent) && !$is_manager)
                <div class="col-lg-7 col-md-12 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-header">
                                <h4>{{__('Usage Log')}}</h4>
                            </div>
                            <div class="card-body">
                                @include('member.payment.usage-log.list')
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

</div>
@endsection
