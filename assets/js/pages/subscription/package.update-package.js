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

    if($("#price_default").val()=="0") $(".hidden_me").hide();
    else $("#validity").show();
    $("#price_default").on('change',function(){
      if($(this).val()=="0") $(".hidden_me").hide();
      else $(".hidden_me").show();
    });
    
});
