<?php
    $review_found=0;
    $review_str = '';
    for($i=1;$i<=10;$i++):
        $var1 = "review_".$i."_description";
        $var2 = "review_".$i."_avatar";
        $var3 = "review_".$i."_name";
        $var4 = "review_".$i."_designation";
        if(!isset($get_landing_language->$var1) || !isset($get_landing_language->$var2) || !isset($get_landing_language->$var3) || !isset($get_landing_language->$var4)) continue;
        if(empty($get_landing_language->$var1) && empty($get_landing_language->$var2) && empty($get_landing_language->$var3) && empty($get_landing_language->$var4)) continue;
        
        $review_str .= '
        <div class="testimonial-item">
          <div class="-mx-4 flex flex-wrap items-center">
            <div class="order-last w-full px-4 lg:order-first lg:w-2/3">
              <div class="text-left">

                <p class="mb-9 font-heading text-base font-light text-dark-text lg:text-lg xl:text-2xl cs-testimonial-body"> “'.display_landing_content($get_landing_language->$var1).'</p>

                <h3 class="mb-1 font-heading text-xl text-dark dark:text-white"> '.display_landing_content($get_landing_language->$var3).' </h3>

                <p class="text-base text-dark-text mb-10"> '.display_landing_content($get_landing_language->$var4).' </p>

              </div>
            </div>
            <div class="w-full px-4 lg:w-1/3">
              <div class="lg:mr-0 relative mx-auto mb-9 sm:mb-2 h-[300px] lg:h-[420px] w-full max-w-[420px] lg:mb-0">
                <div class="flex justify-center items-center cs-top-1/3 z-10">
                  <img src="'.$get_landing_language->$var2.'" class="cs-testimonial-image" alt="testimonial-image" />
                </div>
              </div>
            </div>
          </div>
        </div>';
        $review_found++;
    endfor;
  ?>

  @if($review_found>0 && $disable_review_section=='0')
  <section id="testimonial" class="pt-14 sm:pt-20 lg:pt-[100px]">

    <div class="px-4 xl:container">
      <!-- Section Title -->

      <div class="wow fadeInUp relative mx-auto mb-12 pt-6 text-center md:mb-20 lg:pt-16" data-wow-delay=".2s">
        <span class="title"> {{__("Testimonial")}} </span>
        <h2 class="mx-auto mb-5 max-w-[450px] font-heading text-3xl font-semibold text-dark dark:text-white sm:text-4xl md:text-[50px] md:leading-[60px]"> {{__("What Our Clients Say About Us")}} </h2>
      </div>

      <div class="w-full px-4">

        <div class="wow fadeInUp relative z-10 overflow-hidden rounded bg-cover bg-center px-10 pt-[60px] pb-28 drop-shadow-light dark:drop-shadow-none sm:px-14 md:p-[70px] md:pb-28 lg:pb-[70px]" data-wow-delay=".3s">

          <div class="absolute top-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern"></div>

          <div class="absolute bottom-0 left-1/2 -z-10 -translate-x-1/2">

            <svg width="1174" height="560" viewBox="0 0 1174 560" fill="none" xmlns="http://www.w3.org/2000/svg">

              <g opacity="0.4" filter="url(#filter0_f_41_257)">
                <rect x="450.531" y="279" width="272.933" height="328.051" fill="url(#paint0_linear_41_257)" />
              </g>

              <defs>
                <filter id="filter0_f_41_257" x="0.531494" y="-171" width="1172.93" height="1228.05" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                  <feFlood flood-opacity="0" result="BackgroundImageFix" />
                  <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                  <feGaussianBlur stdDeviation="225" result="effect1_foregroundBlur_41_257" />
                </filter>
                <linearGradient id="paint0_linear_41_257" x1="425.16" y1="343.693" x2="568.181" y2="660.639" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#ABBCFF" />
                  <stop offset="0.859375" stop-color="#4A6CF7" />
                </linearGradient>
              </defs>
            </svg>

          </div>

          <div class="testimonial-active">
            {!!$review_str!!}
          </div>
        </div>

      </div>

    </div>
  </section>
  @endif