"use strict";


function resizeIframe(obj)
{
    setTimeout(function(){
        var cacl_height = obj.contentWindow.document.body.scrollHeight;
        if(parseFloat(cacl_height)<800) cacl_height = '800';
        obj.style.height =  cacl_height + 'px';
    }, 3000);


    $(obj).contents().on("mousedown, mouseup, click", function(){
        setTimeout(function(){
            var cacl_height2 = obj.contentWindow.document.body.scrollHeight;
            if(parseFloat(cacl_height2)<800) cacl_height2 = '800';
            obj.style.height = cacl_height2 + 'px';
        }, 500);
    });
}

function search_in_ul(obj,ul_id)   // obj = 'this' of jquery, ul_id = id of the ul
{
    var filter=$(obj).val().toUpperCase();
    $('#'+ul_id+' li').each(function(){
        var content=$(this).text().trim();

        if (content.toUpperCase().indexOf(filter) > -1) {
            $(this).css('display','');
        }
        else $(this).css('display','none');
    });
}

function htmlspecialchars_decode(str)
{
    if (typeof(str) == "string")
    {
        str = str.replace("&amp;",/&/g);
        str = str.replace("&quot;",/"/g);
        str = str.replace("&#039;",/'/g);
        str = str.replace("&#92;",/\\/g);
        str = str.replace("&lt;",/</g);
        str = str.replace("&gt;",/>/g);
    }
    return str;
}
