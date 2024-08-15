@extends('layouts.auth')
@section('title',__('Transactions'))
@section('content')
<div class="main-content">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{__('Select Package')}}</h3>
                <p class="text-subtitle text-muted">{{__('Buy/renew your subscription package')}}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
            </div>
        </div>
    </div>

    @php
        $currency_icons = get_country_iso_phone_currency_list('currency_icon');
        $currency = $config_data->currency ?? "USD";
        $currency_icon = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
    @endphp

    <section class="section">
        <div class="row">
            <div class="col-12">
                @if(!$payment_package->isEmpty())
                    <div class="pricing">
                        <div class="row">
                            @foreach($payment_package as $pack)
                                <?php if($is_agent && $pack->is_agency=='0') continue;?>
                                <?php if($is_member && $pack->is_agency=='1') continue;?>
                                <?php $text_color = $pack->highlight=='1' ? 'text-success' : 'text-primary'; ?>
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3'}}">
                                <div class="mb-5 {{$pack->highlight=='1' ? 'card border-success border-2' : 'card'}} h-min-550px">
                                    <div class="card-header text-center mt-4 pb-2">
                                        <h4 class="card-title {{$text_color}}">{{$pack->package_name}}</h4>
                                        <h4 class='text-center mt-2  text-dark'>{{$pack->validity}} {{__("days")}}</h4>
                                    </div>
                                    <h1 class="price mb-4 text-dark">
                                        <?php echo format_price($pack->price,$format_settings,$pack->discount_data);?>
                                    </h1>
                                    <ul class="">
                                        @php
                                            $module_ids = $pack->module_ids;
                                            $monthly_limit = json_decode($pack->monthly_limit,true);
                                            $module_names_array = get_modules_set($module_ids);
                                        @endphp
                                        @foreach($module_names_array as $row)
                                            @php
                                                $limit = 0;
                                                $limit = $monthly_limit[$row->id];
                                                if($limit == "0") $limit2 = __("Unlimited");
                                                else $limit2 = $limit;
                                                $limit2 = " : ".$limit2;
                                            @endphp
                                            <li><i data-feather="check-circle" class="{{$pack->highlight=='1' ? 'text-success' : 'text-primary'}}"></i> {{__($row->module_name).$limit2}}</li>
                                        @endforeach
                                    </ul>
                                    <div class="card-footer pt-0">
                                        <a data-id="{{$pack->id}}" class="choose_package btn btn-lg {{$pack->highlight=='1' ? 'bg-success text-white' : 'btn-outline-primary'}} btn-block">{{__('Select Package')}}</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger px-3 pt-4 pb-4">
                        <h4 class="alert-heading">{{__('No subscription package found')}}</h4>
                        <p class="mt-4">{{__('Please contact the application admin regarding this issue.')}}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

</div>
@endsection


@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/member/payment.select-package.js') }}"></script>
@endpush
