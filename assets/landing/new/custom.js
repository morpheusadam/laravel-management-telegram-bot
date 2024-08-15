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
