<script>
	"use strict";
	var get_img_loader = '{{ asset('assets/images/pre-loader/color/Preloader_9.gif') }}';
	var title= '{{__("Update System")}}';
	var update_msg= '{{__("You are about to update system files and database.")}}';
	var get_update_url= '{{ route("update-initiate") }}';
</script>
<script src="{{ asset('assets/js/pages/update-index-v2.js') }}"></script>