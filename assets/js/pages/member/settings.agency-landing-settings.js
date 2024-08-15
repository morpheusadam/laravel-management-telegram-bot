"use strict";

$(document).on('click', '.upload-file', function(e) {
    var item_name = $(this).parent().next().attr('name');

    $('#agency-dropzone .dz-preview').remove();
    $('#agency-dropzone').removeClass('dz-started dz-max-files-reached');
    Dropzone.forElement('#agency-dropzone').removeAllFiles(true);

    $("#upload_modal #current-item-name").val(item_name);
    $("#upload_modal").modal('show');
});

Dropzone.autoDiscover = false;
$("#agency-dropzone").dropzone({
    url: upload_url,
    maxFilesize:2,
    uploadMultiple:false,
    paramName:"file",
    createImageThumbnails:true,
    acceptedFiles: ".png,.jpg,.jpeg,.webp",
    maxFiles:1,
    addRemoveLinks:false,
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    success:function(file, response) {
        if (response.error) {
            Swal.fire({
                icon: 'error',
                text: response.error,
                title: global_lang_error
            });
            return;
        }
        if (response.filename) {
            var item_name = $('#current-item-name').val();
            var elem = "input[name="+item_name+"]";
            $(elem).val(response.filename);
            $("#upload_modal").modal('hide');
        }
    }
});