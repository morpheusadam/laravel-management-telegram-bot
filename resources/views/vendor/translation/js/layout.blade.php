<script type="text/javascript">       
        "use strict";     
        var user_first_name = '';
        var user_last_name = '';
        var action_url = "{{ route('languages.delete') }}";        
        var create_lang_group = "{{ route('languages.translations.create-new-group') }}";

        var confirm_delete = "{{ __('Delete Language') }}";
        var after_deletion_confirm_text = "{{ __('Do you really want to delete this language? it will delete all files of this language.') }}";
        $(document).ready(function($){
            $('[data-bs-toggle=\"tooltip\"]').tooltip();
            $('.select2').select2();
        });
</script>