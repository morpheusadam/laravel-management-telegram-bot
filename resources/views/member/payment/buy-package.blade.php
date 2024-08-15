@extends('layouts.auth')
@section('title',__('Transactions'))
@push('scripts-header')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendors/dropzone/dist/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/member/payment.buy-package.css') }}">
@endpush
@section('content')
<div class="main-content container-fluid">
    @if( Auth::user()->subscription_enabled=="1" && !empty(Auth::user()->subscription_data))
        <div class="row grid-margin">
            <div class="col-12">
                <h3>{{__('Your Current Subscription')}}</h3>
                <p class="text-subtitle text-muted">{{__('You are already subscribed to a subscription package')}}</p>                
            </div>
            @include('member.payment.usage-log.top-grid')
        </div>
    @endif

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{__('Payment Methods')}}</h3>
                <p class="text-subtitle text-muted">{{__('Select payment method to pay')}}</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                @if($no_payment_found_error)
                    <div class="alert alert-danger px-3 pt-4 pb-4">
                        <h4 class="alert-heading">{{__('No payment method found')}}</h4>
                        <p class="mt-4">{{__('The application administrator has not yet set up a payment option, or receiving payments has been temporarily disabled. Please notify the administrator about this situation.')}}</p>
                    </div>
                @else
                    <?php echo $buttons_html;?>
                @endif
            </div>
        </div>
    </section>

</div>

<div class="modal fade" id="manual-payment-modal" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo __("Manual payment");?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (isset($manual_payment_instruction) && ! empty($manual_payment_instruction)): ?>
        <div class="row">
          <div class="col-lg-12 mb-4">
            <h6 class="text-primary"><i class="far fa-lightbulb"></i> <?php echo __('Manual payment instructions'); ?></h6>
            <div><?php echo $manual_payment_instruction; ?></div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Paid amount and currency -->
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="form-group">
              <label for="paid-amount"><i class="fa fa-money-bill-alt"></i> <?php echo __('Paid Amount'); ?>:</label>
              <input type="number" name="paid-amount" id="paid-amount" class="form-control" min="1">
              <input type="hidden" id="selected-package-id" value="<?php echo $buy_package_package_id; ?>">
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="form-group">
              <label for="paid-currency"><i class="fa fa-coins"></i> <?php echo __('Currency'); ?></label>

              {!!
                  Form::select("paid-currency",$currency_list,$currency,['id' => 'paid-currency', 'class' => 'form-control select2','style'=>'width:100%'])
              !!}

            </div>
          </div>
        </div>

        <div class="row">
          <!-- Image upload - Dropzone -->
          <div class="col-lg-6">
            <div class="form-group">
              <label><i class="fa fa-paperclip"></i> <?php echo __('Attachment'); ?> <?php echo __('(Max 5MB)');?> </label>
              <div id="manual-payment-dropzone" class="dropzone mb-1">
                <div class="dz-default dz-message">
                  <input class="form-control" name="uploaded-file" id="uploaded-file" type="hidden">
                  <span class="buy-package-style1"><i class="fas fa-cloud-upload-alt buy-package-style2"></i> <?php echo __('Upload'); ?></span>
                </div>
              </div>
              <span class="red">{{ __("Allowed types: pdf, doc, txt, png, jpg and zip") }}</span>
            </div>
          </div>

          <!-- Additional Info -->
          <div class="col-lg-6">
            <div class="form-group">
              <label for="paid-amount"><i class="fa fa-info-circle"></i> <?php echo __('Additional Info'); ?>:</label>
              &nbsp;
              <textarea name="additional-info" id="additional-info" class="form-control buy-package-style3"></textarea>
            </div>
          </div>
        </div>
      </div><!-- ends modal-body -->

      <!-- Modal footer -->
      <div class="modal-footer bg-whitesmoke br d-block">
        <button type="button" id="manual-payment-submit" class="btn btn-primary btn-lg m-0"><i class="fas fa-save"></i> <?php echo __('Submit'); ?></button>
        <button type="button" class="btn btn-secondary btn-lg float-end m-0" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
      </div>
    </div>
  </div>
</div>
@endsection


@push("scripts-footer")
<script src="{{ asset('assets/vendors/dropzone/dist/min/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/member/payment.buy-package-manual.js') }}"></script>
@endpush
