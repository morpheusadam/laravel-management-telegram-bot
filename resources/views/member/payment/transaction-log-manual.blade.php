@extends('layouts.auth')
@section('title',__('Manual Transactions'))
@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{__('Manual Transactions')}}</h3>
                <p class="text-subtitle text-muted">{{__('List of manual payment transactions')}}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('transaction-log')}}">{{__('Transactions')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('Manual')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">

        <div class="card">
            <div class="card-body data-card">
                <div class="table-responsive">
                    <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                        <thead>
                        <tr class="table-light">
                            <th>#</th>
                            <th><?php echo __("Name"); ?></th>
                            <th><?php echo __("Email"); ?></th>
                            <th><?php echo __("Paid Amount"); ?></th>
                            <th><?php echo __("Attachment"); ?></th>
                            <th><?php echo __("Status"); ?></th>
                            <th><?php echo __("Actions"); ?></th>
                            <th><?php echo __("Paid At"); ?></th>
                            <th><?php echo __("Additional Info"); ?></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</div>

<?php if ($is_admin || $is_agent): ?>
  <div class="modal fade" tabindex="-1" role="dialog" id="manual-payment-reject-modal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-times-circle transaction-log-style2"></i> <?php echo __("Manual payment rejection");?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">        
            <div class="row">
              <!-- Additional Info -->
              <div class="col-lg-12">
                <div class="form-group">
                  <label for="paid-amount"><?php echo __('Describe, why do you want to reject this payment?'); ?></label>
                  &nbsp;
                  <textarea name="rejected-reason" id="rejected-reason" class="h-200px form-control"></textarea>
                  <input type="hidden" id="mp-transaction-id">
                  <input type="hidden" id="mp-action-type">
                </div>
              </div>  
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer bg-whitesmoke br d-block">
          <button type="button" id="manual-payment-reject-submit" class="btn btn-primary"><?php echo __('Submit'); ?></button>      
          <button type="button" class="btn btn-secondary btn-lg float-end m-0" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="modal fade" tabindex="-1" role="dialog" id="manual-payment-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo __("Manual payment");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          
          <!-- Manual payment instruction -->
          <div id="manual-payment-instructions" class="row d-none">
            <div class="col-lg-12 mb-4">
              <div class="alert alert-light alert-has-icon">
                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                <div class="alert-body">
                  <div class="alert-title"><?php echo __('Manual payment instructions'); ?></div>
                  <p id="payment-instructions"></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Paid amount and currency -->
          <div class="row">
            <div class="col-lg-6 mb-4">
              <div class="form-group">
                <label for="paid-amount"><i class="fa fa-money-bill-alt"></i> <?php echo __('Paid Amount'); ?>:</label>
                <input type="number" name="paid-amount" id="paid-amount" class="form-control" min="1">
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="form-group">
                <label for="paid-currency"><i class="fa fa-coins"></i> <?php echo __('Currency'); ?></label>
                <?php $w_100 = "width:100% !important";?>              
                {!!
                  Form::select("paid-currency",$currency_list,[],['id' => 'paid-currency', 'class' => 'form-control select2','style'=>$w_100])
              !!}
              </div>
            </div>
          </div>          
          
          <!-- Image upload - Dropzone -->
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label><i class="fa fa-paperclip"></i> <?php echo __('Attachment'); ?> <?php echo __('(Max 5MB)');?> </label>
                <div id="manual-payment-dropzone" class="dropzone mb-1">
                  <div class="dz-default dz-message">
                    <input class="form-control" name="uploaded-file" id="uploaded-file" type="hidden">
                    <span class="transaction-log-style3"><i class="fas fa-cloud-upload-alt transaction-log-style4"></i> <?php echo __('Upload'); ?></span>
                  </div>
                </div>
                <span class="red">Allowed types: pdf, doc, txt, png, jpg and zip</span>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="paid-amount"><i class="fa fa-info-circle"></i> <?php echo __('Additional Info'); ?>:</label>
                &nbsp;
                <textarea name="additional-info" id="additional-info" class="form-control"></textarea>
              </div>
              <input type="hidden" id="selected-package-id">
              <input type="hidden" id="mp-resubmitted-id">
            </div>  
          </div>

        </div><!-- ends container -->
      </div><!-- ends modal-body -->

      <!-- Modal footer -->
      <div class="modal-footer bg-whitesmoke br">
        <button type="button" id="manual-payment-submit" class="btn btn-primary"><?php echo __('Submit'); ?></button>      
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo __("Close"); ?></button>
      </div>
      <div id="mp-spinner" class="justify-content-center align-items-center d-flex"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i></div><!-- spinner -->
    </div>
  </div>
</div>
@endsection


@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/member/payment.transaction-log-manual.js') }}"></script>
@endpush
