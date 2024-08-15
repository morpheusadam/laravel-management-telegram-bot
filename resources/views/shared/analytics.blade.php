<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$userAgent = strtolower($userAgent);
?>

@if(!str_contains($userAgent, "googlebot") && !str_contains($userAgent, "google.com/bot"))

  <?php if(isset($get_analytics_code['fb_pixel_id']) && !empty($get_analytics_code['fb_pixel_id'])) : ?>
    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?php echo $get_analytics_code['fb_pixel_id'] ?? ""; ?>');
      <?php if(!$is_agency_site) : ?>
        fbq('init', '342869686661279');
      <?php endif; ?>
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=<?php echo $get_analytics_code['fb_pixel_id'] ?? ""; ?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
  <?php endif; ?>


  <?php if(isset($get_analytics_code['google_analytics_id']) && !empty($get_analytics_code['google_analytics_id'])) : ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $get_analytics_code['google_analytics_id'] ?? ''; ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?php echo $get_analytics_code['google_analytics_id'] ?? ""; ?>');
    </script>
  <?php endif; ?>

@endif
