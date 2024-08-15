@extends('layouts.auth')
@section('title',__('Dashboard'))
@section('content')
<div class="main-content container-fluid">
    <?php
        $current_month_name = date('M', mktime(0, 0, 0, $dashboard_selected_month, 10));
        $previous_month_name = date('M', mktime(0, 0, 0, ($dashboard_selected_month-1), 10));
        $month_year_name = $current_month_name.' '.$dashboard_selected_year;
    ?>
    @auth
        @if (!auth()->user()->email_verified_at && !$is_manager)
            <div class="alert alert-light-warning alert-dismissible fade show p-4 border-warning border-dashed"  role="alert">
                <h5 class="alert-heading text-dark">
                    <i class="far fa-envelope-open fs-1 float-start mt-1 me-3"></i>
                    {{__('Verify Email')}} : <small>{{__('Email is not verified yet. Please verify your email.')}}</small>
                </h5>
                <p class="">{{ __('Click the link to get started') }} : <a href="{{ route('verification.notice') }}" class="text-success fw-bold">{{ __('Start Email Verification') }}</a></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endauth

    <!-- FOR LOCALE FILE ENTRY PURPOSE -->
    <span class="d-none">
        {{__('Connect Bot')}}     
        {{__('Group Members')}}
        {{__('Group Management')}}
    </span>


    <div class="page-title pb-3">
        <h3 class="d-inline me-2">{{__('Dashboard')}}</h3>
        <div class="btn-group float-end">
            <button type="button" class="btn btn-sm btn-outline-light dropdown-toggle ms-2 rounded text-dark px-2 py-1 dashstyle1" data-bs-toggle="dropdown" aria-expanded="false">
                {{date('M', mktime(0, 0, 0, $dashboard_selected_month, 10))}}
            </button>
            <ul class="dropdown-menu onchange_action" id="change_month">
                <?php
                for($i=1;$i<=12;$i++){
                    $month_name = date('M', mktime(0, 0, 0, $i, 10));
                    $active = $dashboard_selected_month==$i ? '' : '';
                    echo '<li><a data-item="'.$i.'" class="dropdown-item '.$active.'" href="#">'.__($month_name).'</a></li>';
                }?>
            </ul>
            <button type="button" class="btn btn-sm btn-outline-light dropdown-toggle no-radius text-dark px-2 py-1" data-bs-toggle="dropdown" aria-expanded="false">
                {{$dashboard_selected_year}}
            </button>
            <ul class="dropdown-menu onchange_action" id="change_year">
                <?php
                for($i=date('Y');$i>(date('Y')-5);$i--){
                    $active = $dashboard_selected_year==$i ? '' : '';
                    echo '<li><a data-item="'.$i.'" class="dropdown-item '.$active.'" href="#">'.$i.'</a></li>';
                }?>
            </ul>
        </div>
    </div>


    <?php
        $broadcast_summary = [];
        $dashboard_days_in_month =  cal_days_in_month(CAL_GREGORIAN, $dashboard_selected_month, $dashboard_selected_year);
        for($i=1;$i<=$dashboard_days_in_month;$i++){
            $broadcast_summary[$i] = 0;
        }
        $telegram_total_broadcasts = 0;
        $telegram_total_broadcasts_tobe_sent = 0;
        $telegram_total_broadcasts_sent = 0;
        $telegram_broadcast_table_content = '';
        $telegram_last_try_error_count = 0;


        $broadcast_summary_data = array_values($broadcast_summary);
        $broadcast_summary_days_data = array_keys($broadcast_summary);

        $subscriber_count_yearly = 0;
        $subscriber_gain_data = [];
        $whatsapp_subscriber_gain_data = [];
        $total_subscriber_gain_data = [];
        $current_month = (int) $dashboard_selected_month;
        $current_month_subscriber = 0;
        $previous_month_subscriber =0;

        for($i=1;$i<=12;$i++){
            $subscriber_gain_data[$i] = 0;
            $total_subscriber_gain_data[$i] = 0;
            $whatsapp_subscriber_gain_data[$i] = 0;
            $subscriber_gain_previous_year_data[$i] = 0;
        }
        foreach ($telegram_monthly_subscriber_data as $key=>$value){
            $new_date = (int) $value->new_date;
            $subscriber_count_yearly = $subscriber_count_yearly+$value->data;
            $subscriber_gain_data[$new_date] = $value->data;
            $total_subscriber_gain_data[$new_date] = isset($total_subscriber_gain_data[$new_date]) ? $total_subscriber_gain_data[$new_date] = $total_subscriber_gain_data[$new_date]+$value->data : $total_subscriber_gain_data[$new_date];
            if($new_date==$current_month) $current_month_subscriber = $value->data;
            if($new_date==$current_month-1) $previous_month_subscriber = $value->data;
        }


        ksort($subscriber_gain_data);
        ksort($total_subscriber_gain_data);
        $subscriber_gain_data_month_names = array_keys($subscriber_gain_data);
        $subscriber_gain_data_month_data = array_values($subscriber_gain_data);
        $total_subscriber_gain_data_month_data = array_values($total_subscriber_gain_data);

        foreach ($subscriber_gain_data_month_names as $key=>$val)
        {
            $montn_name = date("M", mktime(0, 0, 0, $val, 10));
            $subscriber_gain_data_month_names[$key] = __($montn_name);
        }
        $max_value1 = max($subscriber_gain_data_month_data);
        $max_value = max([$max_value1]);
        if($max_value > 10) $step_size = floor($max_value/10);
        else $step_size = 1;

        $max_value_total = max($total_subscriber_gain_data_month_data);
        if($max_value_total > 10) $total_step_size = floor($max_value_total/10);
        else $total_step_size = 1;

        $current_month_subscriber = $current_month_subscriber;
        $previous_month_subscriber = $previous_month_subscriber;

        $subscriber_difference = round($current_month_subscriber-$previous_month_subscriber);
        $subscriber_difference_abs = abs($subscriber_difference);
        if($previous_month_subscriber==0) {
            $subscriber_difference_percentage = $current_month_subscriber*100;
            $subscriber_difference_percentage = round($subscriber_difference_percentage).'%';
        }
        else{
             $subscriber_difference_percentage = ($subscriber_difference>0) ? ($subscriber_difference/max($previous_month_subscriber,1))*100 : ($subscriber_difference/max($current_month_subscriber,1))*100;
             $subscriber_difference_percentage = !empty($subscriber_difference_percentage) ? round($subscriber_difference_percentage).'%' : '';
        }
        $current_month_subscriber_percentage = $current_month_subscriber>0 ? ($current_month_subscriber/max($subscriber_count_yearly,1))*100 : 0;
        $previous_month_subscriber_percentage = $previous_month_subscriber>0 ? ($previous_month_subscriber/max($subscriber_count_yearly,1))*100 : 0;
        $current_month_subscriber_percentage = round($current_month_subscriber_percentage);
        $previous_month_subscriber_percentage = round($previous_month_subscriber_percentage);
    ?>

    <div class="clearfix"></div>
    <section class="section">

        <div class="row mt-2">
            <div class="col-12 col-md-4">
                <div class="card card-icon-bg-md box-shadow pb-0 dashstyle2">
                    <div class="card-body bg-light-purple ps-4 pe-2 dashstyle3">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-white">
                                            <i class="fas fa-users text-primary fs-3"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-4 text-dark fw-bold">{{$telegram_group_count}}</div>
                                        <div class="fw-bold text-muted">{{__('Group')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-icon-bg-md box-shadow pb-0 dashstyle4">
                    <div class="card-body bg-white ps-4 pe-2 dashstyle5">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-primary">
                                            <i class="fab fa-telegram text-white fs-3"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-4 text-dark fw-bold">{{$telegram_bot_count}}</div>
                                        <div class="fw-bold text-muted">{{__('Bot')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-icon-bg-md box-shadow pb-0 dashstyle6">
                    <div class="card-body bg-light-purple ps-4 pe-2 dashstyle5">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-white">
                                            <i class="fas fa-user text-primary fs-3"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-4 text-dark fw-bold">{{$total_member}}</div>
                                        <div class="fw-bold text-muted">{{__('Active Members')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mb-2">
            <div class="col-12 col-md-4">
                <div class="card card-icon-bg-md">
                    <div class="card-header"><h4 class="card-title">{{__('Group Members')}} <span class="text-sm">{{$month_year_name}}</span></h4></div>
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-2">
                                        <div class="symbol-label bg-light-success">
                                            <i class="fas fa-users text-success list-icon"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-6 text-dark fw-bold">{{$join_member}}</div>
                                        <div class="fs-6 text-muted">{{__('Joined Members')}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-2">
                                        <div class="symbol-label bg-light-success">
                                            <i class="fas fa-user-plus text-primary list-icon"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-6 text-dark fw-bold">{{$left_member}}</div>
                                        <div class="fs-6 text-muted">{{__('Left Members')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-2">
                                        <div class="symbol-label bg-light-info bg-light-danger">
                                            <i class="fas fa-user-minus list-icon text-danger"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-6 text-dark fw-bold">{{$banned_member}}</div>
                                        <div class="fs-6 text-muted">{{__('Banned Members')}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-2">
                                        <div class="symbol-label bg-light-danger">
                                            <i class="fas fa-user-times list-icon text-danger"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-6 text-dark fw-bold">{{$total_mute_member}}</div>
                                        <div class="fs-6 text-muted">{{__('Mute Members')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer px-0 pt-4 pb-0">
                        <canvas id="subscriber_summary" height="100px"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card card-icon-bg-lg box-shadow">
                    <div class="card-header bg-primary bg-gradient p-0">
                        <h4 class="card-title text-white p-4">{{__('Message Campaign')}} <span class="text-sm">{{$month_year_name}}</span></h4>
                        <canvas id="broadcast_summary" height="100px"></canvas>
                    </div>
                    <div class="card-body pb-0 px-4 px-md-3">
                        <div class="row g-0">
                            <div class="col bg-light-warning border-warning border-dashed ps-2 ps-md-3 pe-1 py-3 rounded-4 me-3 mb-3 ">
                                <span class="text-warning d-block my-2">
                                     <i class="fas fa-paper-plane fs-6"></i> <span class="fs-6 ms-0 fw-bold">{{$pending_campaign}}</span>
                                </span>
                                <a href="#" class="text-sm text-muted">{{__('Pending')}}</a>
                            </div>
                            <div class="col bg-light-success border-success border-dashed ps-2 ps-md-3 pe-1 py-3 rounded-4 me-3 mb-3">
                                <span class="text-success d-block my-2">
                                     <i class="fas fa-paper-plane fs-6"></i> <span class="fs-6 ms-0 fw-bold">{{$completed_campaign}}</span>
                                </span>
                                <a href="#" class="text-sm text-muted">{{__('Completed')}}</a>
                            </div>
                            <div class="col bg-light-danger border-danger border-dashed ps-2 ps-md-3 pe-1 py-3 rounded-4 mb-3">
                                <span class="text-danger d-block my-2">
                                     <i class="fas fa-paper-plane fs-6"></i> <span class="fs-6 ms-0 fw-bold">{{$processing_campaign}}</span>
                                </span>
                                <a href="#" class="text-sm text-muted">{{__('Processing')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 col-lg-4">
                <div class="card m-0">
                    <div class="card-header">
                        <h5>{{__('Members')}} : {{$month_year_name}}</h5>
                    </div>
                    <div class="card-body dashstyle7">
                        <div id="radialBars"></div>
                        <div class="text-center mb-4">
                            <h6>{{__('From last month')}}</h6>
                            <?php
                            if($subscriber_difference!=0) echo $subscriber_difference>0 ? '<h2 class="text-success">+'.convert_number_numeric_phrase($subscriber_difference).' ('.$subscriber_difference_percentage.')</h2>' : ' <h2 class="text-danger">-'.convert_number_numeric_phrase($subscriber_difference_abs).' ('.$subscriber_difference_percentage.')</h2>';
                            else echo '<h2 class="text-muted">-</h2>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>


        </div>


        <div class="row mb-4">
            <div class="col-12">
                <div class="card m-0">
                    <div class="card-header">
                        <h5>{{__('Joined Members')}} : {{$dashboard_selected_year}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <canvas id="monthly_subscriber_years_pie" height="170px" class="mt-5"></canvas>
                                <div id="monthly_subscriber_years_pie_legend" class="mt-3"></div>
                            </div>
                            <div class="col-12 col-md-8">
                                <canvas id="monthly_subscriber_years" height="120px" class="mt-2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts-footer')
<script>
    "use strict";
    var subscriber_gain_data_month_names = <?php echo json_encode($subscriber_gain_data_month_names)?>;
    var subscriber_gain_data_month_data = <?php echo json_encode($subscriber_gain_data_month_data)?>;
    var total_subscriber_gain_data_month_data = <?php echo json_encode($total_subscriber_gain_data_month_data)?>;
    var broadcast_summary_data = <?php echo json_encode($broadcast_summary_data)?>;
    var broadcast_summary_days_data = <?php echo json_encode($broadcast_summary_days_data)?>;
    var step_size = <?php echo $step_size?>;
    var total_step_size = <?php echo $total_step_size?>;
    var current_month_subscriber = '<?php echo $current_month_subscriber?>';
    var previous_month_subscriber = '<?php echo $previous_month_subscriber?>';
    var current_month_subscriber_percentage = '{{$current_month_subscriber_percentage}}';
    var previous_month_subscriber_percentage = '{{$previous_month_subscriber_percentage}}';
    var subscriber_count_yearly = '{{$subscriber_count_yearly}}';
    var current_month_name = '{{$current_month_name}}';
    var previous_month_name = '{{__($previous_month_name)}}';
    var current_year_name_telegram = "{{ __('Telegram') }} - {{$dashboard_selected_year}}";
    var local_subscribers = '{{__('Members')}}';
    var local_subscribers_this_year = local_subscribers+" - "+'{{$dashboard_selected_year}}';
    var local_subscriber_gain_year = '{{__('Member Growth')}} - {{$dashboard_selected_year}}';
    var pending_campaign = '{{__('Pending Campaign')}}';
    var message_pending = '{{__('Message Pending')}}';
    var message_sent = '{{__('Message Sent')}}';
    var dashboard_change_data = "{{route('dashboard-change-data')}}";
</script>
<script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
@endpush

@push('styles-header')
<link rel="stylesheet" href="{{ asset('assets/vendors/chartjs/Chart.min.css') }}">
<link  rel="stylesheet" href="{{ asset('assets/css/dashboard.css')}}"></script>
@endpush
