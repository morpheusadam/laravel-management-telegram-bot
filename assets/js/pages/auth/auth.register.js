"use strict";
$('#register-form').on('submit', function (event) {
    event.preventDefault();
    const submitButtonElement = $('#form-submit-button');
    submitButtonElement.addClass('disabled btn-progress');
    $.ajax({
        method: 'post',
        dataType: 'JSON',
        data: $(this).serialize(),
        url: url_register,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success: function (response) {
            submitButtonElement.removeClass('disabled btn-progress');
            if (false === response.error) {
                Swal.fire({
                    title: success,
                    html: response.message,
                    icon: 'success',
                }).then(result => {
                    if (result.isConfirmed) {
                        if (result.isConfirmed) {
                            window.location.replace(url_login);
                        }
                    }
                });
            }
            if (true === response.error) {
                Swal.fire({
                    title: warning,
                    html: response.message,
                    icon: 'warning'
                });
            }
            return false;
        },
        error: function (xhr, statusText) {
            submitButtonElement.removeClass('disabled btn-progress');
            const msg = handleAjaxError(xhr, statusText);
            Swal.fire({
                title: error,
                html: msg,
                icon: 'warning'
            });
            return false;
        },
    });
});
