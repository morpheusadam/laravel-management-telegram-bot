<script type="text/javascript">
    "use strict";
	var current_path = '{{Request::path()}}';
	var current_route = '{{Route::currentRouteName()}}';
	var base_url = "{{url('/')}}";
	var csrf_token = '{{csrf_token()}}';
	var logo_url = "{{config('app.logo')}}";
    var logo_white_url = "{{config('app.logo_alt')}}";

    var landing_url_accept_cookie = "{{route('accept-cookie')}}";   

    var global_lang_success = "{{__('Success')}}";
    var global_lang_error = "{{__('Error')}}";
    var global_lang_confirm = "{{__('Confirm')}}";
    var global_lang_delete = "{{__('Delete')}}";
    var global_lang_cancel = "{{__('Cancel')}}";
</script>