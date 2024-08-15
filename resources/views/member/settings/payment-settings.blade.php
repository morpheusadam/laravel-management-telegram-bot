@extends('layouts.auth')
@section('title',__('Payment Settings'))
@push("styles-header")
<link rel="stylesheet" href="{{ asset('assets/css/pages/settings.payment-settings.css') }}">
@endpush
@section('content')
    <div class="main-content <?php echo isset($iframe) && $iframe ? '' : 'container-fluid'?>">
        <div class="page-title <?php echo isset($iframe) && $iframe ? 'd-none' : ''?>">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{__('Payment Settings')}} </h3>
                    <p class="text-subtitle text-muted">{{__('Payment accounts setup')}}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class='breadcrumb-header'>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('general-settings')}}">{{__('Settings')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('Payment')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('save_payment_accounts_status')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Successful')}}</h4>
                <p> {{ __('Payment accounts have been saved successfully.') }}</p>
            </div>
        @endif
        @if (session()->has('paypal_error'))
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('Error')}}</h4>
                <p> {{ "Paypal :" }}{{session('paypal_error')}}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-warning mb-0 no-radius">
                <h4 class="alert-heading">{{__('Something Missing')}}</h4>
                <p> {{ __('Something is missing. Please check the the required inputs.') }}</p>
            </div>
            <?php
            if(!empty($errors->all()))
                echo '<ul class="list-group mb-4">';
                foreach ($errors->all() as $err){
                    echo '<li class="list-group-item fw-bold text-warning no-radius"><i class="fas fa-exclamation-circle"></i> '.$err.'</li>';
                }
                echo '</ul>';
            ?>
        @endif
        @if (session('save_payment_accounts_minimun_one_required')=='1')
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('No Data')}}</h4>
                <p> {{ __('You must enable at least one payment account.') }}</p>
            </div>
        @endif


        <section class="section">
            <form  class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('payment-settings-action') }}">
                @csrf
                <input type="hidden" name="ecommerce_store_id" value="{{$ecommerce_store_id}}">
                <input type="hidden" name="whatsapp_bot_id" value="{{$whatsapp_bot_id}}">
                <div class="row">
                    <div class="col-12 <?php echo isset($iframe) && $iframe ? 'col-md-3' : 'col-md-4'?>">
                        <div class="card mb-4 <?php echo isset($iframe) && $iframe ? '' : 'h-min-480px'?>">
                            <div class="card-header">
                                <h4>{{__('Currency')}}</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                $manual_payment_instruction = $xdata->manual_payment_instruction ?? '';
                                $manual_payment_status = $xdata->manual_payment_status ?? '0';
                                $decimal_point = $xdata->decimal_point ?? '2';
                                $currency_position = $xdata->currency_position ?? 'left';
                                $thousand_comma = $xdata->thousand_comma ?? '0';
                                $currency = $xdata->currency ?? 'USD';
                                ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="">{{ __("Currency") }} </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                                <?php echo Form::select('currency',get_country_iso_phone_currency_list('currency_name'),old('currency', $currency),array('class'=>'form-control select2'));?>
                                            </div>
                                            @if ($errors->has('currency'))
                                                <span class="text-danger d-none"> {{ $errors->first('currency') }} </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="">{{ __("Currency Position") }} </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-adjust"></i></span>
                                                <?php echo Form::select('currency_position',['left'=>__('Left'),'right'=>__('Right')],old('currency_position', $currency_position),array('class'=>'form-control'));?>
                                            </div>
                                            @if ($errors->has('currency_position'))
                                                <span class="text-danger d-none"> {{ $errors->first('currency_position') }} </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="">{{ __("Decimal Place") }} </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dot-circle"></i></span>
                                                <input type="number" name="decimal_point" id="decimal_point" min="0" class="form-control" value="{{old('decimal_point',$decimal_point)}}">
                                            </div>
                                            @if ($errors->has('decimal_point'))
                                                <span class="text-danger d-none"> {{ $errors->first('decimal_point') }} </span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="thousand_comma" >{{ __('Thousand Comma') }}</label>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" id="thousand_comma" name="thousand_comma" type="checkbox" value="1" <?php echo old('thousand_comma',$thousand_comma)=='1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="thousand_comma">{{__("Enable")}}</label>
                                                        </div>
                                                    </span>
                                                </div>
                                                @if ($errors->has('thousand_comma'))
                                                    <span class="text-danger d-none"> {{ $errors->first('thousand_comma') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 <?php echo isset($iframe) && $iframe ? 'col-md-9' : 'col-md-8'?>">
                        <div class="card mb-4 h-min-480px">
                            <div class="card-header">
                                <h4>{{__('Payment APIs')}}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                        <div class="col-8">
                                            <div class="tab-content" id="v-pills-tabContent">

                                                @if(check_build_version()=='double')

                                                <div class="tab-pane fade active show" id="paypal-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $paypal_data = isset($xdata->paypal) ? json_decode($xdata->paypal) : [];
                                                    if(config('app.is_demo')=='1') $paypal_data = [];
                                                    $paypal_client_id = $paypal_data->paypal_client_id ?? '';
                                                    $paypal_client_secret = $paypal_data->paypal_client_secret ?? '';
                                                    $paypal_app_id = $paypal_data->paypal_app_id ?? '';
                                                    $paypal_payment_type = $paypal_data->paypal_payment_type ?? 'manual';
                                                    $paypal_mode = $paypal_data->paypal_mode ?? 'live';
                                                    $paypal_status = $paypal_data->paypal_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Paypal Client Id") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                                                    <input name="paypal_client_id" value="{{old('paypal_client_id',$paypal_client_id)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('paypal_client_id'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('paypal_client_id') }} </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">{{ __("Paypal Client Secret") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="paypal_client_secret" value="{{old('paypal_client_secret',$paypal_client_secret)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('paypal_client_secret'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('paypal_client_secret') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12 d-none">
                                                            <div class="form-group">
                                                                <label for="paypal_payment_type" >{{ __('Recurring Payment') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="paypal_payment_type" name="paypal_payment_type" type="checkbox" value="recurring" <?php echo Request::segment(3) == "0" ? 'checked' : '';?>>
                                                                                <label class="form-check-label" for="paypal_payment_type">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('paypal_payment_type'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('paypal_payment_type') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="paypal_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="paypal_mode" name="paypal_mode" type="checkbox" value="sandbox" <?php echo old('paypal_mode',$paypal_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="paypal_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('paypal_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('paypal_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="paypal_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="paypal_status" name="paypal_status" type="checkbox" value="1" <?php echo old('paypal_status',$paypal_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="paypal_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('paypal_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('paypal_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="stripe-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $stripe_data = isset($xdata->stripe) ? json_decode($xdata->stripe) : [];
                                                    if(config('app.is_demo')=='1') $stripe_data = [];
                                                    $stripe_secret_key = $stripe_data->stripe_secret_key ?? '';
                                                    $stripe_publishable_key = $stripe_data->stripe_publishable_key ?? '';
                                                    $stripe_status= $stripe_data->stripe_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Stripe Secret Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="stripe_secret_key" value="{{old('stripe_secret_key',$stripe_secret_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('stripe_secret_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('stripe_secret_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Stripe Publishable Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="stripe_publishable_key" value="{{old('stripe_publishable_key',$stripe_publishable_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('stripe_publishable_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('stripe_publishable_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="stripe_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="stripe_status" name="stripe_status" type="checkbox" value="1" <?php echo old('stripe_status',$stripe_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="stripe_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('stripe_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('stripe_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                 <div class="tab-pane fade" id="yoomoney-block" role="tabpanel" aria-labelledby="">
                                                    <?php

                                                    $yoomoney_data = isset($xdata->yoomoney) ? json_decode($xdata->yoomoney) : [];
                                                    if(config('app.is_demo')=='1') $yoomoney_data = [];
                                                    $yoomoney_shop_id = $yoomoney_data->yoomoney_shop_id ?? '';
                                                    $yoomoney_secret_key = $yoomoney_data->yoomoney_secret_key ?? '';
                                                    $yoomoney_status= $yoomoney_data->yoomoney_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("yoomoney Shop ID") }} </label>
                                                                <div class="input-group">

                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="yoomoney_shop_id" value="{{old('yoomoney_shop_id',$yoomoney_shop_id)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('yoomoney_shop_id'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('yoomoney_shop_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("yoomoney Secret Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="yoomoney_secret_key" value="{{old('yoomoney_secret_key',$yoomoney_secret_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('yoomoney_secret_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('yoomoney_secret_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="yoomoney_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="yoomoney_status" name="yoomoney_status" type="checkbox" value="1" <?php echo old('yoomoney_status',$yoomoney_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="yoomoney_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('yoomoney_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('yoomoney_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="razorpay-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $razorpay_data = isset($xdata->razorpay) ? json_decode($xdata->razorpay) : [];
                                                    if(config('app.is_demo')=='1') $razorpay_data = [];
                                                    $razorpay_key_id = $razorpay_data->razorpay_key_id ?? '';
                                                    $razorpay_key_secret = $razorpay_data->razorpay_key_secret ?? '';
                                                    $razorpay_status = $razorpay_data->razorpay_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Razorpay Key ID") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="razorpay_key_id" value="{{old('razorpay_key_id',$razorpay_key_id)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('razorpay_key_id'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('razorpay_key_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Razorpay Key Secret") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="razorpay_key_secret" value="{{old('razorpay_key_secret',$razorpay_key_secret)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('razorpay_key_secret'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('razorpay_key_secret') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="razorpay_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="razorpay_status" name="razorpay_status" type="checkbox" value="1" <?php echo old('razorpay_status',$razorpay_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="razorpay_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('razorpay_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('razorpay_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="paystack-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $paystack_data = isset($xdata->paystack) ? json_decode($xdata->paystack) : [];
                                                    if(config('app.is_demo')=='1') $paystack_data = [];
                                                    $paystack_secret_key = $paystack_data->paystack_secret_key ?? '';
                                                    $paystack_public_key = $paystack_data->paystack_public_key ?? '';
                                                    $paystack_status = $paystack_data->paystack_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Paystack Secret Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="paystack_secret_key" value="{{old('paystack_secret_key',$paystack_secret_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('paystack_secret_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('paystack_secret_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Razorpay Key Secret") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="paystack_public_key" value="{{old('paystack_public_key',$paystack_public_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('paystack_public_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('paystack_public_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="paystack_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="paystack_status" name="paystack_status" type="checkbox" value="1" <?php echo old('paystack_status',$paystack_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="paystack_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('paystack_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('paystack_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="mercadopago-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $mercadopago_data = isset($xdata->mercadopago) ? json_decode($xdata->mercadopago) : [];
                                                    if(config('app.is_demo')=='1') $mercadopago_data = [];
                                                    $mercadopago_public_key = $mercadopago_data->mercadopago_public_key ?? '';
                                                    $mercadopago_access_token = $mercadopago_data->mercadopago_access_token ?? '';
                                                    $mercadopago_country = $mercadopago_data->mercadopago_country ?? '';
                                                    $mercadopago_status = $mercadopago_data->mercadopago_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Mercado Pago Public Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="mercadopago_public_key" value="{{old('mercadopago_public_key',$mercadopago_public_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('mercadopago_public_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('mercadopago_public_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Mercado Pago Access Token") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="mercadopago_access_token" value="{{old('mercadopago_access_token',$mercadopago_access_token)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('mercadopago_access_token'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('mercadopago_access_token') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="mercadopago_country" >{{ __('Country') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                                        <?php echo Form::select('mercadopago_country',get_mercadopago_country_list(),old('mercadopago_country', $mercadopago_country),array('class'=>'form-control select2'));?>

                                                                    </div>
                                                                    @if ($errors->has('mercadopago_country'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('mercadopago_country') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="mercadopago_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="mercadopago_status" name="mercadopago_status" type="checkbox" value="1" <?php echo old('mercadopago_status',$mercadopago_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="mercadopago_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('mercadopago_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('mercadopago_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>



                                                <div class="tab-pane fade" id="flutterwave-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $flutterwave_data =  isset($xdata->flutterwave) ? json_decode($xdata->flutterwave) : [];
                                                    if(config('app.is_demo')=='1') $flutterwave_data = [];
                                                    $flutterwave_api_key = $flutterwave_data->flutterwave_api_key ?? '';
                                                    $flutterwave_status = $flutterwave_data->flutterwave_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Flutterwave Public Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="flutterwave_api_key" value="{{old('flutterwave_api_key',$flutterwave_api_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('flutterwave_api_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('flutterwave_api_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="flutterwave_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-4 w-100">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="flutterwave_status" name="flutterwave_status" type="checkbox" value="1" <?php echo old('flutterwave_status',$flutterwave_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="flutterwave_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('flutterwave_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('flutterwave_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>



                                                <div class="tab-pane fade" id="mollie-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $mollie_data =  isset($xdata->mollie) ? json_decode($xdata->mollie) : [];
                                                    if(config('app.is_demo')=='1') $mollie_data = [];
                                                    $mollie_api_key = $mollie_data->mollie_api_key ?? '';
                                                    $mollie_status = $mollie_data->mollie_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Mollie API Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="mollie_api_key" value="{{old('mollie_api_key',$mollie_api_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('mollie_api_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('mollie_api_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="mollie_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="mollie_status" name="mollie_status" type="checkbox" value="1" <?php echo old('mollie_status',$mollie_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="mollie_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('mollie_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('mollie_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>



                                                <div class="tab-pane fade" id="sslcommerz-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $sslcommerz_data = isset($xdata->sslcommerz) ? json_decode($xdata->sslcommerz) : [];
                                                    if(config('app.is_demo')=='1') $sslcommerz_data = [];
                                                    $sslcommerz_store_id = $sslcommerz_data->sslcommerz_store_id ?? '';
                                                    $sslcommerz_store_password = $sslcommerz_data->sslcommerz_store_password ?? '';
                                                    $sslcommerz_mode = $sslcommerz_data->sslcommerz_mode ?? 'live';
                                                    $sslcommerz_status = $sslcommerz_data->sslcommerz_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("SSLCommerz Store ID") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="sslcommerz_store_id" value="{{old('sslcommerz_store_id',$sslcommerz_store_id)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('sslcommerz_store_id'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('sslcommerz_store_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("SSLCommerz Store Password") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="sslcommerz_store_password" value="{{old('sslcommerz_store_password',$sslcommerz_store_password)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('sslcommerz_store_password'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('sslcommerz_store_password') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="sslcommerz_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="sslcommerz_mode" name="sslcommerz_mode" type="checkbox" value="sandbox" <?php echo old('sslcommerz_mode',$sslcommerz_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="sslcommerz_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('sslcommerz_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('sslcommerz_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="sslcommerz_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="sslcommerz_status" name="sslcommerz_status" type="checkbox" value="1" <?php echo old('sslcommerz_status',$sslcommerz_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="sslcommerz_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('sslcommerz_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('sslcommerz_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="senangpay-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $senangpay_data = isset($xdata->senangpay) ? json_decode($xdata->senangpay) : [];
                                                    if(config('app.is_demo')=='1') $senangpay_data = [];
                                                    $senangpay_merchent_id = $senangpay_data->senangpay_merchent_id ?? '';
                                                    $senangpay_secret_key = $senangpay_data->senangpay_secret_key ?? '';
                                                    $senangpay_mode = $senangpay_data->senangpay_mode ?? 'live';
                                                    $senangpay_status = $senangpay_data->senangpay_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("senangPay Merchent ID") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="senangpay_merchent_id" value="{{old('senangpay_merchent_id',$senangpay_merchent_id)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('senangpay_merchent_id'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('senangpay_merchent_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("senangPay Secret Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="senangpay_secret_key" value="{{old('senangpay_secret_key',$senangpay_secret_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('senangpay_secret_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('senangpay_secret_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="senangpay_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="senangpay_mode" name="senangpay_mode" type="checkbox" value="sandbox" <?php echo old('senangpay_mode',$senangpay_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="senangpay_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('senangpay_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('senangpay_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="senangpay_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="senangpay_status" name="senangpay_status" type="checkbox" value="1" <?php echo old('senangpay_status',$senangpay_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="senangpay_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('senangpay_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('senangpay_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="alert alert-light-info">
                                                                @if(Request::segment(3) != "0" && !empty(Request::segment(3)))
                                                                <b>{{__('Senangpay return URL')}}</b> : <br><i>{{route('ecommerce-store-proceed-checkout-senangpay')}}</i>
                                                                @elseif(Request::segment(4) != "0" && !empty(Request::segment(4)))
                                                                <b>{{__('Senangpay return URL')}}</b> : <br><i>{{route('whatsapp-catalog-store-proceed-checkout-senangpay')}}</i>
                                                                @else
                                                                <b>{{__('Senangpay return URL')}}</b> : <br><i>{{route('senangpay-action')}}</i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="instamojo-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $instamojo_data = isset($xdata->instamojo) ? json_decode($xdata->instamojo) : [];
                                                    if(config('app.is_demo')=='1') $instamojo_data = [];
                                                    $instamojo_api_key = $instamojo_data->instamojo_api_key ?? '';
                                                    $instamojo_auth_token = $instamojo_data->instamojo_auth_token ?? '';
                                                    $instamojo_mode = $instamojo_data->instamojo_mode ?? 'live';
                                                    $instamojo_status = $instamojo_data->instamojo_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Instamojo API Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="instamojo_api_key" value="{{old('instamojo_api_key',$instamojo_api_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('instamojo_api_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('instamojo_api_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Instamojo Auth Token") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="instamojo_auth_token" value="{{old('instamojo_auth_token',$instamojo_auth_token)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('instamojo_auth_token'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('instamojo_auth_token') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="instamojo_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="instamojo_mode" name="instamojo_mode" type="checkbox" value="sandbox" <?php echo old('instamojo_mode',$instamojo_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="instamojo_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('instamojo_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('instamojo_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="instamojo_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="instamojo_status" name="instamojo_status" type="checkbox" value="1" <?php echo old('instamojo_status',$instamojo_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="instamojo_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('instamojo_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('instamojo_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>


                                                <div class="tab-pane fade" id="instamojo_v2-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $instamojo_v2_data = isset($xdata->instamojo_v2) ? json_decode($xdata->instamojo_v2) : [];
                                                    if(config('app.is_demo')=='1') $instamojo_v2_data = [];
                                                    $instamojo_client_id = $instamojo_v2_data->instamojo_client_id ?? '';
                                                    $instamojo_client_secret = $instamojo_v2_data->instamojo_client_secret ?? '';
                                                    $instamojo_v2_mode = $instamojo_v2_data->instamojo_v2_mode ?? 'live';
                                                    $instamojo_v2_status = $instamojo_v2_data->instamojo_v2_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Instamojo Client ID") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="instamojo_client_id" value="{{old('instamojo_client_id',$instamojo_client_id)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('instamojo_client_id'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('instamojo_client_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Instamojo Client Secret") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="instamojo_client_secret" value="{{old('instamojo_client_secret',$instamojo_client_secret)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('instamojo_client_secret'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('instamojo_client_secret') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="instamojo_v2_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="instamojo_v2_mode" name="instamojo_v2_mode" type="checkbox" value="sandbox" <?php echo old('instamojo_v2_mode',$instamojo_v2_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="instamojo_v2_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('instamojo_v2_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('instamojo_v2_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="instamojo_v2_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="instamojo_v2_status" name="instamojo_v2_status" type="checkbox" value="1" <?php echo old('instamojo_v2_status',$instamojo_v2_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="instamojo_v2_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('instamojo_v2_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('instamojo_v2_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="toyyibpay-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $toyyibpay_data = isset($xdata->toyyibpay) ? json_decode($xdata->toyyibpay) : [];
                                                    if(config('app.is_demo')=='1') $toyyibpay_data = [];
                                                    $toyyibpay_secret_key = $toyyibpay_data->toyyibpay_secret_key ?? '';
                                                    $toyyibpay_category_code = $toyyibpay_data->toyyibpay_category_code ?? '';
                                                    $toyyibpay_mode = $toyyibpay_data->toyyibpay_mode ?? 'live';
                                                    $toyyibpay_status = $toyyibpay_data->toyyibpay_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("toyyibPay Secret Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="toyyibpay_secret_key" value="{{old('toyyibpay_secret_key',$toyyibpay_secret_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('toyyibpay_secret_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('toyyibpay_secret_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("toyyibPay Category Code") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="toyyibpay_category_code" value="{{old('toyyibpay_category_code',$toyyibpay_category_code)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('toyyibpay_category_code'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('toyyibpay_category_code') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="toyyibpay_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="toyyibpay_mode" name="toyyibpay_mode" type="checkbox" value="sandbox" <?php echo old('toyyibpay_mode',$toyyibpay_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="toyyibpay_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('toyyibpay_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('toyyibpay_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="toyyibpay_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="toyyibpay_status" name="toyyibpay_status" type="checkbox" value="1" <?php echo old('toyyibpay_status',$toyyibpay_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="toyyibpay_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('toyyibpay_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('toyyibpay_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="xendit-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $xendit_data = isset($xdata->xendit) ? json_decode($xdata->xendit) : [];
                                                    if(config('app.is_demo')=='1') $xendit_data = [];
                                                    $xendit_secret_api_key = $xendit_data->xendit_secret_api_key ?? '';
                                                    $xendit_status = $xendit_data->xendit_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Xendit Secret API Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="xendit_secret_api_key" value="{{old('xendit_secret_api_key',$xendit_secret_api_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('xendit_secret_api_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('xendit_secret_api_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="xendit_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="xendit_status" name="xendit_status" type="checkbox" value="1" <?php echo old('xendit_status',$xendit_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="xendit_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('xendit_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('xendit_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="myfatoorah-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $myfatoorah_data = isset($xdata->myfatoorah) ? json_decode($xdata->myfatoorah) : [];
                                                    if(config('app.is_demo')=='1') $myfatoorah_data = [];
                                                    $myfatoorah_api_key = $myfatoorah_data->myfatoorah_api_key ?? '';
                                                    $myfatoorah_mode = $myfatoorah_data->myfatoorah_mode ?? 'live';
                                                    $myfatoorah_status = $myfatoorah_data->myfatoorah_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Myfatoorah API Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="myfatoorah_api_key" value="{{old('myfatoorah_api_key',$myfatoorah_api_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('myfatoorah_api_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('myfatoorah_api_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="myfatoorah_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="myfatoorah_mode" name="myfatoorah_mode" type="checkbox" value="sandbox" <?php echo old('myfatoorah_mode',$myfatoorah_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="myfatoorah_mode">{{__("Enable")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('myfatoorah_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('myfatoorah_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="myfatoorah_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="myfatoorah_status" name="myfatoorah_status" type="checkbox" value="1" <?php echo old('myfatoorah_status',$myfatoorah_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="myfatoorah_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('myfatoorah_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('myfatoorah_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="paymaya-block" role="tabpanel" aria-labelledby="">
                                                    <?php
                                                    $paymaya_data = isset($xdata->paymaya) ? json_decode($xdata->paymaya) : [];
                                                    if(config('app.is_demo')=='1') $paymaya_data = [];
                                                    $paymaya_public_key = $paymaya_data->paymaya_public_key ?? '';
                                                    $paymaya_secret_key = $paymaya_data->paymaya_secret_key ?? '';
                                                    $paymaya_mode = $paymaya_data->paymaya_mode ?? 'live';
                                                    $paymaya_status = $paymaya_data->paymaya_status ?? '0';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("PayMaya Public Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                                    <input name="paymaya_public_key" value="{{old('paymaya_public_key',$paymaya_public_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('paymaya_public_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('paymaya_public_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("PayMaya Secret Key") }} </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                                    <input name="paymaya_secret_key" value="{{old('paymaya_secret_key',$paymaya_secret_key)}}"  class="form-control" type="text">
                                                                </div>
                                                                @if ($errors->has('paymaya_secret_key'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('paymaya_secret_key') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="paymaya_mode" >{{ __('Sandbox Mode') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                <span class="input-group-text pt-2 w-100 bg-white">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" id="paymaya_mode" name="paymaya_mode" type="checkbox" value="sandbox" <?php echo old('paymaya_mode',$paymaya_mode)=='sandbox' ? 'checked' : ''; ?>>
                                                                        <label class="form-check-label" for="paymaya_mode">{{__("Enable")}}</label>
                                                                    </div>
                                                                </span>
                                                                    </div>
                                                                    @if ($errors->has('paymaya_mode'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('paymaya_mode') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="paymaya_status" >{{ __('Status') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="paymaya_status" name="paymaya_status" type="checkbox" value="1" <?php echo old('paymaya_status',$paymaya_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="paymaya_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('paymaya_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('paymaya_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                @endif

                                                <div class="tab-pane fade <?php if(check_build_version()!='double') echo 'active show';?>" id="manual-block" role="tabpanel" aria-labelledby="">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="manual_payment_status" >{{ __('Manual Payment') }}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text pt-2 w-100 bg-white">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input" id="manual_payment_status" name="manual_payment_status" type="checkbox" value="1" <?php echo old('manual_payment_status',$manual_payment_status)=='1' ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="manual_payment_status">{{__("Active")}}</label>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                    @if ($errors->has('manual_payment_status'))
                                                                        <span class="text-danger d-none"> {{ $errors->first('manual_payment_status') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12" id="manual_payment_instruction_block">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Manual Payment Instruction") }} </label>
                                                                <textarea name="manual_payment_instruction" id="summernote" class="summernote form-control h-min-200px">{{old('manual_payment_instruction',$manual_payment_instruction)}}</textarea>
                                                                @if ($errors->has('manual_payment_instruction'))
                                                                    <span class="text-danger d-none"> {{ $errors->first('manual_payment_instruction') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="nav d-block nav-pills h-max-350px overflow-y" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                @if(check_build_version()=='double')
                                                    <a class="nav-link active" data-bs-toggle="pill" href="#paypal-block" role="tab" aria-controls="" aria-selected="true">PayPal</a>
                                                    <a class="nav-link" data-bs-toggle="pill"  href="#stripe-block" role="tab" aria-controls="" aria-selected="true">Stripe</a>
                                                    <a class="nav-link" data-bs-toggle="pill"  href="#yoomoney-block" role="tab" aria-controls="" aria-selected="true">YooMoney</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#razorpay-block" role="tab" aria-controls="" aria-selected="true">Razorpay</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#paystack-block" role="tab" aria-controls="" aria-selected="true">Paystack</a>
                                                    <a class="nav-link" data-bs-toggle="pill"href="#mollie-block" role="tab" aria-controls="" aria-selected="true">Mollie</a>
                                                    <a class="nav-link" data-bs-toggle="pill"href="#toyyibpay-block" role="tab" aria-controls="" aria-selected="true">toyyibPay</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#paymaya-block" role="tab" aria-controls="" aria-selected="true">PayMaya</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#instamojo-block" role="tab" aria-controls="" aria-selected="true">Instamojo</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#instamojo_v2-block" role="tab" aria-controls="" aria-selected="true">Instamojo v2</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#senangpay-block" role="tab" aria-controls="" aria-selected="true">senangPay</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#xendit-block" role="tab" aria-controls="" aria-selected="true">Xendit</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#myfatoorah-block" role="tab" aria-controls="" aria-selected="true">Myfatoorah</a>
                                                    <a class="nav-link" data-bs-toggle="pill" href="#mercadopago-block" role="tab" aria-controls="" aria-selected="true">Mercado Pago</a>

                                                    <a class="nav-link" data-bs-toggle="pill" href="#flutterwave-block" role="tab" aria-controls="" aria-selected="true">Flutterwave</a>

                                                    <a class="nav-link d-none" data-bs-toggle="pill"  href="#sslcommerz-block" role="tab" aria-controls="" aria-selected="true">SSLCommerz</a>
                                                    <a class="nav-link <?php if(check_build_version()!='double') echo 'active';?>" data-bs-toggle="pill" href="#manual-block" role="tab" aria-controls="" aria-selected="true">{{__('Manual Payment')}}</a >
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-save"></i> {{__('Save')}}</button>
                    </div>
                </div>


            </form>

            @if($is_admin && Request::segment(3) == "0" && Request::segment(4) == "0")
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>{{__('API Log')}}</h3>
                        <p class="text-subtitle text-muted">{{__('List of payment api request')}}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body data-card">
                    <div class="table-responsive">
                        <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                            <thead>
                            <tr class="table-light">
                                <th>#</th>
                                <th>
                                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                </th>
                                <th>{{__("ID") }}</th>
                                <th>{{__("Buyer Email") }}</th>
                                <th>{{__("Buyer Name") }}</th>
                                <th>{{__("Seller Email") }}</th>
                                <th>{{__("Seller Name") }}</th>
                                <th>{{__("Method") }}</th>
                                <th>{{__("Request at") }}</th>
                                <th>{{__("Request Data") }}</th>
                                <th>{{__("Error") }}</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </section>

    </div>

@endsection

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/member/settings.payment-settings-manual.js') }}"></script>
@endpush

@if($is_admin)
    @push('scripts-footer')
        <script src="{{ asset('assets/js/pages/member/settings.payment-settings.js') }}"></script>
    @endpush
@endif

