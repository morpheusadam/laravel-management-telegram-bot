<?php
$lang_display = __('Package Manager');
$title_display = $lang_display;
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{$title_display}}
                    <a href="{{route('create-package')}}" class="btn btn-outline-primary"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                </h3>
                <p class="text-subtitle text-muted">{{__('List of user subscription packages')}}</p>
            </div>
        </div>
    </div>
    @if (session('save_package_status')=='1')
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Successful')}}</h4>
            <p> {{ __('Package/role has been saved successfully.') }}</p>
        </div>
    @elseif (session('save_package_status')=='0')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Failed')}}</h4>
            <p> {{ __('Something went wrong. Failed to save package/role.') }}</p>
        </div>
    @endif
    <section class="section">
        <div class="card">
            <div class="card-body data-card">
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3" id="searchbox">
                            <?php $two_block=false;?>
                            <div class="input-group-prepend">
                                <input type="text" class="form-control no-radius" autofocus id="search_value" name="search_value" placeholder="{{__("Search...")}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                        <thead>
                        <tr class="table-light">
                            <th>#</th>
                            <th>{{__("Package ID") }}</th>
                            <th>{{__("Package/Role") }}</th>
                            <th>{{__("Type") }}</th>
                            <th>{{__("Price") }} - <?php echo isset($payment_config->currency) ? $payment_config->currency : 'USD';?></th>
                            <th>{{__("Validity") }} - {{__("days") }}</th>
                            <th>{{__("Default") }}</th>
                            <th class="min-width-90px">{{__("Actions") }}</th>
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

@push('styles-header')
    @include('subscription.package.list-package-css')
@endpush

@push('scripts-footer')
<script src="{{ asset('assets/js/pages/subscription/package.list-package.js') }}"></script>
@endpush
