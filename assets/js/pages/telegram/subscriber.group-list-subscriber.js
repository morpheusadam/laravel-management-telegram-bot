"use strict";

var perscroll;
var perscroll2;
var perscroll3;
var table='';
var table2 ='';
var table3 ='';
var upto = 1000;
var telegram_group_id ='';
var telegram_group_subscriber_id ='';
function toggleKeywordAnalysis() {
	var censorWordsCheckbox = document.getElementById("censor_words");
	var keywordAnalysisDiv = document.getElementById("keyword_analysis");

	if (censorWordsCheckbox.checked) {
	keywordAnalysisDiv.classList.remove("d-none");
	} else {
	keywordAnalysisDiv.classList.add("d-none");
	}
}
function scheduleOption() {
	var send_later = document.getElementById("send_later");
	var schedule_option = document.getElementById("schedule_option");

	if (send_later.checked) {
	schedule_option.classList.remove("d-none");
	} else {
	schedule_option.classList.add("d-none");
	}
}

var statistics_chart = document.getElementById("myChart");
if(statistics_chart != null) {
    var statistics_chart2 = statistics_chart.getContext('2d');
    var myChart = new Chart(statistics_chart2, {
        type: 'line',
        data: {
            labels: JSON.parse(member_chart_labels),
            datasets: [{    
                label: telegram_group_activity_chart_title,
                data: JSON.parse(member_chart_values),
                borderWidth: 3,
                borderColor: '#0d8bf1',
                backgroundColor: 'transparent',
                pointBackgroundColor: '#0d8bf1',
                pointBorderColor: '#0d8bf1',
                pointRadius: 3
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        stepSize: member_chart_steps
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false,
                        color: '#dee2e6',
                        lineWidth: 1
                    }
                }]
            },
        }
    });  
}

function set_tab_menu_id_session(link_id,reload){
    $.ajax({
        type:'POST' ,
        url:telegram_group_manager_url_set_active_group_tab_menu_session,
        data:{link_id},
        async: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response){
            if(reload) location.reload();
        },
        error: function (xhr, statusText) {
            const msg = handleAjaxError(xhr, statusText);
            Swal.fire({icon: 'error',title: global_lang_error,html: msg});
            return false;
        }
    });
}

$(document).ready(function(){

	setTimeout(function(){
	    var telegram_group_manager_telegram_group_tab_menu_id_session_temp = telegram_group_manager_telegram_group_tab_menu_id_session.replace('-tab','');
	    var temp = "[href='#"+telegram_group_manager_telegram_group_tab_menu_id_session_temp+"']";
	    $(temp).tab('show');
	}, 200);

	
	$('#v-pills-tab .nav-link').on('shown.bs.tab', function (e){
		var link_id = $(this).attr('id');
		set_tab_menu_id_session(link_id,false);
		if(link_id=='v-pills-group-subscriber-tab'){
		    $("#put_action_title").html(telegram_group_manager_lang_active);
		    if(table==''){
		       if(1)
		       setTimeout(function(){
		       }, 500);
		        table = $("#mytable").DataTable({
		        	    fixedHeader: false,
		        	    colReorder: true,
		        	    serverSide: true,
		        	    processing:true,
		        	    bFilter: false,
		        	    order: [[ 6, "desc" ]],
		        	    pageLength: 10,
		        	    lengthMenu: [[10, 25, 50, 100,200,500], [10, 25, 50, 100,200,500]],
		        	    buttons:
		        	    [
		        	        {
		        	            extend: 'csv',
		        	            text: global_lang_download,
		        	            exportOptions: {
		        	                columns: [ 0,3,4,5,6,7,8]
		        	            }
		        	        }
		        	     ],

		        	    ajax:
		        	        {
		        	            "url": telegram_group_list_subscriber_url_data,
		        	            "type": 'POST',
		        	            beforeSend: function (xhr) {
		        	                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
		        	            },
		        	            data: function ( d )
		        	            {
		        	                d.search_value = $('#search_value').val();
		        	                d.subscriber_status = $('#subscriber_status').val();
		        	                d.label_id = $('#label_id').val();
		        	                d.is_subscribed = $('#is_subscribed').val();
		        	                d.telegram_bot_input_flow_campaign_id = $('#auto_selected_flow_id').val();
		        	            }
		        	        },
		        	    language:
		        	        {
		        	            url: global_url_datatable_language
		        	        },
		        	    dom: '<"top"Bf>rt<"bottom"lip><"clear">',
		        	    columnDefs: [
		        	        {
		        	            targets: [1,6],
		        	            className: 'text-center'
		        	        },
		        	        {
	                            targets: [3,6,7,8],
	                            visible: false
                       		 },
		        	        {
		        	            targets: [0,1,2],
		        	            sortable: false
		        	        }
		        	    ],

		        	    fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
		        	        if(areWeUsingScroll)
		        	        {
		        	            if (perscroll) perscroll.destroy();
		        	            perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
		        	        }
		        	        var $searchInput = $('#mytable_filter input');
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
		        	            perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
		        	        }
		        	    }
		        	});

		    }

		    else table.draw();
		}
		else if(link_id=='v-pills-filtering-message-tab'){
		    $("#put_action_title").html(telegram_group_message_filtering);
		}
		else if(link_id=='v-pills-group-activity-tab'){
		    $("#put_action_title").html(telegram_group_activity);
		}
		else if(link_id=='v-pills-send-message-tab'){
		    $("#put_action_title").html(telegram_group_message_send);
		    if(table2==''){
		       if(1)
		       setTimeout(function(){
		       }, 500);
		        table2 = $("#mytable2").DataTable({
		        	    fixedHeader: false,
		        	    colReorder: true,
		        	    serverSide: true,
		        	    processing:true,
		        	    bFilter: false,
		        	    order: [[ 4, "desc" ]],
		        	    pageLength: 10,
		        	    lengthMenu: [[10, 25, 50, 100,200,500], [10, 25, 50, 100,200,500]],
		        	    buttons:
		        	    [
		        	        {
		        	            extend: 'csv',
		        	            text: global_lang_download,
		        	            exportOptions: {
		        	                columns: [ 0,3,4,5]
		        	            }
		        	        }
		        	     ],

		        	    ajax:
		        	        {
		        	            "url": telegram_campaign_list_data,
		        	            "type": 'POST',
		        	            beforeSend: function (xhr) {
		        	                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
		        	            },
		        	            data: function ( d )
		        	            {
		        	                d.search_value_send_message = $('#search_value_send_message').val();
		        	                d.search_status = $('#search_status').val();

		        	            }
		        	        },
		        	    language:
		        	        {
		        	            url: global_url_datatable_language
		        	        },
		        	    dom: '<"top"Bf>rt<"bottom"lip><"clear">',
		        	    columnDefs: [
		        	        {
		        	            targets: [1,4],
		        	            className: 'text-center'
		        	        },
		        	        {
		        	            targets: [0,1,2],
		        	            sortable: false
		        	        }							
		        	    ],

		        	    fnInitComplete:function(){  // when initialization is completed then apply scroll plug
		        	        if(areWeUsingScroll)
		        	        {
		        	            if (perscroll2) perscroll2.destroy();
		        	            perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
		        	        }
		        	        var $searchInput = $('#mytable2_filter input');
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
		        	            perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
		        	        }
		        	    }
		        	});

		    }
		    else table2.draw();

		}
	});

	

	$(document).on('change', '#subscriber_status', function(e) {
		    table.draw();
	});

	$(document).on('change', '#search_status', function(e) {
		    table2.draw();
	});

	$(document).on('click', '.edit_telegram_group_campaign', function(e) {
		 e.preventDefault();
		 var campaign_id = $(this).data('id');
		$.ajax({
		 	url: telegram_group_edit_campaign,
		 	type: 'POST',
	     	data:{campaign_id},
		       async: false,
		       beforeSend: function (xhr) {
		           xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
		       },
		       success:function(response){
		         $('#campaign_id').val(campaign_id);
		         $('#campaign_name').val(response.campaign_name);
		         $('#text_message').text(response.text);
		         $('#pin_this_announcement').prop('checked', response.pin_post == 1);
		         $('#preview_the_URL_in_the_text_message').prop('checked',response.preview_the_URL_in_the_text_message ==='1');
		         $('#protected_messages_no_copying_or_forwarding').prop('checked',response.protected_messages_no_copying_or_forwarding ==='1');
		         $('#sound_alerts_for_messages').prop('checked',response.sound_alerts_for_messages ==='1');
		         var formattedDateTime = moment(response.schedule_time, 'Do MMM YY HH:mm').format('YYYY-MM-DD HH:mm:ss');
		         $('#schedule_time').val(formattedDateTime);
		         $('#timezone select').val(response.timezone).trigger('change');
		         $("#group_message_send_modal").modal('show');
		       },
		       error: function (xhr, statusText) {
		           const msg = handleAjaxError(xhr, statusText);
		           Swal.fire({icon: 'error',title: global_lang_error,html: msg});
		           return false;
		       }
		});
		 
	});

	$(document).on('click', '#download_data', function(e) {
	    e.preventDefault();
	    $(".buttons-csv").click();
	});

	$(document).on('click', '#delete-row', function(e) {
	    e.preventDefault();
	    
	});



	$(document).on('keyup', '#search_value_send_message', function(e) {
    	if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table2.draw(false);
	});
	$(document).on('keyup', '#search_value', function(e) {
	    if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table.draw(false);
	});

	

	$(document).on('click','.mute_member',function(e){
	    e.preventDefault();
		$('#mute_duration').val('');
		$('#mute_time_duration').val('');
	    var mute_id = $(this).data('id');
	    var mute_date_time =$(this).data('muted-time');
	    const values = mute_id.split(" ");
	     telegram_group_id = values[0];
	     telegram_group_subscriber_id = values[1];
	     if(mute_date_time == ''){
	   	   $("#muted_date_time").html('');
	   	   $("#unmute_member").hide();
	     }
	     else{
	     	var muted_time_text = telegram_muted_date_text_lang+' : ' +mute_date_time;
	     	$("#unmute_member").show();
	     	$("#muted_date_time").html(muted_time_text);
	     }

	    $("#mute_member_modal").modal('show');
	});

	$(document).on('click', '.total_message', function(e) {
        e.preventDefault();
	    var total_msg_id = $(this).data('id');
	    const values = total_msg_id.split(" ");
	    telegram_group_id = values[0];
	    telegram_group_subscriber_id = values[1]


        $("#total_msg_modal").modal('show');

        setTimeout(function(){
            if(table3==''){
                table3 = $("#mytable3").DataTable({
                    fixedHeader: false,
                    colReorder: true,
                    serverSide: true,
                    processing:true,
                    bFilter: true,
                    order: [[ 1, "asc" ]],
                    pageLength: 10,
                    ajax:
                        {
                            "url": telegram_group_show_subscriber_message,
                            "type": 'POST',
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                            },
							data:{telegram_group_subscriber_id:telegram_group_subscriber_id}
                        },
                    language:
                        {
                            url: global_url_datatable_language
                        },
                    dom: '<"top"f>rt<"bottom"lip><"clear">',
                    columnDefs: [
                        {
                            targets: [0,3],
                            className: 'text-center'
                        },
                        {
                            targets: [0,2],
                            sortable: false
                        },
                        {
                            targets: [1],
                            visible: false
                        }

                    ],
                    fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                        if(areWeUsingScroll)
                        {
                            if (perscroll3) perscroll3.destroy();
                            perscroll3 = new PerfectScrollbar('#mytable3_wrapper .dataTables_scrollBody');
                        }
                        var $searchInput = $('#mytable3_filter input');
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
                            perscroll3 = new PerfectScrollbar('#mytable3_wrapper .dataTables_scrollBody');
                        }
                    }
                });
            }
            else table3.draw();
        }, 1000);


    });
	 
	$(document).on('click','#create_group_campaign',function(){
		$('#campaign_id').val('');
		$('#campaign_name').val('');
		$('#text_message').val('');
		$('#pin_this_announcement').prop('checked', false);
		$('#preview_the_URL_in_the_text_message').prop('checked', false);
		$('#protected_messages_no_copying_or_forwarding').prop('checked', false);
		$('#sound_alerts_for_messages').prop('checked', false);
		$('#schedule_time').val('');
	    $("#group_message_send_modal").modal('show');
	});


	$(document).on('click','#mute_member_submit',function(e){
	    e.preventDefault();
	    const now = new Date();
	 	var mute_time = $('#mute_time_duration').val();
	 	var mute_type = $('#mute_duration').val();

	 	if(mute_time == ''){
	 		    Swal.fire({
	 		        title: global_lang_warning,
	 		        text: telegram_group_select_mute_time_lang,
	 		        icon: 'warning',
	 		        confirmButtonText: global_lang_ok
	 		    });
		    return false;
	 	}
	 	$.ajax({
	 	    context: this,
	 	    type:'POST',
	 	    url: telegram_group_mute_chat_member,
	 	    data:{telegram_group_id:telegram_group_id,telegram_group_subscriber_id:telegram_group_subscriber_id,mute_time:mute_time,mute_type:mute_type},
	 	    beforeSend: function (xhr) {
	 	        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
	 	    },
	 	    success:function(response){
	 	    	if(!response.ok){
	 	    		Swal.fire({
	 	    		    title: global_lang_warning,
	 	    		    text: response.description,
	 	    		    icon: 'warning',
	 	    		    confirmButtonText: global_lang_ok
	 	    		});
	 	    		return false;
	 	    	}
	 	    	else{
	 	    		Swal.fire({
	 	    		    title: global_lang_success,
	 	    		    text: telegram_group_mute_chat_member_lang,
	 	    		    icon: 'success',
	 	    		    confirmButtonText: global_lang_ok
	 	    		}).then((result) => {
                           	$('#mute_duration').val('');
                           	$('#mute_time_duration').val('');
                            $("#mute_member_modal").modal('hide');
                            table.draw();
                        });
	 	    		
	 	    	}
	 	    }
	 	});
	
	});

	$(document).on('change', '#time_selection', function(event) {
		 $('#time_selection_form').submit();
	});


	$(document).on('click','.ban_member',function(e){
	    e.preventDefault();


	    Swal.fire({
	        title: global_lang_confirm,
	        text: global_lang_ban_confirmation,
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#d33',
	        cancelButtonColor: '',
	        confirmButtonText: global_lang_ban_member,
	        cancelButtonText: global_lang_cancel
	    }).then((result) => {
	        if (result.isConfirmed) {
	      
	        	var id = $(this).data('id');
	            $.ajax({
	                context:this,
	                method: 'post',
	                data: {id:id},
	                url: telegram_group_banned_chat_member,
	                beforeSend: function (xhr) {
	                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
	                },
	                success: function (response) {
           	 	    	if(!response.ok){
           	 	    		Swal.fire({
           	 	    		    title: global_lang_warning,
           	 	    		    text: response.description,
           	 	    		    icon: 'warning',
           	 	    		    confirmButtonText: global_lang_ok
           	 	    		});
           	 	    		return false;
           	 	    	}
           	 	    	else{
           	 	    		Swal.fire({
           	 	    		    title: global_lang_success,
           	 	    		    text: telegram_group_banned_chat_member_lang,
           	 	    		    icon: 'success',
           	 	    		    confirmButtonText: global_lang_ok
           	 	    		}).then((result) => {
	                            table.draw();
                       		 });
           	 	    		
           	 	    	}
	                },
	             
	            });
	        }
	    });
	
	});
	$(document).on('click','.unban_member',function(e){
	    e.preventDefault();

	    Swal.fire({
	        title: global_lang_confirm,
	        text: global_lang_unban_confirmation,
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#d33',
	        cancelButtonColor: '',
	        confirmButtonText: global_lang_unban_member,
	        cancelButtonText: global_lang_cancel
	    }).then((result) => {
	        if (result.isConfirmed) {
	      
	        	var id = $(this).data('id');
	            $.ajax({
	                context:this,
	                method: 'post',
	                data: {id:id},
	                url: telegram_group_unban_chat_member,
	                beforeSend: function (xhr) {
	                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
	                },
	                success: function (response) {
           	 	    	if(!response.ok){
           	 	    		Swal.fire({
           	 	    		    title: global_lang_warning,
           	 	    		    text: response.description,
           	 	    		    icon: 'warning',
           	 	    		    confirmButtonText: global_lang_ok
           	 	    		});
           	 	    		return false;
           	 	    	}
           	 	    	else{
           	 	    		Swal.fire({
           	 	    		    title: global_lang_success,
           	 	    		    text: telegram_group_unban_chat_member_lang,
           	 	    		    icon: 'success',
           	 	    		    confirmButtonText: global_lang_ok
           	 	    		}).then((result) => {
	                            table.draw();
                       		 });
           	 	    		
           	 	    	}
	                },
	             
	            });
	        }
	    });
	
	});


	$(document).on('click','#unmute_member',function(e){
		e.preventDefault();
	    $.ajax({
	 	    context: this,
	 	    type:'POST',
	 	    url: telegram_group_unmute_chat_member,
	 	    data:{telegram_group_id:telegram_group_id,telegram_group_subscriber_id:telegram_group_subscriber_id},
	 	    beforeSend: function (xhr) {
	 	        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
	 	    },
	 	    success:function(response){
	 	    	if(!response.ok){
	 	    		Swal.fire({
	 	    		    title: global_lang_warning,
	 	    		    text: response.description,
	 	    		    icon: 'warning',
	 	    		    confirmButtonText: global_lang_ok
	 	    		});
	 	    		return false;
	 	    	}
	 	    	else{
	 	    		Swal.fire({
	 	    		    title: global_lang_success,
	 	    		    text: telegram_group_unmute_chat_member_lang,
	 	    		    icon: 'success',
	 	    		    confirmButtonText: global_lang_ok
	 	    		}).then((result) => {
                           	$('#mute_duration').val('');
                           	$('#mute_time').val('');
                            $("#mute_member_modal").modal('hide');
                            table.draw();
                        });
	 	    		
	 	    	}
	 	    }
	 	});
	});



	$(".group_list_item").click(function(e) {
	    e.preventDefault();
	    var telegram_group_id = $(this).attr('telegram_group_id');
	    $('.group_list_item').removeClass('active');
	    $(this).addClass('active');

	    $.ajax({
	        type:'POST' ,
	        url:telegram_group_manager_url_set_active_bot_session,
	        data:{telegram_group_id},
	        beforeSend: function (xhr) {
	            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
	        },
	        success:function(response){
	            set_tab_menu_id_session('v-pills-group-activity-tab',true);
	        },
	        error: function (xhr, statusText) {
	            const msg = handleAjaxError(xhr, statusText);
	            Swal.fire({icon: 'error',title: global_lang_error,html: msg});
	            return false;
	        }
	    });
	});

	$(document).on('click','#bulk_delete_contact',function(e){
	    e.preventDefault();
	    var ids = [];
	    $("#mytable_wrapper .datatableCheckboxRow:checked").each(function ()
	    {
	        ids.push(parseInt($(this).val()));
	    });
	    var selected = ids.length;

	    if(selected==0)
	    {
	        Swal.fire({
	            title: global_lang_warning,
	            text: telegram_list_subscriber_lang_warning_select_subscriber,
	            icon: 'warning',
	            confirmButtonText: global_lang_ok
	        });
	        return false;
	    }
	    if(selected>upto)
	    {
	        Swal.fire({
	            title: global_lang_warning,
	            text: telegram_list_subscriber_lang_warning_select_subscriber_limit+' '+upto,
	            icon: 'warning',
	            confirmButtonText: global_lang_ok
	        });
	        return false;
	    }

	    Swal.fire({
	        title: global_lang_confirm,
	        text: global_lang_delete_confirmation,
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#d33',
	        cancelButtonColor: '',
	        confirmButtonText: global_lang_delete,
	        cancelButtonText: global_lang_cancel
	    }).then((result) => {
	        if (result.isConfirmed) {
	            var telegram_bot_id=$("#telegram_bot_id").val();

	            if(ids.length==0) return false;

	            $.ajax({
	                context:this,
	                method: 'post',
	                dataType: 'JSON',
	                data: {ids},
	                url: telegram_group_list_subscriber_url_delete_subscriber,
	                beforeSend: function (xhr) {
	                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
	                },
	                success: function (response) {
	                    var swal_lang = global_lang_success;
	                    var swal_icon = 'success';
	                    var swal_text = telegram_list_subscriber_lang_success_delete_subscriber+' ('+ids.length+')';
	                    if(response.status=='0'){
	                        swal_lang = global_lang_warning;
	                        swal_icon = 'error';
	                        swal_text = response.message;
	                    }
	                    Swal.fire({
	                        title: swal_lang,
	                        text: swal_text,
	                        icon: swal_icon,
	                        confirmButtonText: global_lang_ok
	                    }).then((result) => {
	                        table.draw(false);
	                    });
	                },
	                error: function (xhr, statusText) {
	                    const msg = handleAjaxError(xhr, statusText);
	                    Swal.fire({icon: 'error',title: global_lang_error,html: msg});
	                    return false;
	                },
	            });
	        }
	    });

	});

	$('#campaign_data_form').submit(function(event) {
		event.preventDefault(); 
	  
		var formData = $(this).serialize();
		var text_message = $("#text_message").val()
		var campaign_name = $("#campaign_name").val();

		var missing_input = false;
		if (text_message === '' || campaign_name === '') {
		missing_input = true;
		}
		
	    if(missing_input) {
	    	Swal.fire({title: global_lang_warning, text: global_lang_fill_required_fields,icon: 'warning',confirmButtonText: global_lang_ok});
            return false;
	    }
	
		$("#save_campaign_data").attr('disabled',true);

		$.ajax({
		  url: group_message_send,
		  type: 'POST',
		  data: formData,
		  success: function(response) {
			$("#save_campaign_data").removeAttr('disabled');
			if(response.error=='1') Swal.fire({title: global_lang_error, text: response.message,icon: 'error',confirmButtonText: global_lang_ok});
			else {
				Swal.fire({title: global_lang_success, text: response.message,icon: 'success',confirmButtonText: global_lang_ok})
				.then(function () {
				  $("#group_message_send_modal").modal('hide');
				  table2.draw();
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
	  


});