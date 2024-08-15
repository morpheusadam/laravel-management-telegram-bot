"user strict";


$(document).ready(function() {
	$(document).on('click', '.delete_lang', function(event) {
		event.preventDefault();

		var language = $(this).attr("locale_name");

		Swal.fire({
		  title: confirm_delete,
		  text: after_deletion_confirm_text,
		  icon: 'warning',
		  buttons: true,
		  dangerMode: true,
		  showCancelButton: true,
		}).then((result) => {
			if(result.isConfirmed) {
				$.ajax({
					url: action_url+'/'+language,
					type: 'GET',
					dataType:'json',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					success:function(response){
						if(response.status=='1')
							Swal.fire('Success',response.message,'success').then((value) => {location.reload()});
						else
							Swal.fire('Error',response.message,'error');
					}
				});
			}

		})

		
	});


	$(document).on('click','#create_contact_group',function(e){
	  e.preventDefault();
	  const { value: group_name } = Swal.fire({
	    title: 'Enter your Group name',
	    input: 'text',
	    inputLabel: 'Your Group name',
	    inputValue: '',
	    showCancelButton: true,
	    inputValidator: (value) => {
	      if (value) {
	      	var dir_name = $(this).attr('lang-dir');
	        $.ajax({
	          type: 'GET',
	          dataType:'json',
	          url:create_lang_group+'/'+dir_name+'/'+value,
	          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
	          success:function(response){

	             	if(response.error) {
	             		Swal.fire('Error',response.error,'error');

	             	} else {
	                	var newOption = new Option(response.text, response.id, true, true);
	                	$('#group').append(newOption).trigger('change');
	              	}
	          }
	        });
	      }
	    }
	  })

	});
});