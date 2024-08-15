"use strict";

function handleAjaxError(xhr) {
    let msg = '';

    if (xhr.status === 0) {
        msg = 'Verify internet connection.';
    } else if (xhr.status === 404) {
        msg = 'Page not found.'
    } else if (xhr.status === 422) {
        msg = handleLaravelResponse422(xhr.responseJSON);
    } else if (xhr.status === 413) {
        msg = 'The file is too large.';
    } else if (xhr.status === 500) {
        msg = 'Internal server error.';
    } else if (xhr.statusText === 'parsererror') {
        msg = 'JSON parse failed.';
    } else if (xhr.statusText === 'timeout') {
        msg = 'Time out error.';
    } else if (xhr.statusText === 'abort') {
        msg = 'Ajax request aborted.';
    } else {
        msg = 'Uncaught Error: ' + xhr.responseText;
    }

    return msg;
}

function handleLaravelResponse422(response) {
    let html = '';
    if (response.errors) {
        const errors = Object.values(response.errors);
        if (errors.length > 0) {
            html += '<p class="m-1">' + errors[0] + '</p>';
        }
    }

    return html;
}

function sweetAlertCommonParams(data) {
    let customClass = {
        actions: 'd-flex justify-content-between',
        confirmButton: 'btn btn-primary',
        denyButton: 'btn btn-danger',
        cancelButton: 'btn btn-secondary',
        closeButton: '',
        footer:  '',
    };

    if (data.customClass) {
        customClass = Object.assign(customClass, data.customClass);
    }

    return {
        width: data.width || '35rem',
        focusConfirm: false,
        buttonsStyling: false,
        allowOutsideClick: false,
        showConfirmButton: data.showConfirmButton || false,
        showCancelButton: data.showCancelButton || false,
        showCloseButton: data.showCloseButton || false,
        closeButtonHtml: '&times;',
        customClass,
        showClass: {
            popup: false,
        }
    }
}

/*https://codeseven.github.io/toastr/demo.html*/
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
