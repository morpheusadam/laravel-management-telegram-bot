<?php $display_currency = $parent_user_id==1 ? '$' : $curency_icon.' ';?>
<div class="row">
    <div class="col-12">
        <div class="card card-icon-bg-md border-light box-shadow pb-0 stat-style1">
            <div class="card-body bg-light ps-4 pe-2 border-radius-10px">
                <div class="row">
                    <div class="col">
                        <div class="d-flex align-items-center my-2">
                            <div class="symbol symbol-50px me-3">
                                <div class="symbol-label bg-white">
                                    <i class="fas fa-shopping-bag text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <?php $pricing_link = route('pricing-plan'); ?>
                                <div class="fs-6 text-dark fw-bold">{{$package_name}}</div>
                                <div class="fs-6 text-muted">{{__('Package')}}  <a class="stat-style2 text-sm btn btn-primary ms-2" href="{{$pricing_link}}">{{Auth::user()->package_id==1 ?__('Upgrade to Pro') : __('Renew / Upgrade')}}</a></div>
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
                                    <?php
                                    $print_validity = ' / '.$validity.' '.__("Days");
                                    if($is_agent && Auth()->user()->agent_has_ppu=='1'){
                                        $print_validity = " (".date("d/m/y",strtotime(Auth()->user()->agent_ppu_expiry_date)).")";
                                    }
                                    ?>
                                    <?php if($price=="Trial") $price=0; ?>
                                    <?php echo $price>0 ? $display_currency.number_format($price,'2','.','').$print_validity : __('Free');?>
                                </div>
                                @if($is_agent && Auth()->user()->agent_has_ppu=='1')
                                    <div class="fs-6 text-muted">{{__('Remaining Message')}} : <b>{{number_format(Auth()->user()->agent_ppu_remaining-$message_used)}}</b></div>
                                @endif
                                <div class="fs-6 text-muted">{{__('Price & Validity')}}</div>
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
                                   <?php echo !empty($expired_date) ? date("dS M Y",strtotime($expired_date)) : __('Never'); ?>
                                </div>
                                <div class="fs-6 text-muted">{{__('Account Expiry')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
