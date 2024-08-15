<script>
    "use strict";
    $(document).ready(function(){

        setTimeout(function() {
            $("#package_bot_subscriber_range_agency").val('2').trigger('change');
        }, 500);

        $('#package_bot_subscriber_range').on('change', function (e) {
            var selected = $(this).val();
            var purchase_url = '{{route("buy-package",":id")}}';
            var package_map = <?php echo $package_map ?? "''";?>;
            var package_id = 1;
            var price = '';
            var subscriber = -1;
            var discount_message = '';
            var validity_text = '';
            var name = '';
            var terms = '';
            var percent = '';

            $('.premium-li').addClass('cs-d-none');
            $('.premium-'+selected).removeClass('cs-d-none');

            if(typeof(package_map[selected]['id'])!=="undefined") package_id = parseInt(package_map[selected]['id']);
            if(typeof(package_map[selected]['price'])!=="undefined") price = package_map[selected]['price'];
            if(typeof(package_map[selected]['subscriber'])!=="undefined") subscriber = package_map[selected]['subscriber'];
            if(typeof(package_map[selected]['discount_message'])!=="undefined") discount_message = package_map[selected]['discount_message'];
            if(typeof(package_map[selected]['validity_text'])!=="undefined") validity_text = package_map[selected]['validity_text'];
            if(typeof(package_map[selected]['name'])!=="undefined") name = package_map[selected]['name'];
            if(typeof(package_map[selected]['terms'])!=="undefined") terms = package_map[selected]['terms'];
            if(typeof(package_map[selected]['percent'])!=="undefined") percent = package_map[selected]['percent'];
            if(terms=='') terms = "{{__('Grow your business with access to pro features and increased limit.')}}";

            if(subscriber==0) subscriber = "<i class='fas fa-infinity'></i>";
            if(subscriber<1) subscriber = '';
            purchase_url = purchase_url.replace(":id", package_id);
            $("#package_price").html(price);
            $("#package_bot_subscriber").html(subscriber);
            $("#package_link").attr('href',purchase_url);
            $("#validity_text").html(validity_text.toLowerCase());
            $("#package_name").html(name);
            $("#discount_extra_message").text(terms);
            $("#discount_percentage").html(percent);

            if(discount_message!=''){
                $("#package_price_save").html(discount_message).addClass('cs-d-block').removeClass('cs-d-none');
            }
            else {
                $("#package_price_save").addClass('cs-d-none').removeClass('cs-d-block');
            }

        });
    });

    const rangeInput = document.getElementById('package_bot_subscriber_range');
    const rangeLabels = document.getElementById('rangeLabels').querySelectorAll('button');

    rangeInput.addEventListener('input', () => {
      const value = rangeInput.value;
      rangeLabels.forEach(label => {
        const labelValue = label.getAttribute('data-value');
        if (labelValue === value) {
          label.classList.add('active');
        } else {
          label.classList.remove('active');
        }
      });
    });

    rangeLabels.forEach(label => {
      label.addEventListener('click', () => {
        const value = label.getAttribute('data-value');
        rangeInput.value = value;
        rangeInput.dispatchEvent(new Event('input'));
      });
    });

</script>