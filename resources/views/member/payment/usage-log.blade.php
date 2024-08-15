@extends('layouts.auth')
@section('title',__('Usage Log'))
@section('content')
    <div class="main-content container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{__('Usage Log')}}</h3>
                    <p class="text-subtitle text-muted">{{__('Module-wise usage log')}}</p>
                </div>
        </div>

        <section class="section">
           @include('member.payment.usage-log.stat')
           @include('member.payment.usage-log.list')   
        </section>
    </div>
@endsection
