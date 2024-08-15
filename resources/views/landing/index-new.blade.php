@extends('layouts.landing-new')
@section('title',$meta_title)
@section('meta_title',$meta_title)
@section('meta_description',$meta_description)
@section('meta_keyword',$meta_keyword)
@section('meta_author',$meta_author)
@section('meta_image',$meta_image)
@section('meta_image_width',$meta_image_width)
@section('meta_image_height',$meta_image_height)
@section('content')
  
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

  <!-- Banner-Section-Start -->
  <section class="banner_section">
    <!-- hero bg -->
    <div class="hero_bg"> <img src="{{asset('assets/landing/new/images/hero-bg.png')}}" alt="image" > </div>
    <!-- container start -->
    <div class="container">
      <!-- row start -->
      <div class="row">
        <div class="col-lg-6 col-md-12"  data-aos="fade-right" data-aos-duration="1500">
          <!-- banner text -->
          <div class="banner_text">
            <!-- h1 -->
            <h1>{{__(":appname for",['appname'=>config('app.name')])}}<span><br><span id="typewriter"></span></span></h1>
            <!-- p -->
            <h4 class="mt-4 mb-4">{{__("Connecting & Empowering Communities")}}</h4>
            <p>{!!__("Dynamic antispam toolkit, filtering content, promoting respect, and fine-tuning member contributions for Telegram groups.",['appname'=>config('app.name'),'linebreak'=>'<br>'])!!}
            </p>
          </div>

          <div class="trial_box">
            <!-- form -->
            <form action="" id="registerForm" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="100">
                <div class="form-group">
                    <input type="email" id="registerEmail" class="form-control" autofocus placeholder="{{__('Enter your email')}}">
                </div>
                <div class="form-group">
                    <button class="btn" id="registerURL">{{__('Free Signup')}}</button>
                </div>
            </form>
        </div>

        </div>

        <!-- banner images start -->
        <div class="col-lg-3 col-md-6"  data-aos="fade-in" data-aos-duration="1500">
          <div class="banner_images image_box1">
              <span class="banner_image1"> <img class="moving_position_animatin" src="{{$get_landing_language->banner_image1??asset('assets/landing/new/images/bannerimage1.png')}}" alt="image" > </span>
              <span class="banner_image2"> <img class="moving_animation" src="{{$get_landing_language->banner_image2??asset('assets/landing/new/images/bannerimage2.png')}}" alt="image" > </span>
          </div>
        </div>

        <div class="col-lg-3 col-md-6"  data-aos="fade-in" data-aos-duration="1500">
          <div class="banner_images image_box2">
              <span class="banner_image3"> <img class="moving_animation" src="{{$get_landing_language->banner_image3??asset('assets/landing/new/images/bannerimage3.png')}}" alt="image" > </span>
              <span class="banner_image4"> <img class="moving_position_animatin" src="{{$get_landing_language->banner_image4??asset('assets/landing/new/images/bannerimage4.png')}}" alt="image" > </span>
          </div>
        </div>
        <!-- banner slides end -->

      </div>
      <!-- row end -->
    </div>
    <!-- container end -->
  </section>
  <!-- Banner-Section-end -->




  <!-- Features-Section-Start -->
  <section class="row_am features_section" id="features">
      <!-- section bg -->
      <div class="feature_section_bg"> <img src="{{asset('assets/landing/new/images/section-bg.png')}}" alt="image" > </div>
      <!-- container start -->
      <div class="container">
          <div class="features_inner">  

              <!-- feature image -->
              <div class="feature_img" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="100">
                <img src="{{$get_landing_language->feature_image ?? asset('assets/landing/new/images/device-feature.png')}}" alt="image" >
              </div>

              <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
              <!-- h2 -->
              <h2><span>{{__("Unique & Awesome")}}</span></h2>
              <h2>{{__("Core Features")}}</h2>
              <!-- p -->
              <p>{!!__("Ensure respect with keyword monitoring, and improve interaction through member message limitation,:newline removing redundancy, flagging spam, applying temporary mutes, and controlling message frequency.",['newline'=>'<br>'])!!}</p>
              </div>
              
              <!-- story -->
              <div class="features_block" style="max-height: 1000px;overflow-y:auto;">
                  <div class="row">
                      @foreach($ai_sidebar_group_by_id as $menu_group_id=>$menu_items)
                      <?php if(empty($menu_items)) continue; ?>
                      @foreach($menu_items as $menu_item)
                          <?php
                              $action_url = route('dashboard');
                              $has_access = $menu_item['has_access'] ?? true;
                              $about_text = !empty($menu_item['about_text']) ? $menu_item['about_text'].' : ' : '';
                          ?>
                          <div class="col-md-4">
                              <div class="feature_box mt-4">
                                  <div class="image">
                                     <i class="{{$menu_item['template_thumb']}} cs-icon-lg"></i>
                                  </div>
                                  <div class="text pt-2">
                                      <h4 class="mb-4">{{__($menu_item['template_name'])}}</h4>
                                      <p class="">{{__($menu_item['template_description'])}}</p>
                                  </div>
                              </div>
                          </div>
                      @endforeach
                      @endforeach
                  </div>
              </div>
              <div class="features_block">
                <div class="row">
                  <div class="col-md-4 mb-4">
                    <div class="feature_box" data-aos="fade-up" data-aos-duration="1700">
                        <div class="image">
                          <img src="{{$get_landing_language->details_feature_1_img??asset('assets/landing/new/images/details_feature_1_img.png')}}" height="81" width="81" alt="image" >
                        </div>
                        <div class="text">
                          <h4>{{__("Filter Members Messages")}}</h4>
                          <p>{{__("Pomote a clean and focused group chat by implementing message filtering options. Remove bot commands, images, voice recordings, attached documents, stickers, GIFs, member dice rolls, and links to ensure a clutter-free and productive environment.")}}</p>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-4">
                    <div class="feature_box" data-aos="fade-up" data-aos-duration="1700">
                        <div class="image">
                          <img src="{{$get_landing_language->details_feature_2_img??asset('assets/landing/new/images/details_feature_2_img.png')}}" height="81" width="81" alt="image" >
                        </div>
                        <div class="text">
                          <h4>{{__("Filter Forwarded Messages")}}</h4>
                          <p>{{__(":appname ensures a clean and focused group chat by efficiently handling forwarded messages. It filters out forwarded messages with media attachments and URLs, reducing clutter and enhancing security.",["appname"=>config('app.name')])}}</p>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-4">
                    <div class="feature_box" data-aos="fade-up" data-aos-duration="1700">
                        <div class="image">
                          <img src="{{$get_landing_language->details_feature_3_img??asset('assets/landing/new/images/details_feature_3_img.png')}}" height="81" width="81" alt="image" >
                        </div>
                        <div class="text">
                          <h4>{{__("Keyword Surveillance")}}</h4>
                          <p>{{__(":appname keeps your group chat safe and focused. It automatically removes messages with censor words and efficiently filters forwarded messages, including those with media attachments, URLs, or all forwarded messages.",["appname"=>config('app.name')])}}</p>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="feature_box" data-aos="fade-up" data-aos-duration="1700">
                        <div class="image">
                          <img src="{{$get_landing_language->details_feature_4_img??asset('assets/landing/new/images/details_feature_4_img.png')}}" height="81" width="81" alt="image" >
                        </div>
                        <div class="text">
                          <h4>{{__("Service Message Control")}}</h4>
                          <p>{{__("Helps you keep your group announcements and updates organized and professional. You can delete `User Joined the Group` messages to maintain a clean announcements section and remove `User Left the Group` messages for a more polished appearance.",["appname"=>config('app.name')])}}</p>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="feature_box" data-aos="fade-up" data-aos-duration="1700">
                        <div class="image">
                          <img src="{{$get_landing_language->details_feature_5_img??asset('assets/landing/new/images/details_feature_5_img.png')}}" height="81" width="81" alt="image" >
                        </div>
                        <div class="text">
                          <h4>{{__("New Members Restriction")}}</h4>
                          <p>{{__("You have the power to set restrictions for new group members, ensuring a smooth onboarding process and protection against spam or disruptive behavior. Decide the duration of the restrictions, granting you control over the group dynamics.",["appname"=>config('app.name')])}}</p>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="feature_box" data-aos="fade-up" data-aos-duration="1700">
                        <div class="image">
                          <img src="{{$get_landing_language->details_feature_6_img??asset('assets/landing/new/images/details_feature_6_img.png')}}" height="81" width="81" alt="image" >
                        </div>
                        <div class="text">
                          <h4>{{__("New Members Limitation")}}</h4>
                          <p>{{__(":appname enhances group communication, streamlining it and ensuring security. It prevents message flooding and clutter by deleting repeated messages and deters spamming through content identification.",["appname"=>config('app.name')])}}</p>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
      </div>
      <!-- container end -->
  </section>
  <!-- Features-Section-end -->

  <!-- About-App-Section-Start -->
  <br><br><br><br>
  <section class="row_am about_app_section">
    <!-- container start -->
    <div class="container">
      <!-- row start -->
      <div class="row">
        <div class="col-lg-6">

          <!-- about images -->
          <div class="about_img" data-aos="fade-in" data-aos-duration="1500">
            <div class="frame_img">
              <img class="moving_position_animatin" src="{{$get_landing_language->about_image1??asset('assets/landing/new/images/about-frame.png')}}" alt="image" >
            </div>
            <div class="screen_img">
              <img class="moving_animation" src="{{$get_landing_language->about_image2??asset('assets/landing/new/images/about-screen.png')}}" alt="image" >
            </div>
          </div>
        </div>
        <div class="col-lg-6">

          <!-- about text -->
          <div class="about_text">
            <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="100">

              <!-- h2 -->
              <h2>{{__('Some Awesome Words')}} <span>{{__('About :appname',['appname'=>config('app.name')])}}</span></h2>

              <!-- p -->
              <p>
                {{__(":appname, foster a culture of respect with our keyword monitoring feature, while elevating interaction dynamics through thoughtful member message limitations. We optimize communication by eliminating redundancy, flagging spam, and implementing temporary mutes when needed. With multilingual proficiency in 30+ languages, 95%+ success rate, 88%+ monitoring rate, experience a community platform that prioritizes positive engagement and ensures a smooth and enjoyable user experience.",["appname"=>config('app.name')])}}
              </p>
            </div>

            <!-- UL -->
            <ul class="app_statstic" id="counter" data-aos="fade-in" data-aos-duration="1500">
              <li>
                <div class="icon">
                  <img src="{{asset('assets/landing/new/images/download.png')}}" alt="image" >
                </div>
                <div class="text">
                  <p><span class="counter-value" data-count="25">0</span><span>%+</span></p>
                  <p>{{__('Filtering Options')}}</p>
                </div>
              </li>
              <li>
                <div class="icon">
                  <img src="{{asset('assets/landing/new/images/followers.png')}}" alt="image" >
                </div>
                <div class="text">
                  <p><span class="counter-value" data-count="95">0</span><span>%+</span></p>
                  <p>{{__('Success Rate')}}</p>
                </div>
              </li>
              <li>
                <div class="icon">
                  <img src="{{asset('assets/landing/new/images/reviews.png')}}" alt="image" >
                </div>
                <div class="text">
                  <p><span class="counter-value" data-count="88">0</span><span>%+</span></p>
                  <p>{{__('Monitoring Rate')}}</p>
                </div>
              </li>
              <li>
                <div class="icon">
                  <img src="{{asset('assets/landing/new/images/countries.png')}}" alt="image" >
                </div>
                <div class="text">
                  <p><span class="counter-value" data-count="30">0</span><span>+</span></p>
                  <p>{{__('Languages')}}</p>
                </div>
              </li>
            </ul>
            <!-- UL end -->
            <a href="{{route('register')}}" class="text-uppercase btn puprple_btn" data-aos="fade-in" data-aos-duration="1500">{{__('Start Free Trial')}}</a>
          </div>
        </div>
      </div>
      <!-- row end -->
    </div>
    <!-- container end -->
  </section>
  <!-- About-App-Section-end -->

  <!-- ModernUI-Section-Start -->
  <section class="row_am modern_ui_section">
    <!-- section bg -->
    <div class="modernui_section_bg"> <img src="{{asset('assets/landing/new/images/section-bg.png')}}" alt="image" > </div>
    <!-- container start -->
    <div class="container">
      <!-- row start -->
      <div class="row">
        <div class="col-lg-6">
          <!-- UI content -->
          <div class="ui_text">
            <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="100">
              <h2>{{__("Beautiful Design With")}} <span>{{__("Modern UI")}}</span></h2>
              <p>
                {{__("Embark on a visual journey with our platform`s `Beautiful Design With Modern UI` where aesthetics meet functionality, creating an immersive digital experience that is both captivating and user-centric.")}}
              </p>
            </div>
            <ul class="design_block">
              <li data-aos="fade-up" data-aos-duration="1500">
                <h4>{{__("Carefully Designed")}}</h4>
                <p>{{__("Every aspect of our platform, showcases an intentional and thoughtful approach to user experience, and ensures simplicity and elegance in every interaction.")}}</p>
              </li>
              <li data-aos="fade-up" data-aos-duration="1500">
                <h4>{{__("Functionality Redefined")}}</h4>
                <p>{{__("Each design element serves a purpose, contributing to an interface that not only looks good but also optimizes user interactions and overall usability.")}}</p>
              </li>
              <li data-aos="fade-up" data-aos-duration="1500">
                <h4>{{__("User-Centric")}}</h4>
                <p>{{__("Our focus on user-centric design ensures that every design choice enhances the overall user experience. Navigating through our platform becomes an intuitive journey.")}}</p>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <!-- UI Image -->
          <div class="ui_images" data-aos="fade-in" data-aos-duration="1500">
            <div class="left_img">
              <img class="moving_position_animatin" src="{{$get_landing_language->design_image1??asset('assets/landing/new/images/modern01.png')}}" alt="image" >
            </div>
            <!-- UI Image -->
            <div class="right_img">
              <img class="moving_position_animatin" src="{{asset('assets/landing/new/images/shield_icon.png')}}" alt="image" >
              <img class="moving_position_animatin" src="{{$get_landing_language->design_image2??asset('assets/landing/new/images/modern02.png')}}" alt="image" >
              <img class="moving_position_animatin" src="{{$get_landing_language->design_image3??asset('assets/landing/new/images/modern03.png')}}" alt="image" >
            </div>
          </div>
        </div>
      </div>
      <!-- row end -->
    </div>
    <!-- container end -->
  </section>
  <!-- ModernUI-Section-end -->

  <!-- Use-case-Section-Start -->
  <?php
    $use_cases = [
      ['title'=>__("Community Manager"),'description'=>__("Streamline community interactions with features designed for efficient moderation, member engagement, and content organization. From social media groups to professional forums, our platform enhances community experiences.")],
      ['title'=>__("Event Organizer"),'description'=>__("For those orchestrating events, our software provides tools to manage event-related discussions, RSVPs, and timely announcements, creating a cohesive and engaging event experience for attendees.")],
      ['title'=>__("Customer Manager"),'description'=>__("Elevate customer support with automated responses, categorized FAQs, and streamlined ticket management. Our software ensures efficient query resolution, leaving customers satisfied with prompt assistance.")],
      ['title'=>__("Marketing Expert"),'description'=>__("Marketing professionals can leverage our software to manage promotional campaigns, engage audiences through interactive content, and gather feedback to refine marketing strategies effectively.")],
      ['title'=>__("eCommerce Professional"),'description'=>__("For eCommerce professionals, our software offers streamlined communication with customers, enabling instant product updates, personalized support, and exclusive offers. This ensures efficient customer interactions and assists in building lasting customer relationships.")],
      ['title'=>__("Online Educator"),'description'=>__("Educators can create engaging virtual classrooms, share resources, and facilitate discussions using our platform. From quizzes to study groups, our software enhances the online learning experience.")],
      ['title'=>__("Professional Association"),'description'=>__("Enhance professional networking and knowledge sharing within industry-specific associations. Our software aids in creating valuable connections, sharing insights, and promoting industry advancements.")],
      ['title'=>__("Book Clubber"),'description'=>__("ook enthusiasts can establish digital book clubs for literary discussions, reading challenges, and author interactions. Our software fosters a space for passionate readers to connect.")],
      ['title'=>__("Nonprofit Organizer"),'description'=>__("Nonprofit organizations can streamline volunteer coordination, donation drives, and awareness campaigns through efficient group management. Our platform supports their noble causes.")]
    ]
  ?>
  <section class="row_am features_section" id="use_case">
      <!-- section bg -->
      <div class="feature_section_bg"> <img src="{{asset('assets/landing/new/images/section-bg.png')}}" alt="image" > </div>
      <!-- container start -->
      <div class="container">
          <div class="features_inner pt-5">        
              <div class="section_title pt-5" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
              <!-- h2 -->
              <h2><span>{{__("Use Cases")}}</span></h2>
              <h2>{{__(":appname for Everyone",["appname"=>config('app.name')])}}</h2>
              <!-- p -->
              <p>{{__("Innovative use cases across industries and professions")}}</p>
              </div>
              
              <!-- story -->
              <div class="features_block">
                  <div class="row">
                    @foreach($use_cases as $use_case)
                      <div class="col-md-4">
                        <div class="feature_box" data-aos="fade-up" data-aos-duration="500">
                          <div class="text">
                            <h4 class="pb-3"><b>{{$use_case['title']}}</b></h4>
                            <p>{{$use_case['description']}}</p>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
              </div>
          </div>
      </div>
      <!-- container end -->
  </section>
  <!-- Use-case-Section-end -->

  <!-- How-It-Workes-Section-Start -->
  <section class="row_am how_it_works" id="how_it_work">
    <!-- section bg -->
    <div class="how_section_bg"> <img src="{{asset('assets/landing/new/images/section-bg.png')}}" alt="image" > </div>
    <!-- container start -->
    <div class="container">
      <div class="how_it_inner">
        <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
          <!-- h2 -->
          <h2><span>{{__("How it works")}}</span> - {{__("Easy Steps")}}</h2>
          <!-- p -->
          <p>{{__("Simply input your information, let the system process it effortlessly, and enjoy the seamless results at your fingertips.")}}</p>
        </div>
        <div class="step_block">
          <!-- UL -->
          <ul>
            <!-- step -->
            <li>
              <div class="step_text" data-aos="fade-right" data-aos-duration="1500">
                <h4>{{__("Signup for FREE")}}</h4>
                <p>{{__("Signup for FREE and embark on your journey to explore, engage, and experience our platform without.")}}</p>
              </div>
              <div class="step_number number1">
                <h3>01</h3>
              </div>
              <div class="step_img" data-aos="fade-left" data-aos-duration="1500">
                <img src="{{$get_landing_language->how_it_work1??asset('assets/landing/new/images/download_app.png')}}" alt="image" >
              </div>
            </li>

            <!-- step -->
            <li>
              <div class="step_text" data-aos="fade-left" data-aos-duration="1500">
                <h4>{{__("No Credit Card Required")}}</h4>
                <p>{{__("Experience our full range of features with a 7-day FREE trial – no credit card required. It`s that simple.")}}</p>
              </div>
              <div class="step_number number2"> 
                <h3>02</h3>
              </div>
              <div class="step_img" data-aos="fade-right" data-aos-duration="1500">
                <img src="{{$get_landing_language->how_it_work2??asset('assets/landing/new/images/create_account.png')}}" alt="image" >
              </div>
            </li>

            <!-- step -->
            <li>
              <div class="step_text" data-aos="fade-right" data-aos-duration="1500">
                <h4>{{__("Have any Question?")}}</h4>
                <p>{{__("Simplify your experience by exploring our FAQ – a hub of answers crafted to address your inquiries swiftly and comprehensively.")}}</p>
              </div>
              <div class="step_number number3">
                <h3>03</h3>
              </div>
              <div class="step_img" data-aos="fade-left" data-aos-duration="1500">
                <img src="{{$get_landing_language->how_it_work3??asset('assets/landing/new/images/enjoy_app.png')}}" alt="image" >
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- video section start -->
      @if(isset($get_landing_language->header_image))
      <div class="yt_video" data-aos="fade-in" data-aos-duration="1500">
        <div class="thumbnil">
          <img src="{{asset('assets/landing/new/images/yt_thumb.png')}}" alt="image" >
          <a class="popup-youtube play-button" data-url="{{$get_landing_language->header_image??asset('assets/landing/video/intro.mp4')}}" data-toggle="modal" data-target="#myModal" title="XJj2PbenIsU">
            <span class="play_btn">
              <img src="{{asset('assets/landing/new/images/play_icon.png')}}" alt="image" >
              <div class="waves-block">
                <div class="waves wave-1"></div>
                <div class="waves wave-2"></div>
                <div class="waves wave-3"></div>
              </div>
            </span>
            {{__("Let`s see virtually how it works")}}
            <span>{{__("Watch video")}}</span>
          </a>
        </div>
      </div>
      @endif
      <!-- video section end -->
    </div>
    <!-- container end -->

  </section>
  <!-- How-It-Workes-Section-end -->

  <!-- Testimonial-Section start -->
  <?php
    $review_found=0;
    $review_str = '';
    for($i=1;$i<=3;$i++):
        $var1 = "review_".$i."_description";
        $var2 = "review_".$i."_avatar";
        $var3 = "review_".$i."_name";
        $var4 = "review_".$i."_designation";
        if(!isset($get_landing_language->$var1) || !isset($get_landing_language->$var2) || !isset($get_landing_language->$var3) || !isset($get_landing_language->$var4)) continue;
        if(empty($get_landing_language->$var1) && empty($get_landing_language->$var2) && empty($get_landing_language->$var3) && empty($get_landing_language->$var4)) continue;
        
        $review_str .= '<div class="item">
                          <div class="testimonial_slide_box">
                            <p class="review">
                              '.display_landing_content($get_landing_language->$var1).'
                            </p>
                            <div class="testimonial_img">
                              <img width="100" class="rounded-circle" height="100" width="100" src="'.$get_landing_language->$var2.'" alt="testimonial-image" >
                            </div>
                            <h3>'.display_landing_content($get_landing_language->$var3).'</h3>
                            <span class="designation">'.display_landing_content($get_landing_language->$var4).'</span>
                          </div>
                        </div>';
        $review_found++;
    endfor;
  ?>
  @if($review_found>0 && $disable_review_section=='0')
  <section class="row_am testimonial_section"> 

    <!-- container start -->
    <div class="container">
      <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
        <!-- h2 -->
        <h2><span>{{__("What Our Clients Say About Us")}}</span></h2>
      </div>
      <div class="testimonial_block" data-aos="fade-in" data-aos-duration="1500">
        <div id="testimonial_slider" class="owl-carousel owl-theme">
          {!!$review_str!!}
        </div>
        <!-- avtar faces -->
        <div class="avtar_faces">
          <img src="{{asset('assets/landing/new/images/avtar_testimonial.png')}}" alt="image" >
        </div>
      </div>
    </div>
    <!-- container end -->
  </section>
  @endif
  <!-- Testimonial-Section end -->

  <br><br><br>
  <!-- Beautifull-interface-Section start -->
  <section class="row_am interface_section">
    <!-- container start -->
      <div class="container-fluid">
        <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
            <!-- h2 -->
            <h2>{{__("Beautiful")}} <span>{{__("Interface")}}</span></h2>
            <!-- p -->
            <p>
              {{__("Take a visual tour through our app`s functionality and design excellence – explore the essence of innovation in every screenshot")}}
            </p>
        </div>

        <!-- screen slider start -->
          <div class="screen_slider" >
            <div id="screen_slider" class="owl-carousel owl-theme">
              <div class="item">
                <div class="screen_frame_img">
                    <img src="{{$get_landing_language->ui_image1??asset('assets/landing/new/images/screen-1.png')}}" alt="image" >
                </div>
              </div>
              <div class="item">
                <div class="screen_frame_img">
                    <img src="{{$get_landing_language->ui_image2??asset('assets/landing/new/images/screen-2.png')}}" alt="image" >
                </div>
              </div>
              <div class="item">
                <div class="screen_frame_img">
                    <img src="{{$get_landing_language->ui_image3??asset('assets/landing/new/images/screen-3.png')}}" alt="image" >
                </div>
              </div>
              <div class="item">
                <div class="screen_frame_img">
                    <img src="{{$get_landing_language->ui_image4??asset('assets/landing/new/images/screen-4.png')}}" alt="image" >
                </div>
              </div>
              <div class="item">
                <div class="screen_frame_img">
                    <img src="{{$get_landing_language->ui_image5??asset('assets/landing/new/images/screen-5.png')}}" alt="image" >
                </div>
              </div>
              <div class="item">
                <div class="screen_frame_img">
                    <img src="{{$get_landing_language->ui_image6??asset('assets/landing/new/images/screen-3.png')}}" alt="image" >
                </div>
              </div>
          </div>
          </div>
          <!-- screen slider end -->
      </div>
      <!-- container end -->
  </section>
  <!-- Beautifull-interface-Section end -->

  <!-- FAQ-Section start -->
  <section class="row_am faq_section">
    <!-- section bg -->
    <div class="faq_bg"> <img src="{{asset('assets/landing/new/images/section-bg.png')}}" alt="image" > </div>
    <!-- container start -->
    <div class="container">
      <div class="section_title" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
        <!-- h2 -->
        <h2><span>{{__("FAQ")}}</span> - {{__("Frequently Asked Questions")}}</h2>
        <!-- p -->
        <p>{{__("Discover answers to common queries effortlessly – our Frequently Asked Questions section is your go-to resource for quick and comprehensive information.")}}</p>
      </div>
      <!-- faq data -->
      <div class="faq_panel">
        <div class="accordion" id="accordionExample">
          <div class="card" data-aos="fade-up" data-aos-duration="1500">
            <div class="card-header" id="headingOne">
              <h2 class="mb-0">
                <button type="button" class="btn btn-link active" data-toggle="collapse" data-target="#collapseOne">
                  <i class="icon_faq icofont-plus"></i></i> {{__('How can :appname benefit Telegram group administrators?',['appname'=>config('app.name')])}}</button>
              </h2>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
              <div class="card-body">
                <p>{{__(':appname`s filtering and antispam features enable Telegram group administrators to maintain a vibrant and engaged community. By automatically removing spam, irrelevant content, and unwanted media, :appname creates a clutter-free space for meaningful interactions. It empowers administrators to curate content, promote genuine engagement, and enhance member satisfaction, ultimately building a thriving and spam-free Telegram community.',['appname'=>config('app.name')])}}</p>
              </div>
            </div>
          </div>
          <div class="card" data-aos="fade-up" data-aos-duration="1500">
            <div class="card-header" id="headingTwo">
              <h2 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse"
                  data-target="#collapseTwo"><i class="icon_faq icofont-plus"></i></i> {{__('How can :appname help in creating a spam-free Telegram community?',['appname'=>config('app.name')])}}</button>
              </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
              <div class="card-body">
                <p>{{__(':appname offers advanced filtering and antispam features that empower group administrators to maintain a clean and focused chat environment. It automatically removes various types of messages, such as those containing bot commands, images, voice recordings, documents, stickers, GIFs, links, and forwarded messages with media or links. Additionally, it provides keyword surveillance to automatically remove messages containing censor words, promoting a respectful and inclusive community.',['appname'=>config('app.name')])}}</p>
              </div>
            </div>
          </div>
          <div class="card" data-aos="fade-up" data-aos-duration="1500">
            <div class="card-header" id="headingThree">
              <h2 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse"
                  data-target="#collapseThree"><i class="icon_faq icofont-plus"></i></i>{{__('How does keyword surveillance contribute to a respectful community?')}}</button>
              </h2>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
              <div class="card-body">
                <p>{{__(':appname`s keyword surveillance feature automatically removes messages that contain censor words. By doing so, it promotes a safe and welcoming space for all members, discouraging the use of offensive or inappropriate language within the group.',['appname'=>config('app.name')])}}</p>
              </div>
            </div>
          </div>
          <div class="card" data-aos="fade-up" data-aos-duration="1500">
            <div class="card-header" id="headingFour">
              <h2 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse"
                  data-target="#collapseFour"><i class="icon_faq icofont-plus"></i></i>{{__('What is the Member Message Limitation, and how does it tackle spam?')}}</button>
              </h2>
            </div>
            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
              <div class="card-body">
                <p>{{__('The Member Message Limitation feature is a powerful Antispam feature set. It automatically deletes repetitive messages from members, preventing message flooding and encouraging members to share unique and relevant content. It also categorizes identical messages as spam, discouraging spamming behavior. Furthermore, the system can apply temporary mutes to repeat spam offenders to promote responsible and respectful behavior. Group administrators have control over message frequency, allowing them to strike a balance between active group discussions and preventing spam overload.')}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- container end -->
  </section>
  <!-- FAQ-Section end -->

  <!-- Download-Free-App-section-Start  -->
  <section class="row_am free_app_section" id="getstarted">
    <!-- container start -->
      <div class="container">
          <div class="free_app_inner" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="100"> 
              <!-- row start -->
              <div class="row">
                <!-- content -->
                  <div class="col-md-6">
                      <div class="free_text">
                          <div class="section_title">
                              <h2>{{__("Ready to Join?")}}</h2>
                              <p>{{__("Now that you`re well-versed with all the details, are you prepared to take the next step and join us? Your journey awaits, and we`re excited to welcome you on board!")}}</p>
                          </div>
                          <ul class="app_btn">
                            <li class="ml-0">
                              <a href="{{route('register')}}">{{__("Join Now")}}</a>
                            </li>
                            </li>
                          </ul>
                      </div>
                  </div>

                  <!-- images -->
                  <div class="col-md-6">
                      <div class="free_img">
                          <img src="{{$get_landing_language->start_using_image1??asset('assets/landing/new/images/download-screen01.png')}}" alt="image" >
                          <img class="mobile_mockup" src="{{$get_landing_language->start_using_image2??asset('assets/landing/new/images/download-screen02.png')}}" alt="image" >
                      </div>
                  </div>
              </div>
              <!-- row end -->
          </div>
      </div>
      <!-- container end -->
  </section>
  <!-- Download-Free-App-section-end  -->

  <!-- VIDEO MODAL -->
  <div class="modal fade youtube-video" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <button id="close-video" type="button" class="button btn btn-default text-right" data-dismiss="modal">
            <i class="icofont-close-line-circled"></i>
          </button>
            <div class="modal-body">
                <div id="video-container" class="video-container">
                    <iframe id="youtubevideo" src="" width="640" height="360" frameborder="0" allowfullscreen></iframe>
                </div>        
            </div>
            <div class="modal-footer">
            </div>
        </div> 
    </div>
  </div>

  
@endsection

@push('scripts-header')
  <script>
    "use strict";
    var typingKeywords = <?php echo $typingKeywords; ?>;
    var registerURL = "{{route('register')}}";
  </script>

  <script src="{{ asset('assets/landing/new/index.js') }}"></script>
@endpush