"use strict";

var perscroll;
var table;
var hideCols = [13];
if(is_admin=='0') hideCols = [7,13];

$(document).ready(function() {

    table = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 13, "desc" ]],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100,200,500], [10, 25, 50, 100,200,500]],
        buttons:
        [
            {
                extend: 'csv',
                text: global_lang_download,
                exportOptions: {
                    columns: [ 0,3,4,5,7,9,10,11,12]
                }
            }
         ],
        ajax:
            {
                "url": subscription_list_user_url_data,
                "type": 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                },
                data: function ( d )
                {
                    d.search_value = $('#search_value').val();
                    d.search_package_id = $('#search_package_id').val();
                    d.search_user_type = $('#search_user_type').val();
                }
            },
        language:
            {
                url: global_url_datatable_language
            },
        dom: '<"top"Bf>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
                targets: hideCols,
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,1,2,8,12],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
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

    $(document).on('change', '#search_package_id,#search_user_type', function(e) {
        table.draw(false);
    });

    $(document).on('keyup', '#search_value', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table.draw(false);
    });

    $(document).on('click', '#download_data', function(e) {
        e.preventDefault();
        $(".buttons-csv").click();
    });

    $(document).on('click', '#send_email_ui', function(e) {
        e.preventDefault();
        var user_ids = [];
        $(".datatableCheckboxRow:checked").each(function ()
        {
            user_ids.push(parseInt($(this).val()));
        });

        if(user_ids.length==0)
        {
            Swal.fire({
                title: global_lang_warning,
                text: subscription_list_user_lang_warning_select_user,
                icon: 'warning',
                confirmButtonText: global_lang_ok
            });
            return false;
        }
        else  $("#modal_send_sms_email").modal('show');
    });


    $(document).on('click', '#send_sms_email', function(e) {

        var subject = $("#subject").val();
        var message = $("#message").val();

        var user_ids = [];
        $(".datatableCheckboxRow:checked").each(function ()
        {
            user_ids.push(parseInt($(this).val()));
        });

        if(user_ids.length==0)
        {
            Swal.fire({
                title: global_lang_warning,
                text: subscription_list_user_lang_warning_select_user,
                icon: 'error',
                confirmButtonText: global_lang_ok
            });
            return false;
        }

        if(subject=='')
        {
            $("#subject").addClass('is-invalid');
            return false;
        }
        else
        {
            $("#subject").removeClass('is-invalid');
        }

        if(message=='')
        {
            $("#message").addClass('is-invalid');
            return false;
        }
        else
        {
            $("#message").removeClass('is-invalid');
        }

        $(this).addClass('btn-progress');
        $("#show_message").html('');
        $.ajax({
            context: this,
            type:'POST' ,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            url: subscription_list_user_url_send_email,
            data:{message:message,user_ids:user_ids,subject:subject},
            success:function(response){
                $(this).removeClass('btn-progress');
                $("#show_message").addClass("alert alert-primary text-center");
                $("#show_message").html(response);
                $("#datatableSelectAllRows").prop('checked',false);
            }
        });

    });
});
