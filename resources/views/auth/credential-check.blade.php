@extends('layouts.guest')
@section('title',__('Credential Check'))
@section('content')
    <h4>{{__("Register your Software?")}}</h4>
    <h6 class="fw-light">{{__('Just put the product purchase code once and continue using forever')}}</h6>
    <h5 class="text-success mt-3">{{session('status')}}</h5>
    <form class="pt-3" method="POST" id="recovery_form">
        @csrf
        <div class="form-group">
          <input autofocus type="text" class="form-control form-control-lg" id="purchase_code" name="purchase_code" value="{{ old('purchase_code') }}" placeholder="{{__('Purchase Code')}} *">
          @if ($errors->has('purchase_code'))
              <span class="text-danger">
                  {{ $errors->first('purchase_code') }}
              </span>
          @endif
        </div>
        <div class="mt-3">
          <button type="submit" id="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-uppercase">{{ __('Continue') }}</button>
        </div> 
    </form>
@endsection

@push('scripts-footer')
<script src="{{ asset('assets/js/pages/auth/auth.credential-check.js') }}"></script>
@endpush