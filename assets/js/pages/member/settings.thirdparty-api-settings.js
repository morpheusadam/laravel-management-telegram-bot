"use strict";


$(document).ready(function() {

    $(document).on('click','#new-thirdparty-api-profile',function(e){
        e.preventDefault();

        $("#thirdparty-update-id").val('0');
        $("#thirdparty_api_settings_modal").modal('show');
        $("#thirdparty_api_settings_modal form").find(":input:not([reset=false])").val('');
        $("#thirdparty_api_settings_modal form").find(":input").prop('readonly',false);

        setTimeout(function(){           
            var api_type = $("#new-thirdparty-api-profile").attr('data-type');
            if(api_type=='woocommerce') $(".thirdparty-api-block #woocommerce-block-link").tab("show");
            else if(api_type=='shopify') $(".thirdparty-api-block #shopify-block-link").tab("show");
        }, 500);
    });
    
    $(document).on('click','#save_thirdparty_api_settings',function(e){
        var href = $('.thirdparty-api-block .nav-link.active').attr('href');
        var form_id = href+'-form';
        form_id = form_id.replace('#','');

        var form = document.getElementById(form_id);
        var missing_input = false;

        for(var i=0; i < form.elements.length; i++){
          if(form.elements[i].value === '' && !form.elements[i].hasAttribute('not-required')){
            missing_input = true;
          }
        }

        if(missing_input) {
            Swal.fire({title: global_lang_warning, text: global_lang_fill_required_fields,icon: 'warning',confirmButtonText: global_lang_ok});
            return false;
        }

        $("#save_thirdparty_api_settings").attr('disabled',true);
        var update_id = $("#thirdparty-update-id").val();
        var form_data = $("#"+form_id).serialize()+'&update_id=' + update_id;
        var api_name = form_id.replace('-block-form','');
        form_data = form_data +'&api_name='+api_name+'&is_thirdparty_api=1';

        $.ajax({
            url: member_settings_list_api_settings_url_save,
            method: "POST",
            data: form_data,
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {
                $("#save_thirdparty_api_settings").removeAttr('disabled');
                if(response.error=='1') Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                else {
                    Swal.fire({title: global_lang_success, text: response.message,icon: 'success',confirmButtonText: global_lang_ok})
                    .then(function () {
                      $("#thirdparty_api_settings_modal").modal('hide');
                    });
                }
           },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });


    });
    
    $('#thirdparty_api_settings_modal').on('hidden.bs.modal', function (e) {
      $("#api_type").trigger('change');
      $("#store_type").trigger('change');
      if(typeof(table4)!=='undefined') table4.draw();
    });

});
