<!-- ===== Pricing start ===== -->

<?php
$i=0;
$premium_packages = null;
$agency_packages = null;
$replace_array = [
    __('Day'),
    __('Month'),
    __('Week'),
    __('Year')
];
$module_description = [
  1  => __("You can connect :count Telegram bots"),
  9 => __("You can engage :count group members"),
  22  => __("You can manage :count filtering rules")
];
?>

<section id="pricing" class="pt-14">
    <div class="wow fadeInUp relative z-10 bg-cover bg-center bg-no-repeat py-20 lg:py-[120px]" data-wow-delay=".2s" style="background-image: url('/assets/landing/images/blog/blog-details-2.jpg')">
      <div class="absolute top-0 left-0 z-10 h-full w-full bg-cover bg-center opacity-20 mix-blend-overlay" style="background-image: url('/assets/landing/images/blog/NoisePatern.svg')"></div>
      <div class="absolute top-0 left-0 -z-10 h-full w-full bg-[#EEF1FDEB] dark:bg-[#1D232DD9]"></div>
      <div class="px-4 xl:container">
        <div class="mx-auto max-w-[580px] text-center">
          <h1 class="font-heading text-3xl font-semibold text-dark dark:text-white md:text-[44px] md:leading-tight">
            {{$title}}
          </h1>
        </div>
      </div>
    </div>

    <div class="px-4 xl:container">
      <!-- Section Title -->
      <div class="relative mb-12 w-full text-center md:mb-10" data-wow-delay=".2s">
       
        <h1 class="mx-auto mb-5 max-w-[600px] font-heading  font-semibold text-dark dark:text-white">
        {!!count($package_validity_list)>1 ? '&nbsp;' : __("More pricing plans are coming soon")!!}
        </h1>
        <p>
          @if(count($package_validity_list)>1)
                @foreach($package_validity_list as $kv=>$vv)
                    <a href="{{route('pricing-plan')}}?validity={{$kv}}"
                        class="
                          <?php if($kv==$default_validity) echo 'bg-primary';?>
                          cs-price-button
                          hover:bg-primary
                          inline-flex items-center rounded
                          bg-dark-text font-heading
                          text-base text-white
                          ">
                        {{$vv}}
                    </a>
                @endforeach
          @endif
        </p>
      </div>
    </div>
</section>


<section id="pricing" class="">
  <div class="ud-container">
    <div class="ud-flex ud-flex-wrap ud--mx-4 justify-center">
        <?php $range_values= []; ?>
        @foreach($get_pricing_list as $key=>$value)
            <?php
                $not_in_package = '';
                if($value->is_default=='0') {
                    if($value->is_agency=='1' && $value->is_whitelabel=='1') $agency_packages[] = (array) $value;
                    else {
                      $premium_packages[] = (array) $value;
                      $monthly_limit_pro = json_decode($value->monthly_limit,true);
                      $range_values[] = $monthly_limit_pro[9]??0;
                    }
                    continue;
                }
                $i++;

                $price = $value->price;

                $class = '';
                if($i==1 || $i==4) $class='first-item';
                if($i==3 || $i==6) $class='last-item';

                $validity = $value->validity;
                $validity_extra_info = $value->validity_extra_info;
                if($validity>0){
                    $validity_text = convert_number_validity_phrase($validity);;
                }
                if($validity==0) {
                    $validity_text = __('Forever');
                }
                $module_ids = explode(',',$value->module_ids);                
                $buy_button_name = __('Get Access');
            ?>
        @endforeach

        <?php $package_map = [];?>
        @if(!empty($premium_packages))
            <?php
                $count_premium = 0;
                $min_subscriber = 0;
                $first_package_id = 0;
                $first_package_price = 0;
                $first_package_discount_message = '';
                $first_package_discount_terms = '';
                $first_package_discount_percent = '';
                $first_package_validity = '';
                $first_package_name = '';
                $premium_package_li_str = '';
            ?>
            @foreach($premium_packages as $key=>$value)
                @php
                    $monthly_limit_temp = json_decode($value['monthly_limit'],true);
                    $discount_data = $value['discount_data'];
                    $price_raw_data = format_price($value['price'],$format_settings,$discount_data,['return_raw_array'=>true]);

                    $discount_message = '';
                    if(isset($price_raw_data->discount_valid) && $price_raw_data->discount_valid)
                    $discount_message = __('Save').' '.$price_raw_data->discount_amount_formatted_currency;
                    $package_subscriber_map = convert_number_numeric_phrase($monthly_limit_temp[1],0) ?? 0;
                    $package_id_map = $value['id'];
                    $package_name = $value['package_name'];


                    $validity = $value['validity'];
                    $validity_extra_info = $value['validity_extra_info'];
                    $validity_unit = __('Day');
                    if($validity>0){

                        $validity_text = convert_number_validity_phrase($validity);
                    }
                    if($validity==0) {
                        $validity_text = __('Forever');
                    }

                    $package_discount_percent = $price_raw_data->discount_percent;
                    $package_discount_percent = $package_discount_percent>0  ? " ".$package_discount_percent.'% '.__('OFF') : '';

                    if($count_premium==0){
                        $min_subscriber = $package_subscriber_map;
                        $first_package_id = $package_id_map;
                        $first_package_price = $price_raw_data->display_price_currency ?? '';
                        $first_package_discount_message = $discount_message;
                        $first_package_discount_terms = $price_raw_data->discount_terms;
                        $first_package_validity = $validity_text;
                        $first_package_name = $package_name;
                        $first_package_discount_percent = $package_discount_percent;
                    }

                    $count_premium++;
                    $package_price_map = $price_raw_data->display_price_currency ?? '';
                    $package_map[$count_premium] = ['id'=>$package_id_map,'price'=>$package_price_map,'subscriber'=>$package_subscriber_map,'discount_message'=>$discount_message,"validity_text"=>$validity_text,'name'=>$package_name,'terms'=>$price_raw_data->discount_terms,'percent'=>$package_discount_percent];

                    $module_ids = explode(',',$value['module_ids']);
                    foreach($get_modules as $key2=>$value2):

                        $li_class = 'text-center w-100 ud-font-semibold ud-text-base text-dark dark:text-white premium-li premium-'.$count_premium;
                        $hide_other_package_unavailable_module = $count_premium>1 ? 'cs-d-none' : '';
                        if(!in_array($value2->id,$module_ids)) {
                            $premium_package_li_str .= '
                            <p class="'.$li_class.' '.$hide_other_package_unavailable_module.'">
                                '.__("No access").' : 
                                <del>'.$value2->module_name.'</del>
                            </p>';
                            continue;
                        }                        

                        
                        else $module_name = $value2->module_name;
                        $limit=0;
                        $limit=convert_number_numeric_phrase($monthly_limit_temp[$value2->id],0);
                        if($limit=="0") $limit2=__('Unlimited');
                        else $limit2=$limit;
                        if($value2->extra_text!='' && $limit>0)
                            $limit2=$limit2."/".__('Month');

                        if(isset($module_description[$value2->id])){
                          $module_name = str_replace(':count','<span class="text-primary">'.$limit2.'</span>',$module_description[$value2->id]);
                        }
                        if($count_premium>1) $li_class .= ' cs-d-none';
                        $premium_package_li_str .= '<p class="'.$li_class.'">'.$module_name.'</p>';
                    endforeach;
                @endphp
            @endforeach
            <div class="ud-w-full lg:w-2/3 px-4">
                <div class="ud-relative ud-overflow-hidden ud-py-10 ud-px-8 sm:ud-p-12 md:ud-px-8 lg:ud-px-5 xl:ud-px-9 2xl:ud-px-12 ud-rounded-[20px] ud-border-2 ud-border-[#f3eeff] dark:ud-border-black ud-mb-12 wow fadeInUp" data-wow-delay=".3s">
                  <br>
                  <p id="" class="ud-text-xl text-dark dark:text-white text-center pt-10"><span class="cs-d-inline" id="discount_extra_message">{{!empty($first_package_discount_terms) ? $first_package_discount_terms :__("Grow your business with access to pro features and increased limit.")}}</span><span  class="cs-d-inline">{{__("Pro plans are billed according to the size of your group members.")}}</span></p>                    

                  <div class="pt-10 ud-space-y-3">
                      <h4 class="ud-font-bold ud-text-2xl text-dark dark:text-white ud-mb-2 ud-block text-center">{{__("How many members you expect to engage?")}}</h4>
                      <div class="range-container w-full">
                        <input type="range" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" min="1" max="{{$count_premium}}" step="1" value="1" id="package_bot_subscriber_range">
                        <div class="range-label w-full mb-10" id="rangeLabels">
                          @foreach($range_values as $ri => $rv)
                            <button data-value="{{$ri+1}}">{!!$rv==0 ? "<i class='fas fa-infinity'></i>" : convert_number_numeric_phrase($rv,0)!!}</button>
                          @endforeach
                        </div>
                      </div>
                      <br>
                      <br>
                      <?php echo $premium_package_li_str;?>                 
                  </div>

                    <span class="pt-10 ud-font-bold ud-text-3xl text-dark dark:text-white ud-mb-2 ud-block text-center">
                        <span id="discount_percentage" class="ud-text-3xl text-warning font-bold hidden">{{!empty($first_package_discount_percent) ? " - ".$first_package_discount_percent : '';}}</span>              
                    </span>

                    <h3 class="ud-font-bold text-dark dark:text-white ud-text-[40px] text-center">
                      <span id="" class="text-dark-text dark:text-white">{{__("Cost")}}</span>
                      <span id="package_price" class="text-warning"><?php echo $first_package_price;?></span>
                        <span class="ud-font-normal ud-text-base text-dark dark:text-white" id="validity_text">{{$first_package_validity}}</span>
                      <p id="package_price_save" class="ud-text-2xl text-primary font-bold <?php echo !empty($first_package_discount_message) ? 'cs-d-block' : 'cs-d-none'; ?>"><?php echo $first_package_discount_message;?></p>       
                    </h3>
                    

                    <div class="pt-5 flex justify-center">
                        <a href="{{route('buy-package',$first_package_id)}}" id="package_link" class="inline-flex items-center rounded bg-primary py-[10px] px-6 font-heading text-base text-white  hover:bg-primary md:py-[14px] md:px-8">
                            {{__('Purchase')}}&nbsp;<span id="package_name">{{$first_package_name}}</span>
                        </a>
                    </div>

                    <div>
                      <span class="ud-absolute ud-top-0 ud-left-0">
                        <img src="{{ asset('assets/landing/images/svg/pricing.svg') }}">
                      </span>
                      <span class="ud-absolute ud-top-0 ud-right-0">
                        <img src="{{ asset('assets/landing/images/svg/pricing_2.svg') }}">

                      </span>
                      <span class="ud-absolute ud-top-0 ud-right-32">
                        <img src="{{ asset('assets/landing/images/svg/pricing_3.svg') }}">

                      </span>
                    </div>
                </div>
                <br>
            </div>
        @endif

    </div>
</section>

<!-- ===== CTA Section Start ===== -->
<section id="cta" class="">
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
        <div class="w-full px-4">
          <div class="mx-auto text-center lg:ml-0 lg:mb-0 lg:text-left">
            <h2 class="text-center mb-4 font-heading text-xl font-semibold leading-tight text-dark dark:text-white sm:text-[38px]"> {{__("Looking for a customized solution?")}} </h2>
            <p class="text-center text-base text-dark dark:text-white mb-2"> {{__("Contact our team to get a quote")}} : {{$get_landing_language->company_email??''}}  </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ===== CTA Section End ===== -->

<?php $package_map = json_encode($package_map);?>
<!-- ===== Pricing end ===== -->

@push('scripts-footer')
    <script src="{{asset('assets/cdn/js/jquery-3.6.0.min.js')}}"></script>
    @include('landing.partials.show-pricing-js')
@endpush

