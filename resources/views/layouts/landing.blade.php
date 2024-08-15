<!DOCTYPE html>
<?php $enable_dark_mode = $get_landing_language->enable_dark_mode ?? '0';?>
<html lang="{{ get_current_lang() }}" class="<?php if($enable_dark_mode=='1') echo 'dark'?>">
  <head>
 

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }} - @yield('title')</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="@yield('meta_title')">
    <meta name="description" content="@yield('meta_description')">
    <meta name="keywords" content="@yield('meta_keyword')">
    <meta name="author" content="@yield('meta_author')">

    <!-- Google -->
    <meta name="copyright" content="@yield('meta_author')"/>
    <meta name="application-name" content="{{config('app.name')}}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{url('/')}}">
    <meta property="og:title" content="@yield('meta_title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}"/>
    <meta property="og:image" content="@yield('meta_image')">
    <meta property="og:image:width" content="@yield('meta_image_width')" />
    <meta property="og:image:height" content="@yield('meta_image_height')" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{url('/')}}">
    <meta property="twitter:title" content="@yield('meta_title')">
    <meta property="twitter:description" content="@yield('meta_description')">
    <meta property="twitter:image" content="@yield('meta_image')">

    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="{{config('app.favicon')}}" type="image/svg"/>

    <!--====== CSS ======-->
    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/cdn/css/all.min.css')}}"/>
    {{-- you can include bs5 here : /assets/landing/bs5/bs5.css --}}
    <link href="{{asset('/assets/landing/tailwind/tailwind.css')}}" rel="stylesheet">
    <link href="{{asset('/assets/landing/tailwind/ud-styles.css')}}" rel="stylesheet">
    <link href="{{asset('/assets/landing/tailwind/style.css')}}" rel="stylesheet">
    <link href="{{asset('/assets/landing/custom.css')}}" rel="stylesheet">
    <style>

    </style>
    @include('shared.landing-variables')
    @stack('styles-header')
    @stack('scripts-header')

  </head>
  <body class="dark:bg-dark" dir="{{config('app.localeDirection')}}">
    <!-- ===== Header Start ===== -->
     <header class="header absolute top-0 left-0 w-full">

      <div class="flex w-full flex-wrap px-5 lg:flex-nowrap lg:items-center lg:px-5 xl:px-10 2xl:px-20">
        
        <div class="relative z-[99] max-w-[250px] lg:w-full xl:max-w-[350px]">
          <a href="{{route('home')}}" class="inline-block">
            <img src="{{config('app.logo_alt')}}" alt="logo" class="hidden h-[50px] dark:block" />
            <img src="{{config('app.logo')}}" alt="logo" class="h-[50px] dark:hidden" />
          </a>
        </div>

        <?php $current_route = Route::getCurrentRoute()->uri();?>

        <div class="menu-wrapper fixed top-0 left-0 z-50 h-screen w-full justify-center bg-white px-5 dark:bg-dark lg:visible lg:static lg:flex lg:h-auto lg:justify-start lg:bg-transparent lg:p-0 lg:opacity-100 dark:lg:bg-transparent">

          <div class="w-full self-center">
            <nav>
              <ul class="navbar flex flex-col items-left justify-left     space-y-5 lg:flex-row lg:justify-start lg:space-x-10 lg:space-y-0">
                  @if($disable_landing_page=='0')
                  <li>
                    <a href="{{route('home')}}" class="menu-scroll inline-flex items-center justify-center text-center font-heading text-base text-dark-text hover:text-primary dark:hover:text-white"> {{__("Home")}} </a>
                  </li>
                  

                  <li>
                    <a href="{{route('pricing-plan')}}" class="menu-scroll inline-flex items-center justify-center text-center font-heading text-base text-dark-text hover:text-primary dark:hover:text-white"> {{__("Pricing")}} </a>
                  </li>

                  @if(isset($get_landing_language->links_docs_url) && !empty($get_landing_language->links_docs_url))
                  <li>
                    <a href="{{$get_landing_language->links_docs_url}}" class="menu-scroll inline-flex items-center justify-center text-center font-heading text-base text-dark-text hover:text-primary dark:hover:text-white"> {{__("Documentation")}} </a>
                  </li>
                  @endif

                  @endif
              </ul>
            </nav>
          </div>

          <div class="absolute bottom-0 left-0 flex w-full items-center justify-between space-x-5 self-end p-5 lg:static lg:w-auto lg:self-center lg:p-0">
            <a href="{{route('login')}}" class="w-full whitespace-nowrap rounded bg-primary py-3 px-6 text-center font-heading text-white hover:bg-opacity-90 lg:w-auto"> <?php if(Auth::user()) echo __('Dashboard'); else echo __('Sign In');?> </a>
          </div>

        </div>

        <div class="absolute top-1/2 right-5 z-50 flex -translate-y-1/2 items-center lg:static lg:translate-y-0">
          <button class="menu-toggler relative z-50 text-dark dark:text-white lg:hidden">
            <svg width="28" height="28" viewBox="0 0 28 28" class="cross hidden fill-current">
              <path d="M14.0002 11.8226L21.6228 4.20001L23.8002 6.37745L16.1776 14L23.8002 21.6226L21.6228 23.8L14.0002 16.1774L6.37763 23.8L4.2002 21.6226L11.8228 14L4.2002 6.37745L6.37763 4.20001L14.0002 11.8226Z" />
            </svg>
            <svg width="22" height="22" viewBox="0 0 22 22" class="menu fill-current">
              <path d="M2.75 3.66666H19.25V5.49999H2.75V3.66666ZM2.75 10.0833H19.25V11.9167H2.75V10.0833ZM2.75 16.5H19.25V18.3333H2.75V16.5Z" />
            </svg>
          </button>
        </div>

      </div>
    </header>
    
    <!-- ===== Header End ===== --> 

    @yield('content')


    <!-- ===== Footer Start ===== -->
    <footer class="pt-14 sm:pt-20 lg:pt-[130px]" data-wow-delay=".2s">
      <div class="px-4 xl:container">
        <div class="-mx-4 flex flex-wrap">
          <div class="w-full px-4 sm:w-1/2 md:w-5/12 lg:w-5/12 xl:w-5/12">
            <div class="mb-20 text-center lg:text-left">
              <a href="{{route('home')}}" class="mb-6 inline-block">
                <img src="{{config('app.logo_alt')}}" alt="logo" class="hidden h-[50px] dark:block" />
                <img src="{{config('app.logo')}}" alt="logo" class="h-[50px] dark:hidden" />
              </a>
              <p class="mb-10 text-base text-dark-text"> {{__("Unlock the power of efficient communication and thriving collaboration.")}} </p>
              <div class="flex justify-center lg:justify-start items-center space-x-5">
                @if(isset($get_landing_language->company_telegram_channel) && !empty($get_landing_language->company_telegram_channel))
                    <a href="{{$get_landing_language->company_telegram_channel}}" target="_BLANK" name="social-link" aria-label="social-link" class="text-dark-text hover:text-primary dark:hover:text-white">
                        <i class="fas fa-paper-plane small"></i>
                    </a>
                @endif

                @if(isset($get_landing_language->company_fb_page) && !empty($get_landing_language->company_fb_page))
                  <a href="{{$get_landing_language->company_fb_page}}" target="_BLANK" name="social-link" aria-label="social-link" class="text-dark-text hover:text-primary dark:hover:text-white">
                    <i class="fab fa-facebook-square"></i>
                  </a>
                @endif

                @if(isset($get_landing_language->company_youtube_channel) && !empty($get_landing_language->company_youtube_channel))
                <a href="{{$get_landing_language->company_youtube_channel}}" target="_BLANK" name="social-link" aria-label="social-link" class="text-dark-text hover:text-primary dark:hover:text-white">
                  <i class="fab fa-youtube small"></i>
                </a>
                @endif

                @if(isset($get_landing_language->company_twitter_account) && !empty($get_landing_language->company_twitter_account))
                <a href="{{$get_landing_language->company_twitter_account}}" target="_BLANK" name="social-link" aria-label="social-link" class="text-dark-text hover:text-primary dark:hover:text-white">
                  <i class="fab fa-twitter"></i>
                </a>
                @endif

                @if(isset($get_landing_language->company_instagram_account) && !empty($get_landing_language->company_instagram_account))
                <a href="{{$get_landing_language->company_instagram_account}}" target="_BLANK" name="social-link" aria-label="social-link" class="text-dark-text hover:text-primary dark:hover:text-white">
                  <i class="fab fa-instagram"></i>
                </a>
                @endif

                @if(isset($get_landing_language->company_linkedin_channel) && !empty($get_landing_language->company_linkedin_channel))
                <a href="{{$get_landing_language->company_linkedin_channel}}" target="_BLANK" name="social-link" aria-label="social-link" class="text-dark-text hover:text-primary dark:hover:text-white">
                  <i class="fab fa-linkedin"></i>
                </a>
                @endif
              </div>
            </div>
          </div>
          <div class="hidden lg:flex w-1/2 w-1/2 px-4 md:w-3/12 lg:w-3/12 xl:w-2/12">
            <div class="mb-20">
              <h3 class="mb-9 font-heading text-2xl font-medium text-dark dark:text-white"> {{__("Quick")}} </h3>
              <ul class="space-y-4">
                <li>
                  <a href="{{route('home')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Home')}}</a>
                </li>                
                <li>
                  <a href="{{route('register')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Sign Up')}}</a>
                </li>
                <li>
                  <a href="{{route('pricing-plan')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Pricing')}}</a>
                </li>
                <?php $company_support_url = $get_landing_language->company_support_url ?? ''; ?>
                @if(!empty($company_support_url))
                <li>
                  <a href="{{$company_support_url}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Support')}}</a>
                </li>
                @endif
              </ul>
            </div>
          </div>
          <div class="hidden lg:flex w-1/2 px-4 md:w-3/12 lg:w-3/12 xl:w-2/12">
            <div class="mb-20">
              <h3 class="mb-9 font-heading text-2xl font-medium text-dark dark:text-white"> {{__("Legal")}} </h3>
              <ul class="space-y-4">
                <li>
                  <a href="{{route('policy-privacy')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Privacy Policy')}}</a>
                </li>
                <li>
                  <a href="{{route('policy-terms')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Terms of Service')}}</a>
                </li>
                <li>
                    <a href="{{route('policy-gdpr')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('GDPR Policy')}}</a>
                </li>
                <li>
                  <a href="{{route('policy-refund')}}"  class="font-heading text-base text-dark-text hover:text-primary dark:hover:text-white">{{__('Refund Policy')}}</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="w-full px-4 sm:w-1/2 md:w-5/12 lg:w-4/12 xl:w-3/12">
            <div class="mb-20">
              <h3 class="mb-9 text-center lg:text-left font-heading text-2xl font-medium text-dark dark:text-white"> {{__("Get in touch")}} </h3>
              <div class="space-y-7 text-center lg:text-left">
                <?php $company_email = $get_landing_language->company_email??''?>
                <?php $company_address = $get_landing_language->company_address??''?>
                @if(!empty($company_email))
                <div>
                  <p class="font-heading text-base text-dark-text"> {{__("Send us Email")}} </p>
                  <a href="mailto:{{$company_email??''}}" class="font-heading text-base text-dark hover:text-primary dark:text-white dark:hover:text-primary"> {{$company_email??''}} </a>
                </div>
                @endif
                @if(!empty($company_address))
                <div>
                  <p class="font-heading text-base text-dark-text"> {{__('Address')}} </p>
                  <p class="font-heading text-base text-dark dark:text-white">
                    {!! display_landing_content($company_address) !!}
                  </p>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="dark:border-[#2E333D] md:border-t">
          <div class="-mx-4 flex flex-wrap py-5 md:py-7">
            <div class="w-full px-4 md:w-1/2 lg:w-1/3">
              <div>
                <p class="text-center font-heading text-base text-dark-text lg:text-left"> © {{date('Y')}} {{config('app.name')}}. {{__('All rights reserved')}} </p>
              </div>
            </div>
            <div class="w-full px-4 md:w-1/2 lg:w-2/3">
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- ===== Footer End  ===== -->
    <!-- ====== Back To Top Start ===== -->
    <a href="javascript:void(0)" class="hover:shadow-signUp back-to-top fixed bottom-8 right-8 left-auto z-[999] hidden h-10 w-10 items-center justify-center rounded-sm bg-primary text-white shadow-md transition">
      <span class="mt-[6px] h-3 w-3 rotate-45 border-t border-l border-white"></span>
    </a>
    <!-- ====== Back To Top End ===== -->


    @stack('styles-footer')
    <script defer src="{{asset('/assets/landing/tailwind/bundle.js')}}"></script>
    @if(isset($get_analytics_code) && !empty($get_analytics_code))
        @include('shared.analytics')
    @endif
    @stack('scripts-footer')

  </body>
</html>

@yield('modal')
