"use strict";

$(document).ready(function() {
   $(document).on('click', ".choose_package", function(e) {
        e.preventDefault();
        var package_id = $(this).attr('data-id');
        $('#selected-package-id').val(package_id);
        var redirect_url = member_payment_buy_package_url.replace(':id',package_id);
        if(member_payment_buy_package_has_recurring_flag=='1')
        {
            Swal.fire({
                title: member_payment_select_package_lang_already_subscribed,
                html: member_payment_select_package_lang_already_subscribed_lang,
                icon: 'warning',
                confirmButtonColor: '#d33',
                confirmButtonText: global_lang_understand,
            }).then((value) => {
                window.location.assign(redirect_url)
            });
        }
        else
        {
            window.location.assign(redirect_url)
        }
    });
});
