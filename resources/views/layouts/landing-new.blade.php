<!DOCTYPE html>
<?php $enable_dark_mode = '0';?>
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
    <link rel="stylesheet" href="{{asset('assets/cdn/css/all.min.css')}}"/>

    <!--====== CSS ======-->

    <!-- icofont-css-link -->
    <link rel="stylesheet" href="{{asset('assets/landing/new/icofont.min.css')}}">
    <!-- Owl-Carosal-Style-link -->
    <link rel="stylesheet" href="{{asset('assets/landing/new/owl.carousel.min.css')}}">
    <!-- Bootstrap-Style-link -->
    <link rel="stylesheet" href="{{asset('assets/landing/new/bootstrap.min.css')}}">
    <!-- Aos-Style-link -->
    <link rel="stylesheet" href="{{asset('assets/landing/new/aos.css')}}">
    <!-- Coustome-Style-link -->									
    <link rel="stylesheet" href="{{asset('assets/landing/new/style.css')}}">
    <!-- Responsive-Style-link -->
    <link rel="stylesheet" href="{{asset('assets/landing/new/responsive.css')}}">	

    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">

    <script src="{{asset('assets/landing/new/jquery.js')}}"></script>

    <link href="{{asset('/assets/landing/new/custom.css')}}" rel="stylesheet">

    @include('shared.landing-variables')
    @stack('styles-header')
    @stack('scripts-header')

  </head>

  <body class="" dir="{{config('app.localeDirection')}}">
    <div class="page_wrapper">
        <header>
          <!-- container start -->
          <div class="container">
            <!-- navigation bar -->
            <nav class="navbar navbar-expand-lg">
              <a class="navbar-brand" href="{{route('home')}}">
                <img src="{{config('app.logo')}}" alt="logo" >
              </a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                  <div class="toggle-wrap">
                    <span class="toggle-bar"></span>
                  </div>
                </span>
              </button>
    
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                  @if($disable_landing_page=='0')
                    <li class="nav-item active">
                      <a class="nav-link" href="{{route('home')}}">{{__("Home")}}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{route('pricing-plan')}}">{{__('Pricing')}}</a>
                    </li>

                    @if(isset($get_landing_language->links_docs_url) && !empty($get_landing_language->links_docs_url))
                    <li class="nav-item">
                      <a class="nav-link" href="{{$get_landing_language->links_docs_url ?? route('docs')}}">{{__("Documentation")}}</a>
                    </li>
                    @endif

                    <li class="nav-item d-block d-md-none">
                      <a class="nav-link" href="{{route('login')}}"><?php if(Auth::user()) echo __('Dashboard'); else echo __('Sign In');?></a>
                    </li>

                  @endif
                </ul>
              </div>
              <li class="dashboard-btn d-none d-md-block">
                <a class="text-white" href="{{route('login')}}"><?php if(Auth::user()) echo __('Dashboard'); else echo __('Sign In');?></a>
              </li>
            </nav>
            <!-- navigation end -->
          </div>
          <!-- container end -->
        </header>

        @yield('content')

        <!-- Footer-Section start -->
        <footer>
          <!-- section bg -->
          <div class="footer_bg mt-4"> <img src="{{asset('assets/landing/new/images/section-bg.png')}}" alt="Footer-image" > </div>
          <div class="top_footer" id="contact">
              <!-- container start -->
              <div class="container">
                <!-- row start -->
                <div class="row">
                    <!-- footer link 1 -->
                    <div class="col-lg-5 col-md-6 col-12">
                        <div class="abt_side">
                          <div class="logo mb-4"> <img src="{{config('app.logo')}}" alt="logo" ></div>
                          <p class="mb-5">{{__("Unlock the power of efficient communication and thriving collaboration.")}}</p>
                          <ul class="social_media">

                            @if(isset($get_landing_language->company_telegram_channel) && !empty($get_landing_language->company_telegram_channel))
                            <li><a href="{{$get_landing_language->company_telegram_channel}}"><i class="fas fa-paper-plane small"></i></a></li>
                            @endif

                            @if(isset($get_landing_language->company_fb_page) && !empty($get_landing_language->company_fb_page))
                            <li><a href="{{$get_landing_language->company_fb_page}}"><i class="fab fa-facebook-square"></i></a></li>
                            @endif

                            @if(isset($get_landing_language->company_youtube_channel) && !empty($get_landing_language->company_youtube_channel))
                            <li><a href="{{$get_landing_language->company_youtube_channel}}"><i class="fab fa-youtube small"></i></a></li>
                            @endif

                            @if(isset($get_landing_language->company_twitter_account) && !empty($get_landing_language->company_twitter_account))
                            <li><a href="{{$get_landing_language->company_twitter_account}}"><i class="fab fa-twitter"></i></a></li>
                            @endif

                            @if(isset($get_landing_language->company_instagram_account) && !empty($get_landing_language->company_instagram_account))
                            <li><a href="{{$get_landing_language->company_instagram_account}}"><i class="fab fa-instagram"></i></a></li>
                            @endif

                            @if(isset($get_landing_language->company_linkedin_channel) && !empty($get_landing_language->company_linkedin_channel))
                            <li><a href="{{$get_landing_language->company_linkedin_channel}}"><i class="fab fa-linkedin"></i></a></li>
                            @endif

                          </ul>
                        </div>
                    </div>

                    <!-- footer link 2 -->
                    <div class="col-lg-2 col-md-6 col-12">
                        <div class="links">
                          <h3>{{__("Quick")}}</h3>
                            <ul>
                              <li><a href="{{route('home')}}">{{__('Home')}}</a></li>
                              <li><a href="{{route('register')}}">{{__('Sign Up')}}</a></li>
                              <li><a href="{{route('pricing-plan')}}">{{__('Pricing')}}</a></li>
                              <?php $company_support_url = $get_landing_language->company_support_url ?? ''; ?>
                              @if(!empty($company_support_url))
                              <li><a href="{{$company_support_url}}">{{__('Support')}}</a></li>
                              @endif
                            </ul>
                        </div>
                    </div>

                    <!-- footer link 3 -->
                    <div class="col-lg-2 col-md-6 col-12">
                      <div class="links">
                        <h3>{{__("Legal")}}</h3>
                          <ul>
                            <li><a href="{{route('policy-privacy')}}">{{__('Privacy Policy')}}</a></li>
                            <li><a href="{{route('policy-terms')}}">{{__('Terms of Service')}}</a></li>
                            <li><a href="{{route('policy-gdpr')}}">{{__('GDPR Policy')}}</a></li>
                            <li><a href="{{route('policy-refund')}}">{{__('Refund Policy')}}</a></li>
                          </ul>
                      </div>
                    </div>

                    <!-- footer link 4 -->
                    <div class="col-lg-3 col-md-6 col-12">
                      <div class="try_out">
                          <h3>{{__("Get in touch")}}</h3>
                          <ul>
                            <?php $company_email = $get_landing_language->company_email??''?>
                            <?php $company_address = $get_landing_language->company_address??''?>
                            @if(!empty($company_email))
                            <li><span class="text-muted">{{__("Send us Email")}}</span></li>
                            <li><a href="mailto:{{$company_email??''}}"><b>{{$company_email ?? ''}}</b></b></a></li>
                            @endif

                            @if(!empty($company_address))
                            <li class="mt-2"><span class="text-muted"> {{__('Address')}} </span></li>
                            <li><span><b>{!! display_landing_content($company_address) !!}</b></span></li>
                            @endif
                          </ul>
                      </div>
                    </div>
                </div>
                <!-- row end -->
            </div>
            <!-- container end -->
          </div>

          <!-- last footer -->
          <div class="bottom_footer">
            <!-- container start -->
              <div class="container">
                <!-- row start -->
                <div class="row">
                  <div class="col-md-6">
                      <p>© {{date('Y')}} {{config('app.name')}}. {{__('All rights reserved')}} </p>
                  </div>

              </div>
              <!-- row end -->
              </div>
              <!-- container end -->
          </div>

          <!-- go top button -->
          <div class="go_top">
              <span><img src="{{asset('assets/landing/new/images/go_top.png')}}" alt="Go-top" ></span>
          </div>
        </footer>
      <!-- Footer-Section end -->
    </div>


    <!-- owl-js-Link -->
    <script src="{{asset('assets/landing/new/owl.carousel.min.js')}}"></script>
    <!-- bootstrap-js-Link -->
    <script src="{{asset('assets/landing/new/bootstrap.min.js')}}"></script>
    <!-- aos-js-Link -->
    <script src="{{asset('assets/landing/new/aos.js')}}"></script>
    <!-- main-js-Link -->
    <script src="{{asset('assets/landing/new/main.js')}}"></script>
    <script src="{{asset('assets/landing/new/custom.js')}}"></script>


  </body>

</html>