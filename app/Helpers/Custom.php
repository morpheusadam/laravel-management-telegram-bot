<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

if(! function_exists('get_base_url')){
    function get_base_url(){
        return $base_url =  url_convert_to_domain(url('/'));
    }
}

if ( ! function_exists('set_agency_config')){
    function set_agency_config()
    {
        $logged_in = Auth::user() ? true : false;
        if($logged_in) $user_id = Auth::user()->id;

        $user_data = $admin_user_data = '';
        $user_type = 'Member';

        if(!empty($user_id)) {
            $user_select = ['user_type','users.timezone','users.language','users.id as user_id'];
            $email_provided = false;
            if(strpos($user_id, '@') !== false) $email_provided = true;
            $user_where = $email_provided ? ['users.email'=>$user_id] : ['users.id'=>$user_id];
            $user_data = DB::table('users')->select($user_select)->where($user_where)->first();
            if($email_provided) $user_id = $user_data->user_id;
            $user_type = $user_data->user_type;
        }

        $admin_user_data = DB::table('users')->select('settings.*','user_type')->leftJoin('settings','settings.user_id','=','users.id')->where('user_type','Admin')->first();

        $user_timezone = $user_data->timezone ?? '';
        if(empty($user_timezone)) $user_timezone = $admin_user_data->timezone ?? '';

        $user_language = $user_data->language ?? '';
        if(empty($user_language)) $user_language = $admin_user_data->language ?? '';
        $app_name = $admin_user_data->app_name ?? '';
        $app_logo = $admin_user_data->logo ?? '';
        $app_logo_alt = $admin_user_data->logo_alt ?? '';
        $app_favicon = $admin_user_data->favicon ?? '';
        $ai_chat_icon = $admin_user_data->ai_chat_icon ?? '';
        $email_settings = $admin_user_data->email_settings ?? '';
        $force_email_verify = $admin_user_data->force_email_verify ?? '1';

        if(isset($app_name) && !empty($app_name)) {
            config(['app.name' => $app_name]);
        }

        if(isset($app_logo) && !empty($app_logo)) {
            config(['app.logo' => $app_logo]);
        }

        if(isset($app_logo_alt) && !empty($app_logo_alt)) {
            config(['app.logo_alt' => $app_logo_alt]);
        }

        if(isset($app_favicon) && !empty($app_favicon)) {
            config(['app.favicon' => $app_favicon]);
        }
        if(isset($ai_chat_icon) && !empty($ai_chat_icon)) {
            config(['app.ai_chat_icon' => $ai_chat_icon]);
        }

        if(isset($user_timezone) && !empty($user_timezone)) {
            config(['app.userTimezone' => $user_timezone]);
        }

        if(isset($force_email_verify)) {
            config(['app.force_email_verify' => $force_email_verify]);
        }


        if(isset($user_language) && !empty($user_language)) {
            config(['app.locale' => $user_language]);
            App::setLocale($user_language);
            $get_rtl_langauge_list = get_rtl_langauge_list();
            $localeDirection = isset($get_rtl_langauge_list[$user_language]) ? 'rtl' : 'ltr';
            config(['app.localeDirection' => $localeDirection]);
        }
        $email_settings =  isset($email_settings) && !empty($email_settings) ? json_decode($email_settings) : [];
        $default = $email_settings->default ?? '';
        set_email_config($default);

        return ['app_name'=>$app_name,'app_logo'=>$app_logo,'app_logo_alt'=>$app_logo_alt,'app_favicon'=>$app_favicon,'email_settings'=>$email_settings,'force_email_verify'=>$force_email_verify];
    }
}


if ( ! function_exists('set_email_config'))
{
    function set_email_config($default = ''){
        $agent_domain = get_domain_only(url('/'));
        $user_id = 1;
        config(['mail.from.address' => 'no-reply@'.$agent_domain]);
        config(['mail.from.name' => config('app.name')]);
        if(!empty($default) && $default!=0){
            $settings_data = DB::table('settings')->where(['user_id'=>$user_id])->select('email_settings')->first();
            $agent_email_settings = $settings_data->email_settings ?? '';
            $agent_email_settings = json_decode($agent_email_settings);
            $sender_email = $agent_email_settings->sender_email ?? null;
            $sender_name = $agent_email_settings->sender_name ?? null;
            if(!empty($sender_email)) config(['mail.from.address' => $sender_email]);
            if(!empty($sender_name)) config(['mail.from.name' => $sender_name]);

            $email_settings_data = DB::table('settings_sms_emails')->where(['id'=>$default,'api_type'=>'email'])->first();
            if(!empty($email_settings_data)){
                $api_name = $email_settings_data->api_name ?? 'smtp';
                config(['mail.default' => $api_name]);
                $settings_data = json_decode($email_settings_data->settings_data) ?? '';
                if($api_name=='smtp'){
                    config(
                        [
                            'mail.mailers.smtp.host' => $settings_data->host ?? '',
                            'mail.mailers.smtp.port' => $settings_data->port ?? '',
                            'mail.mailers.smtp.encryption' => $settings_data->encryption ?? '',
                            'mail.mailers.smtp.username' => $settings_data->username ?? '',
                            'mail.mailers.smtp.password' => $settings_data->password ?? ''
                        ]);
                }
                else if($api_name=='mailgun'){
                    config(
                        [
                            'services.mailgun.domain' => $settings_data->domain ?? '',
                            'services.mailgun.secret' => $settings_data->secret ?? '',
                            'services.mailgun.endpoint' => $settings_data->endpoint ?? ''
                        ]);
                }
                else if($api_name=='postmark'){
                    config(['services.postmark.token' => $settings_data->token ?? '']);
                }
                else if($api_name=='ses'){
                    config(
                        [
                            'services.ses.key' => $settings_data->key ?? '',
                            'services.ses.secret' => $settings_data->secret ?? '',
                            'services.ses.region' => $settings_data->region ?? ''
                        ]);
                }
                else if($api_name=='mandrill'){
                    config(
                        [
                            'services.mandrill.secret' => $settings_data->secret ?? ''
                        ]);
                }
            }
        }
        return true;
    }
}

if( ! function_exists('get_real_ip')){
    function get_real_ip(){
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}


if ( ! function_exists('get_modules_set'))
{
    function get_modules_set($module_ids=''){
        if(empty($module_ids)) return false;
        $result = DB::table('modules')->select('module_name','id')->whereRaw('FIND_IN_SET(id,?)>0',[$module_ids])->orderByRaw('module_name ASC')->get();
        return $result;
    }
}

if ( ! function_exists('get_sequence_types'))
{
    function get_sequence_types(){
        $ret = array
        (
            'default'=>'Default',
            'custom'=>'Custom',
            'telegram_bot_engagement_tme'=>'t.me',
        );
        return $ret;
    }
}


if ( ! function_exists('convert_datetime_to_timezone'))
{
  function convert_datetime_to_timezone($input_datetime='',$to_timezone='',$display_timezone=false,$date_format='',$from_timezone='UTC')
  {
      if(empty($input_datetime) || $input_datetime=='0000-00-00' || $input_datetime=='0000-00-00 00:00:00') return null;
      if(empty($to_timezone)) $to_timezone = config('app.userTimezone');
      if(empty($to_timezone)) return $input_datetime;
      if(empty($date_format)) $date_format = 'jS M y H:i';
      $date = new DateTime($input_datetime,new DateTimeZone($from_timezone));
      $date->setTimezone(new DateTimeZone($to_timezone));
      $converted = $date->format($date_format);
      $get_timezone_list = get_timezone_list();
      if($display_timezone) {
          if(isset($get_timezone_list[$to_timezone])){
              $exp = explode(')',$get_timezone_list[$to_timezone]);
              $gmt_hour = isset($exp[0]) ? ltrim($exp[0],'(') : '';
              $converted.=' <i>'.str_replace('GMT','',$gmt_hour).'</i>';
          }
      }
      return $converted;
  }
}

if ( ! function_exists('convert_datetime_to_phrase'))
{
  function convert_datetime_to_phrase($input_datetime, $format=false,$current_datetime=null,$return_ago_text=true)
  {
    // pass current_datetime you want subtracted result
    // expected date format YYY-MM-DD H:i:s

    $difference = !empty($current_datetime) ? abs(strtotime($current_datetime) - strtotime($input_datetime)) : strtotime($input_datetime);
    $ago_text = $return_ago_text ? __('ago') : '';

    $years   = floor($difference / (365*60*60*24));
    $months  = floor(($difference - $years * 365*60*60*24) / (30*60*60*24));
    $days    = floor(($difference - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $hours   = floor(($difference - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));
    $minutes = floor(($difference - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
    $seconds = floor(($difference - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

    $result = array(
      "years"   => $years,
      "months"  => $months,
      "days"    => $days,
      "hours"   => $hours,
      "minutes" => $minutes,
      "seconds" => $seconds
    );

    if ($format == true) {

      $years_plular=$months_plular=$days_plular=$hours_plular=$minutes_plular=$seconds_plular="";
      if($result['years']!="" && $result['years']>1) $years_plular='s';
      if($result['months']!="" && $result['months']>1) $months_plular='s';
      if($result['days']!="" && $result['days']>1) $days_plular='s';
      if($result['hours']!="" && $result['hours']>1) $hours_plular='s';
      if($result['minutes']!="" && $result['minutes']>1) $minutes_plular='s';
      if($result['seconds']!="" && $result['seconds']>1) $seconds_plular='s';

      if ($result['years'] > 0)
          return $result['years']." ".__("year").$years_plular." ".$ago_text;
      else if ($result['months'] > 0)
          return $result['months']." ".__("month").$months_plular." ".$ago_text;
      else if ($result['days'] > 0)
          return $result['days']." ".__("day").$days_plular." ".$ago_text;
      else if ($result['hours'] > 0)
          return $result['hours']." ".__("hour").$hours_plular." ".$ago_text;
      else if ($result['minutes'] > 0)
          return $result['minutes']." ".__("minute").$minutes_plular." ".$ago_text;
      else if ($result['seconds'] > 0)
          return $result['seconds']." ".__("second").$seconds_plular." ".$ago_text;
    }
    else return $result;

  }
}


if ( ! function_exists('convert_number_numeric_phrase')) {
    function convert_number_numeric_phrase($n, $precision = 2)
    {
        if ($n < 1000) {
            // Anything less than a thousand
            $n_format = number_format($n);
        } else if ($n < 1000000) {
            // Anything less than a million
            $n_format = number_format($n / 1000, $precision) . 'K';
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision) . 'M';
        } else if ($n < 1000000000000) {
            // Anything less than a trillion
            $n_format = number_format($n / 1000000000, $precision) . 'B';
        } else {
            // At least a trillion
            $n_format = number_format($n / 1000000000000, $precision) . 'T';
        }

        return $n_format;
    }
}

if ( ! function_exists('convert_number_validity_phrase')) {
    function convert_number_validity_phrase($n)
    {
        if ($n>365 && $n % 365 == 0 )  {
            $val = $n/365;
            $n_format = $val.' '.__('Years');
        }
        else if ($n == 365)  $n_format = __('Yearly');
        else if ($n == 180)  $n_format = __('Half-yearly');
        else if ($n == 90)  $n_format = __('Quarterly');
        else if ($n == 30)  $n_format = __('Monthly');
        else if ($n < 30)  $n_format = $n.' '.__('Days');
        else if ($n == 7) $n_format =__('Weekly');
        else $n_format = $n.' '.__('Days');
        return $n_format;
    }
}


if ( ! function_exists('convert_hex_to_rgba')) {
    function convert_hex_to_rgba($color, $opacity = false)
    {
        $default = 'rgb(0,0,0)';
        //Return default if no color provided
        if (empty($color)) return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }
}

if ( ! function_exists('convert_api_param_to_query_string')){
    function convert_api_param_to_query_string($params=[]){
        if(empty($params)) return '';
        $keys = array_keys($params);
        $values = !empty($params) ? array_column($params, 'default') : [];
        $query_string = '';
        $query_string_array = [];
        $i=0;
        foreach($keys as $k){
            $v = $values[$i] ?? '';
            $query_string_array[$k] = $v;
            $i++;
        }
        if(empty($query_string_array)) return '';
        $query_string = '?'.http_build_query($query_string_array);
        return $query_string;
    }
}

if ( ! function_exists('convert_api_param_to_curl_post')){
    function convert_api_param_to_curl_post($params=[]){
        if(empty($params)) return '';
        $keys = array_keys($params);
        $values = !empty($params) ? array_column($params, 'default') : [];
        $query_string = '';
        $i=0;
        foreach($keys as $k){
            $v = $values[$i] ?? '';
            $query_string .= "
-d '".$k."=".$v."' \\";
            $i++;
        }
        return rtrim($query_string,'\\');
    }
}


if ( ! function_exists('convert_xml_to_json')){
    function convert_xml_to_json($xml){
        $obj = new SimpleXMLElement($xml);
        return json_encode($obj);
    }
}

if ( ! function_exists('convert_mime_to_extension')) {
    function convert_mime_to_extension($mime) {
        $mime_map = [
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'application/x-compressed'                                                  => '7zip',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'application/postscript'                                                    => 'ai',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'application/x-troff-msvideo'                                               => 'avi',
            'application/macbinary'                                                     => 'bin',
            'application/mac-binary'                                                    => 'bin',
            'application/x-binary'                                                      => 'bin',
            'application/x-macbinary'                                                   => 'bin',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/bmp'                                                           => 'bmp',
            'application/x-bmp'                                                         => 'bmp',
            'application/x-win-bitmap'                                                  => 'bmp',
            'application/cdr'                                                           => 'cdr',
            'application/coreldraw'                                                     => 'cdr',
            'application/x-cdr'                                                         => 'cdr',
            'application/x-coreldraw'                                                   => 'cdr',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'zz-application/zz-winassoc-cdr'                                            => 'cdr',
            'application/mac-compactpro'                                                => 'cpt',
            'application/pkix-crl'                                                      => 'crl',
            'application/pkcs-crl'                                                      => 'crl',
            'application/x-x509-ca-cert'                                                => 'crt',
            'application/pkix-cert'                                                     => 'crt',
            'text/css'                                                                  => 'css',
            'text/x-comma-separated-values'                                             => 'csv',
            'text/comma-separated-values'                                               => 'csv',
            'application/vnd.msexcel'                                                   => 'csv',
            'application/x-director'                                                    => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/x-dvi'                                                         => 'dvi',
            'message/rfc822'                                                            => 'eml',
            'application/x-msdownload'                                                  => 'exe',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'application/gpg-keys'                                                      => 'gpg',
            'application/x-gtar'                                                        => 'gtar',
            'application/x-gzip'                                                        => 'gzip',
            'application/mac-binhex40'                                                  => 'hqx',
            'application/mac-binhex'                                                    => 'hqx',
            'application/x-binhex40'                                                    => 'hqx',
            'application/x-mac-binhex40'                                                => 'hqx',
            'text/html'                                                                 => 'html',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'text/calendar'                                                             => 'ics',
            'application/java-archive'                                                  => 'jar',
            'application/x-java-application'                                            => 'jar',
            'application/x-jar'                                                         => 'jar',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/x-javascript'                                                  => 'js',
            'application/json'                                                          => 'json',
            'text/json'                                                                 => 'json',
            'application/vnd.google-earth.kml+xml'                                      => 'kml',
            'application/vnd.google-earth.kmz'                                          => 'kmz',
            'text/x-log'                                                                => 'log',
            'audio/x-m4a'                                                               => 'm4a',
            'application/vnd.mpegurl'                                                   => 'm4u',
            'audio/midi'                                                                => 'mid',
            'application/vnd.mif'                                                       => 'mif',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mpeg'                                                                => 'mpeg',
            'application/oda'                                                           => 'oda',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'application/ogg'                                                           => 'ogg',
            'application/x-pkcs10'                                                      => 'p10',
            'application/pkcs10'                                                        => 'p10',
            'application/x-pkcs12'                                                      => 'p12',
            'application/x-pkcs7-signature'                                             => 'p7a',
            'application/pkcs7-mime'                                                    => 'p7c',
            'application/x-pkcs7-mime'                                                  => 'p7c',
            'application/x-pkcs7-certreqresp'                                           => 'p7r',
            'application/pkcs7-signature'                                               => 'p7s',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'application/x-x509-user-cert'                                              => 'pem',
            'application/x-pem-file'                                                    => 'pem',
            'application/pgp'                                                           => 'pgp',
            'application/x-httpd-php'                                                   => 'php',
            'application/php'                                                           => 'php',
            'application/x-php'                                                         => 'php',
            'text/php'                                                                  => 'php',
            'text/x-php'                                                                => 'php',
            'application/x-httpd-php-source'                                            => 'php',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop'                                                   => 'psd',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'application/x-pkcs7'                                                       => 'rsa',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'application/x-stuffit'                                                     => 'sit',
            'application/smil'                                                          => 'smil',
            'text/srt'                                                                  => 'srt',
            'image/svg+xml'                                                             => 'svg',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/x-tar'                                                         => 'tar',
            'application/x-gzip-compressed'                                             => 'tgz',
            'image/tiff'                                                                => 'tiff',
            'text/plain'                                                                => 'txt',
            'text/x-vcard'                                                              => 'vcf',
            'application/videolan'                                                      => 'vlc',
            'text/vtt'                                                                  => 'vtt',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'application/wbxml'                                                         => 'wbxml',
            'video/webm'                                                                => 'webm',
            'audio/x-ms-wma'                                                            => 'wma',
            'application/wmlc'                                                          => 'wmlc',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'application/xhtml+xml'                                                     => 'xhtml',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/xml'                                                           => 'xml',
            'text/xml'                                                                  => 'xml',
            'text/xsl'                                                                  => 'xsl',
            'application/xspf+xml'                                                      => 'xspf',
            'application/x-compress'                                                    => 'z',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
            'text/x-scriptzsh'                                                          => 'zsh',
        ];
        return isset($mime_map[$mime]) === true ? $mime_map[$mime] : '';
    }
}

if ( ! function_exists('format_comment')) // blog comment, product comment, refund comment
{
  function format_comment($string="",$nl2br=true,$strip_tags = '<b><i><u><p><br><pre><code><blockquote><h5><h6>')
  {
    if($string=="") return "";
    $string=html_entity_decode($string);
    $string= !$strip_tags ? $string : strip_tags($string,$strip_tags);
    $string = preg_replace('"\b(https?://\S+)"', '<a target="_BLANK" href="$1">$1</a>', $string); // find and replace links with ancor tag
    return $nl2br ? nl2br($string) : $string;
  }
}

if ( ! function_exists('random_number_generator')){
    function random_number_generator($length=6)
    {
        $rand = substr(uniqid(mt_rand(), true), 0, $length);
        return $rand;
    }
}


if ( ! function_exists('calculate_date_differece'))
{
  function calculate_date_differece($end,$start)
  {
  $diff = abs(strtotime($end) - strtotime($start));
  $years = floor($diff / (365*60*60*24));
  $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
  $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
  return $years." Years ".$months." Months ".$days. " Days";
  }
}


if ( ! function_exists('format_datetime'))
{
  function format_datetime($date)
  {
       return date('d/m/Y h:i:s a',strtotime($date));
  }
 }


if ( ! function_exists('format_date'))
{
  function format_date($date)
  {
       return date('d/m/Y',strtotime($date));
  }
 }

if ( ! function_exists('format_price'))
{
    function format_price($price=0,$format_settings=null,$discount_settings=null,$return_settings=['which_price'=>'both','display_currency'=>true,'return_raw_array'=>false])
    {
        if($price==0) return 0;
        $price = (float) $price;
        $sale_price = $price;

        $currency = $format_settings['currency'] ?? 'USD';
        $decimal_point = $format_settings['decimal_point'] ?? null;
        $thousand_comma = $format_settings['thousand_comma'] ?? '0';
        $currency_position = $format_settings['currency_position'] ?? 'left';
        $thousand_sep = $thousand_comma=='1' ? ',' : '';

        $discount_settings = json_decode($discount_settings);
        $percent = isset($discount_settings->percent) && !is_null($discount_settings->percent) ? (float) $discount_settings->percent : 0;
        $percent_original = $discount_settings->percent ?? 0;
        $start_date = $start_date_original = $discount_settings->start_date ?? null;
        $end_date = $end_date_original = $discount_settings->end_date ?? null;
        $timezone = $discount_settings->timezone ?? null;
        $status = $discount_settings->status ?? '0';
        $terms = $discount_settings->terms ?? '';
        if($status=='0') $terms = '';
        if($status=='0') $percent_original = '';

        $which_price = $return_settings['which_price'] ?? 'both';
        $display_currency = $return_settings['display_currency'] ?? true;
        $return_raw_array = $return_settings['return_raw_array'] ?? false;
        $valid_discount = false;
        $discount_amount = 0;

        if($status=='1') {
            if(empty($timezone)) $timezone = 'Europe/Dublin';
            @date_default_timezone_set($timezone);
            $now_date = date('Y-m-d H:i:s');
            $earlier_date = date('Y-m-d H:i:s', strtotime('-7 days'));
            $later_date = date('Y-m-d H:i:s', strtotime('+7 days'));
            if(empty($start_date)) $start_date = $earlier_date;
            if(empty($end_date)) $end_date = $later_date;

            $discount_timezone = new DateTimeZone($timezone);
            $discount_start_date = new DateTime($start_date);
            $discount_start_date->setTimezone($discount_timezone);
            $start_date = $discount_start_date->format('Y-m-d H:i:s');

            $discount_end_date = new DateTime($end_date);
            $discount_end_date->setTimezone($discount_timezone);
            $end_date = $discount_end_date->format('Y-m-d H:i:s');

            if(strtotime($start_date)<=strtotime($now_date) && strtotime($end_date)>=strtotime($now_date)){
                $apply_percent = 100-$percent;
                $sale_price = $apply_percent>0 ? ($price*$apply_percent)/100 : $price;
                $discount_amount = $price-$sale_price;
                $valid_discount = true;
            }
        }
        @date_default_timezone_set(config('app.timezone'));

        $currency_icons = get_country_iso_phone_currency_list('currency_icon');
        $currency_icon = $currency_icons[$currency] ?? $currency;

        $price_formatted = $price;
        $sale_price_formatted = $sale_price;
        $discount_amount_formatted = $discount_amount;
        if(!empty($decimal_point)){
            $price_formatted = number_format($price,$decimal_point,'.',$thousand_sep);
            $sale_price_formatted = number_format($sale_price,$decimal_point,'.',$thousand_sep);
            $discount_amount_formatted = number_format($discount_amount,$decimal_point,'.',$thousand_sep);
        }

        $display_price = $sale_price_formatted;
        if($sale_price<$price){
            $display_price = '<del style = " font-size: 70% ">'.$price.'</del> '.$sale_price_formatted;
        }

        $display_price_currency = $display_price;
        $price_formatted_currency = $price_formatted;
        $sale_price_formatted_currency = $sale_price_formatted;
        $discount_amount_formatted_currency = $discount_amount_formatted;
        if($display_currency){
            $space = $currency=='USD' ? '' : ' ';
            $display_price_currency = $currency_position=='right' ?  $display_price.' '.$currency_icon : $currency_icon.$space.$display_price;
            $price_formatted_currency = $currency_position=='right' ?  $price_formatted.' '.$currency_icon : $currency_icon.$space.$price_formatted;
            $sale_price_formatted_currency = $currency_position=='right' ?  $sale_price_formatted.' '.$currency_icon : $currency_icon.$space.$sale_price_formatted;
            $discount_amount_formatted_currency = $currency_position=='right' ?  $discount_amount_formatted.' '.$currency_icon : $currency_icon.$space.$discount_amount_formatted;
        }

        if($return_raw_array){
            $return = (object) [
                'price'=>$price,
                'price_formatted'=>$price_formatted,
                'price_formatted_currency'=>$price_formatted_currency,
                'sale_price'=>$sale_price,
                'sale_price_formatted'=>$sale_price_formatted,
                'sale_price_formatted_currency'=>$sale_price_formatted_currency,
                'display_price'=>$display_price,
                'display_price_currency'=>$display_price_currency,
                'discount_valid'=>$valid_discount,
                'discount_amount'=>$discount_amount,
                'discount_amount_formatted'=>$discount_amount_formatted,
                'discount_amount_formatted_currency'=>$discount_amount_formatted_currency,
                'currency'=>$currency,
                'currency_icon'=>$currency_icon,
                'discount_percent'=>$percent_original,
                'discount_start_date'=>$start_date_original,
                'discount_end_date'=>$end_date_original,
                'discount_timezone'=>$timezone,
                'discount_status'=>$status,
                'discount_terms'=>$terms,
                'decimal_point'=>$decimal_point,
                'thousand_comma'=>$thousand_comma,
                'currency_position'=>$currency_position
            ];
            return $return;
        }
        else if($which_price=='both') return $display_price_currency;
        else if($which_price=='sale') return $sale_price_formatted_currency;
        else if($which_price=='original') return $price_formatted_currency;
        else return null;
    }
}


if ( ! function_exists('array_to_assoc'))
{
  function array_to_assoc($result=array(), $index='id', $display='name',$empty_index=true)
  {
    $map_array = array();
    foreach ($result as $key => $value)
    {
      $map_array[$value[$index]] = $value[$display];
    }
    if($empty_index) $map_array[''] = __("Select");
    return $map_array;
  }
}


if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}


if ( ! function_exists('array_add'))
{
    function array_add($array1,$array2){
        $array_1=$array1;
        $array_2=$array2;
        $arra1_count=count($array_1);
        foreach($array_2 as $val){
            $array_1[$arra1_count]=$val;
            $arra1_count++;
        }
        return $array_1;
    }

}

if ( ! function_exists('add_unset_recursive'))
{
    function add_unset_recursive($array=[],$unset_index=''){
        if(empty($array) || empty($unset_index)) return $array;
        if(!is_array($array)) $array = json_decode($array,true);
        if(!is_array($array)) return $array;

        foreach ($array as $key1=>$value1){
            if(isset($value1[$unset_index])) unset($array[$key1][$unset_index]);

            if(is_array($value1))
            foreach ($value1 as $key2=>$value2){
                if(isset($value2[$unset_index])) unset($array[$key1][$key2][$unset_index]);

                if(is_array($value2))
                foreach ($value2 as $key3=>$value3){
                    if(isset($value3[$unset_index])) unset($array[$key1][$key2][$key3][$unset_index]);

                    if(is_array($value3))
                    foreach ($value3 as $key4=>$value4){
                        if(isset($value4[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$unset_index]);

                        if(is_array($value4))
                        foreach ($value4 as $key5=>$value5){
                            if(isset($value5[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$unset_index]);

                            if(is_array($value5))
                            foreach ($value5 as $key6=>$value6){
                                if(isset($value6[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$key6][$unset_index]);

                                if(is_array($value6))
                                foreach ($value6 as $key7=>$value7){
                                    if(isset($value7[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$unset_index]);

                                    if(is_array($value7))
                                    foreach ($value7 as $key8=>$value8){
                                        if(isset($value8[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$key8][$unset_index]);

                                        if(is_array($value8))
                                        foreach ($value8 as $key9=>$value9){
                                            if(isset($value9[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$key8][$key9][$unset_index]);

                                            if(is_array($value9))
                                            foreach ($value9 as $key10=>$value10){
                                                if(isset($value10[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$key8][$key9][$key10][$unset_index]);

                                                if(is_array($value10))
                                                foreach ($value10 as $key11=>$value11){
                                                    if(isset($value11[$unset_index])) unset($array[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$key8][$key9][$key10][$key11][$unset_index]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $array;
    }

}



if ( ! function_exists('array_format_datatable_data'))
{
  function array_format_datatable_data($result=array(), $columns=array(), $start=0,$primary_key='id')
    {
        unset($columns[0]);
        $have_checkbox=false;
        if(in_array('CHECKBOX', $columns))
        {
          $have_checkbox=true;
          $indexof = array_search("CHECKBOX",$columns);
          unset($columns[$indexof]);
        }

        $final_result = array();

        $sl = $start+1;

        foreach ($result as $key => $single_row) {

            $temp = array(0=>$sl);
            $sl++;

            if($have_checkbox)
            {
              $primary_val = isset($single_row->$primary_key) ? $single_row->$primary_key : 0;
              $str = '<div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input datatableCheckboxRow" name="datatableCheckboxRow[]" id="datatableCheckboxRow'.$primary_val.'" type="checkbox"  value="'.$primary_val.'"></div>';
              $temp[1] = $str;
            }
            foreach ($columns as $key1 => $column_name)
                array_push($temp, $single_row->$column_name);

            array_push($final_result, $temp);
        }

        return $final_result;
    }
}

if ( ! function_exists('array_depth')){
    function array_depth(array $array) {
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = array_depth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }
}

if ( ! function_exists('array_flatten')){
    function array_flatten($array=[],$keep_array_index=false) {
        if(is_json($array)) $array = json_decode($array,true);
        if(!is_array($array)) return [];
        if(empty($array)) return [];


        $result = array();
        foreach ($array as $k1 => $v1) {
            if (is_array($v1)) {
                foreach ($v1 as $k2=>$v2){
                    if (is_array($v2)) {
                        foreach ($v2 as $k3=>$v3){
                            if (is_array($v3)) {
                                foreach ($v3 as $k4=>$v4){
                                    if (is_array($v4)) {
                                        foreach ($v4 as $k5=>$v5){
                                            if (is_array($v5)) {
                                                foreach ($v5 as $k6=>$v6){
                                                    if (is_array($v6)) {
                                                        foreach ($v6 as $k7=>$v7){
                                                            if (is_array($v7)) {
                                                                foreach ($v7 as $k8=>$v8){
                                                                    $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5.'>'.$k6.'>'.$k7.'>'.$k8] = $v8;
                                                                }
                                                            }
                                                            if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5.'>'.$k6.'>'.$k7] = $v7;
                                                        }
                                                    }
                                                    if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5.'>'.$k6] = $v6;
                                                }
                                            }
                                            if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5] = $v5;
                                        }
                                    }
                                    if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4] = $v4;
                                }
                            }
                            if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3] = $v3;
                        }
                    }
                    if($keep_array_index) $result[$k1.'>'.$k2] = $v2;
                }
            }
            $result[$k1] = $v1;
        }
        ksort($result);
        return $result;
    }
}


if( ! function_exists('array_to_obj')){
    function array_to_obj($arr) {
        if(is_json($arr)) $arr = json_decode($arr,true);
        if (is_array($arr)){
            $new_arr = array();
            foreach($arr as $k => $v) {
                $new_arr[$k] = array_to_obj($v);
            }
            return (object) $new_arr;
        }
        return $arr;
    }
}

if ( ! function_exists('url_add_http'))
{
    function url_add_http( $url )
    {

	    if ( !preg_match("~^(?:f|ht)tps?://~i", $url) )
	    {
	        $url = "http://" . $url;
	    }
	    return $url;
	}
}


if ( ! function_exists('url_convert_to_domain'))
{
	function url_convert_to_domain($url,$http=false) {
        $return = $url;
		if(!$http){
            $url=str_replace("www.","",$url);
            $url=str_replace("WWW.","",$url);

            if (!preg_match("@^https?://@i", $url) && !preg_match("@^ftps?://@i", $url)) {
                $url = "http://" . $url;
            }
            $parsed=@parse_url($url);
            $return = $parsed['host'] ?? '';
        }
		else{
            $result = @parse_url($url);
            if(isset($result['scheme'])) $return = $result['scheme']."://".$result['host'];
            else $return = $url;
        }

        $return = trim($return);
        $return = trim($return,'/');
        return $return;
	}
}


if ( ! function_exists('url_add_query_string'))
{
    function url_add_query_string($url,$query_index,$query_value)
    {
        $parameters_str = parse_url($url, PHP_URL_QUERY);
        parse_str($parameters_str, $parameters_array);

        $query_param="{$query_index}={$query_value}";

        if(!isset($parameters_array[$query_index])){
            if ($parameters_str)  $url .= "&{$query_param}";
            else  $url .= "?{$query_param}";
        }
        return $url;
    }

}


if ( ! function_exists('url_convert_to_ascii')) {
    function url_convert_to_ascii($url)
    {
        $parts = parse_url($url);
        if (!isset($parts['host']))
            return $url; // missing http? makes parse_url fails

        if (mb_detect_encoding($parts['host']) != 'ASCII' && function_exists("idn_to_ascii")) {
            $parts['host'] = idn_to_ascii($parts['host']);
            return $parts['scheme'] . "://" . $parts['host'];
        }
        return $url;
    }
}

if ( ! function_exists('url_make_canonical')) {
    function url_make_canonical($url='',$return_url=false)
    {
        if(empty($url)) $url = url()->current();
        $search = ['http://','www.'];
        $replace = ['https://',''];
        $url = @parse_url($url, PHP_URL_PATH);
        $url = str_replace($search,$replace,$url);
        if(str_ends_with($url,'post/1')) $url = str_replace('post/1','',$url);
        $url =  url($url);
        $url = str_replace($search,$replace,$url);
        if($return_url) return $url;
        return '<link rel="canonical" href="'.$url.'">';
    }
}



if ( ! function_exists('json_encode_raw')) {
    function json_encode_raw($input, $flags = 0)
    {
        $fails = implode('|', array_filter(array(
            '\\\\',
            $flags & JSON_HEX_TAG ? 'u003[CE]' : '',
            $flags & JSON_HEX_AMP ? 'u0026' : '',
            $flags & JSON_HEX_APOS ? 'u0027' : '',
            $flags & JSON_HEX_QUOT ? 'u0022' : '',
        )));
        $pattern = "/\\\\(?:(?:$fails)(*SKIP)(*FAIL)|u([0-9a-fA-F]{4}))/";
        $callback = function ($m) {
            return html_entity_decode("&#x$m[1];", ENT_QUOTES, 'UTF-8');
        };
        return preg_replace_callback($pattern, $callback, json_encode($input, $flags));
    }
}


if (! function_exists('json_sanitize')) {
    function json_sanitize($json_string_data) {
        $patterns = ["'", "\\", "\n", "\r", "\t", "\f", "\b"];
        $replacements = ["", "\\\\", "\\\\n", "\\\\r", "\\\\t", "\\\\f", "\\\\b"];
        return str_replace($patterns, $replacements, $json_string_data);
    }
}

if ( ! function_exists('get_send_message_method')) {
    function get_send_message_method($type='text')
    {
        if($type=='image') $method = 'sendPhoto';
        else if($type=='audio') $method = 'sendAudio';
        else if($type=='video') $method = 'sendVideo';
        else if($type=='file') $method = 'sendDocument';
        else $method = 'sendMessage';
        return $method;
    }
}



if ( ! function_exists('spintax_process')) {
    function spintax_process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            "spintax_replace",
            $text
        );
    }
}



if ( ! function_exists('spintax_replace')) {
    function spintax_replace($text)
    {
        $text = spintax_process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}


if ( ! function_exists('check_is_mobile_view')) {

  function check_is_mobile_view() {
      $useragent=$_SERVER['HTTP_USER_AGENT'] ?? '';
      return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $useragent);
    }

}


if ( ! function_exists('check_is_valid_email')) {
    function check_is_valid_email($email){
        $email=trim($email);
        $is_valid=0;
        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        if (preg_match($pattern, $email) === 1) $is_valid=1;
        return $is_valid;
    }
}


if ( ! function_exists('check_is_valid_phone')) {
    function check_is_valid_phone($phone){
        $is_valid=0;
        if(preg_match("#\+\d{7}#",$phone)===1) $is_valid=1;
        return $is_valid;
    }
}


if ( ! function_exists('check_is_valid_date_format')) {
    function check_is_valid_date_format($format,$date){

        if (DateTime::createFromFormat($format, $date) !== FALSE) return true;
        else return false;
    }
}


if ( ! function_exists('check_is_agency_site')) {
    function check_is_agency_site(){
        return false;
    }
}


if ( ! function_exists('pre'))
{
  function pre($val)
        {
            echo "<pre>";
            print_r($val);
            echo "</pre>";
        }
}

if( ! function_exists('get_language_list')){
    function get_language_list(){
        return [
            'ab' => 'Abkhazian',
            'aa' => 'Afar',
            'af' => 'Afrikaans',
            'ak' => 'Akan',
            'sq' => 'Albanian',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'an' => 'Aragonese',
            'hy' => 'Armenian',
            'as' => 'Assamese',
            'av' => 'Avaric',
            'ae' => 'Avestan',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'bm' => 'Bambara',
            'ba' => 'Bashkir',
            'eu' => 'Basque',
            'be' => 'Belarusian',
            'bn' => 'Bengali',
            'bh' => 'Bihari languages',
            'bi' => 'Bislama',
            'bs' => 'Bosnian',
            'br' => 'Breton',
            'bg' => 'Bulgarian',
            'my' => 'Burmese',
            'ca' => 'Catalan, Valencian',
            'km' => 'Khmer',
            'ch' => 'Chamorro',
            'ce' => 'Chechen',
            'ny' => 'Chichewa, Chewa, Nyanja',
            'zh' => 'Chinese',
            'cu' => 'Church Slavonic, Old Bulgarian, Old Church Slavonic',
            'cv' => 'Chuvash',
            'kw' => 'Cornish',
            'co' => 'Corsican',
            'cr' => 'Cree',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'dv' => 'Divehi, Dhivehi, Maldivian',
            'nl' => 'Dutch, Flemish',
            'dz' => 'Dzongkha',
            'en' => 'English',
            'eo' => 'Esperanto',
            'et' => 'Estonian',
            'ee' => 'Ewe',
            'fo' => 'Faroese',
            'fj' => 'Fijian',
            'fi' => 'Finnish',
            'fr' => 'French',
            'ff' => 'Fulah',
            'gd' => 'Gaelic, Scottish Gaelic',
            'gl' => 'Galician',
            'lg' => 'Ganda',
            'ka' => 'Georgian',
            'de' => 'German',
            'ki' => 'Gikuyu, Kikuyu',
            'el' => 'Greek (Modern)',
            'kl' => 'Greenlandic, Kalaallisut',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
            'ht' => 'Haitian, Haitian Creole',
            'ha' => 'Hausa',
            'he' => 'Hebrew',
            'hz' => 'Herero',
            'hi' => 'Hindi',
            'ho' => 'Hiri Motu',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'io' => 'Ido',
            'ig' => 'Igbo',
            'id' => 'Indonesian',
            'ia' => 'Interlingua (International Auxiliary Language Association)',
            'ie' => 'Interlingue',
            'iu' => 'Inuktitut',
            'ik' => 'Inupiaq',
            'ga' => 'Irish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'jv' => 'Javanese',
            'kn' => 'Kannada',
            'kr' => 'Kanuri',
            'ks' => 'Kashmiri',
            'kk' => 'Kazakh',
            'rw' => 'Kinyarwanda',
            'kv' => 'Komi',
            'kg' => 'Kongo',
            'ko' => 'Korean',
            'kj' => 'Kwanyama, Kuanyama',
            'ku' => 'Kurdish',
            'ky' => 'Kyrgyz',
            'lo' => 'Lao',
            'la' => 'Latin',
            'lv' => 'Latvian',
            'lb' => 'Letzeburgesch, Luxembourgish',
            'li' => 'Limburgish, Limburgan, Limburger',
            'ln' => 'Lingala',
            'lt' => 'Lithuanian',
            'lu' => 'Luba-Katanga',
            'mk' => 'Macedonian',
            'mg' => 'Malagasy',
            'ms' => 'Malay',
            'ml' => 'Malayalam',
            'mt' => 'Maltese',
            'gv' => 'Manx',
            'mi' => 'Maori',
            'mr' => 'Marathi',
            'mh' => 'Marshallese',
            'ro' => 'Moldovan, Moldavian, Romanian',
            'mn' => 'Mongolian',
            'na' => 'Nauru',
            'nv' => 'Navajo, Navaho',
            'nd' => 'Northern Ndebele',
            'ng' => 'Ndonga',
            'ne' => 'Nepali',
            'se' => 'Northern Sami',
            'no' => 'Norwegian',
            'nb' => 'Norwegian Bokmål',
            'nn' => 'Norwegian Nynorsk',
            'ii' => 'Nuosu, Sichuan Yi',
            'oc' => 'Occitan (post 1500)',
            'oj' => 'Ojibwa',
            'or' => 'Oriya',
            'om' => 'Oromo',
            'os' => 'Ossetian, Ossetic',
            'pi' => 'Pali',
            'pa' => 'Panjabi, Punjabi',
            'ps' => 'Pashto, Pushto',
            'fa' => 'Persian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'qu' => 'Quechua',
            'rm' => 'Romansh',
            'rn' => 'Rundi',
            'ru' => 'Russian',
            'sm' => 'Samoan',
            'sg' => 'Sango',
            'sa' => 'Sanskrit',
            'sc' => 'Sardinian',
            'sr' => 'Serbian',
            'sn' => 'Shona',
            'sd' => 'Sindhi',
            'si' => 'Sinhala, Sinhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'so' => 'Somali',
            'st' => 'Sotho, Southern',
            'nr' => 'South Ndebele',
            'es' => 'Spanish, Castilian',
            'su' => 'Sundanese',
            'sw' => 'Swahili',
            'ss' => 'Swati',
            'sv' => 'Swedish',
            'tl' => 'Tagalog',
            'ty' => 'Tahitian',
            'tg' => 'Tajik',
            'ta' => 'Tamil',
            'tt' => 'Tatar',
            'te' => 'Telugu',
            'th' => 'Thai',
            'bo' => 'Tibetan',
            'ti' => 'Tigrinya',
            'to' => 'Tonga (Tonga Islands)',
            'ts' => 'Tsonga',
            'tn' => 'Tswana',
            'tr' => 'Turkish',
            'tk' => 'Turkmen',
            'tw' => 'Twi',
            'ug' => 'Uighur, Uyghur',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            've' => 'Venda',
            'vi' => 'Vietnamese',
            'vo' => 'Volap_k',
            'wa' => 'Walloon',
            'cy' => 'Welsh',
            'fy' => 'Western Frisian',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'za' => 'Zhuang, Chuang',
            'zu' => 'Zulu'
        ];
    }
}

if( ! function_exists('get_language_list_facebook')){
    function get_language_list_facebook()
    {
        $config=array(
            'default'=> 'Default',
            "af" => "Afrikaans",
            "sq" => "Albanian",
            "ar" => "Arabic",
            "az" => "Azerbaijani",
            "bn" => "Bengali",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "zh_CN" => "Chinese (CHN)",
            "zh_HK" => "Chinese (HKG)",
            "zh_TW" => "Chinese (TAI)",
            "hr" => "Croatian",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "en_GB" => "English (UK)",
            "en_US" => "English (US)",
            "et" => "Estonian",
            "fil" => "Filipino",
            "fi" => "Finnish",
            "fr" => "French",
            "ka" => "Georgian",
            "de" => "German",
            "el" => "Greek",
            "gu" => "Gujarati",
            "ha" => "Hausa",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hu" => "Hungarian",
            "id" => "Indonesian",
            "ga" => "Irish",
            "it" => "Italian",
            "ja" => "Japanese",
            "kn" => "Kannada",
            "kk" => "Kazakh",
            "rw_RW" => "Kinyarwanda",
            "ko" => "Korean",
            "ky_KG" => "Kyrgyz (Kyrgyzstan)",
            "lo" => "Lao",
            "lv" => "Latvian",
            "lt" => "Lithuanian",
            "mk" => "Macedonian",
            "ms" => "Malay",
            "ml" => "Malayalam",
            "mr" => "Marathi",
            "nb" => "Norwegian",
            "fa" => "Persian",
            "pl" => "Polish",
            "pt_BR" => "Portuguese (BR)",
            "pt_PT" => "Portuguese (POR)",
            "pa" => "Punjabi",
            "ro" => "Romanian",
            "ru" => "Russian",
            "sr" => "Serbian",
            "sk" => "Slovak",
            "sv" => "Swedish",
            "sl" => "Slovenian",
            "es" => "Spanish",
            "es_AR" => "Spanish (ARG)",
            "es_ES" => "Spanish (SPA)",
            "es_MX" => "Spanish (MEX)",
            "sw" => "Swahili",
            "sv" => "Swedish",
            "ta" => "Tamil",
            "te" => "Telugu",
            "th" => "Thai",
            "tr" => "Turkish",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "uz" => "Uzbek",
            "vi" => "Vietnamese",
            "zu" => "Zulu",
        );

        asort($config);
        return $config;
    }
}


if ( ! function_exists('get_rtl_langauge_list')){
    function get_rtl_langauge_list(){
        return [
           'ar' => 'Arabic',
           'dv' => 'Divehi, Dhivehi, Maldivian',
           'he' => 'Hebrew',
           'ku' => 'Kurdish',
           'fa' => 'Persian',
           'ur' => 'Urdu'
        ];
    }
}




if ( ! function_exists('get_timezone_list')){
    function get_timezone_list()
    {
        return $timezones =
        array(
            'America/Adak' => '(GMT-10:00) America/Adak (Hawaii-Aleutian Standard Time)',
            'America/Atka' => '(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)',
            'America/Anchorage' => '(GMT-9:00) America/Anchorage (Alaska Standard Time)',
            'America/Juneau' => '(GMT-9:00) America/Juneau (Alaska Standard Time)',
            'America/Nome' => '(GMT-9:00) America/Nome (Alaska Standard Time)',
            'America/Yakutat' => '(GMT-9:00) America/Yakutat (Alaska Standard Time)',
            'America/Dawson' => '(GMT-8:00) America/Dawson (Pacific Standard Time)',
            'America/Ensenada' => '(GMT-8:00) America/Ensenada (Pacific Standard Time)',
            'America/Los_Angeles' => '(GMT-8:00) America/Los_Angeles (Pacific Standard Time)',
            'America/Tijuana' => '(GMT-8:00) America/Tijuana (Pacific Standard Time)',
            'America/Vancouver' => '(GMT-8:00) America/Vancouver (Pacific Standard Time)',
            'America/Whitehorse' => '(GMT-8:00) America/Whitehorse (Pacific Standard Time)',
            'Canada/Pacific' => '(GMT-8:00) Canada/Pacific (Pacific Standard Time)',
            'Canada/Yukon' => '(GMT-8:00) Canada/Yukon (Pacific Standard Time)',
            'Mexico/BajaNorte' => '(GMT-8:00) Mexico/BajaNorte (Pacific Standard Time)',
            'America/Boise' => '(GMT-7:00) America/Boise (Mountain Standard Time)',
            'America/Cambridge_Bay' => '(GMT-7:00) America/Cambridge_Bay (Mountain Standard Time)',
            'America/Chihuahua' => '(GMT-7:00) America/Chihuahua (Mountain Standard Time)',
            'America/Dawson_Creek' => '(GMT-7:00) America/Dawson_Creek (Mountain Standard Time)',
            'America/Denver' => '(GMT-7:00) America/Denver (Mountain Standard Time)',
            'America/Edmonton' => '(GMT-7:00) America/Edmonton (Mountain Standard Time)',
            'America/Hermosillo' => '(GMT-7:00) America/Hermosillo (Mountain Standard Time)',
            'America/Inuvik' => '(GMT-7:00) America/Inuvik (Mountain Standard Time)',
            'America/Mazatlan' => '(GMT-7:00) America/Mazatlan (Mountain Standard Time)',
            'America/Phoenix' => '(GMT-7:00) America/Phoenix (Mountain Standard Time)',
            'America/Shiprock' => '(GMT-7:00) America/Shiprock (Mountain Standard Time)',
            'America/Yellowknife' => '(GMT-7:00) America/Yellowknife (Mountain Standard Time)',
            'Canada/Mountain' => '(GMT-7:00) Canada/Mountain (Mountain Standard Time)',
            'Mexico/BajaSur' => '(GMT-7:00) Mexico/BajaSur (Mountain Standard Time)',
            'America/Belize' => '(GMT-6:00) America/Belize (Central Standard Time)',
            'America/Cancun' => '(GMT-6:00) America/Cancun (Central Standard Time)',
            'America/Chicago' => '(GMT-6:00) America/Chicago (Central Standard Time)',
            'America/Costa_Rica' => '(GMT-6:00) America/Costa_Rica (Central Standard Time)',
            'America/El_Salvador' => '(GMT-6:00) America/El_Salvador (Central Standard Time)',
            'America/Guatemala' => '(GMT-6:00) America/Guatemala (Central Standard Time)',
            'America/Knox_IN' => '(GMT-6:00) America/Knox_IN (Central Standard Time)',
            'America/Managua' => '(GMT-6:00) America/Managua (Central Standard Time)',
            'America/Menominee' => '(GMT-6:00) America/Menominee (Central Standard Time)',
            'America/Merida' => '(GMT-6:00) America/Merida (Central Standard Time)',
            'America/Mexico_City' => '(GMT-6:00) America/Mexico_City (Central Standard Time)',
            'America/Monterrey' => '(GMT-6:00) America/Monterrey (Central Standard Time)',
            'America/Rainy_River' => '(GMT-6:00) America/Rainy_River (Central Standard Time)',
            'America/Rankin_Inlet' => '(GMT-6:00) America/Rankin_Inlet (Central Standard Time)',
            'America/Regina' => '(GMT-6:00) America/Regina (Central Standard Time)',
            'America/Swift_Current' => '(GMT-6:00) America/Swift_Current (Central Standard Time)',
            'America/Tegucigalpa' => '(GMT-6:00) America/Tegucigalpa (Central Standard Time)',
            'America/Winnipeg' => '(GMT-6:00) America/Winnipeg (Central Standard Time)',
            'Canada/Central' => '(GMT-6:00) Canada/Central (Central Standard Time)',
            'Canada/East-Saskatchewan' => '(GMT-6:00) Canada/East-Saskatchewan (Central Standard Time)',
            'Canada/Saskatchewan' => '(GMT-6:00) Canada/Saskatchewan (Central Standard Time)',
            'Chile/EasterIsland' => '(GMT-6:00) Chile/EasterIsland (Easter Is. Time)',
            'Mexico/General' => '(GMT-6:00) Mexico/General (Central Standard Time)',
            'America/Atikokan' => '(GMT-5:00) America/Atikokan (Eastern Standard Time)',
            'America/Bogota' => '(GMT-5:00) America/Bogota (Colombia Time)',
            'America/Cayman' => '(GMT-5:00) America/Cayman (Eastern Standard Time)',
            'America/Coral_Harbour' => '(GMT-5:00) America/Coral_Harbour (Eastern Standard Time)',
            'America/Detroit' => '(GMT-5:00) America/Detroit (Eastern Standard Time)',
            'America/Fort_Wayne' => '(GMT-5:00) America/Fort_Wayne (Eastern Standard Time)',
            'America/Grand_Turk' => '(GMT-5:00) America/Grand_Turk (Eastern Standard Time)',
            'America/Guayaquil' => '(GMT-5:00) America/Guayaquil (Ecuador Time)',
            'America/Havana' => '(GMT-5:00) America/Havana (Cuba Standard Time)',
            'America/Indianapolis' => '(GMT-5:00) America/Indianapolis (Eastern Standard Time)',
            'America/Iqaluit' => '(GMT-5:00) America/Iqaluit (Eastern Standard Time)',
            'America/Jamaica' => '(GMT-5:00) America/Jamaica (Eastern Standard Time)',
            'America/Lima' => '(GMT-5:00) America/Lima (Peru Time)',
            'America/Louisville' => '(GMT-5:00) America/Louisville (Eastern Standard Time)',
            'America/Montreal' => '(GMT-5:00) America/Montreal (Eastern Standard Time)',
            'America/Nassau' => '(GMT-5:00) America/Nassau (Eastern Standard Time)',
            'America/New_York' => '(GMT-5:00) America/New_York (Eastern Standard Time)',
            'America/Nipigon' => '(GMT-5:00) America/Nipigon (Eastern Standard Time)',
            'America/Panama' => '(GMT-5:00) America/Panama (Eastern Standard Time)',
            'America/Pangnirtung' => '(GMT-5:00) America/Pangnirtung (Eastern Standard Time)',
            'America/Port-au-Prince' => '(GMT-5:00) America/Port-au-Prince (Eastern Standard Time)',
            'America/Resolute' => '(GMT-5:00) America/Resolute (Eastern Standard Time)',
            'America/Thunder_Bay' => '(GMT-5:00) America/Thunder_Bay (Eastern Standard Time)',
            'America/Toronto' => '(GMT-5:00) America/Toronto (Eastern Standard Time)',
            'Canada/Eastern' => '(GMT-5:00) Canada/Eastern (Eastern Standard Time)',
            'America/Caracas' => '(GMT-4:-30) America/Caracas (Venezuela Time)',
            'America/Anguilla' => '(GMT-4:00) America/Anguilla (Atlantic Standard Time)',
            'America/Antigua' => '(GMT-4:00) America/Antigua (Atlantic Standard Time)',
            'America/Aruba' => '(GMT-4:00) America/Aruba (Atlantic Standard Time)',
            'America/Asuncion' => '(GMT-4:00) America/Asuncion (Paraguay Time)',
            'America/Barbados' => '(GMT-4:00) America/Barbados (Atlantic Standard Time)',
            'America/Blanc-Sablon' => '(GMT-4:00) America/Blanc-Sablon (Atlantic Standard Time)',
            'America/Boa_Vista' => '(GMT-4:00) America/Boa_Vista (Amazon Time)',
            'America/Campo_Grande' => '(GMT-4:00) America/Campo_Grande (Amazon Time)',
            'America/Cuiaba' => '(GMT-4:00) America/Cuiaba (Amazon Time)',
            'America/Curacao' => '(GMT-4:00) America/Curacao (Atlantic Standard Time)',
            'America/Dominica' => '(GMT-4:00) America/Dominica (Atlantic Standard Time)',
            'America/Eirunepe' => '(GMT-4:00) America/Eirunepe (Amazon Time)',
            'America/Glace_Bay' => '(GMT-4:00) America/Glace_Bay (Atlantic Standard Time)',
            'America/Goose_Bay' => '(GMT-4:00) America/Goose_Bay (Atlantic Standard Time)',
            'America/Grenada' => '(GMT-4:00) America/Grenada (Atlantic Standard Time)',
            'America/Guadeloupe' => '(GMT-4:00) America/Guadeloupe (Atlantic Standard Time)',
            'America/Guyana' => '(GMT-4:00) America/Guyana (Guyana Time)',
            'America/Halifax' => '(GMT-4:00) America/Halifax (Atlantic Standard Time)',
            'America/La_Paz' => '(GMT-4:00) America/La_Paz (Bolivia Time)',
            'America/Manaus' => '(GMT-4:00) America/Manaus (Amazon Time)',
            'America/Marigot' => '(GMT-4:00) America/Marigot (Atlantic Standard Time)',
            'America/Martinique' => '(GMT-4:00) America/Martinique (Atlantic Standard Time)',
            'America/Moncton' => '(GMT-4:00) America/Moncton (Atlantic Standard Time)',
            'America/Montserrat' => '(GMT-4:00) America/Montserrat (Atlantic Standard Time)',
            'America/Port_of_Spain' => '(GMT-4:00) America/Port_of_Spain (Atlantic Standard Time)',
            'America/Porto_Acre' => '(GMT-4:00) America/Porto_Acre (Amazon Time)',
            'America/Porto_Velho' => '(GMT-4:00) America/Porto_Velho (Amazon Time)',
            'America/Puerto_Rico' => '(GMT-4:00) America/Puerto_Rico (Atlantic Standard Time)',
            'America/Rio_Branco' => '(GMT-4:00) America/Rio_Branco (Amazon Time)',
            'America/Santiago' => '(GMT-4:00) America/Santiago (Chile Time)',
            'America/Santo_Domingo' => '(GMT-4:00) America/Santo_Domingo (Atlantic Standard Time)',
            'America/St_Barthelemy' => '(GMT-4:00) America/St_Barthelemy (Atlantic Standard Time)',
            'America/St_Kitts' => '(GMT-4:00) America/St_Kitts (Atlantic Standard Time)',
            'America/St_Lucia' => '(GMT-4:00) America/St_Lucia (Atlantic Standard Time)',
            'America/St_Thomas' => '(GMT-4:00) America/St_Thomas (Atlantic Standard Time)',
            'America/St_Vincent' => '(GMT-4:00) America/St_Vincent (Atlantic Standard Time)',
            'America/Thule' => '(GMT-4:00) America/Thule (Atlantic Standard Time)',
            'America/Tortola' => '(GMT-4:00) America/Tortola (Atlantic Standard Time)',
            'America/Virgin' => '(GMT-4:00) America/Virgin (Atlantic Standard Time)',
            'Antarctica/Palmer' => '(GMT-4:00) Antarctica/Palmer (Chile Time)',
            'Atlantic/Bermuda' => '(GMT-4:00) Atlantic/Bermuda (Atlantic Standard Time)',
            'Atlantic/Stanley' => '(GMT-4:00) Atlantic/Stanley (Falkland Is. Time)',
            'Brazil/Acre' => '(GMT-4:00) Brazil/Acre (Amazon Time)',
            'Brazil/West' => '(GMT-4:00) Brazil/West (Amazon Time)',
            'Canada/Atlantic' => '(GMT-4:00) Canada/Atlantic (Atlantic Standard Time)',
            'Chile/Continental' => '(GMT-4:00) Chile/Continental (Chile Time)',
            'America/St_Johns' => '(GMT-3:-30) America/St_Johns (Newfoundland Standard Time)',
            'Canada/Newfoundland' => '(GMT-3:-30) Canada/Newfoundland (Newfoundland Standard Time)',
            'America/Araguaina' => '(GMT-3:00) America/Araguaina (Brasilia Time)',
            'America/Bahia' => '(GMT-3:00) America/Bahia (Brasilia Time)',
            'America/Belem' => '(GMT-3:00) America/Belem (Brasilia Time)',
            'America/Buenos_Aires' => '(GMT-3:00) America/Buenos_Aires (Argentine Time)',
            'America/Catamarca' => '(GMT-3:00) America/Catamarca (Argentine Time)',
            'America/Cayenne' => '(GMT-3:00) America/Cayenne (French Guiana Time)',
            'America/Cordoba' => '(GMT-3:00) America/Cordoba (Argentine Time)',
            'America/Fortaleza' => '(GMT-3:00) America/Fortaleza (Brasilia Time)',
            'America/Godthab' => '(GMT-3:00) America/Godthab (Western Greenland Time)',
            'America/Jujuy' => '(GMT-3:00) America/Jujuy (Argentine Time)',
            'America/Maceio' => '(GMT-3:00) America/Maceio (Brasilia Time)',
            'America/Mendoza' => '(GMT-3:00) America/Mendoza (Argentine Time)',
            'America/Miquelon' => '(GMT-3:00) America/Miquelon (Pierre & Miquelon Standard Time)',
            'America/Montevideo' => '(GMT-3:00) America/Montevideo (Uruguay Time)',
            'America/Paramaribo' => '(GMT-3:00) America/Paramaribo (Suriname Time)',
            'America/Recife' => '(GMT-3:00) America/Recife (Brasilia Time)',
            'America/Rosario' => '(GMT-3:00) America/Rosario (Argentine Time)',
            'America/Santarem' => '(GMT-3:00) America/Santarem (Brasilia Time)',
            'America/Sao_Paulo' => '(GMT-3:00) America/Sao_Paulo (Brasilia Time)',
            'Antarctica/Rothera' => '(GMT-3:00) Antarctica/Rothera (Rothera Time)',
            'Brazil/East' => '(GMT-3:00) Brazil/East (Brasilia Time)',
            'America/Noronha' => '(GMT-2:00) America/Noronha (Fernando de Noronha Time)',
            'Atlantic/South_Georgia' => '(GMT-2:00) Atlantic/South_Georgia (South Georgia Standard Time)',
            'Brazil/DeNoronha' => '(GMT-2:00) Brazil/DeNoronha (Fernando de Noronha Time)',
            'America/Scoresbysund' => '(GMT-1:00) America/Scoresbysund (Eastern Greenland Time)',
            'Atlantic/Azores' => '(GMT-1:00) Atlantic/Azores (Azores Time)',
            'Atlantic/Cape_Verde' => '(GMT-1:00) Atlantic/Cape_Verde (Cape Verde Time)',
            'Africa/Abidjan' => '(GMT+0:00) Africa/Abidjan (Greenwich Mean Time)',
            'Africa/Accra' => '(GMT+0:00) Africa/Accra (Ghana Mean Time)',
            'Africa/Bamako' => '(GMT+0:00) Africa/Bamako (Greenwich Mean Time)',
            'Africa/Banjul' => '(GMT+0:00) Africa/Banjul (Greenwich Mean Time)',
            'Africa/Bissau' => '(GMT+0:00) Africa/Bissau (Greenwich Mean Time)',
            'Africa/Casablanca' => '(GMT+0:00) Africa/Casablanca (Western European Time)',
            'Africa/Conakry' => '(GMT+0:00) Africa/Conakry (Greenwich Mean Time)',
            'Africa/Dakar' => '(GMT+0:00) Africa/Dakar (Greenwich Mean Time)',
            'Africa/El_Aaiun' => '(GMT+0:00) Africa/El_Aaiun (Western European Time)',
            'Africa/Freetown' => '(GMT+0:00) Africa/Freetown (Greenwich Mean Time)',
            'Africa/Lome' => '(GMT+0:00) Africa/Lome (Greenwich Mean Time)',
            'Africa/Monrovia' => '(GMT+0:00) Africa/Monrovia (Greenwich Mean Time)',
            'Africa/Nouakchott' => '(GMT+0:00) Africa/Nouakchott (Greenwich Mean Time)',
            'Africa/Ouagadougou' => '(GMT+0:00) Africa/Ouagadougou (Greenwich Mean Time)',
            'Africa/Sao_Tome' => '(GMT+0:00) Africa/Sao_Tome (Greenwich Mean Time)',
            'Africa/Timbuktu' => '(GMT+0:00) Africa/Timbuktu (Greenwich Mean Time)',
            'America/Danmarkshavn' => '(GMT+0:00) America/Danmarkshavn (Greenwich Mean Time)',
            'Atlantic/Canary' => '(GMT+0:00) Atlantic/Canary (Western European Time)',
            'Atlantic/Faeroe' => '(GMT+0:00) Atlantic/Faeroe (Western European Time)',
            'Atlantic/Faroe' => '(GMT+0:00) Atlantic/Faroe (Western European Time)',
            'Atlantic/Madeira' => '(GMT+0:00) Atlantic/Madeira (Western European Time)',
            'Atlantic/Reykjavik' => '(GMT+0:00) Atlantic/Reykjavik (Greenwich Mean Time)',
            'Atlantic/St_Helena' => '(GMT+0:00) Atlantic/St_Helena (Greenwich Mean Time)',
            'Europe/Belfast' => '(GMT+0:00) Europe/Belfast (Greenwich Mean Time)',
            'Europe/Dublin' => '(GMT+0:00) Europe/Dublin (Greenwich Mean Time)',
            'Europe/Guernsey' => '(GMT+0:00) Europe/Guernsey (Greenwich Mean Time)',
            'Europe/Isle_of_Man' => '(GMT+0:00) Europe/Isle_of_Man (Greenwich Mean Time)',
            'Europe/Jersey' => '(GMT+0:00) Europe/Jersey (Greenwich Mean Time)',
            'Europe/Lisbon' => '(GMT+0:00) Europe/Lisbon (Western European Time)',
            'Europe/London' => '(GMT+0:00) Europe/London (Greenwich Mean Time)',
            'Africa/Algiers' => '(GMT+1:00) Africa/Algiers (Central European Time)',
            'Africa/Bangui' => '(GMT+1:00) Africa/Bangui (Western African Time)',
            'Africa/Brazzaville' => '(GMT+1:00) Africa/Brazzaville (Western African Time)',
            'Africa/Ceuta' => '(GMT+1:00) Africa/Ceuta (Central European Time)',
            'Africa/Douala' => '(GMT+1:00) Africa/Douala (Western African Time)',
            'Africa/Kinshasa' => '(GMT+1:00) Africa/Kinshasa (Western African Time)',
            'Africa/Lagos' => '(GMT+1:00) Africa/Lagos (Western African Time)',
            'Africa/Libreville' => '(GMT+1:00) Africa/Libreville (Western African Time)',
            'Africa/Luanda' => '(GMT+1:00) Africa/Luanda (Western African Time)',
            'Africa/Malabo' => '(GMT+1:00) Africa/Malabo (Western African Time)',
            'Africa/Ndjamena' => '(GMT+1:00) Africa/Ndjamena (Western African Time)',
            'Africa/Niamey' => '(GMT+1:00) Africa/Niamey (Western African Time)',
            'Africa/Porto-Novo' => '(GMT+1:00) Africa/Porto-Novo (Western African Time)',
            'Africa/Tunis' => '(GMT+1:00) Africa/Tunis (Central European Time)',
            'Africa/Windhoek' => '(GMT+1:00) Africa/Windhoek (Western African Time)',
            'Arctic/Longyearbyen' => '(GMT+1:00) Arctic/Longyearbyen (Central European Time)',
            'Atlantic/Jan_Mayen' => '(GMT+1:00) Atlantic/Jan_Mayen (Central European Time)',
            'Europe/Amsterdam' => '(GMT+1:00) Europe/Amsterdam (Central European Time)',
            'Europe/Andorra' => '(GMT+1:00) Europe/Andorra (Central European Time)',
            'Europe/Belgrade' => '(GMT+1:00) Europe/Belgrade (Central European Time)',
            'Europe/Berlin' => '(GMT+1:00) Europe/Berlin (Central European Time)',
            'Europe/Bratislava' => '(GMT+1:00) Europe/Bratislava (Central European Time)',
            'Europe/Brussels' => '(GMT+1:00) Europe/Brussels (Central European Time)',
            'Europe/Budapest' => '(GMT+1:00) Europe/Budapest (Central European Time)',
            'Europe/Copenhagen' => '(GMT+1:00) Europe/Copenhagen (Central European Time)',
            'Europe/Gibraltar' => '(GMT+1:00) Europe/Gibraltar (Central European Time)',
            'Europe/Ljubljana' => '(GMT+1:00) Europe/Ljubljana (Central European Time)',
            'Europe/Luxembourg' => '(GMT+1:00) Europe/Luxembourg (Central European Time)',
            'Europe/Madrid' => '(GMT+1:00) Europe/Madrid (Central European Time)',
            'Europe/Malta' => '(GMT+1:00) Europe/Malta (Central European Time)',
            'Europe/Monaco' => '(GMT+1:00) Europe/Monaco (Central European Time)',
            'Europe/Oslo' => '(GMT+1:00) Europe/Oslo (Central European Time)',
            'Europe/Paris' => '(GMT+1:00) Europe/Paris (Central European Time)',
            'Europe/Podgorica' => '(GMT+1:00) Europe/Podgorica (Central European Time)',
            'Europe/Prague' => '(GMT+1:00) Europe/Prague (Central European Time)',
            'Europe/Rome' => '(GMT+1:00) Europe/Rome (Central European Time)',
            'Europe/San_Marino' => '(GMT+1:00) Europe/San_Marino (Central European Time)',
            'Europe/Sarajevo' => '(GMT+1:00) Europe/Sarajevo (Central European Time)',
            'Europe/Skopje' => '(GMT+1:00) Europe/Skopje (Central European Time)',
            'Europe/Stockholm' => '(GMT+1:00) Europe/Stockholm (Central European Time)',
            'Europe/Tirane' => '(GMT+1:00) Europe/Tirane (Central European Time)',
            'Europe/Vaduz' => '(GMT+1:00) Europe/Vaduz (Central European Time)',
            'Europe/Vatican' => '(GMT+1:00) Europe/Vatican (Central European Time)',
            'Europe/Vienna' => '(GMT+1:00) Europe/Vienna (Central European Time)',
            'Europe/Warsaw' => '(GMT+1:00) Europe/Warsaw (Central European Time)',
            'Europe/Zagreb' => '(GMT+1:00) Europe/Zagreb (Central European Time)',
            'Europe/Zurich' => '(GMT+1:00) Europe/Zurich (Central European Time)',
            'Africa/Blantyre' => '(GMT+2:00) Africa/Blantyre (Central African Time)',
            'Africa/Bujumbura' => '(GMT+2:00) Africa/Bujumbura (Central African Time)',
            'Africa/Cairo' => '(GMT+2:00) Africa/Cairo (Eastern European Time)',
            'Africa/Gaborone' => '(GMT+2:00) Africa/Gaborone (Central African Time)',
            'Africa/Harare' => '(GMT+2:00) Africa/Harare (Central African Time)',
            'Africa/Johannesburg' => '(GMT+2:00) Africa/Johannesburg (South Africa Standard Time)',
            'Africa/Kigali' => '(GMT+2:00) Africa/Kigali (Central African Time)',
            'Africa/Lubumbashi' => '(GMT+2:00) Africa/Lubumbashi (Central African Time)',
            'Africa/Lusaka' => '(GMT+2:00) Africa/Lusaka (Central African Time)',
            'Africa/Maputo' => '(GMT+2:00) Africa/Maputo (Central African Time)',
            'Africa/Maseru' => '(GMT+2:00) Africa/Maseru (South Africa Standard Time)',
            'Africa/Mbabane' => '(GMT+2:00) Africa/Mbabane (South Africa Standard Time)',
            'Africa/Tripoli' => '(GMT+2:00) Africa/Tripoli (Eastern European Time)',
            'Asia/Amman' => '(GMT+2:00) Asia/Amman (Eastern European Time)',
            'Asia/Beirut' => '(GMT+2:00) Asia/Beirut (Eastern European Time)',
            'Asia/Damascus' => '(GMT+2:00) Asia/Damascus (Eastern European Time)',
            'Asia/Gaza' => '(GMT+2:00) Asia/Gaza (Eastern European Time)',
            'Asia/Istanbul' => '(GMT+2:00) Asia/Istanbul (Eastern European Time)',
            'Asia/Jerusalem' => '(GMT+2:00) Asia/Jerusalem (Israel Standard Time)',
            'Asia/Nicosia' => '(GMT+2:00) Asia/Nicosia (Eastern European Time)',
            'Asia/Tel_Aviv' => '(GMT+2:00) Asia/Tel_Aviv (Israel Standard Time)',
            'Europe/Athens' => '(GMT+2:00) Europe/Athens (Eastern European Time)',
            'Europe/Bucharest' => '(GMT+2:00) Europe/Bucharest (Eastern European Time)',
            'Europe/Chisinau' => '(GMT+2:00) Europe/Chisinau (Eastern European Time)',
            'Europe/Helsinki' => '(GMT+2:00) Europe/Helsinki (Eastern European Time)',
            'Europe/Istanbul' => '(GMT+2:00) Europe/Istanbul (Eastern European Time)',
            'Europe/Kaliningrad' => '(GMT+2:00) Europe/Kaliningrad (Eastern European Time)',
            'Europe/Kiev' => '(GMT+2:00) Europe/Kiev (Eastern European Time)',
            'Europe/Mariehamn' => '(GMT+2:00) Europe/Mariehamn (Eastern European Time)',
            'Europe/Minsk' => '(GMT+2:00) Europe/Minsk (Eastern European Time)',
            'Europe/Nicosia' => '(GMT+2:00) Europe/Nicosia (Eastern European Time)',
            'Europe/Riga' => '(GMT+2:00) Europe/Riga (Eastern European Time)',
            'Europe/Simferopol' => '(GMT+2:00) Europe/Simferopol (Eastern European Time)',
            'Europe/Sofia' => '(GMT+2:00) Europe/Sofia (Eastern European Time)',
            'Europe/Tallinn' => '(GMT+2:00) Europe/Tallinn (Eastern European Time)',
            'Europe/Tiraspol' => '(GMT+2:00) Europe/Tiraspol (Eastern European Time)',
            'Europe/Uzhgorod' => '(GMT+2:00) Europe/Uzhgorod (Eastern European Time)',
            'Europe/Vilnius' => '(GMT+2:00) Europe/Vilnius (Eastern European Time)',
            'Europe/Zaporozhye' => '(GMT+2:00) Europe/Zaporozhye (Eastern European Time)',
            'Africa/Addis_Ababa' => '(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)',
            'Africa/Asmara' => '(GMT+3:00) Africa/Asmara (Eastern African Time)',
            'Africa/Asmera' => '(GMT+3:00) Africa/Asmera (Eastern African Time)',
            'Africa/Dar_es_Salaam' => '(GMT+3:00) Africa/Dar_es_Salaam (Eastern African Time)',
            'Africa/Djibouti' => '(GMT+3:00) Africa/Djibouti (Eastern African Time)',
            'Africa/Kampala' => '(GMT+3:00) Africa/Kampala (Eastern African Time)',
            'Africa/Khartoum' => '(GMT+3:00) Africa/Khartoum (Eastern African Time)',
            'Africa/Mogadishu' => '(GMT+3:00) Africa/Mogadishu (Eastern African Time)',
            'Africa/Nairobi' => '(GMT+3:00) Africa/Nairobi (Eastern African Time)',
            'Antarctica/Syowa' => '(GMT+3:00) Antarctica/Syowa (Syowa Time)',
            'Asia/Aden' => '(GMT+3:00) Asia/Aden (Arabia Standard Time)',
            'Asia/Baghdad' => '(GMT+3:00) Asia/Baghdad (Arabia Standard Time)',
            'Asia/Bahrain' => '(GMT+3:00) Asia/Bahrain (Arabia Standard Time)',
            'Asia/Kuwait' => '(GMT+3:00) Asia/Kuwait (Arabia Standard Time)',
            'Asia/Qatar' => '(GMT+3:00) Asia/Qatar (Arabia Standard Time)',
            'Europe/Moscow' => '(GMT+3:00) Europe/Moscow (Moscow Standard Time)',
            'Europe/Volgograd' => '(GMT+3:00) Europe/Volgograd (Volgograd Time)',
            'Indian/Antananarivo' => '(GMT+3:00) Indian/Antananarivo (Eastern African Time)',
            'Indian/Comoro' => '(GMT+3:00) Indian/Comoro (Eastern African Time)',
            'Indian/Mayotte' => '(GMT+3:00) Indian/Mayotte (Eastern African Time)',
            'Asia/Tehran' => '(GMT+3:30) Asia/Tehran (Iran Standard Time)',
            'Asia/Baku' => '(GMT+4:00) Asia/Baku (Azerbaijan Time)',
            'Asia/Dubai' => '(GMT+4:00) Asia/Dubai (Gulf Standard Time)',
            'Asia/Muscat' => '(GMT+4:00) Asia/Muscat (Gulf Standard Time)',
            'Asia/Tbilisi' => '(GMT+4:00) Asia/Tbilisi (Georgia Time)',
            'Asia/Yerevan' => '(GMT+4:00) Asia/Yerevan (Armenia Time)',
            'Europe/Samara' => '(GMT+4:00) Europe/Samara (Samara Time)',
            'Indian/Mahe' => '(GMT+4:00) Indian/Mahe (Seychelles Time)',
            'Indian/Mauritius' => '(GMT+4:00) Indian/Mauritius (Mauritius Time)',
            'Indian/Reunion' => '(GMT+4:00) Indian/Reunion (Reunion Time)',
            'Asia/Kabul' => '(GMT+4:30) Asia/Kabul (Afghanistan Time)',
            'Asia/Aqtau' => '(GMT+5:00) Asia/Aqtau (Aqtau Time)',
            'Asia/Aqtobe' => '(GMT+5:00) Asia/Aqtobe (Aqtobe Time)',
            'Asia/Ashgabat' => '(GMT+5:00) Asia/Ashgabat (Turkmenistan Time)',
            'Asia/Ashkhabad' => '(GMT+5:00) Asia/Ashkhabad (Turkmenistan Time)',
            'Asia/Dushanbe' => '(GMT+5:00) Asia/Dushanbe (Tajikistan Time)',
            'Asia/Karachi' => '(GMT+5:00) Asia/Karachi (Pakistan Time)',
            'Asia/Oral' => '(GMT+5:00) Asia/Oral (Oral Time)',
            'Asia/Samarkand' => '(GMT+5:00) Asia/Samarkand (Uzbekistan Time)',
            'Asia/Tashkent' => '(GMT+5:00) Asia/Tashkent (Uzbekistan Time)',
            'Asia/Yekaterinburg' => '(GMT+5:00) Asia/Yekaterinburg (Yekaterinburg Time)',
            'Indian/Kerguelen' => '(GMT+5:00) Indian/Kerguelen (French Southern & Antarctic Lands Time)',
            'Indian/Maldives' => '(GMT+5:00) Indian/Maldives (Maldives Time)',
            'Asia/Calcutta' => '(GMT+5:30) Asia/Calcutta (India Standard Time)',
            'Asia/Colombo' => '(GMT+5:30) Asia/Colombo (India Standard Time)',
            'Asia/Kolkata' => '(GMT+5:30) Asia/Kolkata (India Standard Time)',
            'Asia/Katmandu' => '(GMT+5:45) Asia/Katmandu (Nepal Time)',
            'Antarctica/Mawson' => '(GMT+6:00) Antarctica/Mawson (Mawson Time)',
            'Antarctica/Vostok' => '(GMT+6:00) Antarctica/Vostok (Vostok Time)',
            'Asia/Almaty' => '(GMT+6:00) Asia/Almaty (Alma-Ata Time)',
            'Asia/Bishkek' => '(GMT+6:00) Asia/Bishkek (Kirgizstan Time)',
            'Asia/Dhaka' => '(GMT+6:00) Asia/Dhaka (Bangladesh Time)',
            'Asia/Novosibirsk' => '(GMT+6:00) Asia/Novosibirsk (Novosibirsk Time)',
            'Asia/Omsk' => '(GMT+6:00) Asia/Omsk (Omsk Time)',
            'Asia/Qyzylorda' => '(GMT+6:00) Asia/Qyzylorda (Qyzylorda Time)',
            'Asia/Thimbu' => '(GMT+6:00) Asia/Thimbu (Bhutan Time)',
            'Asia/Thimphu' => '(GMT+6:00) Asia/Thimphu (Bhutan Time)',
            'Indian/Chagos' => '(GMT+6:00) Indian/Chagos (Indian Ocean Territory Time)',
            'Asia/Rangoon' => '(GMT+6:30) Asia/Rangoon (Myanmar Time)',
            'Indian/Cocos' => '(GMT+6:30) Indian/Cocos (Cocos Islands Time)',
            'Antarctica/Davis' => '(GMT+7:00) Antarctica/Davis (Davis Time)',
            'Asia/Bangkok' => '(GMT+7:00) Asia/Bangkok (Indochina Time)',
            'Asia/Ho_Chi_Minh' => '(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)',
            'Asia/Hovd' => '(GMT+7:00) Asia/Hovd (Hovd Time)',
            'Asia/Jakarta' => '(GMT+7:00) Asia/Jakarta (West Indonesia Time)',
            'Asia/Krasnoyarsk' => '(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)',
            'Asia/Phnom_Penh' => '(GMT+7:00) Asia/Phnom_Penh (Indochina Time)',
            'Asia/Pontianak' => '(GMT+7:00) Asia/Pontianak (West Indonesia Time)',
            'Asia/Saigon' => '(GMT+7:00) Asia/Saigon (Indochina Time)',
            'Asia/Vientiane' => '(GMT+7:00) Asia/Vientiane (Indochina Time)',
            'Indian/Christmas' => '(GMT+7:00) Indian/Christmas (Christmas Island Time)',
            'Antarctica/Casey' => '(GMT+8:00) Antarctica/Casey (Western Standard Time (Australia))',
            'Asia/Brunei' => '(GMT+8:00) Asia/Brunei (Brunei Time)',
            'Asia/Choibalsan' => '(GMT+8:00) Asia/Choibalsan (Choibalsan Time)',
            'Asia/Chongqing' => '(GMT+8:00) Asia/Chongqing (China Standard Time)',
            'Asia/Chungking' => '(GMT+8:00) Asia/Chungking (China Standard Time)',
            'Asia/Harbin' => '(GMT+8:00) Asia/Harbin (China Standard Time)',
            'Asia/Hong_Kong' => '(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)',
            'Asia/Irkutsk' => '(GMT+8:00) Asia/Irkutsk (Irkutsk Time)',
            'Asia/Kashgar' => '(GMT+8:00) Asia/Kashgar (China Standard Time)',
            'Asia/Kuala_Lumpur' => '(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)',
            'Asia/Kuching' => '(GMT+8:00) Asia/Kuching (Malaysia Time)',
            'Asia/Macao' => '(GMT+8:00) Asia/Macao (China Standard Time)',
            'Asia/Macau' => '(GMT+8:00) Asia/Macau (China Standard Time)',
            'Asia/Makassar' => '(GMT+8:00) Asia/Makassar (Central Indonesia Time)',
            'Asia/Manila' => '(GMT+8:00) Asia/Manila (Philippines Time)',
            'Asia/Shanghai' => '(GMT+8:00) Asia/Shanghai (China Standard Time)',
            'Asia/Singapore' => '(GMT+8:00) Asia/Singapore (Singapore Time)',
            'Asia/Taipei' => '(GMT+8:00) Asia/Taipei (China Standard Time)',
            'Asia/Ujung_Pandang' => '(GMT+8:00) Asia/Ujung_Pandang (Central Indonesia Time)',
            'Asia/Ulaanbaatar' => '(GMT+8:00) Asia/Ulaanbaatar (Ulaanbaatar Time)',
            'Asia/Ulan_Bator' => '(GMT+8:00) Asia/Ulan_Bator (Ulaanbaatar Time)',
            'Asia/Urumqi' => '(GMT+8:00) Asia/Urumqi (China Standard Time)',
            'Australia/Perth' => '(GMT+8:00) Australia/Perth (Western Standard Time (Australia))',
            'Australia/West' => '(GMT+8:00) Australia/West (Western Standard Time (Australia))',
            'Australia/Eucla' => '(GMT+8:45) Australia/Eucla (Central Western Standard Time (Australia))',
            'Asia/Dili' => '(GMT+9:00) Asia/Dili (Timor-Leste Time)',
            'Asia/Jayapura' => '(GMT+9:00) Asia/Jayapura (East Indonesia Time)',
            'Asia/Pyongyang' => '(GMT+9:00) Asia/Pyongyang (Korea Standard Time)',
            'Asia/Seoul' => '(GMT+9:00) Asia/Seoul (Korea Standard Time)',
            'Asia/Tokyo' => '(GMT+9:00) Asia/Tokyo (Japan Standard Time)',
            'Asia/Yakutsk' => '(GMT+9:00) Asia/Yakutsk (Yakutsk Time)',
            'Australia/Adelaide' => '(GMT+9:30) Australia/Adelaide (Central Standard Time (South Australia))',
            'Australia/Broken_Hill' => '(GMT+9:30) Australia/Broken_Hill (Central Standard Time (South Australia/New South Wales))',
            'Australia/Darwin' => '(GMT+9:30) Australia/Darwin (Central Standard Time (Northern Territory))',
            'Australia/North' => '(GMT+9:30) Australia/North (Central Standard Time (Northern Territory))',
            'Australia/South' => '(GMT+9:30) Australia/South (Central Standard Time (South Australia))',
            'Australia/Yancowinna' => '(GMT+9:30) Australia/Yancowinna (Central Standard Time (South Australia/New South Wales))',
            'Antarctica/DumontDUrville' => '(GMT+10:00) Antarctica/DumontDUrville (Dumont-d\'Urville Time)',
            'Asia/Sakhalin' => '(GMT+10:00) Asia/Sakhalin (Sakhalin Time)',
            'Asia/Vladivostok' => '(GMT+10:00) Asia/Vladivostok (Vladivostok Time)',
            'Australia/ACT' => '(GMT+10:00) Australia/ACT (Eastern Standard Time (New South Wales))',
            'Australia/Brisbane' => '(GMT+10:00) Australia/Brisbane (Eastern Standard Time (Queensland))',
            'Australia/Canberra' => '(GMT+10:00) Australia/Canberra (Eastern Standard Time (New South Wales))',
            'Australia/Currie' => '(GMT+10:00) Australia/Currie (Eastern Standard Time (New South Wales))',
            'Australia/Hobart' => '(GMT+10:00) Australia/Hobart (Eastern Standard Time (Tasmania))',
            'Australia/Lindeman' => '(GMT+10:00) Australia/Lindeman (Eastern Standard Time (Queensland))',
            'Australia/Melbourne' => '(GMT+10:00) Australia/Melbourne (Eastern Standard Time (Victoria))',
            'Australia/NSW' => '(GMT+10:00) Australia/NSW (Eastern Standard Time (New South Wales))',
            'Australia/Queensland' => '(GMT+10:00) Australia/Queensland (Eastern Standard Time (Queensland))',
            'Australia/Sydney' => '(GMT+10:00) Australia/Sydney (Eastern Standard Time (New South Wales))',
            'Australia/Tasmania' => '(GMT+10:00) Australia/Tasmania (Eastern Standard Time (Tasmania))',
            'Australia/Victoria' => '(GMT+10:00) Australia/Victoria (Eastern Standard Time (Victoria))',
            'Australia/LHI' => '(GMT+10:30) Australia/LHI (Lord Howe Standard Time)',
            'Australia/Lord_Howe' => '(GMT+10:30) Australia/Lord_Howe (Lord Howe Standard Time)',
            'Asia/Magadan' => '(GMT+11:00) Asia/Magadan (Magadan Time)',
            'Antarctica/McMurdo' => '(GMT+12:00) Antarctica/McMurdo (New Zealand Standard Time)',
            'Antarctica/South_Pole' => '(GMT+12:00) Antarctica/South_Pole (New Zealand Standard Time)',
            'Asia/Anadyr' => '(GMT+12:00) Asia/Anadyr (Anadyr Time)',
            'Asia/Kamchatka' => '(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)'
        );
    }
}


if ( ! function_exists('get_timezone_numeric_list')) {
    function get_timezone_numeric_list()
    {
        $all_time_zone = array(
            '-12' => 'GMT -12.00',
            '-11' => 'GMT -11.00',
            '-10' => 'GMT -10.00',
            '-9' => 'GMT -9.00',
            '-8' => 'GMT -8.00',
            '-7' => 'GMT -7.00',
            '-6' => 'GMT -6.00',
            '-5' => 'GMT -5.00',
            '-4.5' => 'GMT -4.30',
            '-4' => 'GMT -4.00',
            '-3.5' => 'GMT -3.30',
            '-3' => 'GMT +-3.00',
            '-2' => 'GMT +-2.00',
            '-1' => 'GMT -1.00',
            '0' => 'GMT',
            '1' => 'GMT +1.00',
            '2' => 'GMT +2.00',
            '3' => 'GMT +3.00',
            '3.5' => 'GMT +3.30',
            '4' => 'GMT +4.00',
            '5' => 'GMT +5.00',
            '5.5' => 'GMT +5.30',
            '5.75' => 'GMT +5.45',
            '6' => 'GMT +6.00',
            '6.5' => 'GMT +6.30',
            '7' => 'GMT +7.00',
            '8' => 'GMT +8.00',
            '9' => 'GMT +9.00',
            '9.5' => 'GMT +9.30',
            '10' => 'GMT +10.00',
            '11' => 'GMT +11.00',
            '12' => 'GMT +12.00',
            '13' => 'GMT +13.00'
        );

        return $all_time_zone;
    }
}


if ( ! function_exists('get_paypal_stripe_currency_list')) {
    function get_paypal_stripe_currency_list()
    {
        $result =  array('USD', 'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'VND');
        return asort($result);
    }
}


if ( ! function_exists('get_mercadopago_country_list')) {
    function get_mercadopago_country_list()
    {
        return array('ar'=>'Argentina','br'=>'Brazil','co'=>'Colombia','mx'=>'Mexico');
    }
}


if ( ! function_exists('get_country_iso_phone_currency_list')) {
    function get_country_iso_phone_currency_list($return = 'country') // country,currency_name,currecny_icon,phonecode
    {
        $countries = array(
            array('name' => 'Afghanistan', 'iso_alpha2' => 'AF', 'iso_alpha3' => 'AFG', 'iso_numeric' => '4', 'calling_code' => '93', 'currency_code' => 'AFN', 'currency_name' => 'Afghani', 'currency_symbol' => '؋'),
            array('name' => 'Albania', 'iso_alpha2' => 'AL', 'iso_alpha3' => 'ALB', 'iso_numeric' => '8', 'calling_code' => '355', 'currency_code' => 'ALL', 'currency_name' => 'Lek', 'currency_symbol' => 'Lek'),
            array('name' => 'Algeria', 'iso_alpha2' => 'DZ', 'iso_alpha3' => 'DZA', 'iso_numeric' => '12', 'calling_code' => '213', 'currency_code' => 'DZD', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'American Samoa', 'iso_alpha2' => 'AS', 'iso_alpha3' => 'ASM', 'iso_numeric' => '16', 'calling_code' => '1684', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Andorra', 'iso_alpha2' => 'AD', 'iso_alpha3' => 'AND', 'iso_numeric' => '20', 'calling_code' => '376', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Angola', 'iso_alpha2' => 'AO', 'iso_alpha3' => 'AGO', 'iso_numeric' => '24', 'calling_code' => '244', 'currency_code' => 'AOA', 'currency_name' => 'Kwanza', 'currency_symbol' => 'Kz'),
            array('name' => 'Anguilla', 'iso_alpha2' => 'AI', 'iso_alpha3' => 'AIA', 'iso_numeric' => '660', 'calling_code' => '1264', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Antarctica', 'iso_alpha2' => 'AQ', 'iso_alpha3' => 'ATA', 'iso_numeric' => '10', 'calling_code' => '672', 'currency_code' => '', 'currency_name' => '', 'currency_symbol' => ''),
            array('name' => 'Antigua and Barbuda', 'iso_alpha2' => 'AG', 'iso_alpha3' => 'ATG', 'iso_numeric' => '28', 'calling_code' => '1268', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Argentina', 'iso_alpha2' => 'AR', 'iso_alpha3' => 'ARG', 'iso_numeric' => '32', 'calling_code' => '54', 'currency_code' => 'ARS', 'currency_name' => 'Peso', 'currency_symbol' => '$'),
            array('name' => 'Armenia', 'iso_alpha2' => 'AM', 'iso_alpha3' => 'ARM', 'iso_numeric' => '51', 'calling_code' => '374', 'currency_code' => 'AMD', 'currency_name' => 'Dram', 'currency_symbol' => ''),
            array('name' => 'Aruba', 'iso_alpha2' => 'AW', 'iso_alpha3' => 'ABW', 'iso_numeric' => '533', 'calling_code' => '297', 'currency_code' => 'AWG', 'currency_name' => 'Guilder', 'currency_symbol' => 'ƒ'),
            array('name' => 'Australia', 'iso_alpha2' => 'AU', 'iso_alpha3' => 'AUS', 'iso_numeric' => '36', 'calling_code' => '61', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Austria', 'iso_alpha2' => 'AT', 'iso_alpha3' => 'AUT', 'iso_numeric' => '40', 'calling_code' => '43', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Azerbaijan', 'iso_alpha2' => 'AZ', 'iso_alpha3' => 'AZE', 'iso_numeric' => '31', 'calling_code' => '994', 'currency_code' => 'AZN', 'currency_name' => 'Manat', 'currency_symbol' => 'ман'),
            array('name' => 'Bahamas', 'iso_alpha2' => 'BS', 'iso_alpha3' => 'BHS', 'iso_numeric' => '44', 'calling_code' => '1242', 'currency_code' => 'BSD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Bahrain', 'iso_alpha2' => 'BH', 'iso_alpha3' => 'BHR', 'iso_numeric' => '48', 'calling_code' => '973', 'currency_code' => 'BHD', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'Bangladesh', 'iso_alpha2' => 'BD', 'iso_alpha3' => 'BGD', 'iso_numeric' => '50', 'calling_code' => '880', 'currency_code' => 'BDT', 'currency_name' => 'Taka', 'currency_symbol' => ''),
            array('name' => 'Barbados', 'iso_alpha2' => 'BB', 'iso_alpha3' => 'BRB', 'iso_numeric' => '52', 'calling_code' => '1246', 'currency_code' => 'BBD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Belarus', 'iso_alpha2' => 'BY', 'iso_alpha3' => 'BLR', 'iso_numeric' => '112', 'calling_code' => '375', 'currency_code' => 'BYR', 'currency_name' => 'Ruble', 'currency_symbol' => 'p.'),
            array('name' => 'Belgium', 'iso_alpha2' => 'BE', 'iso_alpha3' => 'BEL', 'iso_numeric' => '56', 'calling_code' => '32', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Belize', 'iso_alpha2' => 'BZ', 'iso_alpha3' => 'BLZ', 'iso_numeric' => '84', 'calling_code' => '501', 'currency_code' => 'BZD', 'currency_name' => 'Dollar', 'currency_symbol' => 'BZ$'),
            array('name' => 'Benin', 'iso_alpha2' => 'BJ', 'iso_alpha3' => 'BEN', 'iso_numeric' => '204', 'calling_code' => '229', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Bermuda', 'iso_alpha2' => 'BM', 'iso_alpha3' => 'BMU', 'iso_numeric' => '60', 'calling_code' => '1441', 'currency_code' => 'BMD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Bhutan', 'iso_alpha2' => 'BT', 'iso_alpha3' => 'BTN', 'iso_numeric' => '64', 'calling_code' => '975', 'currency_code' => 'BTN', 'currency_name' => 'Ngultrum', 'currency_symbol' => ''),
            array('name' => 'Bolivia', 'iso_alpha2' => 'BO', 'iso_alpha3' => 'BOL', 'iso_numeric' => '68', 'calling_code' => '591', 'currency_code' => 'BOB', 'currency_name' => 'Boliviano', 'currency_symbol' => '$b'),
            array('name' => 'Bosnia and Herzegovina', 'iso_alpha2' => 'BA', 'iso_alpha3' => 'BIH', 'iso_numeric' => '70', 'calling_code' => '387', 'currency_code' => 'BAM', 'currency_name' => 'Marka', 'currency_symbol' => 'KM'),
            array('name' => 'Botswana', 'iso_alpha2' => 'BW', 'iso_alpha3' => 'BWA', 'iso_numeric' => '72', 'calling_code' => '267', 'currency_code' => 'BWP', 'currency_name' => 'Pula', 'currency_symbol' => 'P'),
            array('name' => 'Bouvet Island', 'iso_alpha2' => 'BV', 'iso_alpha3' => 'BVT', 'iso_numeric' => '74', 'calling_code' => '', 'currency_code' => 'NOK', 'currency_name' => 'Krone', 'currency_symbol' => 'kr'),
            array('name' => 'Brazil', 'iso_alpha2' => 'BR', 'iso_alpha3' => 'BRA', 'iso_numeric' => '76', 'calling_code' => '55', 'currency_code' => 'BRL', 'currency_name' => 'Real', 'currency_symbol' => 'R$'),
            array('name' => 'British Indian Ocean Territory', 'iso_alpha2' => 'IO', 'iso_alpha3' => 'IOT', 'iso_numeric' => '86', 'calling_code' => '', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'British Virgin Islands', 'iso_alpha2' => 'VG', 'iso_alpha3' => 'VGB', 'iso_numeric' => '92', 'calling_code' => '1284', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Brunei', 'iso_alpha2' => 'BN', 'iso_alpha3' => 'BRN', 'iso_numeric' => '96', 'calling_code' => '673', 'currency_code' => 'BND', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Bulgaria', 'iso_alpha2' => 'BG', 'iso_alpha3' => 'BGR', 'iso_numeric' => '100', 'calling_code' => '359', 'currency_code' => 'BGN', 'currency_name' => 'Lev', 'currency_symbol' => 'лв'),
            array('name' => 'Burkina Faso', 'iso_alpha2' => 'BF', 'iso_alpha3' => 'BFA', 'iso_numeric' => '854', 'calling_code' => '226', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Burundi', 'iso_alpha2' => 'BI', 'iso_alpha3' => 'BDI', 'iso_numeric' => '108', 'calling_code' => '257', 'currency_code' => 'BIF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Cambodia', 'iso_alpha2' => 'KH', 'iso_alpha3' => 'KHM', 'iso_numeric' => '116', 'calling_code' => '855', 'currency_code' => 'KHR', 'currency_name' => 'Riels', 'currency_symbol' => '៛'),
            array('name' => 'Cameroon', 'iso_alpha2' => 'CM', 'iso_alpha3' => 'CMR', 'iso_numeric' => '120', 'calling_code' => '237', 'currency_code' => 'XAF', 'currency_name' => 'Franc', 'currency_symbol' => 'FCF'),
            array('name' => 'Canada', 'iso_alpha2' => 'CA', 'iso_alpha3' => 'CAN', 'iso_numeric' => '124', 'calling_code' => '1', 'currency_code' => 'CAD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Cape Verde', 'iso_alpha2' => 'CV', 'iso_alpha3' => 'CPV', 'iso_numeric' => '132', 'calling_code' => '238', 'currency_code' => 'CVE', 'currency_name' => 'Escudo', 'currency_symbol' => ''),
            array('name' => 'Cayman Islands', 'iso_alpha2' => 'KY', 'iso_alpha3' => 'CYM', 'iso_numeric' => '136', 'calling_code' => '1345', 'currency_code' => 'KYD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Central African Republic', 'iso_alpha2' => 'CF', 'iso_alpha3' => 'CAF', 'iso_numeric' => '140', 'calling_code' => '236', 'currency_code' => 'XAF', 'currency_name' => 'Franc', 'currency_symbol' => 'FCF'),
            array('name' => 'Chad', 'iso_alpha2' => 'TD', 'iso_alpha3' => 'TCD', 'iso_numeric' => '148', 'calling_code' => '235', 'currency_code' => 'XAF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Chile', 'iso_alpha2' => 'CL', 'iso_alpha3' => 'CHL', 'iso_numeric' => '152', 'calling_code' => '56', 'currency_code' => 'CLP', 'currency_name' => 'Peso', 'currency_symbol' => ''),
            array('name' => 'China', 'iso_alpha2' => 'CN', 'iso_alpha3' => 'CHN', 'iso_numeric' => '156', 'calling_code' => '86', 'currency_code' => 'CNY', 'currency_name' => 'YuanRenminbi', 'currency_symbol' => '¥'),
            array('name' => 'Christmas Island', 'iso_alpha2' => 'CX', 'iso_alpha3' => 'CXR', 'iso_numeric' => '162', 'calling_code' => '61', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Cocos Islands', 'iso_alpha2' => 'CC', 'iso_alpha3' => 'CCK', 'iso_numeric' => '166', 'calling_code' => '61', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Colombia', 'iso_alpha2' => 'CO', 'iso_alpha3' => 'COL', 'iso_numeric' => '170', 'calling_code' => '57', 'currency_code' => 'COP', 'currency_name' => 'Peso', 'currency_symbol' => '$'),
            array('name' => 'Comoros', 'iso_alpha2' => 'KM', 'iso_alpha3' => 'COM', 'iso_numeric' => '174', 'calling_code' => '269', 'currency_code' => 'KMF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Cook Islands', 'iso_alpha2' => 'CK', 'iso_alpha3' => 'COK', 'iso_numeric' => '184', 'calling_code' => '682', 'currency_code' => 'NZD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Costa Rica', 'iso_alpha2' => 'CR', 'iso_alpha3' => 'CRI', 'iso_numeric' => '188', 'calling_code' => '506', 'currency_code' => 'CRC', 'currency_name' => 'Colon', 'currency_symbol' => '₡'),
            array('name' => 'Croatia', 'iso_alpha2' => 'HR', 'iso_alpha3' => 'HRV', 'iso_numeric' => '191', 'calling_code' => '385', 'currency_code' => 'HRK', 'currency_name' => 'Kuna', 'currency_symbol' => 'kn'),
            array('name' => 'Cuba', 'iso_alpha2' => 'CU', 'iso_alpha3' => 'CUB', 'iso_numeric' => '192', 'calling_code' => '53', 'currency_code' => 'CUP', 'currency_name' => 'Peso', 'currency_symbol' => '₱'),
            array('name' => 'Cyprus', 'iso_alpha2' => 'CY', 'iso_alpha3' => 'CYP', 'iso_numeric' => '196', 'calling_code' => '357', 'currency_code' => 'CYP', 'currency_name' => 'Pound', 'currency_symbol' => ''),
            array('name' => 'Czech Republic', 'iso_alpha2' => 'CZ', 'iso_alpha3' => 'CZE', 'iso_numeric' => '203', 'calling_code' => '420', 'currency_code' => 'CZK', 'currency_name' => 'Koruna', 'currency_symbol' => 'Kč'),
            array('name' => 'Democratic Republic of the Congo', 'iso_alpha2' => 'CD', 'iso_alpha3' => 'COD', 'iso_numeric' => '180', 'calling_code' => '243', 'currency_code' => 'CDF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Denmark', 'iso_alpha2' => 'DK', 'iso_alpha3' => 'DNK', 'iso_numeric' => '208', 'calling_code' => '45', 'currency_code' => 'DKK', 'currency_name' => 'Krone', 'currency_symbol' => 'kr'),
            array('name' => 'Djibouti', 'iso_alpha2' => 'DJ', 'iso_alpha3' => 'DJI', 'iso_numeric' => '262', 'calling_code' => '253', 'currency_code' => 'DJF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Dominica', 'iso_alpha2' => 'DM', 'iso_alpha3' => 'DMA', 'iso_numeric' => '212', 'calling_code' => '1767', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Dominican Republic', 'iso_alpha2' => 'DO', 'iso_alpha3' => 'DOM', 'iso_numeric' => '214', 'calling_code' => '1809', 'currency_code' => 'DOP', 'currency_name' => 'Peso', 'currency_symbol' => 'RD$'),
            array('name' => 'East Timor', 'iso_alpha2' => 'TL', 'iso_alpha3' => 'TLS', 'iso_numeric' => '626', 'calling_code' => '670', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Ecuador', 'iso_alpha2' => 'EC', 'iso_alpha3' => 'ECU', 'iso_numeric' => '218', 'calling_code' => '593', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Egypt', 'iso_alpha2' => 'EG', 'iso_alpha3' => 'EGY', 'iso_numeric' => '818', 'calling_code' => '20', 'currency_code' => 'EGP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'El Salvador', 'iso_alpha2' => 'SV', 'iso_alpha3' => 'SLV', 'iso_numeric' => '222', 'calling_code' => '503', 'currency_code' => 'SVC', 'currency_name' => 'Colone', 'currency_symbol' => '$'),
            array('name' => 'Equatorial Guinea', 'iso_alpha2' => 'GQ', 'iso_alpha3' => 'GNQ', 'iso_numeric' => '226', 'calling_code' => '240', 'currency_code' => 'XAF', 'currency_name' => 'Franc', 'currency_symbol' => 'FCF'),
            array('name' => 'Eritrea', 'iso_alpha2' => 'ER', 'iso_alpha3' => 'ERI', 'iso_numeric' => '232', 'calling_code' => '291', 'currency_code' => 'ERN', 'currency_name' => 'Nakfa', 'currency_symbol' => 'Nfk'),
            array('name' => 'Estonia', 'iso_alpha2' => 'EE', 'iso_alpha3' => 'EST', 'iso_numeric' => '233', 'calling_code' => '372', 'currency_code' => 'EEK', 'currency_name' => 'Kroon', 'currency_symbol' => 'kr'),
            array('name' => 'Ethiopia', 'iso_alpha2' => 'ET', 'iso_alpha3' => 'ETH', 'iso_numeric' => '231', 'calling_code' => '251', 'currency_code' => 'ETB', 'currency_name' => 'Birr', 'currency_symbol' => ''),
            array('name' => 'Falkland Islands', 'iso_alpha2' => 'FK', 'iso_alpha3' => 'FLK', 'iso_numeric' => '238', 'calling_code' => '500', 'currency_code' => 'FKP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'Faroe Islands', 'iso_alpha2' => 'FO', 'iso_alpha3' => 'FRO', 'iso_numeric' => '234', 'calling_code' => '298', 'currency_code' => 'DKK', 'currency_name' => 'Krone', 'currency_symbol' => 'kr'),
            array('name' => 'Fiji', 'iso_alpha2' => 'FJ', 'iso_alpha3' => 'FJI', 'iso_numeric' => '242', 'calling_code' => '679', 'currency_code' => 'FJD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Finland', 'iso_alpha2' => 'FI', 'iso_alpha3' => 'FIN', 'iso_numeric' => '246', 'calling_code' => '358', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'France', 'iso_alpha2' => 'FR', 'iso_alpha3' => 'FRA', 'iso_numeric' => '250', 'calling_code' => '33', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'French Guiana', 'iso_alpha2' => 'GF', 'iso_alpha3' => 'GUF', 'iso_numeric' => '254', 'calling_code' => '', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'French Polynesia', 'iso_alpha2' => 'PF', 'iso_alpha3' => 'PYF', 'iso_numeric' => '258', 'calling_code' => '689', 'currency_code' => 'XPF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'French Southern Territories', 'iso_alpha2' => 'TF', 'iso_alpha3' => 'ATF', 'iso_numeric' => '260', 'calling_code' => '', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Gabon', 'iso_alpha2' => 'GA', 'iso_alpha3' => 'GAB', 'iso_numeric' => '266', 'calling_code' => '241', 'currency_code' => 'XAF', 'currency_name' => 'Franc', 'currency_symbol' => 'FCF'),
            array('name' => 'Gambia', 'iso_alpha2' => 'GM', 'iso_alpha3' => 'GMB', 'iso_numeric' => '270', 'calling_code' => '220', 'currency_code' => 'GMD', 'currency_name' => 'Dalasi', 'currency_symbol' => 'D'),
            array('name' => 'Georgia', 'iso_alpha2' => 'GE', 'iso_alpha3' => 'GEO', 'iso_numeric' => '268', 'calling_code' => '995', 'currency_code' => 'GEL', 'currency_name' => 'Lari', 'currency_symbol' => ''),
            array('name' => 'Germany', 'iso_alpha2' => 'DE', 'iso_alpha3' => 'DEU', 'iso_numeric' => '276', 'calling_code' => '49', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Ghana', 'iso_alpha2' => 'GH', 'iso_alpha3' => 'GHA', 'iso_numeric' => '288', 'calling_code' => '233', 'currency_code' => 'GHC', 'currency_name' => 'Cedi', 'currency_symbol' => '¢'),
            array('name' => 'Gibraltar', 'iso_alpha2' => 'GI', 'iso_alpha3' => 'GIB', 'iso_numeric' => '292', 'calling_code' => '350', 'currency_code' => 'GIP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'Greece', 'iso_alpha2' => 'GR', 'iso_alpha3' => 'GRC', 'iso_numeric' => '300', 'calling_code' => '30', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Greenland', 'iso_alpha2' => 'GL', 'iso_alpha3' => 'GRL', 'iso_numeric' => '304', 'calling_code' => '299', 'currency_code' => 'DKK', 'currency_name' => 'Krone', 'currency_symbol' => 'kr'),
            array('name' => 'Grenada', 'iso_alpha2' => 'GD', 'iso_alpha3' => 'GRD', 'iso_numeric' => '308', 'calling_code' => '1473', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Guadeloupe', 'iso_alpha2' => 'GP', 'iso_alpha3' => 'GLP', 'iso_numeric' => '312', 'calling_code' => '', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Guam', 'iso_alpha2' => 'GU', 'iso_alpha3' => 'GUM', 'iso_numeric' => '316', 'calling_code' => '1671', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Guatemala', 'iso_alpha2' => 'GT', 'iso_alpha3' => 'GTM', 'iso_numeric' => '320', 'calling_code' => '502', 'currency_code' => 'GTQ', 'currency_name' => 'Quetzal', 'currency_symbol' => 'Q'),
            array('name' => 'Guinea', 'iso_alpha2' => 'GN', 'iso_alpha3' => 'GIN', 'iso_numeric' => '324', 'calling_code' => '224', 'currency_code' => 'GNF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Guinea-Bissau', 'iso_alpha2' => 'GW', 'iso_alpha3' => 'GNB', 'iso_numeric' => '624', 'calling_code' => '245', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Guyana', 'iso_alpha2' => 'GY', 'iso_alpha3' => 'GUY', 'iso_numeric' => '328', 'calling_code' => '592', 'currency_code' => 'GYD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Haiti', 'iso_alpha2' => 'HT', 'iso_alpha3' => 'HTI', 'iso_numeric' => '332', 'calling_code' => '509', 'currency_code' => 'HTG', 'currency_name' => 'Gourde', 'currency_symbol' => 'G'),
            array('name' => 'Heard Island and McDonald Islands', 'iso_alpha2' => 'HM', 'iso_alpha3' => 'HMD', 'iso_numeric' => '334', 'calling_code' => '', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Honduras', 'iso_alpha2' => 'HN', 'iso_alpha3' => 'HND', 'iso_numeric' => '340', 'calling_code' => '504', 'currency_code' => 'HNL', 'currency_name' => 'Lempira', 'currency_symbol' => 'L'),
            array('name' => 'Hong Kong', 'iso_alpha2' => 'HK', 'iso_alpha3' => 'HKG', 'iso_numeric' => '344', 'calling_code' => '852', 'currency_code' => 'HKD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Hungary', 'iso_alpha2' => 'HU', 'iso_alpha3' => 'HUN', 'iso_numeric' => '348', 'calling_code' => '36', 'currency_code' => 'HUF', 'currency_name' => 'Forint', 'currency_symbol' => 'Ft'),
            array('name' => 'Iceland', 'iso_alpha2' => 'IS', 'iso_alpha3' => 'ISL', 'iso_numeric' => '352', 'calling_code' => '354', 'currency_code' => 'ISK', 'currency_name' => 'Krona', 'currency_symbol' => 'kr'),
            array('name' => 'India', 'iso_alpha2' => 'IN', 'iso_alpha3' => 'IND', 'iso_numeric' => '356', 'calling_code' => '91', 'currency_code' => 'INR', 'currency_name' => 'Rupee', 'currency_symbol' => '₹'),
            array('name' => 'Indonesia', 'iso_alpha2' => 'ID', 'iso_alpha3' => 'IDN', 'iso_numeric' => '360', 'calling_code' => '62', 'currency_code' => 'IDR', 'currency_name' => 'Rupiah', 'currency_symbol' => 'Rp'),
            array('name' => 'Iran', 'iso_alpha2' => 'IR', 'iso_alpha3' => 'IRN', 'iso_numeric' => '364', 'calling_code' => '98', 'currency_code' => 'IRR', 'currency_name' => 'Rial', 'currency_symbol' => '﷼'),
            array('name' => 'Iraq', 'iso_alpha2' => 'IQ', 'iso_alpha3' => 'IRQ', 'iso_numeric' => '368', 'calling_code' => '964', 'currency_code' => 'IQD', 'currency_name' => 'Dinar', 'currency_symbol' => 'د.ع'),
            array('name' => 'Ireland', 'iso_alpha2' => 'IE', 'iso_alpha3' => 'IRL', 'iso_numeric' => '372', 'calling_code' => '353', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Israel', 'iso_alpha2' => 'IL', 'iso_alpha3' => 'ISR', 'iso_numeric' => '376', 'calling_code' => '972', 'currency_code' => 'ILS', 'currency_name' => 'Shekel', 'currency_symbol' => '₪'),
            array('name' => 'Italy', 'iso_alpha2' => 'IT', 'iso_alpha3' => 'ITA', 'iso_numeric' => '380', 'calling_code' => '39', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Ivory Coast', 'iso_alpha2' => 'CI', 'iso_alpha3' => 'CIV', 'iso_numeric' => '384', 'calling_code' => '225', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Jamaica', 'iso_alpha2' => 'JM', 'iso_alpha3' => 'JAM', 'iso_numeric' => '388', 'calling_code' => '1876', 'currency_code' => 'JMD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Japan', 'iso_alpha2' => 'JP', 'iso_alpha3' => 'JPN', 'iso_numeric' => '392', 'calling_code' => '81', 'currency_code' => 'JPY', 'currency_name' => 'Yen', 'currency_symbol' => '¥'),
            array('name' => 'Jordan', 'iso_alpha2' => 'JO', 'iso_alpha3' => 'JOR', 'iso_numeric' => '400', 'calling_code' => '962', 'currency_code' => 'JOD', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'Kazakhstan', 'iso_alpha2' => 'KZ', 'iso_alpha3' => 'KAZ', 'iso_numeric' => '398', 'calling_code' => '7', 'currency_code' => 'KZT', 'currency_name' => 'Tenge', 'currency_symbol' => 'лв'),
            array('name' => 'Kenya', 'iso_alpha2' => 'KE', 'iso_alpha3' => 'KEN', 'iso_numeric' => '404', 'calling_code' => '254', 'currency_code' => 'KES', 'currency_name' => 'Shilling', 'currency_symbol' => ''),
            array('name' => 'Kiribati', 'iso_alpha2' => 'KI', 'iso_alpha3' => 'KIR', 'iso_numeric' => '296', 'calling_code' => '686', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Kuwait', 'iso_alpha2' => 'KW', 'iso_alpha3' => 'KWT', 'iso_numeric' => '414', 'calling_code' => '965', 'currency_code' => 'KWD', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'Kyrgyzstan', 'iso_alpha2' => 'KG', 'iso_alpha3' => 'KGZ', 'iso_numeric' => '417', 'calling_code' => '996', 'currency_code' => 'KGS', 'currency_name' => 'Som', 'currency_symbol' => 'лв'),
            array('name' => 'Laos', 'iso_alpha2' => 'LA', 'iso_alpha3' => 'LAO', 'iso_numeric' => '418', 'calling_code' => '856', 'currency_code' => 'LAK', 'currency_name' => 'Kip', 'currency_symbol' => '₭'),
            array('name' => 'Latvia', 'iso_alpha2' => 'LV', 'iso_alpha3' => 'LVA', 'iso_numeric' => '428', 'calling_code' => '371', 'currency_code' => 'LVL', 'currency_name' => 'Lat', 'currency_symbol' => 'Ls'),
            array('name' => 'Lebanon', 'iso_alpha2' => 'LB', 'iso_alpha3' => 'LBN', 'iso_numeric' => '422', 'calling_code' => '961', 'currency_code' => 'LBP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'Lesotho', 'iso_alpha2' => 'LS', 'iso_alpha3' => 'LSO', 'iso_numeric' => '426', 'calling_code' => '266', 'currency_code' => 'LSL', 'currency_name' => 'Loti', 'currency_symbol' => 'L'),
            array('name' => 'Liberia', 'iso_alpha2' => 'LR', 'iso_alpha3' => 'LBR', 'iso_numeric' => '430', 'calling_code' => '231', 'currency_code' => 'LRD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Libya', 'iso_alpha2' => 'LY', 'iso_alpha3' => 'LBY', 'iso_numeric' => '434', 'calling_code' => '218', 'currency_code' => 'LYD', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'Liechtenstein', 'iso_alpha2' => 'LI', 'iso_alpha3' => 'LIE', 'iso_numeric' => '438', 'calling_code' => '423', 'currency_code' => 'CHF', 'currency_name' => 'Franc', 'currency_symbol' => 'CHF'),
            array('name' => 'Lithuania', 'iso_alpha2' => 'LT', 'iso_alpha3' => 'LTU', 'iso_numeric' => '440', 'calling_code' => '370', 'currency_code' => 'LTL', 'currency_name' => 'Litas', 'currency_symbol' => 'Lt'),
            array('name' => 'Luxembourg', 'iso_alpha2' => 'LU', 'iso_alpha3' => 'LUX', 'iso_numeric' => '442', 'calling_code' => '352', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Macao', 'iso_alpha2' => 'MO', 'iso_alpha3' => 'MAC', 'iso_numeric' => '446', 'calling_code' => '853', 'currency_code' => 'MOP', 'currency_name' => 'Pataca', 'currency_symbol' => 'MOP'),
            array('name' => 'Macedonia', 'iso_alpha2' => 'MK', 'iso_alpha3' => 'MKD', 'iso_numeric' => '807', 'calling_code' => '389', 'currency_code' => 'MKD', 'currency_name' => 'Denar', 'currency_symbol' => 'ден'),
            array('name' => 'Madagascar', 'iso_alpha2' => 'MG', 'iso_alpha3' => 'MDG', 'iso_numeric' => '450', 'calling_code' => '261', 'currency_code' => 'MGA', 'currency_name' => 'Ariary', 'currency_symbol' => ''),
            array('name' => 'Malawi', 'iso_alpha2' => 'MW', 'iso_alpha3' => 'MWI', 'iso_numeric' => '454', 'calling_code' => '265', 'currency_code' => 'MWK', 'currency_name' => 'Kwacha', 'currency_symbol' => 'MK'),
            array('name' => 'Malaysia', 'iso_alpha2' => 'MY', 'iso_alpha3' => 'MYS', 'iso_numeric' => '458', 'calling_code' => '60', 'currency_code' => 'MYR', 'currency_name' => 'Ringgit', 'currency_symbol' => 'RM'),
            array('name' => 'Maldives', 'iso_alpha2' => 'MV', 'iso_alpha3' => 'MDV', 'iso_numeric' => '462', 'calling_code' => '960', 'currency_code' => 'MVR', 'currency_name' => 'Rufiyaa', 'currency_symbol' => 'Rf'),
            array('name' => 'Mali', 'iso_alpha2' => 'ML', 'iso_alpha3' => 'MLI', 'iso_numeric' => '466', 'calling_code' => '223', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Malta', 'iso_alpha2' => 'MT', 'iso_alpha3' => 'MLT', 'iso_numeric' => '470', 'calling_code' => '356', 'currency_code' => 'MTL', 'currency_name' => 'Lira', 'currency_symbol' => ''),
            array('name' => 'Marshall Islands', 'iso_alpha2' => 'MH', 'iso_alpha3' => 'MHL', 'iso_numeric' => '584', 'calling_code' => '692', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Martinique', 'iso_alpha2' => 'MQ', 'iso_alpha3' => 'MTQ', 'iso_numeric' => '474', 'calling_code' => '', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Mauritania', 'iso_alpha2' => 'MR', 'iso_alpha3' => 'MRT', 'iso_numeric' => '478', 'calling_code' => '222', 'currency_code' => 'MRO', 'currency_name' => 'Ouguiya', 'currency_symbol' => 'UM'),
            array('name' => 'Mauritius', 'iso_alpha2' => 'MU', 'iso_alpha3' => 'MUS', 'iso_numeric' => '480', 'calling_code' => '230', 'currency_code' => 'MUR', 'currency_name' => 'Rupee', 'currency_symbol' => '₨'),
            array('name' => 'Mayotte', 'iso_alpha2' => 'YT', 'iso_alpha3' => 'MYT', 'iso_numeric' => '175', 'calling_code' => '262', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Mexico', 'iso_alpha2' => 'MX', 'iso_alpha3' => 'MEX', 'iso_numeric' => '484', 'calling_code' => '52', 'currency_code' => 'MXN', 'currency_name' => 'Peso', 'currency_symbol' => '$'),
            array('name' => 'Micronesia', 'iso_alpha2' => 'FM', 'iso_alpha3' => 'FSM', 'iso_numeric' => '583', 'calling_code' => '691', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Moldova', 'iso_alpha2' => 'MD', 'iso_alpha3' => 'MDA', 'iso_numeric' => '498', 'calling_code' => '373', 'currency_code' => 'MDL', 'currency_name' => 'Leu', 'currency_symbol' => ''),
            array('name' => 'Monaco', 'iso_alpha2' => 'MC', 'iso_alpha3' => 'MCO', 'iso_numeric' => '492', 'calling_code' => '377', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Mongolia', 'iso_alpha2' => 'MN', 'iso_alpha3' => 'MNG', 'iso_numeric' => '496', 'calling_code' => '976', 'currency_code' => 'MNT', 'currency_name' => 'Tugrik', 'currency_symbol' => '₮'),
            array('name' => 'Montserrat', 'iso_alpha2' => 'MS', 'iso_alpha3' => 'MSR', 'iso_numeric' => '500', 'calling_code' => '1664', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Morocco', 'iso_alpha2' => 'MA', 'iso_alpha3' => 'MAR', 'iso_numeric' => '504', 'calling_code' => '212', 'currency_code' => 'MAD', 'currency_name' => 'Dirham', 'currency_symbol' => ''),
            array('name' => 'Mozambique', 'iso_alpha2' => 'MZ', 'iso_alpha3' => 'MOZ', 'iso_numeric' => '508', 'calling_code' => '258', 'currency_code' => 'MZN', 'currency_name' => 'Meticail', 'currency_symbol' => 'MT'),
            array('name' => 'Myanmar', 'iso_alpha2' => 'MM', 'iso_alpha3' => 'MMR', 'iso_numeric' => '104', 'calling_code' => '95', 'currency_code' => 'MMK', 'currency_name' => 'Kyat', 'currency_symbol' => 'K'),
            array('name' => 'Namibia', 'iso_alpha2' => 'NA', 'iso_alpha3' => 'NAM', 'iso_numeric' => '516', 'calling_code' => '264', 'currency_code' => 'NAD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Nauru', 'iso_alpha2' => 'NR', 'iso_alpha3' => 'NRU', 'iso_numeric' => '520', 'calling_code' => '674', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Nepal', 'iso_alpha2' => 'NP', 'iso_alpha3' => 'NPL', 'iso_numeric' => '524', 'calling_code' => '977', 'currency_code' => 'NPR', 'currency_name' => 'Rupee', 'currency_symbol' => '₨'),
            array('name' => 'Netherlands', 'iso_alpha2' => 'NL', 'iso_alpha3' => 'NLD', 'iso_numeric' => '528', 'calling_code' => '31', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Netherlands Antilles', 'iso_alpha2' => 'AN', 'iso_alpha3' => 'ANT', 'iso_numeric' => '530', 'calling_code' => '599', 'currency_code' => 'ANG', 'currency_name' => 'Guilder', 'currency_symbol' => 'ƒ'),
            array('name' => 'New Caledonia', 'iso_alpha2' => 'NC', 'iso_alpha3' => 'NCL', 'iso_numeric' => '540', 'calling_code' => '687', 'currency_code' => 'XPF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'New Zealand', 'iso_alpha2' => 'NZ', 'iso_alpha3' => 'NZL', 'iso_numeric' => '554', 'calling_code' => '64', 'currency_code' => 'NZD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Nicaragua', 'iso_alpha2' => 'NI', 'iso_alpha3' => 'NIC', 'iso_numeric' => '558', 'calling_code' => '505', 'currency_code' => 'NIO', 'currency_name' => 'Cordoba', 'currency_symbol' => 'C$'),
            array('name' => 'Niger', 'iso_alpha2' => 'NE', 'iso_alpha3' => 'NER', 'iso_numeric' => '562', 'calling_code' => '227', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Nigeria', 'iso_alpha2' => 'NG', 'iso_alpha3' => 'NGA', 'iso_numeric' => '566', 'calling_code' => '234', 'currency_code' => 'NGN', 'currency_name' => 'Naira', 'currency_symbol' => '₦'),
            array('name' => 'Niue', 'iso_alpha2' => 'NU', 'iso_alpha3' => 'NIU', 'iso_numeric' => '570', 'calling_code' => '683', 'currency_code' => 'NZD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Norfolk Island', 'iso_alpha2' => 'NF', 'iso_alpha3' => 'NFK', 'iso_numeric' => '574', 'calling_code' => '', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'North Korea', 'iso_alpha2' => 'KP', 'iso_alpha3' => 'PRK', 'iso_numeric' => '408', 'calling_code' => '850', 'currency_code' => 'KPW', 'currency_name' => 'Won', 'currency_symbol' => '₩'),
            array('name' => 'Northern Mariana Islands', 'iso_alpha2' => 'MP', 'iso_alpha3' => 'MNP', 'iso_numeric' => '580', 'calling_code' => '1670', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Norway', 'iso_alpha2' => 'NO', 'iso_alpha3' => 'NOR', 'iso_numeric' => '578', 'calling_code' => '47', 'currency_code' => 'NOK', 'currency_name' => 'Krone', 'currency_symbol' => 'kr'),
            array('name' => 'Oman', 'iso_alpha2' => 'OM', 'iso_alpha3' => 'OMN', 'iso_numeric' => '512', 'calling_code' => '968', 'currency_code' => 'OMR', 'currency_name' => 'Rial', 'currency_symbol' => '﷼'),
            array('name' => 'Pakistan', 'iso_alpha2' => 'PK', 'iso_alpha3' => 'PAK', 'iso_numeric' => '586', 'calling_code' => '92', 'currency_code' => 'PKR', 'currency_name' => 'Rupee', 'currency_symbol' => '₨'),
            array('name' => 'Palau', 'iso_alpha2' => 'PW', 'iso_alpha3' => 'PLW', 'iso_numeric' => '585', 'calling_code' => '680', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Palestinian Territory', 'iso_alpha2' => 'PS', 'iso_alpha3' => 'PSE', 'iso_numeric' => '275', 'calling_code' => '', 'currency_code' => 'ILS', 'currency_name' => 'Shekel', 'currency_symbol' => '₪'),
            array('name' => 'Panama', 'iso_alpha2' => 'PA', 'iso_alpha3' => 'PAN', 'iso_numeric' => '591', 'calling_code' => '507', 'currency_code' => 'PAB', 'currency_name' => 'Balboa', 'currency_symbol' => 'B/.'),
            array('name' => 'Papua New Guinea', 'iso_alpha2' => 'PG', 'iso_alpha3' => 'PNG', 'iso_numeric' => '598', 'calling_code' => '675', 'currency_code' => 'PGK', 'currency_name' => 'Kina', 'currency_symbol' => ''),
            array('name' => 'Paraguay', 'iso_alpha2' => 'PY', 'iso_alpha3' => 'PRY', 'iso_numeric' => '600', 'calling_code' => '595', 'currency_code' => 'PYG', 'currency_name' => 'Guarani', 'currency_symbol' => 'Gs'),
            array('name' => 'Peru', 'iso_alpha2' => 'PE', 'iso_alpha3' => 'PER', 'iso_numeric' => '604', 'calling_code' => '51', 'currency_code' => 'PEN', 'currency_name' => 'Sol', 'currency_symbol' => 'S/.'),
            array('name' => 'Philippines', 'iso_alpha2' => 'PH', 'iso_alpha3' => 'PHL', 'iso_numeric' => '608', 'calling_code' => '63', 'currency_code' => 'PHP', 'currency_name' => 'Peso', 'currency_symbol' => 'Php'),
            array('name' => 'Pitcairn', 'iso_alpha2' => 'PN', 'iso_alpha3' => 'PCN', 'iso_numeric' => '612', 'calling_code' => '870', 'currency_code' => 'NZD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Poland', 'iso_alpha2' => 'PL', 'iso_alpha3' => 'POL', 'iso_numeric' => '616', 'calling_code' => '48', 'currency_code' => 'PLN', 'currency_name' => 'Zloty', 'currency_symbol' => 'zł'),
            array('name' => 'Portugal', 'iso_alpha2' => 'PT', 'iso_alpha3' => 'PRT', 'iso_numeric' => '620', 'calling_code' => '351', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Puerto Rico', 'iso_alpha2' => 'PR', 'iso_alpha3' => 'PRI', 'iso_numeric' => '630', 'calling_code' => '1', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Qatar', 'iso_alpha2' => 'QA', 'iso_alpha3' => 'QAT', 'iso_numeric' => '634', 'calling_code' => '974', 'currency_code' => 'QAR', 'currency_name' => 'Rial', 'currency_symbol' => '﷼'),
            array('name' => 'Republic of the Congo', 'iso_alpha2' => 'CG', 'iso_alpha3' => 'COG', 'iso_numeric' => '178', 'calling_code' => '242', 'currency_code' => 'XAF', 'currency_name' => 'Franc', 'currency_symbol' => 'FCF'),
            array('name' => 'Reunion', 'iso_alpha2' => 'RE', 'iso_alpha3' => 'REU', 'iso_numeric' => '638', 'calling_code' => '', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Romania', 'iso_alpha2' => 'RO', 'iso_alpha3' => 'ROU', 'iso_numeric' => '642', 'calling_code' => '40', 'currency_code' => 'RON', 'currency_name' => 'Leu', 'currency_symbol' => 'lei'),
            array('name' => 'Russia', 'iso_alpha2' => 'RU', 'iso_alpha3' => 'RUS', 'iso_numeric' => '643', 'calling_code' => '7', 'currency_code' => 'RUB', 'currency_name' => 'Ruble', 'currency_symbol' => 'руб'),
            array('name' => 'Rwanda', 'iso_alpha2' => 'RW', 'iso_alpha3' => 'RWA', 'iso_numeric' => '646', 'calling_code' => '250', 'currency_code' => 'RWF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Saint Helena', 'iso_alpha2' => 'SH', 'iso_alpha3' => 'SHN', 'iso_numeric' => '654', 'calling_code' => '290', 'currency_code' => 'SHP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'Saint Kitts and Nevis', 'iso_alpha2' => 'KN', 'iso_alpha3' => 'KNA', 'iso_numeric' => '659', 'calling_code' => '1869', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Saint Lucia', 'iso_alpha2' => 'LC', 'iso_alpha3' => 'LCA', 'iso_numeric' => '662', 'calling_code' => '1758', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Saint Pierre and Miquelon', 'iso_alpha2' => 'PM', 'iso_alpha3' => 'SPM', 'iso_numeric' => '666', 'calling_code' => '508', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Saint Vincent and the Grenadines', 'iso_alpha2' => 'VC', 'iso_alpha3' => 'VCT', 'iso_numeric' => '670', 'calling_code' => '1784', 'currency_code' => 'XCD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Samoa', 'iso_alpha2' => 'WS', 'iso_alpha3' => 'WSM', 'iso_numeric' => '882', 'calling_code' => '685', 'currency_code' => 'WST', 'currency_name' => 'Tala', 'currency_symbol' => 'WS$'),
            array('name' => 'San Marino', 'iso_alpha2' => 'SM', 'iso_alpha3' => 'SMR', 'iso_numeric' => '674', 'calling_code' => '378', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Sao Tome and Principe', 'iso_alpha2' => 'ST', 'iso_alpha3' => 'STP', 'iso_numeric' => '678', 'calling_code' => '239', 'currency_code' => 'STD', 'currency_name' => 'Dobra', 'currency_symbol' => 'Db'),
            array('name' => 'Saudi Arabia', 'iso_alpha2' => 'SA', 'iso_alpha3' => 'SAU', 'iso_numeric' => '682', 'calling_code' => '966', 'currency_code' => 'SAR', 'currency_name' => 'Rial', 'currency_symbol' => '﷼'),
            array('name' => 'Senegal', 'iso_alpha2' => 'SN', 'iso_alpha3' => 'SEN', 'iso_numeric' => '686', 'calling_code' => '221', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Serbia and Montenegro', 'iso_alpha2' => 'CS', 'iso_alpha3' => 'SCG', 'iso_numeric' => '891', 'calling_code' => '', 'currency_code' => 'RSD', 'currency_name' => 'Dinar', 'currency_symbol' => 'Дин'),
            array('name' => 'Seychelles', 'iso_alpha2' => 'SC', 'iso_alpha3' => 'SYC', 'iso_numeric' => '690', 'calling_code' => '248', 'currency_code' => 'SCR', 'currency_name' => 'Rupee', 'currency_symbol' => '₨'),
            array('name' => 'Sierra Leone', 'iso_alpha2' => 'SL', 'iso_alpha3' => 'SLE', 'iso_numeric' => '694', 'calling_code' => '232', 'currency_code' => 'SLL', 'currency_name' => 'Leone', 'currency_symbol' => 'Le'),
            array('name' => 'Singapore', 'iso_alpha2' => 'SG', 'iso_alpha3' => 'SGP', 'iso_numeric' => '702', 'calling_code' => '65', 'currency_code' => 'SGD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Slovakia', 'iso_alpha2' => 'SK', 'iso_alpha3' => 'SVK', 'iso_numeric' => '703', 'calling_code' => '421', 'currency_code' => 'SKK', 'currency_name' => 'Koruna', 'currency_symbol' => 'Sk'),
            array('name' => 'Slovenia', 'iso_alpha2' => 'SI', 'iso_alpha3' => 'SVN', 'iso_numeric' => '705', 'calling_code' => '386', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Solomon Islands', 'iso_alpha2' => 'SB', 'iso_alpha3' => 'SLB', 'iso_numeric' => '90', 'calling_code' => '677', 'currency_code' => 'SBD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Somalia', 'iso_alpha2' => 'SO', 'iso_alpha3' => 'SOM', 'iso_numeric' => '706', 'calling_code' => '252', 'currency_code' => 'SOS', 'currency_name' => 'Shilling', 'currency_symbol' => 'S'),
            array('name' => 'South Africa', 'iso_alpha2' => 'ZA', 'iso_alpha3' => 'ZAF', 'iso_numeric' => '710', 'calling_code' => '27', 'currency_code' => 'ZAR', 'currency_name' => 'Rand', 'currency_symbol' => 'R'),
            array('name' => 'South Georgia and the South Sandwich Islands', 'iso_alpha2' => 'GS', 'iso_alpha3' => 'SGS', 'iso_numeric' => '239', 'calling_code' => '', 'currency_code' => 'GBP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'South Korea', 'iso_alpha2' => 'KR', 'iso_alpha3' => 'KOR', 'iso_numeric' => '410', 'calling_code' => '82', 'currency_code' => 'KRW', 'currency_name' => 'Won', 'currency_symbol' => '₩'),
            array('name' => 'Spain', 'iso_alpha2' => 'ES', 'iso_alpha3' => 'ESP', 'iso_numeric' => '724', 'calling_code' => '34', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Sri Lanka', 'iso_alpha2' => 'LK', 'iso_alpha3' => 'LKA', 'iso_numeric' => '144', 'calling_code' => '94', 'currency_code' => 'LKR', 'currency_name' => 'Rupee', 'currency_symbol' => '₨'),
            array('name' => 'Sudan', 'iso_alpha2' => 'SD', 'iso_alpha3' => 'SDN', 'iso_numeric' => '736', 'calling_code' => '249', 'currency_code' => 'SDD', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'Suriname', 'iso_alpha2' => 'SR', 'iso_alpha3' => 'SUR', 'iso_numeric' => '740', 'calling_code' => '597', 'currency_code' => 'SRD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Svalbard and Jan Mayen', 'iso_alpha2' => 'SJ', 'iso_alpha3' => 'SJM', 'iso_numeric' => '744', 'calling_code' => '', 'currency_code' => 'NOK', 'currency_name' => 'Krone', 'currency_symbol' => 'kr'),
            array('name' => 'Swaziland', 'iso_alpha2' => 'SZ', 'iso_alpha3' => 'SWZ', 'iso_numeric' => '748', 'calling_code' => '268', 'currency_code' => 'SZL', 'currency_name' => 'Lilangeni', 'currency_symbol' => ''),
            array('name' => 'Sweden', 'iso_alpha2' => 'SE', 'iso_alpha3' => 'SWE', 'iso_numeric' => '752', 'calling_code' => '46', 'currency_code' => 'SEK', 'currency_name' => 'Krona', 'currency_symbol' => 'kr'),
            array('name' => 'Switzerland', 'iso_alpha2' => 'CH', 'iso_alpha3' => 'CHE', 'iso_numeric' => '756', 'calling_code' => '41', 'currency_code' => 'CHF', 'currency_name' => 'Franc', 'currency_symbol' => 'CHF'),
            array('name' => 'Syria', 'iso_alpha2' => 'SY', 'iso_alpha3' => 'SYR', 'iso_numeric' => '760', 'calling_code' => '963', 'currency_code' => 'SYP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'Taiwan', 'iso_alpha2' => 'TW', 'iso_alpha3' => 'TWN', 'iso_numeric' => '158', 'calling_code' => '886', 'currency_code' => 'TWD', 'currency_name' => 'Dollar', 'currency_symbol' => 'NT$'),
            array('name' => 'Tajikistan', 'iso_alpha2' => 'TJ', 'iso_alpha3' => 'TJK', 'iso_numeric' => '762', 'calling_code' => '992', 'currency_code' => 'TJS', 'currency_name' => 'Somoni', 'currency_symbol' => ''),
            array('name' => 'Tanzania', 'iso_alpha2' => 'TZ', 'iso_alpha3' => 'TZA', 'iso_numeric' => '834', 'calling_code' => '255', 'currency_code' => 'TZS', 'currency_name' => 'Shilling', 'currency_symbol' => ''),
            array('name' => 'Thailand', 'iso_alpha2' => 'TH', 'iso_alpha3' => 'THA', 'iso_numeric' => '764', 'calling_code' => '66', 'currency_code' => 'THB', 'currency_name' => 'Baht', 'currency_symbol' => '฿'),
            array('name' => 'Togo', 'iso_alpha2' => 'TG', 'iso_alpha3' => 'TGO', 'iso_numeric' => '768', 'calling_code' => '228', 'currency_code' => 'XOF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Tokelau', 'iso_alpha2' => 'TK', 'iso_alpha3' => 'TKL', 'iso_numeric' => '772', 'calling_code' => '690', 'currency_code' => 'NZD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Tonga', 'iso_alpha2' => 'TO', 'iso_alpha3' => 'TON', 'iso_numeric' => '776', 'calling_code' => '676', 'currency_code' => 'TOP', 'currency_name' => 'Paanga', 'currency_symbol' => 'T$'),
            array('name' => 'Trinidad and Tobago', 'iso_alpha2' => 'TT', 'iso_alpha3' => 'TTO', 'iso_numeric' => '780', 'calling_code' => '1868', 'currency_code' => 'TTD', 'currency_name' => 'Dollar', 'currency_symbol' => 'TT$'),
            array('name' => 'Tunisia', 'iso_alpha2' => 'TN', 'iso_alpha3' => 'TUN', 'iso_numeric' => '788', 'calling_code' => '216', 'currency_code' => 'TND', 'currency_name' => 'Dinar', 'currency_symbol' => ''),
            array('name' => 'Turkey', 'iso_alpha2' => 'TR', 'iso_alpha3' => 'TUR', 'iso_numeric' => '792', 'calling_code' => '90', 'currency_code' => 'TRY', 'currency_name' => 'Lira', 'currency_symbol' => 'YTL'),
            array('name' => 'Turkmenistan', 'iso_alpha2' => 'TM', 'iso_alpha3' => 'TKM', 'iso_numeric' => '795', 'calling_code' => '993', 'currency_code' => 'TMM', 'currency_name' => 'Manat', 'currency_symbol' => 'm'),
            array('name' => 'Turks and Caicos Islands', 'iso_alpha2' => 'TC', 'iso_alpha3' => 'TCA', 'iso_numeric' => '796', 'calling_code' => '1649', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Tuvalu', 'iso_alpha2' => 'TV', 'iso_alpha3' => 'TUV', 'iso_numeric' => '798', 'calling_code' => '688', 'currency_code' => 'AUD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'U.S. Virgin Islands', 'iso_alpha2' => 'VI', 'iso_alpha3' => 'VIR', 'iso_numeric' => '850', 'calling_code' => '1340', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Uganda', 'iso_alpha2' => 'UG', 'iso_alpha3' => 'UGA', 'iso_numeric' => '800', 'calling_code' => '256', 'currency_code' => 'UGX', 'currency_name' => 'Shilling', 'currency_symbol' => ''),
            array('name' => 'Ukraine', 'iso_alpha2' => 'UA', 'iso_alpha3' => 'UKR', 'iso_numeric' => '804', 'calling_code' => '380', 'currency_code' => 'UAH', 'currency_name' => 'Hryvnia', 'currency_symbol' => '₴'),
            array('name' => 'United Arab Emirates', 'iso_alpha2' => 'AE', 'iso_alpha3' => 'ARE', 'iso_numeric' => '784', 'calling_code' => '971', 'currency_code' => 'AED', 'currency_name' => 'Dirham', 'currency_symbol' => ''),
            array('name' => 'United Kingdom', 'iso_alpha2' => 'GB', 'iso_alpha3' => 'GBR', 'iso_numeric' => '826', 'calling_code' => '44', 'currency_code' => 'GBP', 'currency_name' => 'Pound', 'currency_symbol' => '£'),
            array('name' => 'United States', 'iso_alpha2' => 'US', 'iso_alpha3' => 'USA', 'iso_numeric' => '840', 'calling_code' => '1', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'United States Minor Outlying Islands', 'iso_alpha2' => 'UM', 'iso_alpha3' => 'UMI', 'iso_numeric' => '581', 'calling_code' => '', 'currency_code' => 'USD', 'currency_name' => 'Dollar', 'currency_symbol' => '$'),
            array('name' => 'Uruguay', 'iso_alpha2' => 'UY', 'iso_alpha3' => 'URY', 'iso_numeric' => '858', 'calling_code' => '598', 'currency_code' => 'UYU', 'currency_name' => 'Peso', 'currency_symbol' => '$U'),
            array('name' => 'Uzbekistan', 'iso_alpha2' => 'UZ', 'iso_alpha3' => 'UZB', 'iso_numeric' => '860', 'calling_code' => '998', 'currency_code' => 'UZS', 'currency_name' => 'Som', 'currency_symbol' => 'лв'),
            array('name' => 'Vanuatu', 'iso_alpha2' => 'VU', 'iso_alpha3' => 'VUT', 'iso_numeric' => '548', 'calling_code' => '678', 'currency_code' => 'VUV', 'currency_name' => 'Vatu', 'currency_symbol' => 'Vt'),
            array('name' => 'Vatican', 'iso_alpha2' => 'VA', 'iso_alpha3' => 'VAT', 'iso_numeric' => '336', 'calling_code' => '39', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'),
            array('name' => 'Venezuela', 'iso_alpha2' => 'VE', 'iso_alpha3' => 'VEN', 'iso_numeric' => '862', 'calling_code' => '58', 'currency_code' => 'VEF', 'currency_name' => 'Bolivar', 'currency_symbol' => 'Bs'),
            array('name' => 'Vietnam', 'iso_alpha2' => 'VN', 'iso_alpha3' => 'VNM', 'iso_numeric' => '704', 'calling_code' => '84', 'currency_code' => 'VND', 'currency_name' => 'Dong', 'currency_symbol' => '₫'),
            array('name' => 'Wallis and Futuna', 'iso_alpha2' => 'WF', 'iso_alpha3' => 'WLF', 'iso_numeric' => '876', 'calling_code' => '681', 'currency_code' => 'XPF', 'currency_name' => 'Franc', 'currency_symbol' => ''),
            array('name' => 'Western Sahara', 'iso_alpha2' => 'EH', 'iso_alpha3' => 'ESH', 'iso_numeric' => '732', 'calling_code' => '', 'currency_code' => 'MAD', 'currency_name' => 'Dirham', 'currency_symbol' => ''),
            array('name' => 'Yemen', 'iso_alpha2' => 'YE', 'iso_alpha3' => 'YEM', 'iso_numeric' => '887', 'calling_code' => '967', 'currency_code' => 'YER', 'currency_name' => 'Rial', 'currency_symbol' => '﷼'),
            array('name' => 'Zambia', 'iso_alpha2' => 'ZM', 'iso_alpha3' => 'ZMB', 'iso_numeric' => '894', 'calling_code' => '260', 'currency_code' => 'ZMK', 'currency_name' => 'Kwacha', 'currency_symbol' => 'ZK'),
            array('name' => 'Zimbabwe', 'iso_alpha2' => 'ZW', 'iso_alpha3' => 'ZWE', 'iso_numeric' => '716', 'calling_code' => '263', 'currency_code' => 'ZWD', 'currency_name' => 'Dollar', 'currency_symbol' => 'Z$')
        );

        $output = array();
        foreach ($countries as $key => $value) {
            if ($return == 'country') $output[$value['iso_alpha2']] = $value['name'];
            else if ($return == 'currency_name') $output[$value['currency_code']] = $value['currency_code'] . " (" . $value['currency_name'] . ")";
            else if ($return == 'currency_icon') $output[$value['currency_code']] = !empty($value['currency_symbol']) ? $value['currency_symbol'] : $value['currency_code'];
            else $output[$value['iso_alpha2']] = $value['calling_code'];
        }
        if (isset($output[''])) unset($output['']);

        asort($output);
        return $output;
    }
}


if ( ! function_exists('get_real_ip')) {
    function get_real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}


if ( ! function_exists('has_module_access')) {
    function has_module_access($module_id=0,$user_module_ids=[],$is_admin=false,$is_manager=false){
        $user_module_ids = array_filter($user_module_ids);
        if($is_admin && !$is_manager) return true;
        if(in_array($module_id,$user_module_ids)) return true;
        return false;
    }
}

if ( ! function_exists('has_team_access')) {
    function has_team_access($is_admin=false){
        return $is_admin;
    }
}

if(!function_exists('run_curl')){

    function run_curl($url= '', $post_data = '', $json=false, $options=[], $timeout=true, $pass_array=false, $check_curl_error=true)
    {

        $header = $options['CURLOPT_HTTPHEADER'] ?? true;
        if($header===true) $header = ["Content-type: application/json"];
        if($header===false) $header = '';
        $agent = $options['CURLOPT_USERAGENT'] ?? 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3';
        $user_pwd = $options['CURLOPT_USERPWD'] ?? '';

        if(is_int($timeout)) $time = $timeout;
        else if(is_bool($timeout) && $timeout===true) $time = 60;
        else $time = 0;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if($time>0) curl_setopt($curl, CURLOPT_TIMEOUT, $time);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        if(!empty($header)) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if(!empty($post_data))
        {
            if(is_array($post_data) && !$pass_array) $post_data = json_encode($post_data);
            if(!empty($header)) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            if(!empty($user_pwd)) curl_setopt($curl, CURLOPT_USERPWD, $user_pwd);
        }
        $st = curl_exec($curl);

        json_decode($st);
        $is_invalid_json = json_last_error() == JSON_ERROR_NONE ? false : true;
        if($is_invalid_json) {
            $is_invalid_xml = @simplexml_load_string($st) ? false : true;
            if(!$is_invalid_xml) $st = convert_xml_to_json($st);
        }

        $result = $st;
        if($check_curl_error)
        {
            $curl_info=curl_getinfo($curl);
            if($is_invalid_json && ($curl_info['http_code']>299 || $curl_info['http_code']< 200))
            {
                $curl_error =  curl_error($curl);
                if(empty($curl_error) && is_string($st)) $curl_error = $st;
                $result = [];
                $result['ok'] = false;
                $result['description'] = $curl_error;
                $result['error_code'] = $curl_info['http_code'] ?? '';
                $result = json_encode($result);
            }
        }
        curl_close($curl);
        $response = !$json ? json_decode($result,TRUE) : $result;
        return $response;
    }
}

if(! function_exists('file_create_directory')){
    function file_create_directory($myDir=""){
        if(empty($myDir)) return false;
        $myDir = 'storage'.DIRECTORY_SEPARATOR .$myDir;
        if(File::exists($myDir)) return $myDir;
        if(File::makeDirectory($myDir, 0777, true)) return $myDir;
        else return false;
    }
}

if(! function_exists('file_create_directory_monthly')){
    function file_create_directory_monthly($myDir="",$user_id=0){
        if(empty($myDir) || $user_id==0) return false;
        $myDir = 'storage'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.date("Y").DIRECTORY_SEPARATOR.date("n").DIRECTORY_SEPARATOR.$myDir;
        if(File::exists($myDir)) return $myDir;
        if(File::makeDirectory($myDir, 0777, true)) return $myDir;
        else return false;
    }
}

if ( ! function_exists('file_delete_directory')) {
    function file_delete_directory($dirPath = "")
    {
        if (!is_dir($dirPath))
            return false;

        $files = new DirectoryIterator($dirPath);
        foreach ($files as $file) {
            // check if not . or ..
            if (!$file->isDot()) {
                $file->isDir() ? file_delete_directory($file->getPathname()) : unlink($file->getPathname());
            }
        }
        rmdir($dirPath);
        return true;
    }
}

if ( ! function_exists('file_create_zip')) {
    function file_create_zip($fromPath = null, $toPath = null)
    {
        if (empty($toPath)) $toPath = $fromPath . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($toPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot open ' . $toPath);
        }
        file_add_zip_content($zip, $fromPath);
        $zip->close();
        return $toPath;
    }
}

if ( ! function_exists('file_add_zip_content')) {
    function file_add_zip_content(\ZipArchive $zip, string $fromPath)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $fromPath,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($fromPath) + 1);

                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
    }
}

if ( ! function_exists('file_unzip')){
    function file_unzip($filePath=null,$extractPath=null){
        if(empty($filePath) || empty($extractPath)) return false;
        $zip = new \ZipArchive();
        $zip->open($filePath);

        if($zip->open($filePath) === TRUE)
        {
            $zip->extractTo($extractPath);
            $zip->close();
        }
        return $extractPath;
    }
}

if ( ! function_exists('valid_to_delete')) {
    function valid_to_delete($table = '', $where = [])
    {
        $count = DB::table($table)->select('id')->where($where)->count();
        return $count>0 ? true : false;
    }
}


if(! function_exists('get_selected_sidebar')){
    function get_selected_sidebar($route='dashboard')
    {
        $map_list = [
            'telegram-group-manager' => ['telegram-group-manager'],
            'list-package' => ['list-package','create-package','update-package'],
            'list-user' => ['list-user','create-user','update-user'],
            'select-package' => ['select-package','buy-package'],
            'transaction-log' => ['transaction-log','transaction-log-manual'],
            'general-settings' => ['payment-settings','agency-landing-editor']
        ];
        $select = $route;
        foreach ($map_list as $key=>$value)
        {
            if(in_array($route,$value)) $select = $key;
        }
        return $select;

    }
}

if(! function_exists('full_width_page_routes')){
    function full_width_page_routes(){
        return ['telegram-group-manager'];
    }
}

if(! function_exists('display_landing_content')){
    function display_landing_content($string=''){
        return str_replace(['{{APP_NAME}}','{{BOT_FATHER}}'],[config('app.name'),'<a href="https://t.me/botfather" target="_BLANK">BotFather</a>'],$string);
    }
}

if(! function_exists('get_current_lang')){
    function get_current_lang(){
        $lang = app()->getLocale();
        $exp = explode('-',$lang);
        return $exp[0] ?? 'en';
    }
}

if ( ! function_exists('get_domain_only'))
{
    function get_domain_only($url='') {
        if(empty($url)) return $url;
        $url=str_replace("www.","",$url);
        $url=str_replace("WWW.","",$url);

        if (!preg_match("@^https?://@i", $url) && !preg_match("@^ftps?://@i", $url)) {
            $url = "http://" . $url;
        }
        $parsed=@parse_url($url);
        return $parsed['host'];

    }
}

if ( ! function_exists('get_public_path'))
{
    function get_public_path($param='') {
        $replace = get_domain_only(ENV('APP_URL'))=='telegram-group.test' ? 'public' : 'html';
        $path = storage_path($param);
        $path = str_replace('storage',$replace,$path);
        return $path;

    }
}

if(! function_exists('is_mobile_device')){
    function is_mobile_device(){
        $useragent=$_SERVER['HTTP_USER_AGENT'] ?? '';
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
            return true;
        else return false;

    }
}

if(! function_exists('is_json')){
    function is_json($string=null){
        return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}

if(! function_exists('string_find')){
    function string_find($str='', $starting_word='', $ending_word='')
    {
        if(empty($str) || empty($starting_word) || empty($ending_word)) return $str;
        $subtring_start = @strpos($str, $starting_word);
        //Adding the starting index of the starting word to
        //its length would give its ending index
        $subtring_start += strlen($starting_word);
        //Length of our required sub string
        $size = @strpos($str, $ending_word, $subtring_start) - $subtring_start;
        // Return the substring from the index substring_start of length size
        return @substr($str, $subtring_start, $size);
    }
}

if(! function_exists('str_replace_first')){
    function str_replace_first($search, $replace, $subject)
    {
        $search = '/'.preg_quote($search, '/').'/';
        return preg_replace($search, $replace, $subject, 1);
    }
}

if(! function_exists('array_undot')){
    function array_undot($input){
        return $input;
    }
}

if( ! function_exists('check_build_version') ){

    function check_build_version()
    {
        if(file_exists(base_path('config/build-type.txt')))
        {
            $encoded = file_get_contents(base_path('config/build-type.txt'));
            $encrypt_method = "AES-256-CBC";
            $secret_key = 't8Mk8fsJMnFw69FGG5';
            $secret_iv = '9fljzKxZmMmoT358yZ';
            $key = hash('sha256', $secret_key);
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $decoded = openssl_decrypt(base64_decode($encoded), $encrypt_method, $key, 0, $iv);

            $decoded = explode('_', $decoded);
            $decoded = array_pop($decoded);
            return $decoded;
        }
        else return 'single';
    }
}
