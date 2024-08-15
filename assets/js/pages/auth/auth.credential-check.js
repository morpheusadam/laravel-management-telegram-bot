"use strict";

$(document).ready(function() {
  $(document).on('click','#submit',function(e){
    e.preventDefault();
    const submitButtonElement = $('#submit');
    var purchase_code = $("#purchase_code").val().trim();
    if(purchase_code=='')
    {
        $("#purchase_code").addClass('is-invalid');
        return false;
    }
    else
    {
        $("#purchase_code").removeClass('is-invalid');
    }
    
    var domain_name = base_url;
    $(this).trigger('blur');
    $(this).addClass("btn-progress");
    $.ajax({
        context:this,
        type: "POST",
        data:{domain_name:domain_name,purchase_code:purchase_code},
        url : purchase_code_active,
        dataType: 'JSON',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response)
        {
          $(this).removeClass("btn-progress");
          var response = JSON.parse(response);
          if(response.status == "success")
          {
            var link = base_url+'/dashboard';
            window.location.href = link;
          }
          else 
          {
            var success_message=response.reason;
            var span = document.createElement("span");
            span.innerHTML = success_message;
            swal.fire({ title:global_lang_error,text:success_message, content:span,icon:'error'});
          } 
        },
        error: function (xhr, statusText) {
            submitButtonElement.removeClass('disabled');
            submitButtonElement.removeClass('btn-progress');
            const msg = handleAjaxError(xhr, statusText);
            Swal.fire({
                title: global_lang_error,
                html: msg,
                icon: 'warning'
            });
            return false;
        },
      });         

  });
});