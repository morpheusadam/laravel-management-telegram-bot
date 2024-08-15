<script>
      "use strict";
      var csrf_token = '{{ csrf_token() }}';
      var url_login = '{{ route('login') }}'
      var url_register = '{{ route('register') }}';
      var url_dashboard = '{{ route('dashboard') }}';
      var success = '{{ __('Success') }}';
      var warning = '{{ __('Warning') }}';
      var error = '{{ __('Error') }}';
      var base_url = '{{ url("/") }}';
      var global_lang_success = "{{__('Success')}}";
      var global_lang_error = "{{__('Error')}}";
      var global_lang_confirm = "{{__('Confirm')}}";
      var global_lang_delete = "{{__('Delete')}}";
      var global_lang_cancel = "{{__('Cancel')}}";
      var purchase_code_active = '{{ route("credential-check-action") }}'
</script>