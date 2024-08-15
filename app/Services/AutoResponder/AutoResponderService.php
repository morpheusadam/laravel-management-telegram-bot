<?php
namespace App\Services\AutoResponder;
class AutoResponderService extends AutoResponderServiceInterface
{
    public $invalid_api_error = ['ok'=>false,'description'=> 'Invalid API settings provided','error_code'=> 'Bad Request'];

    public function mailchimp_segment_list($mailChimpApiKey)
    {
        $apikey_explode = explode('-',$mailChimpApiKey); // The API ID is the last part of your api key, after the hyphen (-),
        if(is_array($apikey_explode) && isset($apikey_explode[1])) $api_id=$apikey_explode[1];
        else $api_id="";

        if($mailChimpApiKey=="" || $api_id=="") return $this->invalid_api_error;

        $auth = base64_encode( 'user:'.$mailChimpApiKey );

        $url="https://".$api_id.".api.mailchimp.com/3.0/lists?fields=lists";
        $response = run_curl($url,'',false,['CURLOPT_HTTPHEADER'=>['Content-Type: application/json','Authorization: Basic '.$auth],'CURLOPT_USERAGENT'=>'PHP-MCAPI/2.0'],false);

        return $response;
    }

    public function mailchimp_add_contact($mailChimpApiKey,$mailChimpListId,$data,$tags)
    {
        $apikey_explode = explode('-',$mailChimpApiKey); // The API ID is the last part of your api key, after the hyphen (-),
        if(is_array($apikey_explode) && isset($apikey_explode[1])) $api_id=$apikey_explode[1];
        else $api_id="";

        if($mailChimpApiKey=="" || $api_id=="" || $mailChimpListId=="" || $data=="") return $this->invalid_api_error;

        $auth = base64_encode( 'user:'.$mailChimpApiKey );
        $insert_data=array
        (
            'email_address'  => $data['email'],
            'status'         => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
            'merge_fields'  => array('FNAME'=>$data['firstname'],'LNAME'=>$data['lastname'],'CITY'=>'','MMERGE5'=>"Subscriber")
        );

        if($tags!="")
        {
            if(is_array($tags)) $insert_data['tags']=$tags;
            else $insert_data['tags']=array($tags);
        }

        $insert_data=json_encode($insert_data);
        $url="https://".$api_id.".api.mailchimp.com/3.0/lists/".$mailChimpListId."/members/";
        return $result = run_curl($url,$insert_data,false,['CURLOPT_HTTPHEADER'=>['Content-Type: application/json','Authorization: Basic '.$auth],'CURLOPT_USERAGENT'=>'PHP-MCAPI/2.0'],false);
    }

    public  function sendinblue_segment_list($apiKey)
    {
        $url="https://api.sendinblue.com/v3/contacts/lists";
        $header=array("api-key: {$apiKey}","Content-Type: application/json");
        return $response = run_curl($url,'',false,['CURLOPT_HTTPHEADER'=>$header],false);
    }

    public function sendinblue_add_contact($api_key,$list_id,$data)
    {
        $url="https://api.sendinblue.com/v3/contacts";
        $header=array("api-key: {$api_key}","Content-Type: application/json");
        $postdata['email']=$data['email'];
        $postdata['attributes']['FIRSTNAME'] =$data['firstname'];
        $postdata['attributes']['LASTNAME'] = $data['lastname'];
        $postdata['listIds'][0]=(int)$list_id;
        $postdata=json_encode($postdata);
        return $response = run_curl($url,$postdata,false,['CURLOPT_HTTPHEADER'=>$header]);

    }

    public function activecampaign_segment_list($url,$api_key)
    {
        $url=$url."/api/3/lists";
        $header=array("Api-Token: {$api_key}","Content-Type: application/json");
        return $response = run_curl($url,'',false,['CURLOPT_HTTPHEADER'=>$header],false);
    }

    public function activecampaign_add_contact($api_key,$url,$list_id,$data)
    {
        $url_add=$url."/api/3/contacts";
        $header=array("Api-Token: {$api_key}","Content-Type: application/json");
        $postdata['contact']['email']=$data['email'];
        $postdata['contact']['firstName']=$data['firstname'];
        $postdata['contact']['lastName']=$data['lastname'];
        $postdata=json_encode($postdata);
        $response = run_curl($url_add,$postdata,false,['CURLOPT_HTTPHEADER'=>$header]);
        if(!$response['ok']) return $response;

        $contact_id = $response['contact']['id'];
        $url_list_update=$url."/api/3/contactLists";
        $postdata = array();
        $postdata['contactList']['list']=(int)$list_id;
        $postdata['contactList']['contact']=$contact_id;
        $postdata['contactList']['status']=1;

        $postdata=json_encode($postdata);
        return $response = run_curl($url_list_update,$postdata,false,['CURLOPT_HTTPHEADER'=>$header],false);

    }

    public function mautic_segment_list($base_64_user_pass,$url)
    {
        $url=$url."/api/segments";
        $header = array("Authorization: Basic " . $base_64_user_pass);
        return $response = run_curl($url,'',false,['CURLOPT_HTTPHEADER'=>$header],false);
    }

    public function mautic_add_contact($base_64_user_pass,$url,$list_id,$data,$tag)
    {
        $url_add=$url."/api/contacts/new";

        $header = array("Authorization: Basic " . $base_64_user_pass);

        $post_data = array(
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'ipAddress' => $_SERVER['REMOTE_ADDR'],
            'tags' =>$tag,
            'overwriteWithBlank' => true,
        );

        $response = run_curl($url_add,$post_data,false,['CURLOPT_HTTPHEADER'=>$header],false);

        if(isset($response['ok']) && !$response['ok']) return $response;

        $contact_id=$response['contact']['id']??'';
        $url_segment_update=$url."/api/segments/{$list_id}/contact/{$contact_id}/add";
        $final_response = run_curl($url_segment_update,'',false,['CURLOPT_HTTPHEADER'=>$header],false);
        return $final_response;
    }

    public function acelle_segment_list($api_token,$url)
    {
        $url=$url."/lists?api_token={$api_token}";
        $header=array("accept:application/json");
        return $response = run_curl($url,'',false,['CURLOPT_HTTPHEADER'=>$header],false);
    }

    public function acelle_add_contact($api_token,$url,$list_id,$data)
    {
        $url=$url."/subscribers?list_uid={$list_id}&api_token={$api_token}";
        $post_data = array(
            'FIRST_NAME' => $data['firstname'],
            'LAST_NAME'  => $data['lastname'],
            'EMAIL'     =>  $data['email'],
        );
        return $response = run_curl($url,$post_data,false,[],false);

    }


}
