@extends('layouts.auth')
@section('title',__('Settings'))
@section('content')
    <div class="main-content container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{($is_member)?__('Integrations'):__('Settings')}} </h3>
                    <p class="text-subtitle text-muted">{{($is_member)?__('Integrate 3rd Party APIs'):__('Settings and API Integration')}}</p>
                </div>
            </div>
        </div>

        @if (session('save_agency_account_status')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Successful')}}</h4>
                <p> {{ __('Settings have been saved successfully.') }}</p>
            </div>
        @endif


        @if ($errors->any())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('Something Missing')}}</h4>
                <p> {{ __('Something is missing. Please check the the required inputs.') }}</p>
            </div>
        @endif
        @if (session('save_agency_account_minimun_one_required')=='1')
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('No Data')}}</h4>
                <p> {{ __('You must enable at least one email account.') }}</p>
            </div>
        @endif


        <?php
        $xapp_name = $xdata->app_name ?? '';
        $email_settings = isset($xdata->email_settings) ? json_decode($xdata->email_settings) : [];

        $default_email = $email_settings->default ?? '';
        $sender_name = $email_settings->sender_name ??  $xapp_name;
        if(empty($sender_name)) $sender_name = config('app.name');

        $sender_email = $email_settings->sender_email ?? '';
        if(empty($sender_email)) $sender_email = 'no-reply@'.get_domain_only(url('/'));

        $upload_settings = isset($xdata->upload_settings) ? json_decode($xdata->upload_settings) : [];
        $upload_bot_image = $upload_settings->bot->image ?? config('app.upload.bot.image');
        $upload_bot_video = $upload_settings->bot->video ?? config('app.upload.bot.video');
        $upload_bot_audio = $upload_settings->bot->audio ?? config('app.upload.bot.audio');
        $upload_bot_file = $upload_settings->bot->file ?? config('app.upload.bot.file');
        $models_name = [
            "gpt-3.5-turbo" => "gpt-3.5-turbo",
            "gpt-3.5-turbo-0301" => "gpt-3.5-turbo-0301",
            "gpt-4" => "gpt-4 (closed beta)",
            "gpt-4-0314" => "gpt-4-0314 (closed beta)",
            "gpt-4-32k" => "gpt-4-32k(closed beta)",
            "gpt-4-32k-0314" => "gpt-4-32k-0314 (closed beta)"
        ];
        ?>


        <section class="section">

            <form  class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('general-settings-action') }}">
                @csrf

                <?php
                $nav_items = [];
                if(!$is_member) array_push($nav_items, ['tab'=>true,'id'=>'general-tab','href'=>'#general','title'=>__('General'),'subtitle'=>__('Brand & Preference'),'icon'=>'fas fa-cog']);
                if($is_admin) array_push($nav_items, ['tab'=>true,'id'=>'cron-tab','href'=>'#cron','title'=>__('Cron'),'subtitle'=>__('Cron Commands'),'icon'=>'fas fa-tasks']);
                array_push($nav_items, ['tab'=>true,'id'=>'email-tab','href'=>'#email','title'=>__('Email'),'subtitle'=>__('Integration'),'icon'=>'far fa-envelope']);
                array_push($nav_items, ['tab'=>true,'id'=>'emailauto-tab','href'=>'#emailauto','title'=>__('Responder'),'subtitle'=>__('Integration'),'icon'=>'far fa-paper-plane']);
                if(!$is_member){
                    array_push($nav_items, ['tab'=>true,'id'=>'analytics-tab','href'=>'#analytics','title'=>__('Script'),'subtitle'=>__('Analytics Code'),'icon'=>'fas fa-code']);
                    array_push($nav_items, ['tab'=>false,'href'=>route('payment-settings',0),'title'=>__('Payment'),'subtitle'=>__('Integration'),'icon'=>'fas fa-credit-card']);
                    array_push($nav_items, ['tab'=>false,'href'=>route('agency-landing-editor'),'title'=>__('Landing'),'subtitle'=>__('Page Setup'),'icon'=>'fas fa-home']);
                    array_push($nav_items, ['tab'=>false,'href'=>route('languages.index'),'title'=>__('Language'),'subtitle'=>__('Multi-lingual Editor'),'icon'=>'fas fa-language','target'=>'_BLANK']);
                }

                ?>

                <div class="row">
                    <div class="col-12 col-lg-2">

                        <div class="d-flex d-lg-none header-tabs align-items-stretch w-100 mb-5 mb-lg-0 general-settings-style1" id="myTab">
                            <ul class="nav nav-tabs nav-stretch flex-nowrap w-100 h-100 myTab" role="tablist">
                                  @foreach($nav_items as $index=>$nav)
                                    <li class="nav-item flex-equal no-radius pt-1" role="presentation">
                                        <a class="nav-link d-flex flex-column text-nowrap flex-center w-100 px-2 px-lg-4 py-3 py-lg-4 text-center no-radius" href="{{$nav['href']??''}}" id="{{$nav['id']??''}}"  <?php if($nav['tab']) echo 'data-bs-toggle="tab" aria-selected="true" role="tab"'; if(isset($nav['target'])) echo 'target="'.$nav['target'].'"';?>>
                                            <span class="text-uppercase text-dark fw-bold fs-6 fs-lg-5"><i class="text-primary {{$nav['icon']??''}}"></i> {{$nav['title']}}</span>
                                            <span class="text-gray-500 fs-8 fs-lg-7 text-muted">{{$nav['subtitle']}}</span>
                                        </a>
                                    </li>
                                  @endforeach
                            </ul>
                        </div>

                        <div class="d-none d-lg-block" id="myTab2">
                            <ul class="nav nav-tabs myTab" role="tablist" aria-orientation="vertical">
                              @foreach($nav_items as $index=>$nav)
                               <li class="nav-item w-100 pt-1" role="presentation">
                                    <a class="nav-link d-flex flex-column text-nowrap flex-center w-100 ps-2 ps-lg-4 py-2 no-radius" href="{{$nav['href']??''}}" id="{{$nav['id']??''}}"  <?php if($nav['tab']) echo 'data-bs-toggle="tab" aria-selected="true" role="tab"'; if(isset($nav['target'])) echo 'target="'.$nav['target'].'"';?>>
                                        <span class="text-uppercase text-dark fw-bold fs-6 fs-lg-5"><i class="text-primary {{$nav['icon']??''}}"></i> {{$nav['title']}}</span>
                                        <span class="text-gray-500 fs-8 fs-lg-7 text-muted">{{$nav['subtitle']}}</span>
                                    </a>
                                </li>
                              @endforeach
                            </ul>
                        </div>

                    </div>

                    <div class="col-12 col-lg-10">
                        <div class="tab-content" id="myTabContent">
                            @if(!$is_member)
                                <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                                    <div class="card">
                                        <div class="row">
                                            <div class="col-12 col-lg-8">
                                                <div class="card mb-4 no-shadow">
                                                    <div class="card-header pt-5">
                                                        <h4>{{__('Brand Settings')}} </h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">{{ __("Brand name") }} </label>
                                                                    <div class="input-group">
                                                                        @php
                                                                            $app_name = old('app_name', $xapp_name);
                                                                            if(empty($app_name)) $app_name = config('app.name');
                                                                        @endphp
                                                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                                        <input name="app_name" value="{{old('app_name',$app_name)}}"  class="form-control" type="text">
                                                                    </div>
                                                                    @if ($errors->has('app_name'))
                                                                        <span class="text-danger"> {{ $errors->first('app_name') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="">{{ __("Logo") }} </label>
                                                                    <?php $logo  = !empty($xdata->logo) ? $xdata->logo : asset('assets/images/logo.png');?>
                                                                    <img src="{{ $logo }}" class="mb-2 border rounded" alt="" height="70px" width="100%">
                                                                    <div class="position-relative">
                                                                        <input type="file" id="logo" class="form-control" name="logo" >
                                                                        @if ($errors->has('logo'))
                                                                            <span class="text-danger"> {{ $errors->first('logo') }} </span>
                                                                        @else
                                                                            <span class="small"> 1MB, 500x150px, png/jpg/webp </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-lg-4">
                                                                 <div class="form-group">
                                                                     <label for="">{{ __("White Logo") }} </label>
                                                                     <?php $logo_alt  = !empty($xdata->logo_alt) ? $xdata->logo_alt : asset('assets/images/logo-white.png');?>
                                                                     <img src="{{ $logo_alt }}" class="mb-2 border rounded bg-primary" alt="" height="70px" width="100%">
                                                                     <div class="position-relative">
                                                                        <input type="file" id="logo_alt" class="form-control" name="logo_alt" >
                                                                        @if ($errors->has('logo_alt'))
                                                                            <span class="text-danger"> {{ $errors->first('logo_alt') }} </span>
                                                                        @else
                                                                            <span class="small"> 1MB, 500x150px, png/jpg/webp </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-lg-4">
                                                                <div class="form-group">
                                                                      <p class="m-0 text-center">
                                                                        <label for="">{{ __("Favicon") }} </label><br>
                                                                        <?php $favicon  = !empty($xdata->favicon) ? $xdata->favicon : asset('assets/images/favicon.png'); ?>
                                                                        <img src="{{ $favicon }}" class="mb-2 border rounded text-center" alt="" height="70px">
                                                                      </p>
                                                                      <div class="position-relative">
                                                                        <input type="file" id="favicon" class="form-control" name="favicon" >
                                                                        @if ($errors->has('favicon'))
                                                                            <span class="text-danger"> {{ $errors->first('favicon') }} </span>
                                                                        @else
                                                                            <span class="small"> 100KB, 100x100px, png/jpg/webp</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-4">
                                                <div class="card mb-4 no-shadow">
                                                    <div class="card-header pt-5">
                                                        <h4>{{__('Preference')}}</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">{{ __("Timezone") }} </label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                                        @php
                                                                            $selected = old('timezone', $xdata->timezone ?? '');
                                                                            if(empty($selected)) $selected = config('app.timezone');
                                                                            $timezone_list = get_timezone_list();
                                                                            echo Form::select('timezone',$timezone_list,$selected,array('class'=>'form-control select2'));
                                                                        @endphp
                                                                    </div>
                                                                    @if ($errors->has('timezone'))
                                                                        <span class="text-danger"> {{ $errors->first('timezone') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">{{ __("Locale") }} </label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                                        <?php echo Form::select('language',$language_list,old('language', $xdata->language ?? 'en'),array('class'=>'form-control'));?>
                                                                    </div>
                                                                    @if ($errors->has('language'))
                                                                        <span class="text-danger"> {{ $errors->first('language') }} </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($is_agent)
                                            <div class="col-12 d-none">
                                                <div class="card no-radius">
                                                    <div class="card-header"><h4>{{ __("Agency URLs") }}</h4></div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <div class="input-group mb-4">
                                                                <h6>{{__('User Signup URL')}}</h6>
                                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">{{route('register')}}?at={{$user_id}}</code></pre>
                                                            </div>
                                                            <div class="input-group mb-4">
                                                                <h6>{{__('User Login URL')}}</h6>
                                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">{{route('login')}}?at={{$user_id}}</code></pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($is_admin)
                                <div class="tab-pane fade" id="cron" role="tabpanel" aria-labelledby="cron-tab">
                                    <div class="card no-radius">
                                    <div class="card-header"><h4>{{ __("Cron Commands") }}</h4></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="input-group mb-4">
                                                <h6>{{__('Telegram Scheduled Message (every 3 minutes)')}}</h6>
                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">curl {{route('telegram-group-broadcast-send')}} >/dev/null 2>&1</code></pre>
                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">curl {{route('telegram-group-broadcast-delete')}} >/dev/null 2>&1</code></pre>
                                            </div>
                                            @if(check_build_version()=='double')
                                            <div class="input-group mb-4">
                                                <h6>{{__('PayPal Subscription (every 5 minutes)')}}</h6>
                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">curl {{route('get-paypal-subscriber-transaction')}} >/dev/null 2>&1</code></pre>
                                            </div>
                                            @endif
                                            <div class="input-group mb-4">
                                                <h6>{{__('Disable Bot for Expired Users (every day)')}}</h6>
                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">curl {{route('telegram-disable-bot-expired-users')}} >/dev/null 2>&1</code></pre>
                                            </div>
                                            <div class="input-group mb-4">
                                                <h6>{{__('Clean Junk Data (every week)')}}</h6>
                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">curl {{route('telegram-clean-junk-data')}} >/dev/null 2>&1</code></pre>
                                            </div>
                                            <div class="input-group mb-4">
                                                <h6>{{__('Clean Log File (every week)')}}</h6>
                                                <pre><code class="language-html" data-prismjs-copy="{{__('Copy')}}">curl {{route('clean-system-logs')}} >/dev/null 2>&1</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            @endif

                            <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-12 col-md-4 <?php if($is_member) echo 'd-none';?>">
                                            <div class="card-header pt-5">
                                                <h4>{{__('Email Sender')}}</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="default_email" >{{ __('Default Profile') }} *</label>
                                                            <div class="form-group">
                                                                <div class="input-group" id="default-main-container">
                                                                </div>
                                                                @if ($errors->has('default_email'))
                                                                    <span class="text-danger"> {{ $errors->first('default_email') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="">{{ __("Default Sender Email") }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                                                <input name="sender_email" value="{{$sender_email}}"  class="form-control" type="email">
                                                            </div>
                                                            @if ($errors->has('sender_email'))
                                                                <span class="text-danger"> {{ $errors->first('sender_email') }} </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="">{{ __("Default Sender Name") }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                                                <input name="sender_name" value="{{$sender_name}}"  class="form-control" type="text">
                                                            </div>
                                                            @if ($errors->has('sender_name'))
                                                                <span class="text-danger"> {{ $errors->first('sender_name') }} </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12  <?php echo $is_member ? 'col-md-12' : 'col-md-8';?>">
                                            <div class="card mb-4 no-shadow">
                                                <div class="card-header pt-5">
                                                    <h4>{{__('Email Profile')}} <a id="new-profile" href="#" class="ms-3 btn btn-outline-primary btn-sm"><i class="fas fa-plus-circle"></i> {{__('New')}}</a></h4>
                                                </div>
                                                <div class="card no-shadow">
                                                    <div class="card-body data-card pt-0">
                                                        <div class="table-responsive">
                                                            <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                                                                <thead>
                                                                <tr class="table-light">
                                                                    <th>#</th>
                                                                    <th>
                                                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                                                    </th>
                                                                    <th>{{__("Profile Name") }}</th>
                                                                    <th>{{__("API Name") }}</th>
                                                                    <th>{{__("Updated at") }}</th>
                                                                    <th>{{__("Actions") }}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                           <div class="tab-pane fade" id="emailauto" role="tabpanel" aria-labelledby="emailauto-tab">
                                <div class="card">
                                    <div class="row">
                                        @if(!$is_member)
                                        <div class="col-12 col-md-4">
                                            <div class="card-header pt-5">
                                                <h4>{{__('Signup Integration')}}</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach ($autoresponser_dropdown_values as $key_root => $value_root)
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="default_email" >{{ __(ucfirst($key_root)) }} {{__('List')}}</label>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fas fa-stream"></i></span>

                                                                    <select class="form-control select2" id="" name="auto_responder_signup_settings[]" multiple>
                                                                        <?php
                                                                        foreach ($value_root as $key => $value)
                                                                        {
                                                                            if(!isset($value['data'])) continue;
                                                                            echo '<optgroup label="'.addslashes($value['api_name']).' : '.addslashes($value['profile_name']).'">';

                                                                            foreach ($value['data'] as $key2 => $value2)
                                                                            {
                                                                                $selected = '';
                                                                                if(isset($value_root['selected']) && in_array($value2['table_id'], $value_root['selected'])) $selected = 'selected';
                                                                                echo "<option value='".$value2['table_id']."-".$key_root."' ".$selected.">".$value2['list_name']."</option>";
                                                                            }
                                                                            echo '</optgroup>';
                                                                        } ?>
                                                                    </select>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-12 <?php echo !$is_member ? 'col-md-8' : '';?>">
                                            <div class="card mb-4 no-shadow">
                                                <div class="card-header pt-5">
                                                    <h4>{{__('Auto Responder Profile')}} <a id="new-auto-profile" href="#" class="ms-3 btn btn-outline-primary btn-sm"><i class="fas fa-plus-circle"></i> {{__('New')}}</a></h4>
                                                </div>
                                                <div class="card no-shadow">
                                                    <div class="card-body data-card pt-0">
                                                        <div class="table-responsive">
                                                            <table class='table table-hover table-bordered table-sm w-100' id="mytable2" >
                                                                <thead>
                                                                <tr class="table-light">
                                                                    <th>#</th>
                                                                    <th>
                                                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                                                    </th>
                                                                    <th>{{__("Profile Name") }}</th>
                                                                    <th>{{__("API Name") }}</th>
                                                                    <th>{{__("Updated at") }}</th>
                                                                    <th>{{__("Actions") }}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            @if(!$is_member)
                            <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="card mb-4 no-shadow">
                                                <div class="card-header pt-5">
                                                    <h4>{{__('Script and Analytics')}}</h4>
                                                </div>
                                                <div class="card-body">

                                                    <?php
                                                        $analytics_data = isset($xdata->analytics_code) ? json_decode($xdata->analytics_code,true):[];
                                                        $fb_pixel_id = isset($analytics_data['fb_pixel_id']) ? $analytics_data['fb_pixel_id']:"";
                                                        $google_analytics_id = isset($analytics_data['google_analytics_id']) ? $analytics_data['google_analytics_id']:"";
                                                        $tme_widget_id = isset($analytics_data['tme_widget_id']) ? $analytics_data['tme_widget_id']:"";
                                                        $whatsapp_widget_id = isset($analytics_data['whatsapp_widget_id']) ? $analytics_data['whatsapp_widget_id']:"";
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Facebook Pixel Id") }}</label>
                                                                <input type="text" class="form-control" id="fb_pixel_id" name="fb_pixel_id" value="{{ old('fb_pixel_id', $fb_pixel_id) }}" placeholder="{{ __('Ex: 342869686661279') }}">

                                                                @if ($errors->has('fb_pixel_id'))
                                                                    <span class="text-danger"> {{ $errors->first('fb_pixel_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">{{ __("Google Analytics Id") }}</label>
                                                                <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id', $google_analytics_id) }}" placeholder="{{ __('Ex: G-Z2TZKBFV49') }}">

                                                                @if ($errors->has('google_analytics_id'))
                                                                    <span class="text-danger"> {{ $errors->first('google_analytics_id') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        @if(!$is_member)
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary me-1"><i class="fas fa-save"></i> {{__('Save')}}</button>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </form>
        </section>

    </div>


    <div class="modal fade" id="email_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Email Profile')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <input type="hidden" id="update-id" value="0">
                   <div class="card-body">
                        <div class="row">
                            <div class="col-7 col-md-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane active show" id="smtp-block" role="tabpanel" aria-labelledby="">
                                        <form id="smtp-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Host") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-server"></i></span>
                                                            <input name="host" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('host'))
                                                            <span class="text-danger"> {{ $errors->first('host') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Username") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            <input name="username" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('username'))
                                                            <span class="text-danger"> {{ $errors->first('username') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Password") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="password" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('password'))
                                                            <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Port") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-plug"></i></span>
                                                            <input name="port" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('port'))
                                                            <span class="text-danger"> {{ $errors->first('port') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Encryption") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                                            <?php echo Form::select('encryption',array(''=>'Default','tls'=>"TLS",'ssl'=>"SSL"),'',array('class'=>'form-control','not-required'=>'true')); ?>
                                                        </div>
                                                        @if ($errors->has('encryption'))
                                                            <span class="text-danger"> {{ $errors->first('encryption') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="mailgun-block" role="tabpanel" aria-labelledby="">
                                        <form id="mailgun-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Domain") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-server"></i></span>
                                                            <input name="domain" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('domain'))
                                                            <span class="text-danger"> {{ $errors->first('domain') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Secret") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="secret" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('secret'))
                                                            <span class="text-danger"> {{ $errors->first('secret') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Endpoint") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-genderless"></i></span>
                                                            <input name="endpoint" value="api.eu.mailgun.net"  class="form-control" type="text" reset="false">
                                                        </div>
                                                        @if ($errors->has('endpoint'))
                                                            <span class="text-danger"> {{ $errors->first('endpoint') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="postmark-block" role="tabpanel" aria-labelledby="">
                                        <form id="postmark-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Token") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="token" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('token'))
                                                            <span class="text-danger"> {{ $errors->first('token') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="ses-block" role="tabpanel" aria-labelledby="">
                                        <form id="ses-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                            <input name="key" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('key'))
                                                            <span class="text-danger"> {{ $errors->first('key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Secret") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="secret" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('secret'))
                                                            <span class="text-danger"> {{ $errors->first('secret') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Region") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-genderless"></i></span>
                                                            <input name="region" value="us-east-1"  class="form-control" type="text" reset="false">
                                                        </div>
                                                        @if ($errors->has('region'))
                                                            <span class="text-danger"> {{ $errors->first('region') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="mandrill-block" role="tabpanel" aria-labelledby="">
                                        <form id="mandrill-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="secret" value=""  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('secret'))
                                                            <span class="text-danger"> {{ $errors->first('secret') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <div class="col-5 col-md-3">
                                <div class="nav d-block nav-pills email-block" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="d-block nav-link active" data-bs-toggle="pill" href="#smtp-block" id="smtp-block-link" role="tab" aria-controls="" aria-selected="true">{{__('SMTP')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#mailgun-block" id="mailgun-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mailgun')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#postmark-block"  id="postmark-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Postmark')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#ses-block" id="ses-block-link" role="tab" aria-controls="" aria-selected="true">{{__('SES')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#mandrill-block" id="mandrill-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mandril')}}</a>
                                </div>
                            </div>
                        </div>
                   </div>

                </div>

                <div class="modal-footer d-block">
                    <button type="button" class="btn btn-primary float-start" id="save_email_settings"><i class="fas fa-save"></i> {{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="email_auto_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Auto Responder Profile')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="auto-update-id" value="0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-7 col-md-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane active show" id="mailchimp-block" role="tabpanel" aria-labelledby="">
                                        <form id="mailchimp-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="api_key" value="" non-editable="true"  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="sendinblue-block" role="tabpanel" aria-labelledby="">
                                        <form id="sendinblue-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="api_key" value="" non-editable="true"  class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="activecampaign-block" role="tabpanel" aria-labelledby="">
                                        <form id="activecampaign-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="api_key" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API URL") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                            <input name="api_url" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('api_url'))
                                                            <span class="text-danger"> {{ $errors->first('api_url') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="mautic-block" role="tabpanel" aria-labelledby="">
                                        <form id="mautic-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Username") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-user"></i></span>
                                                            <input name="username" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('username'))
                                                            <span class="text-danger"> {{ $errors->first('username') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Password") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="password" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('password'))
                                                            <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Base URL") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                            <input name="base_url" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('base_url'))
                                                            <span class="text-danger"> {{ $errors->first('base_url') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="acelle-block" role="tabpanel" aria-labelledby="">
                                        <form id="acelle-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="api_key" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API URL") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                            <input name="api_url" value="" non-editable="true" class="form-control" type="text">
                                                        </div>
                                                        @if ($errors->has('api_url'))
                                                            <span class="text-danger"> {{ $errors->first('api_url') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <div class="col-5 col-md-3">
                                <div class="nav d-block nav-pills email-auto-block" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="d-block nav-link active" data-bs-toggle="pill" href="#mailchimp-block" id="mailchimp-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mailchimp')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#sendinblue-block" id="sendinblue-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Sendinblue')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#activecampaign-block"  id="activecampaign-block-link" role="tab" aria-controls="" aria-selected="true">{{__('ActiveCampaign')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#mautic-block" id="mautic-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mautic')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#acelle-block" id="acelle-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Acelle')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer d-block">
                    <button type="button" class="btn btn-primary float-start" id="save_email_auto_settings"><i class="fas fa-save"></i> {{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles-footer')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/modal-right.css') }}">
@endpush

@push('scripts-footer')
    <script>
        "use strict";
        var ajax_set_active_tag_id = '{{route('general-settings-set-session-active-tab')}}';
        var active_tag_id = '{{session('general_settings_active_tab_id')}}';
    </script>
    <script src="{{ asset('assets/js/pages/member/settings.general-settings.js') }}"></script>
    <script src="{{ asset('assets/js/pages/member/settings.thirdparty-api-settings.js') }}"></script>
@endpush
