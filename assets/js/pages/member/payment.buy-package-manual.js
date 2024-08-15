"user strict";


function delete_uploaded_file(filename) {
  if('' !== filename) {     
    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      data: { filename },
      url: manual_payment_upload_file_delete_route,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      success: function(data) {
        $('#uploaded-file').val('');
      }
    });
  }

  // Empties form values
  empty_form_values();     
}

// Empties form values
function empty_form_values() {
  $('#paid-amount').val(''),
  $('.dz-preview').remove();
  $('#additional-info').val(''),
  $('#paid-currency').prop("selectedIndex", 0);

}

$(document).ready(function() {
	$(document).ready(function(){
	  $(document).on('click', '#manual-payment-button', function(event) {
	    event.preventDefault();
	    $('#manual-payment-modal').modal('show');
	  });

	  // Handles form submit
	  $(document).on('click', '#manual-payment-submit', function() {
	    
	    // Reference to the current el
	    var that = this;

	    // Shows spinner
	    $(that).addClass('disabled btn-progress');

	    var data = {
	      paid_amount: $('#paid-amount').val(),
	      paid_currency: $('#paid-currency').val(),
	      package_id: $('#selected-package-id').val(),
	      additional_info: $('#additional-info').val(),
	    };

	    $.ajax({
	      type: 'POST',
	      dataType: 'JSON',
	      url: manual_payment_submission_route,
	      data: data,
	      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	      success: function(response) {
	        if (response.success) {
	          // Hides spinner
	          $(that).removeClass('disabled btn-progress');

	          // Empties form values
	          empty_form_values();
	          $('#selected-package-id').val('');  

	          // Shows success message
	          Swal.fire({
	            icon: 'success',
	            title: global_lang_success,
	            text: response.success,
	          });

	          // Hides modal
	          $('#manual-payment-modal').modal('hide');
	        }

	        // Shows error message
	        if (response.error) {
	          // Hides spinner
	          $(that).removeClass('disabled btn-progress');

	          Swal.fire({
	            icon: 'error',
	            title: global_lang_error,
	            text: response.error,
	          });
	        }
	      },
	      error: function(xhr, status, error) {
	        $(that).removeClass('disabled btn-progress');
	      },
	    });
	  });

	  $('#manual-payment-modal').on('hidden.bs.modal', function (e) {
	    var filename = $(uploaded_file).val();
	    $('#selected-package-id').val(''); 
	  });

	});
});

// Uploads files
var uploaded_file = $('#uploaded-file');
Dropzone.autoDiscover = false;
$("#manual-payment-dropzone").dropzone({ 
  url: manual_payment_upload_file_route,
  maxFilesize:5,
  uploadMultiple:false,
  paramName:"file",
  createImageThumbnails:true,
  acceptedFiles: ".pdf,.doc,.txt,.png,.jpg,.jpeg,.zip",
  maxFiles:1,
  addRemoveLinks:true,
  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
  success:function(file, response) {
    // Shows error message
    if (response.error) {
      swal({
        icon: 'error',
        text: response.error,
        title: global_lang_error
      });
      return;
    }

    if (response.filename) {
      $(uploaded_file).val(response.filename);
    }
  },
  removedfile: function(file) {
    var filename = $(uploaded_file).val();
    delete_uploaded_file(filename);
  },
});