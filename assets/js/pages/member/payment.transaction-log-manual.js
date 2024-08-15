"use strict";

var perscroll;
var table;
var drop_menu = '<a href="javascript:;" id="date_range_picker" class="btn btn-outline-primary float-end icon-left btn-icon"><i class="far fa-calendar"></i> '+global_lang_choose_data+'</a><input type="hidden" id="date_range_val">'
$(document).ready(function() {

    setTimeout(function(){
        $("#mytable_filter").append(drop_menu);
        $('#date_range_picker').daterangepicker({
            format: "YYYY/MM/DD",
            separator: "-",
            startDate: moment().subtract(29, 'days'),
            endDate  : moment(),
            "applyLabel": global_lang_apply,
            "cancelLabel": global_lang_cancel,
            "fromLabel": global_lang_from,
            "toLabel": global_lang_to,
            "customRangeLabel": global_lang_custom
        }, function (start, end) {
            $('#date_range_val').val(start.format('YYYY/MM/DD') + '-' + end.format('YYYY/MM/DD')).change();
        });
    }, 1000);


    table = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 5, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": member_transaction_log_manual_url_data,
                "type": 'POST',
                data: function ( d )
                {
                    d.date_range = $('#date_range_val').val();
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
                targets: [3],
                className: 'text-end'
            },
            {
                targets: [0,4,5,6,7],
                className: 'text-center'
            },
            {
                targets: [4,6,8],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
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
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('change', '#date_range_val', function(event) {
        event.preventDefault();
        table.draw();
    });


    // Downloads file
    $(document).on('click', '#mp-download-file', function(e) {
      e.preventDefault();

      // Makes reference 
      var that = this;

      // Starts spinner
      $(that).removeClass('btn-outline-info');
      $(that).addClass('btn-info disabled btn-progress');

      // Grabs ID
      var file = $(this).data('id');

      // Requests for file
      $.ajax({
        type: 'POST',
        data: { file },
        dataType: 'JSON',
        url: manual_payment_download_file_route,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success: function(res) {
          // Stops spinner
          $(that).removeClass('btn-info disabled btn-progress');
          $(that).addClass('btn-outline-info');

          // Shows error if something goes wrong
          if (res.error) {
            Swal.fire({
              icon: 'error',
              text: res.error,
              title: global_lang_error,
            });
            return;
          }

          // If everything goes well, requests for downloading the file
          if (res.status && 'ok' === res.status) {
            window.location = manual_payment_download_file_route;
          }
        },
        error: function(xhr, status, error) {
          // Stops spinner
          $(that).removeClass('btn-info disabled btn-progress');
          $(that).addClass('btn-outline-info');

          // Shows internal errors
          Swal.fire({
            icon: 'error',
            text: error,
            title: global_lang_error,
          });
        }
      });
    });

    if(is_admin || is_agent) {
        // Approve manual transaction
        $(document).on('click', '#mp-approve-btn, #mp-reject-btn', function(e) {
          e.preventDefault();

          // Makes reference
          var that = this;

          // Gets transaction ID
          var id = $(that).data('id');
          var action_type = $(that).attr('id');

          if ('mp-reject-btn' === action_type) {
            var reject_modal = $('#manual-payment-reject-modal');

            // Sets values to rejection form's hidden fields
            $('#mp-transaction-id').val(id);
            $('#mp-action-type').val(action_type);

            // Opens up rejection modal
            $(reject_modal).modal('show');
            return;
          }

          // Gets classes
          var prev_btn_el = $(that).parent().prev(); 
          var el_classes = prev_btn_el ? prev_btn_el[0].className : '';
          var new_classes = el_classes ? el_classes.replace('-outline', '') : '';

          // Shows spinner
          $(prev_btn_el).removeClass();
          $(prev_btn_el).addClass(new_classes.concat(' disabled btn-progress'));

          $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: { id, action_type },
            url: manual_payment_handle_action_route,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function(res) {

              // Stops spinner
              $(prev_btn_el).removeClass();
              $(prev_btn_el).addClass(el_classes);

              // Shows error if something goes wrong
              if (res.error) {
                Swal.fire({
                  icon: 'error',
                  text: res.error,
                  title: global_lang_error,
                });
                return;
              }
              // If everything goes well, requests for downloading the file
              if (res.status && 'ok' === res.status) {
                // Shows success message
                Swal.fire({
                  icon: 'success',
                  text: res.message,
                  title: global_lang_success,
                }).then((value) => {
                    table.draw();
                });

                // Reloads datatable
              }
            },
            error: function(xhr, status, error) {
              // Stops spinner
              $(prev_btn_el).removeClass();
              $(prev_btn_el).addClass(el_classes);

              // Shows error if something goes wrong
              Swal.fire({
                icon: 'error',
                text: xhr.responseText,
                title: global_lang_error,
              });            
            }
          });
        });

        // Handles payment's approval
        $(document).on('click', '#manual-payment-reject-submit', function(e) {
          e.preventDefault();

          // Makes reference
          var that = this;

          // Starts spinner
          $(that).addClass('btn-progress disabled');

          // Gets some vars
          var id = $('#mp-transaction-id').val();
          var action_type = $('#mp-action-type').val();
          var rejected_reason = $('#rejected-reason').val();

          $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: { id, action_type, rejected_reason },
            url: manual_payment_handle_action_route,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function(res) {
              // Stops spinner
              $(that).removeClass('btn-progress disabled');

              // Shows error if something goes wrong
              if (res.error) {
                Swal.fire({
                  icon: 'error',
                  text: res.error,
                  title: global_lang_error,
                });
                return;
              }
              // If everything goes well, requests for downloading the file
              if (res.status && 'ok' === res.status) {
                // Shows success message
                Swal.fire({
                  icon: 'success',
                  text: res.message,
                  title: global_lang_success,
                });

                // Clears rejection msg
                $('#rejected-reason').val('');

                // Closes modal
                $('#manual-payment-reject-modal').modal('toggle');

                // Reloads datatable
                table.ajax.reload();
              }
            },
            error: function(xhr, status, error) {
              // Stops spinner
              $(that).removeClass('btn-progress disabled');

              // Shows error if something goes wrong
              Swal.fire({
                icon: 'error',
                text: xhr.responseText,
                title: global_lang_error,
              });            
            }
          });
        });
    }

});
