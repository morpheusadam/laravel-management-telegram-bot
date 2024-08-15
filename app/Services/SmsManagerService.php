<?php

namespace App\Services;

class SmsManagerService implements SmsManagerServiceInterface{


    public $userid;
    public $api_name;
    public $plivo_auth_id;
    public $plivo_auth_token;
    public $plivo_from;
    public $africastalking_sender;
    public $africastalking_api_key;
    public $clickatell_api_id;
    public $twilio_account_sid;
    public $twilio_auth_token;
    public $twilio_from;
    public $nexmo_api_key;
    public $nexmo_api_secret;
    public $nexmo_from;
    public $user;
    public $password;
    public $routesms_host_name;

    public function send_sms($msg, $recepient)
    {
        $msg=html_entity_decode($msg);

        if(!is_array($recepient)){
            $recepient = array($recepient);
        }

        $api_name=$this->api_name;
        $hostname = $this->routesms_host_name;
        if(substr($hostname, -1) == '/') $hostname = substr($hostname, 0, -1);
        $message_info =[];

        if($api_name=='plivo')
        {
            foreach($recepient as $to_number)
            {
                $msg=html_entity_decode($msg);
                $message_info = $this->plivo_sms_send($this->plivo_from,$to_number,$msg);
            }
        }

        else if($api_name=='twilio')
        {

            foreach($recepient as $to_number)
            {
                $message_info = $this->twilio_sms_sent($this->twilio_from,$to_number,$msg);
            }
        }
        else if($api_name == 'nexmo'){
            foreach ($recepient as $to_number)
            {
                $message_info = $this->nexmo_sms_sent($this->nexmo_from,$to_number,$msg);
            }
        }

        else if($api_name=='clickatell'){
            $msg=urlencode($msg);
            $message_info = $this->clickatell_platform_send_sms($recepient,$msg);
        }

        else if($api_name == 'africastalking')
        {
            foreach($recepient as $to_number)
            {
                $message_info	= $this->africastalking_send_sms($to_number,$msg);
            }
        }
        return $message_info;

    }

    public function twilio_sms_sent($from,$dst,$msg){

        $url = "https://api.twilio.com/2010-04-01/Accounts/$this->twilio_account_sid/SMS/Messages";
        $data_array = array (
            'From' => $from,
            'To' => $dst,
            'Body' => $msg,
        );

        $data = http_build_query($data_array);
        $result = run_curl($url,$data,false,['CURLOPT_HTTPHEADER'=>false,'CURLOPT_USERPWD'=>$this->twilio_account_sid . ":" . $this->twilio_auth_token],true,true);
        if(isset($result['SMSMessage']['Sid'])){
            $response['description'] = $result['SMSMessage']['Sid'] ?? '';
            $response['ok'] = true;
            $response['error_code'] = '';
        }
        else
        {
            $error_json = isset($result['description']) ?  json_decode($result['description'],true) : [];
            $is_invalid_json = json_last_error() == JSON_ERROR_NONE ? false : true;
            $error_msg = '';

            if($is_invalid_json) $error_msg = $result['description'] ?? 'Failed to send message';
            if(!$is_invalid_json && isset($error_json['RestException']['Message'])) $error_msg = $error_json['RestException']['Message'];

            $response=  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
        }
        return $response;
    }

    public function nexmo_sms_sent($from,$dst,$msg){
        $url = 'https://rest.nexmo.com/sms/json';
        $data = [
            'api_key' => $this->nexmo_api_key,
            'api_secret' => $this->nexmo_api_secret,
            'to' => $dst,
            'from' => $from,
            'text' => $msg
        ];

        $result = run_curl($url,$data,false);

        if(isset($result['ok']) && $result['ok']==false) return $result;

        if(isset($result['messages'][0]['message-id'])){
            $response['description'] = $result['messages'][0]['message-id'] ?? '';
            $response['ok'] = true;
            $response['error_code'] = '';
        }
        else
        {
            $error_msg = $result['messages'][0]['error-text'] ?? 'Failed to send message';
            $response=  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
        }
        return $response;
    }

    public function plivo_sms_send($src,$dst,$text)
    {

        $dst=ltrim($dst, '+');
        $PLIVO_AUTH_ID = $this->plivo_auth_id;
        $PLIVO_AUTH_TOKEN = $this->plivo_auth_token;

        try
        {
            $url = 'https://api.plivo.com/v1/Account/'.$PLIVO_AUTH_ID.'/Message/';
            $data = array("src" => "$src", "dst" => "$dst", "text" => "$text");
            $data_string = json_encode($data);
            $result = run_curl($url,$data_string,false,['CURLOPT_USERPWD'=>$PLIVO_AUTH_ID . ":" . $PLIVO_AUTH_TOKEN]);
            if(isset($result['ok']) && $result['ok']==false) return $result;
            if(isset($result['message_uuid']))
            {
                $response['description'] = $result['message_uuid'][0] ?? '';
                $response['ok'] = true;
                $response['error_code'] = '';
            }
            else
            {
               $error_msg = $result['error'] ?? 'Failed to send message';
               $response=  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
            }
            return $response;
        }
        catch (Exception $e)
        {
            $error_msg = $e->getMessage()  ?? 'Error in configuration';
            $response=  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
            return $response;
        }
    }


    function clickatell_platform_send_sms($to_numbers,$msg)
    {
        try
        {
            if(!is_array($to_numbers))
            {
                $to_numbers = array($to_numbers);
            }
            for($i=0;$i<count($to_numbers);$i++)
            {
                $to_numbers[$i]=ltrim($to_numbers[$i], '+');
                $to_numbers[$i]=ltrim($to_numbers[$i], '0');
            }
            $to_numbers=implode(",",$to_numbers);

            $url="https://platform.clickatell.com/messages/http/send?apiKey={$this->clickatell_api_id}&to={$to_numbers}&content={$msg}&unicode=1";
            $result = run_curl($url,'',false);
            if(isset($result['ok']) && $result['ok']==false) return $result;

            if(isset($result['messages'][0]['apiMessageId']) && $result['messages'][0]['accepted']== 'true') {
                $response['description'] = $result['messages'][0]['apiMessageId'] ?? '';
                $response['ok'] = true;
                $response['error_code'] = '';
            }
            else {
                if(isset($result['messages'][0]['errorDescription']) && !empty($result['messages'][0]['errorDescription'])) {
                    $response['description']= $result['messages'][0]['errorDescription'] ?? 'Failed to send message';
                    $response['ok'] = false;
                    $response['error_code'] = '';
                } else if(isset($result['errorDescription']) && !empty($result['errorDescription'])){
                    $error_msg= $result['errorDescription'] ?? 'Failed to send message';
                    $response =  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
                } else {
                    $response =  ['ok'=>false,'description'=>'Something went wrong','error_code'=>''];
                }
            }
            return $response;
        }
        catch (Exception $e)
        {
            $error_msg = $e->getMessage()  ?? 'Error in configuration';
            $response=  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
            return $response;
        }
    }



    function  africastalking_send_sms($to,$msg)
    {
        $api_key = $this->africastalking_api_key;
        $username = $this->africastalking_sender;
        try
        {
            $url =  (env('APP_ENV')!='local') ? 'http://api.africastalking.com/version1/messaging' : 'http://api.sandbox.africastalking.com/version1/messaging';
            $data = array("username" =>$username, "message" =>$msg, "to" => $to);
            $header = array("Accept: application/json","Apikey:{$api_key}");
            $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0';
            $result = run_curl($url,$data,false,['CURLOPT_HTTPHEADER'=>$header,'CURLOPT_USERAGENT'=>$agent],true,true);

            if(isset($result['ok']) && $result['ok']==false) return $result;
            $response['description']=isset($result['SMSMessageData']['Recipients'][0]['messageId']) ? $result['SMSMessageData']['Recipients'][0]['messageId'] : "";
            $response['ok'] = true;
            $response['error_code'] = '';
            return $response;
        }
        catch (Exception $e)
        {
            $error_msg = $e->getMessage()  ?? 'Error in configuration';
            $response=  ['ok'=>false,'description'=>$error_msg,'error_code'=>''];
            return $response;
        }

    }




}
