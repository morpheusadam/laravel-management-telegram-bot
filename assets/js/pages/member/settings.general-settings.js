"use strict";

var perscroll;
var perscroll2;
var perscroll3;
var perscroll4;
var perscroll5;
var table =''; // email
var table2 =''; // autoresponder
var table3 =''; //sms
var table4 =''; //thirdparty api
var table5 =''; //video tutorials
$(document).ready(function() {

	setTimeout(function(){
		if(active_tag_id=='') {
            if($("#myTab").css('display')!='none') active_tag_id = $("#myTab .nav-item:first-child .nav-link").attr('id');
            else active_tag_id = $("#myTab2 .nav-item:first-child .nav-link").attr('id');
        }
		if($("#myTab").css('display')!='none')$("#myTab #"+active_tag_id).tab("show");
        else $("#myTab2 #"+active_tag_id).tab("show");
	}, 500);

	reload_default_email();

	$('.myTab .nav-link').on('shown.bs.tab', function (e) {
		var link_id = $(this).attr('id');

		$.ajax({
            url: ajax_set_active_tag_id,
            method: "POST",
            data: {link_id},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            }

        });

		if(link_id!='email-tab' && link_id!='sms-tab' && link_id!='emailauto-tab'&& link_id!='thirdparty-api-tab' && link_id!='video-tab') return false;

		var table_name = 'mytable';
		var ajax_url = member_settings_list_api_settings_url_data;
		var video_tutorial_data = member_settings_list_video_tutorial_data;
		var video_tutorial_update_data_action = video_tutorial_update_data_action;
		var is_autoresponder = 0;
		var is_sms = 0;
        var is_thirdparty_api = 0;
        var is_video_tutorials = 0;

		if(link_id=='emailauto-tab'){
			table_name = 'mytable2';
			is_autoresponder = 1;
		}
        else if(link_id=='sms-tab'){
            table_name = 'mytable3';
            is_sms = 1;
        }

		if( !is_autoresponder){

			if(is_sms){
			    setTimeout(function(){
                    if(table3==''){
                        table3 = $("#"+table_name).DataTable({
                            fixedHeader: false,
                            colReorder: true,
                            serverSide: true,
                            processing:true,
                            bFilter: true,
                            order: [[ 4, "desc" ]],
                            pageLength: 5,
                            lengthMenu: [5, 10, 20, 50, 100],
                            ajax:
                                {
                                    "url": ajax_url,
                                    "type": 'POST',
                                    data: function ( d )
                                    {
                                        d.is_autoresponder = is_autoresponder;
                                        d.is_sms = is_sms;
                                        d.is_thirdparty_api = is_thirdparty_api;

                                    },
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                                    },
                                },
                            language:
                                {
                                    url: global_url_datatable_language
                                },
                            dom: '<"top"f>rt<"bottom"lip><"clear">',
                            columnDefs: [
                                {
                                    targets: [1,4],
                                    visible: false
                                },
                                {
                                    targets: [3,5],
                                    className: 'text-center'
                                },
                                {
                                    targets: [3],
                                    sortable: false
                                }
                            ],
                            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                                if(areWeUsingScroll)
                                {
                                    if (perscroll3) perscroll3.destroy();
                                    perscroll3 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                                var $searchInput = $('div.dataTables_filter input');
                                $searchInput.unbind();
                                $searchInput.bind('keyup', function(e) {
                                    if(this.value.length > 2 || this.value.length==0) {
                                        table3.search( this.value ).draw();
                                    }
                                });
                            },
                            scrollX: 'auto',
                            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                                if(areWeUsingScroll)
                                {
                                    if (perscroll3) perscroll3.destroy();
                                    perscroll3 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                            }
                        });
                    }
                    else table3.draw();
                }, 500);
            }

            else if(is_thirdparty_api){
                setTimeout(function(){
                    if(table4==''){
                        table4 = $("#"+table_name).DataTable({
                            fixedHeader: false,
                            colReorder: true,
                            serverSide: true,
                            processing:true,
                            bFilter: true,
                            order: [[ 4, "desc" ]],
                            pageLength: 5,
                            lengthMenu: [5, 10, 20, 50, 100],
                            ajax:
                                {
                                    "url": ajax_url,
                                    "type": 'POST',
                                    data: function ( d )
                                    {
                                        d.is_autoresponder = is_autoresponder;
                                        d.is_sms = is_sms;
                                        d.is_thirdparty_api = is_thirdparty_api;
                                    },
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                                    },
                                },
                            language:
                                {
                                    url: global_url_datatable_language
                                },
                            dom: '<"top"f>rt<"bottom"lip><"clear">',
                            columnDefs: [
                                {
                                    targets: [1,4],
                                    visible: false
                                },
                                {
                                    targets: [3,5],
                                    className: 'text-center'
                                },
                                {
                                    targets: [3],
                                    sortable: false
                                }
                            ],
                            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                                if(areWeUsingScroll)
                                {
                                    if (perscroll4) perscroll4.destroy();
                                    perscroll4 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                                var $searchInput = $('div.dataTables_filter input');
                                $searchInput.unbind();
                                $searchInput.bind('keyup', function(e) {
                                    if(this.value.length > 2 || this.value.length==0) {
                                        table4.search( this.value ).draw();
                                    }
                                });
                            },
                            scrollX: 'auto',
                            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                                if(areWeUsingScroll)
                                {
                                    if (perscroll4) perscroll4.destroy();
                                    perscroll4 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                            }
                        });
                    }
                    else table4.draw();
                }, 500);
            }
            else if(is_video_tutorials){
                setTimeout(function(){
                    if(table5==''){
                        table5 = $("#"+table_name).DataTable({
                            fixedHeader: false,
                            colReorder: true,
                            serverSide: true,
                            processing:true,
                            bFilter: true,
                            order: [[ 2, "desc" ]],
                            pageLength: 30,
                            lengthMenu: [ 30 ,50, 100],
                            ajax:
                                {
                                    "url": video_tutorial_data,
                                    "type": 'POST',
                                    data: function ( d )
                                    {
                                        d.is_autoresponder = is_autoresponder;
                                        d.is_thirdparty_api = is_thirdparty_api;
                                        d.is_video_tutorials = is_thirdparty_api;
                                    },
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                                    },
                                },
                            language:
                                {
                                    url: global_url_datatable_language
                                },
                            dom: '<"top"f>rt<"bottom"lip><"clear">',
                            columnDefs: [
                                {
                                    targets: [0, 2], // 0 for #, 2 for actions
                                    className: "text-center"
                                },
                                {
                                    targets: 2,
                                    sortable: false
                                }

                            ],
                            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                                if(areWeUsingScroll)
                                {
                                    if (perscroll5) perscroll5.destroy();
                                    perscroll5 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                                var $searchInput = $('div.dataTables_filter input');
                                $searchInput.unbind();
                                $searchInput.bind('keyup', function(e) {
                                    if(this.value.length > 2 || this.value.length==0) {
                                        table5.search( this.value ).draw();
                                    }
                                });
                            },
                            scrollX: 'auto',
                            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                                if(areWeUsingScroll)
                                {
                                    if (perscroll5) perscroll5.destroy();
                                    perscroll5 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                            }
                        });
                    }
                    else table5.draw();
                }, 500);
            }

            else { // email
                setTimeout(function(){
                    if(table=='')
                    {
                        table = $("#"+table_name).DataTable({
                            fixedHeader: false,
                            colReorder: true,
                            serverSide: true,
                            processing:true,
                            bFilter: true,
                            order: [[ 4, "desc" ]],
                            pageLength: 5,
                            lengthMenu: [5, 10, 20, 50, 100],
                            ajax:
                                {
                                    "url": ajax_url,
                                    "type": 'POST',
                                    data: function ( d )
                                    {
                                        d.is_autoresponder = is_autoresponder;
                                    },
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                                    },
                                },
                            language:
                                {
                                    url: global_url_datatable_language
                                },
                            dom: '<"top"f>rt<"bottom"lip><"clear">',
                            columnDefs: [
                                {
                                    targets: [1,4],
                                    visible: false
                                },
                                {
                                    targets: [3,5],
                                    className: 'text-center'
                                },
                                {
                                    targets: [3],
                                    sortable: false
                                }
                            ],
                            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                                if(areWeUsingScroll)
                                {
                                    if (perscroll) perscroll.destroy();
                                    perscroll = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                                var $searchInput = $('div.dataTables_filter input');
                                $searchInput.unbind();
                                $searchInput.bind('keyup', function(e) {
                                    if(this.value.length > 2 || this.value.length==0) {
                                        table.search( this.value ).draw();
                                    }
                                });
                            },
                            scrollX: 'auto',
                            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                                if(areWeUsingScroll)
                                {
                                    if (perscroll) perscroll.destroy();
                                    perscroll = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
                                }
                            }
                        });
                    }
                    else table.draw();
                }, 500);
            }
		}
		
        else{ // auto responder
			reload_default_autoresponder();
			setTimeout(function(){
				if(table2=='')
				{
				  table2 = $("#"+table_name).DataTable({
				      fixedHeader: false,
				      colReorder: true,
				      serverSide: true,
				      processing:true,
				      bFilter: true,
				      order: [[ 4, "desc" ]],
				      pageLength: 5,
				      lengthMenu: [5, 10, 20, 50, 100],
				      ajax:
				          {
				              "url": ajax_url,
				              "type": 'POST',
				              data: function ( d )
				              {
				                  d.is_autoresponder = is_autoresponder;
				              },
				              beforeSend: function (xhr) {
				                  xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
				              },
				          },
				      language:
				          {
				              url: global_url_datatable_language
				          },
				      dom: '<"top"f>rt<"bottom"lip><"clear">',
				      columnDefs: [
				          {
				              targets: [1,4],
				              visible: false
				          },
				          {
				              targets: [3,5],
				              className: 'text-center'
				          },
				          {
				              targets: [3],
				              sortable: false
				          }
				      ],
				      fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
				          if(areWeUsingScroll)
				          {
				              if (perscroll2) perscroll2.destroy();
				              perscroll2 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
				          }
				          var $searchInput = $('div.dataTables_filter input');
				          $searchInput.unbind();
				          $searchInput.bind('keyup', function(e) {
				              if(this.value.length > 2 || this.value.length==0) {
				                  table2.search( this.value ).draw();
				              }
				          });
				      },
				      scrollX: 'auto',
				      fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
				          if(areWeUsingScroll)
				          {
				              if (perscroll2) perscroll2.destroy();
				              perscroll2 = new PerfectScrollbar('#'+table_name+'_wrapper .dataTables_scrollBody');
				          }
				      }
				  });
				}
				else table2.draw();
			}, 500);
		}
	});

	$(document).on('click','#new-profile',function(e){
		$("#update-id").val('0');
		$("#email_settings_modal").modal('show');
		$("#email_settings_modal form").find(":input:not([reset=false])").val('');
		$("#email_settings_modal form").find(":input").prop('readonly',false);
	});

    $(document).on('click','#new-sms-profile',function(e){
        $("#sms-update-id").val('0');
        $("#sms_settings_modal").modal('show');
        $("#sms_settings_modal form").find(":input:not([reset=false])").val('');
        $("#sms_settings_modal form").find(":input").prop('readonly',false);
    });

	$(document).on('click','#new-auto-profile',function(e){
		$("#auto-update-id").val('0');
		$("#email_auto_settings_modal").modal('show');
		$("#email_auto_settings_modal form").find(":input:not([reset=false])").val('');
		$("#email_auto_settings_modal form").find(":input").prop('readonly',false);
	});

	$(document).on('click','#save_email_settings',function(e){
		var href = $('.email-block .nav-link.active').attr('href');
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

        $("#save_email_settings").attr('disabled',true);
        var update_id = $("#update-id").val();
        var form_data = $("#"+form_id).serialize()+'&update_id=' + update_id;
        var api_name = form_id.replace('-block-form','');
        form_data = form_data +'&api_name='+api_name;

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
                $("#save_email_settings").removeAttr('disabled');
                if(response.error=='1') Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                else {
                	Swal.fire({title: global_lang_success, text: response.message,icon: 'success',confirmButtonText: global_lang_ok})
                	.then(function () {
                	  $("#email_settings_modal").modal('hide');
                	  reload_default_email();
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

	$(document).on('click','#save_sms_settings',function(e){
		var href = $('.sms-block .nav-link.active').attr('href');
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

        $("#save_sms_settings").attr('disabled',true);
        var update_id = $("#sms-update-id").val();
        var form_data = $("#"+form_id).serialize()+'&update_id=' + update_id;
        var api_name = form_id.replace('-block-form','');
        form_data = form_data +'&api_name='+api_name+'&is_sms=1';

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
                $("#save_sms_settings").removeAttr('disabled');
                if(response.error=='1') Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                else {
                	Swal.fire({title: global_lang_success, text: response.message,icon: 'success',confirmButtonText: global_lang_ok})
                	.then(function () {
                	  $("#sms_settings_modal").modal('hide');
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
    $(document).on('click','#save_video_settings',function(e){

        var id = $('#video-update-id').val();
        var text = tinymce.activeEditor.getContent();; 
          
        $('#video_settings_modal').modal('hide');

        $.ajax({
            url: video_tutorial_update_data_action,
            method: "POST",
            data: {id,text},
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {
                if(response.error=='1') Swal.fire({title: global_lang_error, icon: 'error'});
                else{
                Swal.fire({title: global_lang_success, icon: 'success'});

                }
            },

        });


    });

	$(document).on('click','#save_email_auto_settings',function(e){
		var href = $('.email-auto-block .nav-link.active').attr('href');
		var form_id = href+'-form';
		form_id = form_id.replace('#','');

		var form = document.getElementById(form_id);
		var missing_input = false;

	    for(var i=0; i < form.elements.length; i++){
	      if(form.elements[i].value == '' && !form.elements[i].hasAttribute('not-required')){
	       	missing_input = true;
	      }
	    }

	    if(missing_input) {
	    	Swal.fire({title: global_lang_warning, text: global_lang_fill_required_fields,icon: 'warning',confirmButtonText: global_lang_ok});
            return false;
	    }

        $("#save_email_auto_settings").attr('disabled',true);
        var update_id = $("#auto-update-id").val();
        var form_data = $("#"+form_id).serialize()+'&update_id=' + update_id;
        var api_name = form_id.replace('-block-form','');
        form_data = form_data +'&api_name='+api_name+'&is_autoresponder=1';

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
                $("#save_email_auto_settings").removeAttr('disabled');
                if(response.error=='1') Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
                else {
                	Swal.fire({title: global_lang_success, text: response.message,icon: 'success',confirmButtonText: global_lang_ok})
                	.then(function () {
                	  $("#email_auto_settings_modal").modal('hide');
                	  location.reload();
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

	$('#email_settings_modal').on('hidden.bs.modal', function (e) {
	  table.draw();
	});

	$('#sms_settings_modal').on('hidden.bs.modal', function (e) {
	  table3.draw();
	});

	$('#email_auto_settings_modal').on('hidden.bs.modal', function (e) {
	  table2.draw();
	});

	$(document).on('click','.update-api-settings-row',function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var is_autoresponder = 0;
		var is_sms = 0;
        var is_thirdparty_api = 0;
		var modal_id = '#email_settings_modal';
		var update_field = '#update-id';

		if($(this).hasClass('is_sms')) is_sms = 1;
        if($(this).hasClass('is_thirdparty_api')) is_thirdparty_api = 1;
		if($(this).hasClass('is_autoresponder')) is_autoresponder = 1;
		if(is_autoresponder){
		    modal_id = '#email_auto_settings_modal';
            update_field = '#auto-update-id';
        }
		else if(is_sms){
            modal_id = '#sms_settings_modal';
            update_field = '#sms-update-id';
        }
        else if(is_thirdparty_api){
            modal_id = '#thirdparty_api_settings_modal';
            update_field = '#thirdparty-update-id';
        }

		$(update_field).val(id);

		$.ajax({
            url: member_settings_list_api_settings_url_update_data,
            method: "POST",
            data: {id,is_autoresponder,is_sms,is_thirdparty_api},
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {
                $(modal_id).modal('show');
                setTimeout(function(){
                	var temp = "[href='#"+response.api_name+"-block']";
                	$(temp).tab('show');

                	var temp2 = '';
                	$.each(response, function(key, value) {
                		temp2 = "#"+response.api_name+"-block-form [name="+key+"]";
                		$(temp2).val(value);
                		var hasAttribute = $(temp2).attr('non-editable');
                		if (typeof hasAttribute !== 'undefined' && hasAttribute !== false) $(temp2).prop("readonly", true);
                		else $(temp2).prop("readonly", false);
                	});
                }, 500);

            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });
	});
	$(document).on('click','.update-video-settings-row',function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var is_video_tutorials = 1;
		var modal_id = '#video_settings_modal';
		var update_field = '#video-update-id';
        $('#video-update-id').val(id);


		$(update_field).val(id);

		$.ajax({
            url: member_settings_list_video_update_data,
            method: "POST",
            data: {id,is_video_tutorials},
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response)
            {
                $(modal_id).modal('show');
                $('#video_name').val(response.name);
                tinymce.activeEditor.setContent(response.text);

            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            }

        });
	});
    
    $('#video_settings_modal').on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
    });

});

function reload_default_email() {

	$.ajax({
        url: common_function_url_get_email_profile_dropdown,
        method: "POST",
        data: {icon:true},
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response)
        {
            $("#default-main-container").html(response);
        }

    });
}


function reload_default_autoresponder() {

}
