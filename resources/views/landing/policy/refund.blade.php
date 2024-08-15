@extends('layouts.landing')
@section('title',$title)
@section('meta_title',$meta_title)
@section('meta_description',$meta_description)
@section('meta_keyword',$meta_keyword)
@section('meta_author',$meta_author)
@section('meta_image',$meta_image)
@section('meta_image_width',$meta_image_width)
@section('meta_image_height',$meta_image_height)
@section('content')
<section class="pt-[130px]" id="policy-page">
  <div class="wow fadeInUp relative z-10 bg-cover bg-center bg-no-repeat py-20 lg:py-[120px]" data-wow-delay=".2s">
    <div class="absolute top-0 left-0 z-10 h-full w-full bg-cover bg-center opacity-20 mix-blend-overlay bg-noise-pattern"></div>
    <div class="absolute top-0 left-0 -z-10 h-full w-full bg-[#EEF1FDEB] dark:bg-[#1D232DD9]"></div>
    <div class="px-4 xl:container">
      <div class="mx-auto max-w-[580px] text-center">
        <h1 class="font-heading text-3xl font-semibold text-dark dark:text-white md:text-[44px] md:leading-tight">
          {{$title}}
        </h1>
      </div>
    </div>
  </div>
  <div class="px-4 pt-24 xl:container">
    <div class="border-b pb-20 dark:border-[#2E333D] lg:pb-[130px]">
          <h4 class="text-base text-dark-text mb-4">{{__('Application is not as described')}}</h4>
          <p class="mb-12 text-base text-dark-text">
            {{__('An application is `not as described` if it is materially different from the application description or preview so be sure to `tell it like it is` when it comes to the features and functionality of items. If it turns out the application is `not as described` we are obligated to refund buyers of that item.')}}
          </p>

          <h4 class="text-base text-dark-text mb-4">{{__('Application doesn`t work the way it should')}}</h4>
          <p class="mb-12 text-base text-dark-text">{{__('If an application doesn`t work the way it should and can`t easily be fixed we are obligated to refund buyers of the application. This includes situations where application has a problem that would have stopped a buyer from buying it if they`d known about the problem in the first place. If the application can be fixed, then we do so promptly by updating our application otherwise we are obligated to refund buyers of that application.')}}
          </p>

          <h4 class="text-base text-dark-text mb-4">{{__('Application has a security vulnerability')}}</h4>
          <p class="mb-12 text-base text-dark-text">{{__('If an application contains a security vulnerability and can`t easily be fixed we are obligated to refund buyers of the application. If the application can be fixed, then we do so promptly by updating our application. If our application contains a security vulnerability that is not patched in an appropriate timeframe then we are obligated to refund buyers of that application.')}}</p>

          <h4 class="text-base text-dark-text mb-4">{{__('Application support is promised but not provided')}}</h4>
          <p class="mb-12 text-base text-dark-text">{{__('If we promise our buyers application support and we do not provide that support in accordance with the application support policy we are obligated to refund buyers who have purchased support.')}}</p>

          <h4 class="text-base text-dark-text mb-4">{{__('No refund scenario')}}</h4>
          <p class="mb-2 text-base text-dark-text">{{__('If our application is materially similar to the description and preview and works the way it should, there is generally no obligation to provide a refund in situations like the following:')}}
        </p>

          <ul style="list-style-type:circle" class="mt-0">
            <li>{{__('Buyer doesn`t want it after they`ve purchase it.')}}</li>
            <li>{{__('The application did not meet the their expectations.')}}</li>
            <li>{{__('Buyer is not satisfied with the current feature availability of the service.')}}</li>
            <li>{{__('Buyer simply change their mind.')}}</li>
            <li>{{__('Buyer bought a service by mistake.')}}</li>
            <li>{{__('Buyer do not have sufficient expertise to use the application.')}}</li>
            <li>{{__('Buyer ask for goodwill.')}}</li>
            <li>{{__('Problems originated from the API providing organization.')}}</li>
            <li>{{__('No refund will be provided after 30 days from the purchase of a service.')}}</li>
          </ul>


        <br>
        <br>
        <h4 class="text-base text-dark-text mb-4">{{__('Force Refund')}}</h4>
        <p class="mb-12 text-base text-dark-text">{{__('We hold the authority to refund buyer purchase by force without any request from buyer end. Force refund will stop app access as well as support access by denying purchase code with immediate action.')}}</p>

        <h4 class="text-base text-dark-text mb-4">{{__('Refund Request')}}</h4>
        <p class="mb-12 text-base text-dark-text">{{__('If a buyer eligible to get a refund then he/she must open a support ticket.')}}</p>
    </div>
  </div>
</section>
@endsection
