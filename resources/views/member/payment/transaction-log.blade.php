@extends('layouts.auth')
@section('title',$is_admin || $is_agent ? __('Earnings') : __('Transactions'))
@section('content')
    <div class="main-content container-fluid">
        <div class="page-title pb-3">
            <div class="row">
                <div class="col-12">
                    <h3>{{$is_admin || $is_agent ? __('Earnings') : __('Transactions')}}
                        <span id="subtitle"></span>                        
                        <a href="{{route('transaction-log-manual')}}" class="btn btn-outline-primary"><i class="fas fa-history"></i> {{__('Manual Transactions')}}</a>                        
                    </h3>
                    <p class="text-subtitle text-muted">{{__('List of transactions')}}</p>
                </div>
            </div>
        </div>
        @if (session('xendit_currency_error')!='')
            <div class="alert alert-danger">
                <h4 class="alert-heading">{{__('Payment Failed')}}</h4>
                <p>
                    {{ __('Something went wrong. Failed to complete payment.') }}&nbsp;{{session('xendit_currency_error')}}
                </p>
            </div>
        @elseif(session('payment_success')=='0')
            <div class="alert alert-danger">
                <h4 class="alert-heading">{{__('Payment Cancelled')}}</h4>
                <p>
                    {{ __('Something went wrong. The payment was cancelled.') }}
                </p>
            </div>
        @elseif(session('payment_success')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Payment Successful')}}</h4>
                <p>
                    {{ __('Payment has been processed successfully. It may take few minutes to appear payment in this list.') }}
                </p>
            </div>
        @endif
        <section class="section">

            @if($is_admin || $is_agent)

                <div class="row">
                    <div class="col-12">
                        <div class="card card-icon-bg-md border-primary box-shadow pb-0 transaction-log-style1">
                            <div class="card-body bg-light-purple ps-4 pe-2 border-radius-10px">
                                <div class="row">
                                    <div class="col">
                                        <div class="d-flex align-items-center my-2">
                                            <div class="symbol symbol-50px me-3">
                                                <div class="symbol-label bg-white">
                                                    <i class="fas fa-user-circle text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fs-6 text-dark fw-bold">{{$user_count}}</div>
                                                <div class="fs-6 text-muted">{{__('Users')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center my-2">
                                            <div class="symbol symbol-50px me-3">
                                                <div class="symbol-label bg-white">
                                                    <i class="fas fa-coins text-warning"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fs-6 text-dark fw-bold">
                                                    <?php echo $currency_icon.convert_number_numeric_phrase($payment_today); ?>
                                                </div>
                                                <div class="fs-6 text-muted">{{__('Today')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center my-2">
                                            <div class="symbol symbol-50px me-3">
                                                <div class="symbol-label bg-white">
                                                    <i class="far fa-calendar text-danger"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fs-6 text-dark fw-bold">
                                                    <?php echo $currency_icon.convert_number_numeric_phrase($payment_month); ?>
                                                </div>
                                                <div class="fs-6 text-muted">{{__('This Month')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center my-2">
                                            <div class="symbol symbol-50px me-3">
                                                <div class="symbol-label bg-white">
                                                    <i class="far fa-calendar text-danger"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fs-6 text-dark fw-bold">
                                                    <?php echo $currency_icon.convert_number_numeric_phrase($payment_year); ?>
                                                </div>
                                                <div class="fs-6 text-muted">{{__('This Year')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $payment_differnce = round($payment_month-$payment_month_previous);
                $payment_differnce_abs = abs($payment_differnce);
                if($payment_month_previous==0) {
                    $payment_differnce_percentage = $payment_month*100;
                    $payment_differnce_percentage = round($payment_differnce_percentage).'%';
                }
                else{
                    $payment_differnce_percentage = ($payment_differnce>0) ? ($payment_differnce/max($payment_month_previous,1))*100 : ($payment_differnce/max($payment_month,1))*100;
                    $payment_differnce_percentage = !empty($payment_differnce_percentage) ? round($payment_differnce_percentage).'%' : '';
                }

                $user_summary = [];
                $days_in_month =  cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                for($i=1;$i<=$days_in_month;$i++){
                    $user_summary[$i] = 0;
                }
                foreach ($user_data as $key=>$value){
                    $day = (int) date('d',strtotime($value->created_at));
                    $user_summary[$day]++;
                }
                $user_summary_data = array_values($user_summary);
                $user_summary_label = array_keys($user_summary);
                ?>

                <div class="row ">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo __("Earning Comparison")." : ".$year." ".__("vs")." ".$lastyear; ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-9">
                                        <canvas id="comparison-chart" height="120px"></canvas>
                                    </div>
                                    <div class="col-12 col-lg-3">
                                        <div class="text-center mt-md-5 pt-md-5 pt-3">
                                            <h6>{{__('From last month')}}</h6>
                                            <?php
                                            if($payment_differnce!=0) echo $payment_differnce>0 ? '<h3 class="text-success">+'.$currency_icon.convert_number_numeric_phrase($payment_differnce).'<br> ('.$payment_differnce_percentage.')</h3>' : ' <h3 class="text-danger">-'.$currency_icon.convert_number_numeric_phrase($payment_differnce_abs).' <br>('.$payment_differnce_percentage.')</h3>';
                                            else echo '<h3 class="text-muted">-</h3>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card card-icon-bg-md min-height-450px">
                            <div class="card-header"><h4 class="card-title"><?php echo __("Top Countries"); ?></h4></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h6>{{$year}}</h6>
                                        <?php
                                        $count=1;
                                        foreach ($this_year_top as $key => $value)
                                        {   if(strtolower($key)=='uk') $key='GB';?>
                                        <div class="d-flex align-items-center mb-2">
                                            <img class="border me-2" data-bs-toggle="tooltip" src="{{asset('assets/vendors/flag-icon-css/flags/4x3').'/'.strtolower($key)}}.svg" alt="{{strtolower($key)}}" width="60" height="45">
                                            <div>
                                                <div class="fs-6 text-dark fw-bold"><?php echo '<b>'.$currency_icon.convert_number_numeric_phrase($value).'</b>'; ?></div>
                                                <div class="fs-6 text-muted"><?php echo isset($country_names[$key]) ? __($country_names[$key]) : "-"; ?></div>
                                            </div>
                                        </div>
                                        <?php
                                        $count++;
                                        if($count==6) break;
                                        } ?>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <h6>{{$lastyear}}</h6>
                                        <?php
                                        $count=1;
                                        foreach ($last_year_top as $key => $value)
                                        {    if(strtolower($key)=='uk') $key='GB';?>
                                        <div class="d-flex align-items-center mb-2">
                                            <img class="me-2 border" data-bs-toggle="tooltip" src="{{asset('assets/vendors/flag-icon-css/flags/4x3').'/'.strtolower($key)}}.svg" alt="{{strtolower($key)}}" width="60" height="45">
                                            <div>
                                                <div class="fs-6 text-dark fw-bold"><?php echo '<b>'.$currency_icon.convert_number_numeric_phrase($value).'</b>'; ?></div>
                                                <div class="fs-6 text-muted"><?php echo isset($country_names[$key]) ? __($country_names[$key]) : "-"; ?></div>
                                            </div>
                                        </div>
                                        <?php
                                        $count++;
                                        if($count==6) break;
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo __("Users Gain")." : ".date('M Y'); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <canvas id="user_chart" height="110px"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            <div class="card">
                <div class="card-header"><h4>{{__('Transaction Log')}}</h4></div>
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
                                <th>{{__("Email") }}</th>
                                <th>{{__("First Name") }}</th>
                                <th>{{__("Last Name") }}</th>
                                <th>{{__("Method") }}</th>
                                <th>{{__("Amount") }}</th>
                                <th>{{__("Package") }}</th>
                                <th>{{__("Billing Cycle") }}</th>
                                <th>{{__("Paid at") }}</th>
                                @if(Auth::user()->parent_user_id || $is_admin)
                                    <th>{{__("Invoice") }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection

<?php
if($is_admin || $is_agent):
    $max1 = (!empty($this_year_earning)) ? max($this_year_earning) : 0;
    $max2 = (!empty($last_year_earning)) ? max($last_year_earning) : 0;
    $steps = round(max(array($max1,$max2))/7);
    if($steps==0) $steps = 1;

    $max_user = max($user_summary_data);
    if($max_user > 10) $user_step_size = floor($max_user/10);
    else $user_step_size = 1;
endif;
?>

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/member/payment.transaction-log.js') }}"></script>
    @if($is_admin || $is_agent)
        <script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
        <script>
            var comparison_chart_labels = <?php echo json_encode(array_values($month_names))?>;
            var comparison_chart_data1= <?php echo json_encode(array_values($this_year_earning))?>;
            var comparison_chart_data2= <?php echo json_encode(array_values($last_year_earning))?>;
            var comparison_chart_year = "{{$year}}";
            var comparison_chart_lastyear = "{{$lastyear}}";
            var comparison_chart_steps = "{{$steps}}";
            var user_summary_data = <?php echo json_encode($user_summary_data)?>;
            var user_summary_label = <?php echo json_encode($user_summary_label)?>;
            var user_step_size = "{{$user_step_size}}";
            var user_locale = "{{__('User')}}";
        </script>
        <script src="{{ asset('assets/js/pages/member/payment.transaction-log-summary.js') }}"></script>
    @endif
@endpush

@if($is_admin || $is_agent)
    @push('styles-header')
        <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/chartjs/Chart.min.css') }}">
    @endpush
@endif
