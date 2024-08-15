"use strict";

function getImageHtml(response) {
    let html = '<div class="col-12 px-0">';
    html += '<div class="media-delete-wrapper">';
    html += '<div class="media-delete-icon" data-image="' + response.fileInfo.filename + '" data-media-id="' + response.fileInfo.id + '">';
    html += '<i class="fa fa-times"></i>';
    html += '</div>';
    html += '<input class="d-none" name="media" type="text" value="' + response.fileInfo.filename +'">';
    html += '<img class="img-thumbnail" src="' + assetUploadUrl + response.fileInfo.filename +'" alt="">';
    html += '</div>';
    html += '</div>';
    return html;
}

function getAudioHtml(response) {
    let html = '<div class="col-12 px-0">';
    html += '<div class="media-delete-wrapper">';
    html += '<div class="media-delete-icon media-delete-icon--audio" data-image="' + response.fileInfo.filename + '" data-media-id="' + response.fileInfo.id + '">';
    html += '<i class="fa fa-times"></i>';
    html += '</div>';
    html += '<input class="d-none" name="media" type="text" value="' + response.fileInfo.filename +'">';
    html += '<audio controls>';
    html += '<source src="' + assetUploadUrl + response.fileInfo.filename + '" type="' + response.fileInfo.mime_type + '">Your browser does not support the video tag.';
    html += '</audio>';
    html += '</div>';
    html += '</div>';
    return html;
}

function getVideoHtml(response) {
    let html = '<div class="col-12 px-0">';
    html += '<div class="media-delete-wrapper">';
    html += '<div class="media-delete-icon" data-image="' + response.fileInfo.filename + '" data-media-id="' + response.fileInfo.id + '">';
    html += '<i class="fa fa-times"></i>';
    html += '</div>';
    html += '<input class="d-none" name="media" type="text" value="' + response.fileInfo.filename +'">';
    html += '<video width="100%" height="auto" controls>';
    html += '<source src="' + assetUploadUrl + response.fileInfo.filename + '" type="' + response.fileInfo.mime_type + '">Your browser does not support the video tag.';
    html += '</video>';
    html += '</div>';
    html += '</div>';
    return html;
}

function getFileHtml(response) {
    let html = '<div class="col-12 px-0">';
    html += '<div class="media-delete-wrapper">';
    html += '<div class="media-delete-icon" data-image="' + response.fileInfo.filename + '" data-media-id="' + response.fileInfo.id + '">';
    html += '<i class="fa fa-times"></i>';
    html += '</div>';
    html += '<input class="d-none" name="media" type="text" value="' + response.fileInfo.filename +'">';
    html += '<span class="badge badge-light text-truncate">' + response.fileInfo.filename +'</span>';
    html += '</div>';
    html += '</div>';
    return html;
}