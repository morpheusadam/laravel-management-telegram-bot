"use strict";
var postbackPayload = [];
var botTemplatePayload = [];
var livechatDropzone;

function openTab(url) {
   var win = window.open(url, '_blank');
   win.focus();
}

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('#postback_reply_button').tooltip();
});

var headerType = null;
var buttonType = null;

function initiateTemplateLoad(){
    const whatsapp_bot_id_with_business_id = $('#api_send_whatsapp_bot_id').val();

     if(whatsapp_bot_id_with_business_id==''){
         Swal.fire({icon: 'error', title: global_lang_error, html: api_send_lang_warning_select_account});
         e.preventDefault();
         return false;
    }

    const whatsapp_id_bussiness = whatsapp_bot_id_with_business_id.split("-"); 
    const whatsapp_bot_id = whatsapp_id_bussiness[0];
    const whatsapp_business_id = whatsapp_id_bussiness[1];
    const phone_number_id = whatsapp_id_bussiness[2];
   

    if (whatsapp_business_id){
        fetchPostbackPayload(whatsapp_bot_id, function (response) {
            $('#bot-postback-list').html('').html(
                getBotPostbackHtmlList(response)
            );
        });

        fetchBotTemplatePayload(whatsapp_business_id,function(response) {
            $('#bot-template-list').html('').html(
                getBotTemplateHtmlList(response)
            );
        })
    }
};

$(document).ready(function() {
    livechatDropzone = uploadFileViaDropzone({
        csrf_token,
        file_upload_url: flow_builder_upload_media,
        file_delete_url: flow_builder_delete_media,
        elementForInsertingUrlOntoInput: $('#bot-template-header-media-url-input'),
    });

    setTimeout(function(){ initiateTemplateLoad() }, 500);

    
    $('#api_send_whatsapp_bot_id').on('change', function (event) {
         initiateTemplateLoad();
    });

    $('#postbackModal').on('show.bs.modal', function (event) {
         $('#pills-bot-flow-tab').tab('show');
    });

    $('#template-modal').on('show.bs.modal', function (event) {
        const templateId = $(event.relatedTarget).data('id')
        const templateName = $(event.relatedTarget).data('template-name')
        headerType = $(event.relatedTarget).data('header-type');
        buttonType = $(event.relatedTarget).data('button-type');

        $('#template-modal-label').find('span').text(templateName)
        $('#send-message-template-action').attr('data-id',templateId)

        const buttonList = getButtonList(templateId);
        const dynamicVariableList = getDynamicVariableList(templateId);

        var buttonType = '';
        if (buttonList.length > 0 && "0" in buttonList && "type" in buttonList[0]){
            var buttonType = buttonList[0]['type'];
        }

        if (buttonType!='quick_reply' && false === dynamicVariableList.some(item => item.startsWith('#!')) && headerType != 'media') {
            $(event.relatedTarget).addClass('bg-light btn-progress');
            const whatsapp_bot_template_id = $('#send-message-template-action').attr('data-id');
            var whatsapp_bot_id_with_business_id = $('#api_send_whatsapp_bot_id').val();
            var whatsapp_id_bussiness = whatsapp_bot_id_with_business_id.split("-"); 
            var whatsapp_bot_id = whatsapp_id_bussiness[0];
            var whatsapp_business_id = whatsapp_id_bussiness[1];
            var phone_number_id = whatsapp_id_bussiness[2];
            const chat_id = $('#api_send_chat_id').val();

            $.ajax({
                url: api_send_whatsapp_url,
                method: 'POST',
                dataType: 'JSON',
                data: {
                    whatsapp_bot_template_id: whatsapp_bot_template_id,
                    whatsapp_bot_id: whatsapp_bot_id,
                    phone_number_id: phone_number_id,
                    chat_id: chat_id,
                    botTemplateHeaderType: headerType,
                    botTemplateButtonType: buttonType,
                    botTemplateHeaderMediaUrl: null,
                    botTemplateQuickreplyButtonValues: [],
                    botTemplateDynamicVariableValues: [],
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                },
                success: function (response) {
                    $("#template_sample_response").removeClass('d-none');
                    $(event.relatedTarget).removeClass('bg-light btn-progress');
                    if (response.status == '1') {
                        var textarea = '<div style="text-align:left !important">';
                        textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_api_end_point+' </label><div class="form-group"><input class="form-control" value="'+response.endpoint+'"></div>';
                        textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_example+' </label><div class="form-group"><textarea class="form-control" name="" id="" rows="5">'+response.example+'</textarea></div>';
                        textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_example_post+' </label><div class="form-group"><textarea style="white-space: pre; overflow-wrap: normal; overflow: hidden; outline: none;" class="form-control" name="" id="" rows="7">'+response.post_example+'</textarea></div>';
                        textarea = textarea+'</div>';
                        Swal.fire({icon: 'success', title: response.message, html: textarea})
                            .then((result) => {
                                if (result.isConfirmed) {
                                    $("#template-modal").modal('hide');
                                }
                            });
                    } else {
                        Swal.fire({icon: 'error', title: global_lang_error, html: response.message});
                    }
                },
            });

            return false;
        }

        manageButtonFieldsWithPostbackList(buttonList);

        manageDynamicVariableFieldsWithValues(dynamicVariableList);

        const botTemplateQuickreplyButtonElement = $('#bot-template-quickreply-button-wrapper')
        const botTemplateHeaderMediaElement = $('#bot-template-header-media-wrapper')

        if ('quick_reply' === buttonType) {
            botTemplateQuickreplyButtonElement.removeClass('d-none').addClass('d-block')
        } else {
            botTemplateQuickreplyButtonElement.removeClass('d-block').addClass('d-none')
        }

        if ('media' === headerType) {
            const headerSubtype = $(event.relatedTarget).data('header-subtype')
            if (mimeTypesMap[headerSubtype]) {
                const acceptedFiles = mimeTypesMap[headerSubtype].join(',')
                $(livechatDropzone.element).find('[type="file"]').attr('accept', acceptedFiles)
                livechatDropzone.options.acceptedFiles = acceptedFiles
            } else {
                $(livechatDropzone.element).find('[type="file"]').attr('accept', 'unknown')
                livechatDropzone.options.acceptedFiles = 'unknown'
            }

            botTemplateHeaderMediaElement.removeClass('d-none').addClass('d-block')
        } else {
            botTemplateHeaderMediaElement.removeClass('d-block').addClass('d-none')
            $(livechatDropzone.element).find('[type="file"]').attr('accept', 'unknown')
            livechatDropzone.options.acceptedFiles = 'unknown'
        }
    });

    submitForm({ headerType, buttonType });

    $("#api_send_text_message_submit").on('click',function(e){
        var chat_id = $("#api_send_chat_id").val();
        var message_text = $("#api_send_plain_message_text").val();
        var message_type = "template";
        const whatsapp_bot_id_with_business_id = $('#api_send_whatsapp_bot_id').val();
        const whatsapp_id_bussiness = whatsapp_bot_id_with_business_id.split("-"); 
        const whatsapp_bot_id = whatsapp_id_bussiness[0];
        const whatsapp_business_id = whatsapp_id_bussiness[1];
        const phone_number_id = whatsapp_id_bussiness[2];

        if(whatsapp_bot_id==''){
             Swal.fire({icon: 'error', title: global_lang_error, html: api_send_lang_warning_select_account});
             e.preventDefault();
             return false;
        }
               
        if(message_text==''){
             Swal.fire({icon: 'error', title: global_lang_error, html: api_send_lang_warning_message_required});
             e.preventDefault();
             return false;
        }


        $.ajax({
            url: api_send_whatsapp_url,
            method: 'POST',
            dataType: 'JSON',
            data: {
                whatsapp_bot_id,
                phone_number_id,
                chat_id,
                message_type,
                message_text
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response) {
                if(response.status=='1'){
                    var textarea = '<div style="text-align:left !important">';
                    textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_api_end_point+' </label><div class="form-group"><textarea class="form-control" name="" id="" rows="3">'+response.endpoint+'</textarea></div>';
                    textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_example+' </label><div class="form-group"><textarea class="form-control" name="" id="" rows="4">'+response.example+'</textarea></div>';
                    textarea = textarea+'</div>';
                    Swal.fire({icon: 'success',title: response.message,html: textarea});
                }
                else{
                    Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                }                

            }
        });
    });
});




function fetchBotTemplatePayload(whatsapp_business_id,callback) {
    $.ajax({
        url: whatsapp_livechat_get_template_list_url.replace(':whatsapp_business_id', whatsapp_business_id),
        type: 'GET',
        dataType: 'JSON',
        async: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response) {
            botTemplatePayload = response;
            callback(response);
        }
    });
}


function fetchPostbackPayload(whatsapp_bot_id, callback) {
    $.ajax({
        url: whatsapp_livechat_get_postback_list_json_url.replace(':whatsapp_bot_id', whatsapp_bot_id),
        type: 'GET',
        dataType: 'JSON',
        async: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        },
        success:function(response) {
            postbackPayload = response;
            callback(response);
        }
    });
}

function getBotTemplateHtmlList(templates) {
    if ( !Array.isArray(templates)) return '<div id="error" class="bg-light"><div class="container text-center pt-5"><h1 class="error-title text-secondary fw-normal mb-0 pt-5"><i class="far fa-frown"></i></h1><p class="text-muted error-text pt-2">'+api_send_lang_warning_no_template_found+'</p></div></div>';

    let html = '<div class="list-group">';

    for (const template of templates) {
        html += '<a href="#" data-bs-toggle="modal" data-bs-target="#template-modal" data-id="' + template.id + '" data-template-name="' + template.template_name + '" data-header-type="' + template.header_type + '" data-header-subtype="' + template.header_subtype + '" data-button-type="' + template.button_type + '" class="list-group-item flex-column align-items-start bot-template-list-item item-searchable">';
            html += '<div class="d-flex w-100 justify-content-between mt-1">';
                html += '<h6 class="mb-1"><i class="fas fa-check-circle text-success"></i> ' + template.template_name + '</h6>';
            html += '</div>';
        html += '</a>';
    }

    html +=' </div>';

    return html;
}


function getBotPostbackHtmlList(postbacks) {
    if (! Array.isArray(postbacks)) return;

    let html = '<div class="list-group">';
    for (const postback of postbacks) {
        html += '<a href="#" data-id="' + postback.postback_id + '"  class="list-group-item flex-column align-items-start postback-item item-searchable">';
            html += '<div class="d-flex w-100 justify-content-between mt-1">';
                html += '<h6 class="mb-1"><i class="fas fa-check-circle text-success"></i> ' + postback.template_name + '</h6>';
            html += '</div>';
        html += '</a>';
    }

    html +=' </div>';

    return html;
}

const getButtonList = (templateId) => {
    const template = botTemplatePayload.find(item => parseInt(item.id, 10) === parseInt(templateId, 10))
    if(template && template.button_content) {
        const content = JSON.parse(template.button_content || '{}')
        if (content && 'buttons' === content.type && Array.isArray(content.buttons)) {
            return content.buttons
        }
    }

    return []
}

const manageButtonFieldsWithPostbackList = async (buttons) => {
    const botTemplateQuickreplyButtonElement = $('#bot-template-quickreply-button-wrapper')
    botTemplateQuickreplyButtonElement.html('')
    let index = 0

    for (const button of buttons) {
        const randomInt = getRandomInt(1000, 9999)
        const html = '' +
            '<div class="form-group pb-3">' +
            '<label class="pb-1 control-label">' + button.text + '</label>' +
            '<select id="bot-template-quickreply-buttons-' + randomInt + '" class="form-control select2 bot-template-quickreply-button">' + preparePostbackSelectOptions(postbackPayload) + '</select>' +
            '<script type="text/javascript">$("#bot-template-quickreply-buttons-' + randomInt + '").select2({width: "100%"})</script>' +
            '</div>'

        botTemplateQuickreplyButtonElement.append(html)

        index++
    }
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);

    return Math.floor(Math.random() * (max - min) + min);
}

const getDynamicVariableList = (templateId) => {
    const content = botTemplatePayload.find(item => parseInt(item.id, 10) === parseInt(templateId, 10))

    if(content && content.variable_map) {
        const parsedContent = JSON.parse(content.variable_map || '{}')
        if (parsedContent && parsedContent.body && isPlainObject(parsedContent.body)) {
            return Object.values(parsedContent.body)
        }
    }

    return []
}

function isPlainObject(obj) {
    return obj ? obj.constructor === {}.constructor : false
}

const manageDynamicVariableFieldsWithValues = async (variables) => {
    const botTemplateDynamicVariableElement = $('#bot-template-dynamic-variable-wrapper')
    botTemplateDynamicVariableElement.html('')
    let position = 0

    for (const variable of variables) {
        position++

        if (! variable.startsWith('#!') && ! variable.endsWith('!#')) {
            continue
        }

        const html = '' +
            '<div class="form-group pb-3">' +
            '<label class="pb-1 control-label">' + variable.replace(/[#|!]/g, '') + '</label>' +
            '<input type="text" class="form-control bot-template-dynamic-variable" data-position="' + position + '">' +
            '</div>'

        botTemplateDynamicVariableElement.append(html)
    }
}

function submitForm(data, callback) {
    $('.template-modal-form').submit(function(event) {
        event.preventDefault();

        const buttonElement = $(event.target).find('[type="submit"]')[0];
        buttonElement.classList.add('btn-progress')

        const botTemplateQuickreplyButtonElement = $('.bot-template-quickreply-button')
        const botTemplateQuickreplyButtonInputLabels = []
        const botTemplateQuickreplyButtonValues = botTemplateQuickreplyButtonElement.map((index, element) => { // eslint-disable-line
            if (new Boolean(element.value).valueOf() === false) {
                botTemplateQuickreplyButtonInputLabels.push(cleanXss($(element).prev().text()))
            }
            return element.value
        }).get()

        const botTemplateDynamicVariableElement = $('.bot-template-dynamic-variable')
        const botTemplateDynamicVariableValues = botTemplateDynamicVariableElement.map((index, element) => { // eslint-disable-line
            const label = cleanXss($(element).prev().text())
            const position = $(element).data('position')
            return { label, value: element.value, position }
        }).get()

        const botTemplateHeaderMediaUrl = $('#bot-template-header-media-url-input').val() || null

        let hasError = false;
        if (botTemplateQuickreplyButtonElement.length !== botTemplateQuickreplyButtonValues.filter(Boolean).length) {
            hasError = true;
        }

        const emptyDynamicVariableFields = botTemplateDynamicVariableValues
            .filter(item => new Boolean(item.value).valueOf() === false)

        if ((botTemplateDynamicVariableElement.length ===
            botTemplateDynamicVariableValues.length) &&
            emptyDynamicVariableFields.length > 0
        ) {
            hasError = true;
        }

        if ('media' === data.headerType) {
            if (! botTemplateHeaderMediaUrl) {
                hasError = true;
            }
        }

        if (hasError) {
            buttonElement.classList.remove('btn-progress')
            $('.error-message').text(global_all_fields_are_required)
            return;
        }

        var whatsapp_bot_template_id = $('#send-message-template-action').attr('data-id');
        var whatsapp_bot_id_with_business_id = $('#api_send_whatsapp_bot_id').val();
        var whatsapp_id_bussiness = whatsapp_bot_id_with_business_id.split("-"); 
        var whatsapp_bot_id = whatsapp_id_bussiness[0];
        var whatsapp_business_id = whatsapp_id_bussiness[1];
        var phone_number_id = whatsapp_id_bussiness[2];
        var chat_id = $('#api_send_chat_id').val();
        var message_type ="template";

        $.ajax({
            url: api_send_whatsapp_url,
            method: 'POST',
            dataType: 'JSON',
            data: {
                whatsapp_bot_template_id: whatsapp_bot_template_id,
                whatsapp_bot_id: whatsapp_bot_id,
                phone_number_id: phone_number_id,
                chat_id: chat_id,
                message_type,
                botTemplateHeaderMediaUrl,
                botTemplateQuickreplyButtonValues,
                botTemplateDynamicVariableValues,
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            },
            success:function(response) {
                $("#template_sample_response").removeClass('d-none');
                buttonElement.classList.remove('btn-progress');
                $('#postback_reply_button').html('<i class="fas fa-robot"></i>');
                if(response.status=='1'){
                   var textarea = '<div style="text-align:left !important">';
                    textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_api_end_point+' </label><div class="form-group"><input class="form-control" value="'+response.endpoint+'"></div>';
                    textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_example+' </label><div class="form-group"><textarea class="form-control" name="" id="" rows="5">'+response.example+'</textarea></div>';
                    textarea = textarea+'<label for="" class="mt-4 mb-2">'+api_send_lang_example_post+' </label><div class="form-group"><textarea style="white-space: pre; overflow-wrap: normal; overflow: hidden; outline: none;" class="form-control" name="" id="" rows="7">'+response.post_example+'</textarea></div>';
                    textarea = textarea+'</div>';
                    Swal.fire({icon: 'success',title: response.message,html: textarea})
                    .then((result) => {
                        if (result.isConfirmed) {
                            $("#template-modal").modal('hide');
                            $("#postbackModal").modal('hide');
                            $('#refresh_data').click();
                        }
                    });
                }
                else{
                    Swal.fire({icon: 'error',title: global_lang_error,html: response.message});
                }
            }
        });
    });
}

function preparePostbackSelectOptions (optionsArray, defaultValue = '') {
    let html = ''

    for (const option of optionsArray) {
        if ('object' === typeof option) {
            if (defaultValue === option.key) {
                html += '<option value="' + option.postback_id + '" selected>' + option.template_name + '</option>'
            } else {
                html += '<option value="' + option.postback_id + '">' + option.template_name + '</option>'
            }
        }
    }

    return html
}


const uploadFileViaDropzone = (data) => {
    const config = {
        elementId: data.elementId || '#file-upload-dropzone',
        elementForInsertingUrlOntoInput: data.elementForInsertingUrlOntoInput,
        maxFiles: data.maxFiles || 1,
        maxFilesize: data.maxFilesize || 20,
        acceptedFiles: data.acceptedFiles || ".png, .jpg, .jpeg, .webp, .JPEG, .JPG, .PNG, .WEBP, .aac, .amr, .mp3, .AAC, .AMR, .MP3, .mp4, .MP4, .3gpp, .3GPP, .doc, .docx, .pdf, .txt, .ppt, .pptx, .xls, .xlsx, .DOC, .DOCX, .PDF, .TXT, .PPT, .PPTX, .XLS, .XLSX",
        uploadMultiple: data.uploadMultiple || false,
        fileUploadUrl: data.file_upload_url,
        fileDeleteUrl: data.file_delete_url,
        csrfToken: data.csrf_token
    }

    let serverGeneratedFilename = ''
    window.flowBuilderUploadedFileData = {}

    const dropzone = new Dropzone(config.elementId, {
        url: config.fileUploadUrl,
        maxFilesize: config.maxFilesize,
        uploadMultiple: config.uploadMultiple,
        paramName: "media_file",
        createImageThumbnails: true,
        acceptedFiles: config.acceptedFiles,
        maxFiles: config.maxFiles,
        addRemoveLinks: true,
        headers:{
            'X-CSRF-TOKEN': config.csrfToken
        },

        // eslint-disable-next-line
        success: async function(file, response) {
            // Display message if error
            if (false === response.status) {
                await Swal.fire({
                    icon: 'error',
                    text: response.message,
                    title: 'Error!',
                });

                return
            }

            if (response.status) {
                serverGeneratedFilename = response.file
                window.flowBuilderUploadedFileData = {
                    mime_type: response.file_type,
                    file: response.file,
                }

                config.elementForInsertingUrlOntoInput
                && config.elementForInsertingUrlOntoInput.val(response.file)
            }
        },

        // eslint-disable-next-line
        removedfile: function(file) {
            const fileData = window.flowBuilderUploadedFileData
            if (fileData.file && (fileData.file === serverGeneratedFilename)) {
                // eslint-disable-next-line
                const result = deleteUploadedFile(serverGeneratedFilename)
                    .then(response => {
                        if (response.status) {
                            window.flowBuilderUploadedFileData = {}
                            config.elementForInsertingUrlOntoInput
                            && config.elementForInsertingUrlOntoInput.val('')
                            $(config.elementId + ' .dz-preview').remove()
                            Dropzone.forElement(config.elementId).removeAllFiles(true)
                        }
                    }).catch(error => {
                        if (error.status !== '200' && error.statusText) {
                            const message = error.status + ' ' + error.statusText
                            alert(message)
                        } else {
                            console.log(error)
                        }
                    })
            }
        },

        // eslint-disable-next-line
        error: function(file, message, xhr) {
            file.previewElement.remove()
            $('.dropzone.dz-started .dz-message').show()
            toastr.warning(message, global_lang_warning, {'positionClass':'toast-bottom-right'});
        },
    })

    // Removes previous files if there is any
    Dropzone.forElement(config.elementId).removeAllFiles(true)

    const deleteUploadedFile = async (file) => {
        return await $.ajax({
            type: 'POST',
            url: config.fileDeleteUrl,
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', config.csrfToken);
            },
            data: { file },
        })
    }

    return dropzone;
}

const mimeTypesMap = {
    image: ['.png', '.jpg', '.jpeg', '.webp', '.JPEG', '.JPG', '.PNG', '.WEBP'],
    audio: ['.aac', '.amr', '.mp3', '.mp4', '.opus', '.AAC', '.AMR', '.MP3', '.MP4', '.OPUS'],
    video: ['.mp4', '.3gp', '.3gpp', '.MP4', '.3GP', '.3GPP'],
    document: ['.doc', '.docx', '.pdf', '.txt', '.ppt', '.pptx', '.xls', '.xlsx', '.DOC', '.DOCX', '.PDF', '.TXT', '.PPT', '.PPTX', '.XLS', '.XLSX'],
}

function cleanXss(string) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        "/": '&#x2F;',
    };
    const reg = /[&<>"'/]/ig;
    return string.replace(reg, (match)=>(map[match]));
}