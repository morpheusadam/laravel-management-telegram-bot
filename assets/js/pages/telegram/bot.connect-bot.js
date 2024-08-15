"use strict";
$(document).ready(function(){
    $('.delete_bot').on('click', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

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

                $(this).removeClass('btn-outline-danger');
                $(this).addClass('btn-danger btn-progress');
                $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id},
                    url: telegram_connect_bot_url_delete,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    },
                    success: function (response) {
                        $(this).removeClass('btn-danger');
                        $(this).removeClass('btn-progress');
                        $(this).addClass('btn-outline-danger');

                        if (false === response.error) {
                            toastr.success(response.message, global_lang_success,{'positionClass':'toast-bottom-right'});
                            $(this).parent().parent().parent().hide();
                        }
                        if (true === response.error) Swal.fire({icon: 'error',title: global_lang_error,text: response.message});
                        return false;
                    },
                    error: function (xhr, statusText) {
                        $(this).removeClass('btn-danger');
                        $(this).removeClass('btn-progress');
                        $(this).addClass('btn-outline-danger');

                        const msg = handleAjaxError(xhr, statusText);
                        Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                        return false;
                    },
                });
            }
        });

    });

    $('.sync_bot').on('click', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        
        $(this).addClass('btn-progress');
        $.ajax({
            context:this,
            method: 'post',
            dataType: 'JSON',
            data: {id},
            url: telegram_connect_bot_url_sync,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success: function (response) {
                $(this).removeClass('btn-progress');
                if (false === response.error) {
                    toastr.success(response.message, global_lang_success,{'positionClass':'toast-bottom-right'});
                }
                if (true === response.error) Swal.fire({icon: 'error',title: global_lang_error,text: response.message});
                return false;
            },
            error: function (xhr, statusText) {
                $(this).removeClass('btn-progress');

                const msg = handleAjaxError(xhr, statusText);
                Swal.fire({icon: 'error',title: global_lang_error,html: msg});
                return false;
            },
        });
         

    });
});
