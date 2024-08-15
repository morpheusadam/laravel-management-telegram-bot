<?php
$action_type = request()->type;
if(empty($action_type)) $action_type = 'user';
$title_display = $action_type=='team' ? __('Create Team') : __('Create User');
$title_display_des = $action_type=='team' ? __('Create a new team member') : __('Create a new user');
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 text-center">
                <h3>{{$title_display}}</h3>
                <p class="text-subtitle text-muted">{{$title_display_des}}</p>
            </div>
        </div>
    </div>

    <section id="basic-horizontal-layouts">
        <div class="row match-height">
            <div class="col-lg-8 offset-md-2 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('User Information') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('create-user-action') }}">
                                @csrf
                                <input type="hidden" name="action_type" id="action_type" value="{{$action_type}}">
                                <div class="form-body">

                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="name"> {{ __("Full Name") }} *</label>
                                                <input name="name"  class="form-control" type="text" value="{{old('name')}}">
                                                @if ($errors->has('name'))
                                                    <span class="text-danger"> {{ $errors->first('name') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="name"> {{ __("Email") }} *</label>
                                                <input name="email"  class="form-control" type="email" value="{{old('email')}}">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="name"> {{ __("Mobile") }}</label>
                                                <input name="mobile"  class="form-control" type="text" value="{{old('mobile')}}">
                                                @if ($errors->has('mobile'))
                                                    <span class="text-danger"> {{ $errors->first('mobile') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="name"> {{ __("Password") }} *</label>
                                                <input name="password"  class="form-control" type="password">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="name"> {{ __("Confirm Password") }} *</label>
                                                <input name="password_confirmation"  class="form-control" type="password">
                                                @if ($errors->has('password_confirmation'))
                                                    <span class="text-danger"> {{ $errors->first('password_confirmation') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Address') }}</label>
                                                <textarea id="address"  class="form-control" name="address">{{old('address')}}</textarea>
                                                @if ($errors->has('address'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('address') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-4 d-none">
                                            <div class="form-group">
                                                <label>{{ __('User Type') }}*</label>
                                                <input type="text" class="form-control" name="user_type" id="user_type" value="<?php echo $action_type=='team' ? 'Manager' : 'Member';?>">
                                                @if ($errors->has('user_type'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('user_type') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>{{ $action_type=='team' ? __('Team Role') :  __('Subscription Package') }}*</label>
                                                <select name="package_id" class="form-control select2">
                                                    <option value="">{{__('Select')}}</option>
                                                    @foreach($packages as $value)
                                                        <?php $selected = $value->id == old('package_id') ? 'selected' : '';?>
                                                        <option {{$selected}} value="{{$value->id}}">{{$value->package_name}} ({{$value->validity}} {{__('Days')}})</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('package_id'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('package_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($action_type!='team')
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>{{ __('Expiry date') }}*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                                    <input type="text" name="expired_date" class="form-control datepicker" value="{{old('expired_date')}}">
                                                </div>
                                                @if ($errors->has('expired_date'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('expired_date') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-3 mt-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="status" name="status" type="checkbox" value="1" <?php echo (old('status')=='0') ? '' : 'checked'; ?>>
                                                <label class="form-check-label" for="status">{{__("Status")}}</label>
                                            </div>
                                            @if ($errors->has('status'))
                                                <span class="text-danger"> {{ $errors->first('status') }} </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="form-footer mt-4">
                                    <button type="submit" class="btn btn-primary me-1"><i class="fas fa-save"></i> {{__('Create')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection