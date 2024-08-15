"use strict";

$(document).ready(function()
{
	$(document).on('click', '.update', function(event) {
		swal.fire({
	      title: title,
	      text: update_msg,
	      icon: 'warning',
	      buttons: true,
	      dangerMode: true,
	    })
	    .then((willDelete) => {
	      if (willDelete) {
  			if($(this).is('[disabled=disabled]') == false)
  			{				
  				$("#update_success").modal('show');
  				var warning_msg="<?php echo __('do not close this window or refresh page untill update done.');?>";
  				var loading = warning_msg+'<br></<br><img src='+get_img_loader+' class="center-block" height="30" width="30">';
         		$("#update_success_content").attr('class','text-center').html(loading);

  				var updateVersionId = $(this).attr('updateid');
  				var version = $(this).attr('version');

  				var data = {"update_version_id" : updateVersionId,"version" : version};

  				$.ajax({
                    type: "POST",
  					data: data,
  					url: get_update_url,
  					dataType: 'JSON',
  					beforeSend: function (xhr) {
  					    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
  					},
  					success : function(response)
  					{
  						var what_class="";
  						if(response.status=='1') what_class='alert alert-success text-center';
  						else what_class='alert alert-danger text-center';
  						$("#update_success_content").attr('class',what_class).html(response.message);
  					},
			        error:function(response){
			          var span = document.createElement("span");
			          span.innerHTML = response.responseText;
			          swal({ title:'<?php echo __("Error!"); ?>', content:span,icon:'error'});
			        }
  				})
  				
  			}
	      } 
	    });
	});	

	$('#update_success').on('hidden.bs.modal', function () { 
		location.reload(); 
	});
	$('.modal-dialog').parent().on('show.bs.modal', function(e){ if($(this).attr('id')!="update_success")$(e.relatedTarget.attributes['data-bs-target'].value).appendTo('body'); })
});