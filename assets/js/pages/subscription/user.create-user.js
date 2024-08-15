"use strict";
$(document).ready(function(){
    $('#user_type').on('change', function (e) {
        e.preventDefault();
        var user_type = $(this).val();
        if(user_type!='' && user_type!='Member'){
            $('.hide_if_special_role').addClass('d-none');
        }
        else {
            $('.hide_if_special_role').removeClass('d-none');
        }
    });
});