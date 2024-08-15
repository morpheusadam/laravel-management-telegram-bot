"use strict";
var chk1 = $("input[name='is_agency']");
var chk2 = $("input[name='is_whitelabel']");
var inp1 = $("input[name='user_limit']");
var inp2 = $("input[name='subscriber_limit']");

$(document).ready(function() {

    chk1.on('change', function(){
        if(!this.checked) 
        {
            chk2.prop('checked',this.checked);
            inp1.val('0');
            inp2.val('0');
        }
    });  

    $('.modules').on('change', function(){    
        var module_id = $(this).val();
        if($(this).prop('checked')) {
            $('.module_access'+module_id+'[value=1]').prop('checked',true);
            $('.module_access'+module_id+'[value=2]').prop('checked',true);
            $('.module_access'+module_id+'[value=3]').prop('checked',true);
        }
        else $('.module_access'+module_id).prop('checked',false);
    });    
});
