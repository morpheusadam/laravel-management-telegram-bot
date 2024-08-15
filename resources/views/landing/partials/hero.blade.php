<?php
  $typingKeywords = [
    __("Community Manager"),
    __('Event Organizer'),
    __('Online Educator'),
    __('Content Creator'),
    __('Book Clubber'),
    __('Customer Manager'),
    __('Marketing Expert'),
    __('eCommerce Professional'),
    __('Fitness Coache'),
    __('Freelancer'),
    __('Nonprofit Organizer')
  ];
  $typingKeywords = json_encode($typingKeywords,JSON_HEX_APOS);
?>
<section id="home" class="relative z-40 overflow-hidden">
  <div class="px-5 lg:px-5 xl:px-10 2xl:px-20">
    <div class="-mx-4 flex flex-wrap items-center">
      <div class="w-full lg:w-2/3 px-3 text-center lg:text-left">

        <span class="mb-10 inline-block rounded-full bg-primary bg-opacity-5 py-[10px] px-5 font-heading text-base text-primary dark:bg-white dark:bg-opacity-20 dark:text-white" data-wow-delay=".2s">
          <span class="mr-2 inline-block h-2 w-2 rounded-full bg-primary"></span>
          {{__("Connecting Conversations, Empowering Communities",['appname'=>config('app.name')])}}
        </span>


        <p class="mb-5 lg:mb-10 font-heading text-2xl text-dark dark:text-white sm:text-4xl md:text-[50px] md:leading-[60px]">
          {{__(":appname for",['appname'=>config('app.name')])}}
        </p>

        <div class="mb-10 lg:mb-20 lg:ml-1 lg:text-left">
          <h1 class="wow fadeInUp mb-5 font-heading text-2xl dark:text-white sm:text-4xl md:text-[50px] md:leading-[60px]" data-wow-delay=".3s">
           <span class="txt-type" data-wait="3000" data-words='<?php echo $typingKeywords?>'></span><span class="txt-type">
          </h1>
        </div>


        <p class="mt-10 mb-3 text-3xl text-dark dark:text-white sm:text-[35px] sm:leading-[60px]">
          {{__("Elevate Group Management for Growing Community")}}
        </p>


        <p class="pl-10 mb-10 text-dark dark:text-white text-xl sm:text-[35px] sm:leading-[60px]">{!!__("Dynamic antispam toolkit, filtering content, promoting respect, and fine-tuning member contributions.",['appname'=>config('app.name'),'linebreak'=>'<br>'])!!}</p>

        <p class="hidden lg:block pl-10 mb-10 text-dark dark:text-white text-xl sm:text-[35px] sm:leading-[60px]">{!!__("Ensure respect with keyword monitoring, and improve interaction through member message limitation, removing redundancy, flagging spam, applying temporary mutes, and controlling message frequency.",['appname'=>config('app.name'),'linebreak'=>'<br>'])!!}</p>

        <div class="mb-5 lg:mb-20 flex flex-wrap items-center justify-center lg:items-start lg:justify-start" data-wow-delay=".5s">          
          <a href="{{route('register')}}" class="inline-flex items-center rounded bg-primary py-[10px] px-6 font-heading text-base text-white hover:bg-opacity-90 md:py-[14px] md:px-8">{{__('Get Started')}} <span class="pl-3">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.172 7L6.808 1.636L8.222 0.222L16 8L8.222 15.778L6.808 14.364L12.172 9H0V7H12.172Z" fill="white" />
            </svg>
          </span>
          </a>
          @if(isset($get_landing_language->links_docs_url) && !empty($get_landing_language->links_docs_url))
          <a href="{{$get_landing_language->links_docs_url}}" class="inline-flex items-center rounded py-[14px] px-8 font-heading text-base text-dark hover:text-primary dark:text-white dark:hover:text-primary">
            <span class="pr-3">
              <svg width="24" height="24" viewBox="0 0 24 24" class="fill-current">
                <path d="M19.376 12.416L8.777 19.482C8.70171 19.5321 8.61423 19.5608 8.52389 19.5652C8.43355 19.5695 8.34373 19.5492 8.264 19.5065C8.18427 19.4639 8.1176 19.4003 8.07111 19.3228C8.02462 19.2452 8.00005 19.1564 8 19.066V4.934C8.00005 4.84356 8.02462 4.75482 8.07111 4.67724C8.1176 4.59966 8.18427 4.53615 8.264 4.49346C8.34373 4.45077 8.43355 4.43051 8.52389 4.43483C8.61423 4.43915 8.70171 4.46789 8.777 4.518L19.376 11.584C19.4445 11.6297 19.5006 11.6915 19.5395 11.7641C19.5783 11.8367 19.5986 11.9177 19.5986 12C19.5986 12.0823 19.5783 12.1633 19.5395 12.2359C19.5006 12.3085 19.4445 12.3703 19.376 12.416Z" />
              </svg>
            </span> {{__("Explore More")}} </a>
          @endif
        </div>

      </div>
      <div class="w-full lg:w-1/3 px-3 text-center mb-10">
        <div class="cs-video-block flex items-center justify-center">
          <img class="" src="{{$get_landing_language->details_feature_1_img??asset('assets/landing/images/hero/hero_image_2.jpg')}}" >
        </div>
      </div>

    </div>

  </div>
  
  <div class="absolute bottom-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern"></div>
  <div class="absolute top-0 right-0 -z-10">
    <svg width="1356" height="860" viewBox="0 0 1356 860" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g opacity="0.5" filter="url(#filter0_f_201_2181)">
        <rect x="450.088" y="-126.709" width="351.515" height="944.108" transform="rotate(-34.6784 450.088 -126.709)" fill="url(#paint0_linear_201_2181)" />
      </g>
      <defs>
        <filter id="filter0_f_201_2181" x="0.0878906" y="-776.711" width="1726.24" height="1876.4" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
          <feFlood flood-opacity="0" result="BackgroundImageFix" />
          <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
          <feGaussianBlur stdDeviation="225" result="effect1_foregroundBlur_201_2181" />
        </filter>
        <linearGradient id="paint0_linear_201_2181" x1="417.412" y1="59.4717" x2="966.334" y2="603.857" gradientUnits="userSpaceOnUse">
          <stop stop-color="#ABBCFF" />
          <stop offset="0.859375" stop-color="#4A6CF7" />
        </linearGradient>
      </defs>
    </svg>
  </div>
  <div class="absolute bottom-0 left-0 -z-10">
    <svg width="1469" height="498" viewBox="0 0 1469 498" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g opacity="0.3" filter="url(#filter0_f_201_2182)">
        <rect y="450" width="1019" height="261" fill="url(#paint0_linear_201_2182)" />
      </g>
      <defs>
        <filter id="filter0_f_201_2182" x="-450" y="0" width="1919" height="1161" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
          <feFlood flood-opacity="0" result="BackgroundImageFix" />
          <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
          <feGaussianBlur stdDeviation="225" result="effect1_foregroundBlur_201_2182" />
        </filter>
        <linearGradient id="paint0_linear_201_2182" x1="-94.7239" y1="501.47" x2="-65.8058" y2="802.2" gradientUnits="userSpaceOnUse">
          <stop stop-color="#ABBCFF" />
          <stop offset="0.859375" stop-color="#4A6CF7" />
        </linearGradient>
      </defs>
    </svg>
  </div>
</section>
