@php
    $subscription_enabled = Auth::user()->subscription_enabled ?? "0";
    $subscription_data = Auth::user()->subscription_data ?? "";
    $subscription_data = json_decode($subscription_data,true);
    if(isset($subscription_data['validity'])) $subscription_data['validity'] = $subscription_data['validity'].' '. __("Days");
    $subscription_data['date'] = isset($subscription_data['time']) ? date("dS M Y",strtotime($subscription_data['time'])) : null;
    $validity = $subscription_data['validity'] ?? '';
    $method = $subscription_data['method'] ?? '';
    $package_id = $subscription_data['package_id'] ?? 0;
    $package_data = DB::table('packages')->select('package_name')->where('id',$package_id)->first();
    $package_name = $package_data->package_name ?? '';
    $package_name = $package_name;
    $currency_icons = get_country_iso_phone_currency_list('currency_icon');

    $amount = ['title'=>__('Amount'),'value'=>0,'icon'=>''];
    if(isset($subscription_data['amount']) && isset($subscription_data['currency'])){
        $currency = $subscription_data['currency'] ?? 'USD';
        $currency = $currency_icons[$currency] ?? $currency;
        $amount = $currency.$subscription_data['amount'].'/'.$validity;
        $amount = ['title'=>__('Price'),'value'=>$amount,'icon'=>'fas fa-coins text-warning'];
    }
    $subscription_data_formated = [
        'package_name'  => ['title'=>__('Package'),'value'=>$package_name,'icon'=>'fas fa-shopping-bag text-primary'],
        'amount'        => $amount,
        'method'        => ['title'=>__('Method'),'value'=>$method ?? '','icon'=>'fas fa-credit-card text-primary'],
        'date'          => ['title'=>__('Subscribed at'),'value'=>$subscription_data['date'] ?? '','icon'=>'far fa-clock text-success'],
    ];
@endphp

<div class="row">
@foreach($subscription_data_formated as $k => $data)
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="card card-icon-bg-md border-light box-shadow pb-0 transaction-log-style1">
            <div class="card-body bg-light ps-4 pe-2 border-radius-10px">
                <div class="row">
                    <div class="col">
                        <div class="d-flex align-items-center my-2">
                            <div class="symbol symbol-50px me-3">
                                <div class="symbol-label bg-white">
                                    <i class="{{ $data['icon'] ?? 'fas fa-circle' }}"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fs-6 text-dark fw-bold">{!! $data['value'] ?? '' !!}</div>
                                <div class="fs-6 text-muted">{!! $data['title'] ?? '' !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
@endforeach  
    <div class="col-12">
        <div class="alert alert-light-warning alert-dismissible fade show p-4 border-warning border-dashed" role="alert">
            <h5 class="alert-heading text-dark">
                <i class="fas fa-ban fs-1 float-start mt-1 me-3"></i>
                {{__('Want to upgrade your plan?')}}
            </h5>
            <p class="text-md mt-2">{{__('Please make sure you cancel your current subscription before purchasing another.')}}</p>

            @if($method=='PayPal')
            <form action="{{ route('paypal-subscription-cancel') }}" class="ms-1">
                @csrf
                <button class="btn btn-warning mt-2 ms-5">{{__('Cancel Subscription')}}</button>
            </form>
            @endif

            </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </p>
        </div>
    </div>
</div>
           