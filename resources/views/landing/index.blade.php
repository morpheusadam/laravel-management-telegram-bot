@extends('layouts.landing')
@section('title',$meta_title)
@section('meta_title',$meta_title)
@section('meta_description',$meta_description)
@section('meta_keyword',$meta_keyword)
@section('meta_author',$meta_author)
@section('meta_image',$meta_image)
@section('meta_image_width',$meta_image_width)
@section('meta_image_height',$meta_image_height)
@section('content')



  <!-- ===== Hero Section Start ===== -->
  @include('landing.partials.hero')
  <!-- ===== Hero Section End ===== -->

  <section id="about" class="pt-14 sm:pt-20">

    <div class="px-4 xl:container">
      <!-- Section Title -->

      <div
        class="relative mx-auto mb-12 pt-6 text-center lg:mb-20 lg:pt-16"
        data-wow-delay=".2s"
      >
        <span class="title">{{__("Features")}}</span>
        <h2
          class="mx-auto mb-5 max-w-[570px] font-heading text-3xl font-semibold text-primary sm:text-4xl md:text-[50px] md:leading-[60px]">{{__("Unique & Awesome Core Features")}}</h2>
        <p class="mx-auto max-w-[570px] text-base text-dark dark:text-white">{{__("Everything you will need to boost your work is right at your fingertips. Take a look at what you can do and how to get started.")}}</p>
      </div>   
      

      <div class="mid">
        <div class="w-full">
            {{-- section one --}}
            <div class="-mx-4 flex flex-wrap items-center"> 
              <div class="w-full px-4 lg:w-1/2 ">
               <div class="image-height">
                <div>
                  <div>
                    <img src="{{$get_landing_language->details_feature_2_img??asset('assets/landing/images/about/about-image-1.png')}}" alt="about-image" />
                  </div>
                </div>
               </div>
              </div>
              <div class="w-full px-4 lg:w-1/2">
                <div class="lg:ml-auto">
                  <h2 class="mb-8 font-heading text-2xl font-bold text-dark dark:text-white sm:text-[35px] sm:leading-[45px]">{{__("Filter Members Messages")}}</h2>
                  <p class="mb-6 text-base lg:text-lg text-dark dark:text-white">{{__("Pomote a clean and focused group chat by implementing message filtering options. Remove bot commands, images, voice recordings, attached documents, stickers, GIFs, member dice rolls, and links to ensure a clutter-free and productive environment. Enhance communication efficiency and maintain a professional atmosphere while preserving group security. Experience more meaningful interactions and increased productivity for all members.")}}</p>
                </div>              
              </div>
            </div>
            {{-- section two  --}}
            <div class="-mx-4 flex flex-wrap items-center">
              <div class="w-full px-4 lg:w-1/2">
               <div class="image-height">
                <div>
                  <div>
                    <img src="{{$get_landing_language->details_feature_3_img??asset('assets/landing/images/about/about-image-2.png')}}" alt="about-image" />
                  </div>
                </div> 
                </div>   
              </div>            
              <div class="w-full px-4 lg:w-1/2 lg:order-first">
               <div class="lg:ml-auto">
                  <h2 class="mb-8 font-heading text-2xl font-bold text-dark dark:text-white sm:text-[35px] sm:leading-[45px]">{{__("Filter Forwarded Messages")}}</h2>
                  <p class="mb-6 text-base lg:text-lg text-dark dark:text-white">{{__(":appname ensures a clean and focused group chat by efficiently handling forwarded messages. It filters out forwarded messages with media attachments and URLs, reducing clutter and enhancing security. For a more organized group experience, it also offers the option to remove all forwarded messages. Enjoy a seamless and clutter-free chat environment with appname`s comprehensive message filtering capabilities.",["appname"=>config('app.name')])}}</p>
                </div>  
              </div>
            </div>
            {{-- section three  --}}
            <div class="-mx-4 flex flex-wrap items-center">
              <div class="w-full px-4 lg:w-1/2">
               <div class="image-height">
                <div>
                  <div>
                    <img src="{{$get_landing_language->details_feature_4_img??asset('assets/landing/images/about/about-image-1.png')}}" alt="about-image" />
                  </div>
                </div>
               </div>
              </div>
              <div class="w-full px-4 lg:w-1/2">
                <div class="lg:ml-auto">
                  <h2 class="mb-8 font-heading text-2xl font-bold text-dark dark:text-white sm:text-[35px] sm:leading-[45px]">{{__("Keyword Surveillance")}}</h2>
                  <p class="mb-6 text-base lg:text-lg text-dark dark:text-white">{{__(":appname keeps your group chat safe and focused. It automatically removes messages with censor words and efficiently filters forwarded messages, including those with media attachments, URLs, or all forwarded messages. Enjoy a clutter-free, secure, and respectful environment for meaningful interactions with :appname`s vigilant monitoring.",["appname"=>config('app.name')])}}</p>
                </div>              
              </div>
            </div>
            {{-- section four  --}}
            <div class="-mx-4 flex flex-wrap items-center">
              <div class="w-full px-4 lg:w-1/2">
               <div class="image-height">
                <div>
                  <div>
                    <img src="{{$get_landing_language->details_feature_5_img??asset('assets/landing/images/about/about-image-2.png')}}" alt="about-image" />
                  </div>
                </div> 
                </div>  
              </div>   
              <div class="w-full px-4 lg:w-1/2 lg:order-first">
                <div class="lg:ml-auto">
                  <h2 class="mb-8 font-heading text-2xl font-bold text-dark dark:text-white sm:text-[35px] sm:leading-[45px]">{{__(
                  "Service Message Control")}}</h2>
                  <p class="mb-6 text-base lg:text-lg text-dark dark:text-white">{{__(":appname`s service message control helps you keep your group announcements and updates organized and professional. With this feature, you can delete `User Joined the Group` messages to maintain a clean announcements section and remove `User Left the Group` messages for a more polished appearance. Enjoy a clutter-free and streamlined group communication, ensuring a professional environment for all members.",["appname"=>config('app.name')])}}
                  </div>              
              </div>
            </div>
            {{-- section five --}}
            <div class="-mx-4 flex flex-wrap items-center">
              <div class="w-full px-4 lg:w-1/2">
               <div class="image-height">
                <div>
                  <div>
                    <img src="{{$get_landing_language->details_feature_6_img??asset('assets/landing/images/about/about-image-1.png')}}" alt="about-image" />
                  </div>
                </div>
               </div>
              </div>
              <div class="w-full px-4 lg:w-1/2">
                <div class="lg:ml-auto">
                  <h2 class="mb-8 font-heading text-2xl font-bold text-dark dark:text-white sm:text-[35px] sm:leading-[45px]">{{__("New Members Restriction")}}</h2>
                  <p class="mb-6 text-base lg:text-lg text-dark dark:text-white">{{__("With :appname, you have the power to set restrictions for new group members, ensuring a smooth onboarding process and protection against spam or disruptive behavior. Decide the duration of the restrictions, granting you control over the group dynamics and fostering a secure and harmonious community.",["appname"=>config('app.name')])}}</p>
                </div>              
              </div>
            </div>
            {{-- section six  --}}
            <div class="-mx-4 flex flex-wrap items-center">
              <div class="w-full px-4 lg:w-1/2">
               <div c>
                <div>
                  <div>
                    <img src="{{$get_landing_language->details_feature_7_img??asset('assets/landing/images/about/about-image-2.png')}}" alt="about-image" />
                  </div>
                </div>
                </div>        
              </div>            
              <div class="w-full px-4 lg:w-1/2 lg:order-first">
                <div class="lg:ml-auto">
                  <h2 class="mb-8 font-heading text-2xl font-bold text-dark dark:text-white sm:text-[35px] sm:leading-[45px]">{{__(
                  "Member Message Limitation")}}</h2>
                  <p class="mb-6 text-base lg:text-lg text-dark dark:text-white">{{__(":appname enhances group communication, streamlining it and ensuring security. It prevents message flooding and clutter by deleting repeated messages and deters spamming through content identification. Responsible behavior is promoted with temporary user muting for message limit violations. Administrators have control over message frequency for better group dynamics and a more organized communication environment.",["appname"=>config('app.name')])}}</p>
                </div>   
              </div>
            </div>
        </div>
      </div>
    

    </div>
  </section>


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

  <section id="" class="pt-14 sm:pt-20 lg:pt-[100px]">
    <div class="px-4 xl:container-fluid">
      <!-- Section Title -->
      <div class="relative mx-auto mb-12 pt-6 text-center lg:mb-20 lg:pt-16" data-wow-delay=".2s">
        <span class="title"> {{__("Use Cases")}} </span>
        <h2 class="mx-auto mb-5 max-w-[570px] font-heading text-3xl font-semibold text-primary sm:text-4xl md:text-[50px] md:leading-[60px]"> {{__(":appname for Everyone",["appname"=>config('app.name')])}} </h2>
        <p class="mx-auto max-w-[570px] text-base text-dark dark:text-white"> {{__("Innovative use cases across industries and professions")}} </p>
      </div>

      <div class="relative z-10 overflow-hidden rounded px-8 pt-0 pb-8 md:px-[70px] md:pb-[70px] lg:px-[60px] lg:pb-[60px] xl:px-[70px] xl:pb-[70px]" data-wow-delay=".3s">
        <div class="absolute top-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern"></div>

        <div class="px-4 xl:container">
          <div class="-mx-4 flex flex-wrap items-center">
            @foreach($use_cases as $use_case)
            <div class="w-full px-3 lg:w-1/3">
              <div class="mx-auto mb-12 max-w-[530px] text-center lg:ml-1 lg:mb-0 lg:text-left">
                <h1 class="cs-use-case-heading text-dark lg:text-white mb-5 font-heading text-2xl font-semibold sm:text-4xl md:text-[500px] md:leading-[60px]" data-wow-delay=".3s"> {{$use_case['title']}}</h1>
                <p class="mb-12 text-base text-dark dark:text-white" data-wow-delay=".4s"> {{$use_case['description']}}</p>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ===== Testimonial Section Start ===== -->
  <section id="cta2" class="pt-28">
    @include('landing.partials.cta')
  </section>
  <!-- ===== Testimonial Section End ===== -->

  <br>
  <br>
  <br>
  <br>
  <!-- ===== FAQ start ===== -->
  <section id="faq" class="ud-pb-[100px] ud-overflow-hidden">
    <div class="ud-container">
      <div class="ud-flex ud-justify-center ud--mx-4">
        <div class="ud-w-full ud-px-4">
          <div
            class="
              ud-max-w-[510px] ud-mx-auto ud-text-center ud-mb-[70px]
              wow
              fadeInUp
            "
            data-wow-delay=".2s"
          >
            <h2
              class="
                ud-font-extrabold ud-text-3xl
                sm:ud-text-4xl
                text-primary
                ud-mb-5
              "
            >
              {{__('Frequently Asked Questions')}}
            </h2>
            <p class="text-base dark:text-white">
              {{__('Still have any question? Answered.')}}
            </p>
          </div>
        </div>
      </div>

      <div class="ud-flex ud-flex-wrap ud--mx-4">
        <div class="ud-w-full lg:ud-w-1 ud-px-4">
          <div class="ud-mb-6 lg:ud-mb-0 ud-relative ud-z-10">
            <div class="ud-bg-white dark:ud-bg-dark ud-border ud-border-[#e4f2fe] ud-rounded-2xl ud-py-12 ud-px-8 sm:ud-p-12 md:ud-py-14 lg:ud-py-10 lg:ud-px-8 xl:ud-p-12 2xl:ud-p-14 wow fadeInUp" data-wow-delay=".3s">
              <div class="">
                <h3 class="ud-font-bold ud-text-xl sm:ud-text-2xl lg:ud-text-xl xl:ud-text-2xl ud-text-black dark:text-white ud-mb-4">
                  {{__('How can :appname benefit Telegram group administrators?',['appname'=>config('app.name')])}}
                </h3>
                <p class="text-base dark:text-white">
                  {{__(':appname`s filtering and antispam features enable Telegram group administrators to maintain a vibrant and engaged community. By automatically removing spam, irrelevant content, and unwanted media, :appname creates a clutter-free space for meaningful interactions. It empowers administrators to curate content, promote genuine engagement, and enhance member satisfaction, ultimately building a thriving and spam-free Telegram community.',['appname'=>config('app.name')])}}
                </p>
              </div>
            </div>
          </div>
          <p>&nbsp;</p>
          <div class="ud-mb-6 lg:ud-mb-0 ud-relative ud-z-10">
            <div class="ud-bg-white dark:ud-bg-dark ud-border ud-border-[#e4f2fe] ud-rounded-2xl ud-py-12 ud-px-8 sm:ud-p-12 md:ud-py-14 lg:ud-py-10 lg:ud-px-8 xl:ud-p-12 2xl:ud-p-14 wow fadeInUp" data-wow-delay=".3s">
              <div class="">
                <h3 class="ud-font-bold ud-text-xl sm:ud-text-2xl lg:ud-text-xl xl:ud-text-2xl ud-text-black dark:text-white ud-mb-4">
                  {{__('How can :appname help in creating a spam-free Telegram community?',['appname'=>config('app.name')])}}
                </h3>
                <p class="text-base dark:text-white">
                  {{__(':appname offers advanced filtering and antispam features that empower group administrators to maintain a clean and focused chat environment. It automatically removes various types of messages, such as those containing bot commands, images, voice recordings, documents, stickers, GIFs, links, and forwarded messages with media or links. Additionally, it provides keyword surveillance to automatically remove messages containing censor words, promoting a respectful and inclusive community.',['appname'=>config('app.name')])}}
                </p>
              </div>
            </div>
          </div>
          <p>&nbsp;</p>
          <div class="ud-mb-6 lg:ud-mb-0 ud-relative ud-z-10">
            <div class="ud-bg-white dark:ud-bg-dark ud-border ud-border-[#e4f2fe] ud-rounded-2xl ud-py-12 ud-px-8 sm:ud-p-12 md:ud-py-14 lg:ud-py-10 lg:ud-px-8 xl:ud-p-12 2xl:ud-p-14 wow fadeInUp" data-wow-delay=".3s">
              <div class="">
                <h3 class="ud-font-bold ud-text-xl sm:ud-text-2xl lg:ud-text-xl xl:ud-text-2xl ud-text-black dark:text-white ud-mb-4">
                  {{__('How does keyword surveillance contribute to a respectful community?')}}
                </h3>
                <p class="text-base dark:text-white">
                  {{__(':appname`s keyword surveillance feature automatically removes messages that contain censor words. By doing so, it promotes a safe and welcoming space for all members, discouraging the use of offensive or inappropriate language within the group.',['appname'=>config('app.name')])}}
                </p>
              </div>
            </div>
          </div>
          <p>&nbsp;</p>
          <div class="ud-mb-6 lg:ud-mb-0 ud-relative ud-z-10">
            <div class="ud-bg-white dark:ud-bg-dark ud-border ud-border-[#e4f2fe] ud-rounded-2xl ud-py-12 ud-px-8 sm:ud-p-12 md:ud-py-14 lg:ud-py-10 lg:ud-px-8 xl:ud-p-12 2xl:ud-p-14 wow fadeInUp" data-wow-delay=".3s">
              <div class="">
                <h3 class="ud-font-bold ud-text-xl sm:ud-text-2xl lg:ud-text-xl xl:ud-text-2xl ud-text-black dark:text-white ud-mb-4">
                  {{__('What is the Member Message Limitation, and how does it tackle spam?')}}
                </h3>
                <p class="text-base dark:text-white">
                  {{__('The Member Message Limitation feature is a powerful Antispam feature set. It automatically deletes repetitive messages from members, preventing message flooding and encouraging members to share unique and relevant content. It also categorizes identical messages as spam, discouraging spamming behavior. Furthermore, the system can apply temporary mutes to repeat spam offenders to promote responsible and respectful behavior. Group administrators have control over message frequency, allowing them to strike a balance between active group discussions and preventing spam overload.')}}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ===== FAQ end ===== -->

  <!-- ===== CTA Section Start ===== -->
  <section id="cta" class="pt-14 sm:pt-20">
    <div class="px-4 xl:container">
      <div class="relative overflow-hidden bg-cover bg-center py-[60px] px-10 drop-shadow-light dark:drop-shadow-none sm:px-[70px]" data-wow-delay=".2s">
        <div class="absolute top-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern"></div>
        <div class="absolute bottom-0 left-1/2 -z-10 -translate-x-1/2">
          <svg width="1215" height="259" viewBox="0 0 1215 259" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.6" filter="url(#filter0_f_63_363)">
              <rect x="450" y="189" width="315" height="378" fill="url(#paint0_linear_63_363)" />
            </g>
            <defs>
              <filter id="filter0_f_63_363" x="0" y="-261" width="1215" height="1278" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                <feGaussianBlur stdDeviation="225" result="effect1_foregroundBlur_63_363" />
              </filter>
              <linearGradient id="paint0_linear_63_363" x1="420.718" y1="263.543" x2="585.338" y2="628.947" gradientUnits="userSpaceOnUse">
                <stop stop-color="#ABBCFF" />
                <stop offset="0.859375" stop-color="#4A6CF7" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="-mx-4 flex flex-wrap items-center">
          <div class="w-full px-4 lg:w-2/3">
            <div class="mx-auto text-center lg:ml-0 lg:mb-0 lg:text-left">
              <h2 class="mb-4 font-heading text-xl font-semibold leading-tight text-dark dark:text-white sm:text-[38px]"> {{__("Ready to Join?")}} </h2>
              <p class="text-base text-dark dark:text-white mb-2"> {{__("Join the club of group admins")}} </p>
            </div>
          </div>
          <div class="w-full px-4 lg:w-1/3">
            <div class="text-center lg:text-right">
              <a href="{{route("register")}}" class="inline-flex items-center rounded bg-primary py-[14px] px-8 font-heading text-base text-white hover:bg-opacity-90"> {{__("Get Started Now")}} </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ===== CTA Section End ===== -->


  <!-- ===== Testimonial Section Start ===== -->
  @include('landing.partials.testimonial')
  <!-- ===== Testimonial Section End ===== -->

@endsection

@push('script-footer')
  <script src="{{asset('/assets/landing/main.js')}}"></script>
@endpush