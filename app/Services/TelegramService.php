<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
class TelegramService implements TelegramServiceInterface
{

    public $link = "https://api.telegram.org/bot";
    public $bot_token;
    public $name;
    public $chatId;
    public $text;
    public $callback_data;
    public $callback_id;
    public $callback_from_id;
    public $group_id;


    public function __construct()
    {
    }


    public function get_bot_details()
    {
        $url = $this->link.$this->bot_token."/getMe";
        return run_curl($url);
    }

    public function set_webhook()
    {
        $webhook_url = env('NGROK_URL')!='' ? env('NGROK_URL').'/webhook/telegram-webhook/'.$this->bot_token : route('telegram-webhook',$this->bot_token);
        $url = $this->link.$this->bot_token."/setWebhook?url=".$webhook_url.'&max_connections=100';
        return run_curl($url);
    }

    public function delete_webhook()
    {
        $url = $this->link.$this->bot_token."/deleteWebhook";
        return run_curl($url);
    }

    public function get_webhook_info()
    {
        $url = $this->link.$this->bot_token."/getWebhookInfo";
        return run_curl($url,'',true);
    }

    public function send($method,$data)
    {
        $msg = !is_array($data) ? json_decode($data,true) : $data;        
        if(isset($msg['delay_in_reply'])){
            $typing_on_delay_time = $msg['delay_in_reply'];
            if($typing_on_delay_time=="") $typing_on_delay_time = 0;
            unset($msg['delay_in_reply']);
            $data = json_encode($msg);
            if($typing_on_delay_time>0) sleep($typing_on_delay_time);
        }

        $url = $this->link.$this->bot_token. "/" . $method;
        return run_curl($url,$data,true);
    }

    public function get_bot_id($bot_token){
        $botToken = $bot_token;

        // Set up the API URL for getMe
        $apiUrl = 'https://api.telegram.org/bot' . $botToken . '/getMe';

        // Set up the curl request for getMe
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Send the API request for getMe
        $response = curl_exec($curl);

        // Check the API response for getMe
        if ($response === false) {
            echo 'Error: Failed to send API request: ' . curl_error($curl);
        } else {
            $responseData = json_decode($response, true);
            if (!$responseData['ok']) {
                echo 'Error: ' . $responseData['description'];
            } else {
               return $responseData['result']['id'];
                
            }
        }
        curl_close($curl);
    }

    public function delete_message($chatID, $messageID){
        $token=$this->bot_token;
        $url = "https://api.telegram.org/bot{$token}/deleteMessage";
        $params = array(
            'chat_id' => $chatID,
            'message_id' => $messageID
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response; 
        
    }

    public function check_group_admin($group_id, $userID)
    {
         $token=$this->bot_token;
         $url = "https://api.telegram.org/bot{$token}/getChatMember?chat_id=$group_id&user_id=$userID";
         $curl = curl_init($url);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         $response = curl_exec($curl);
         curl_close($curl);

         // Step 3: Parse API response
          return $result = json_decode($response, true);
    }

    public function pinMessage($chatID, $messageID) {
        $token=$this->bot_token;
        $url = "https://api.telegram.org/bot{$token}/pinChatMessage";
        $params = array(
            'chat_id' => $chatID,
            'message_id' => $messageID,
            'disable_notification' => true // Optional: Set to true to send the message silently
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
    }

    
    public function Ban_UnbanChatMember($method ='',$group_id,$subscriber_id){
        $token=$this->bot_token;
        $url = 'https://api.telegram.org/bot' . $token . '/'.$method;
        $params = array(
            'chat_id' => $group_id,
            'user_id' => $subscriber_id
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }


    public function mute_chat_member($group_id='',$telegram_group_subscriber_id='',$until_date='')
    {
        $token = $this->bot_token;
        $apiUrl = 'https://api.telegram.org/bot' . $token . '/';
        $muteEndpoint = 'restrictChatMember';

        $muteData = array(
            'chat_id' => $group_id,
            'user_id' => $telegram_group_subscriber_id,
            'permissions' => json_encode(
                       array(
                           'can_send_messages' => false,
                           'can_send_media_messages' => false,
                           'can_send_polls' => false,
                           'can_send_other_messages' => false,
                           'can_add_web_page_previews' => false,
                           'can_change_info' => false,
                           'can_invite_users' => false,
                           'can_pin_messages' => false
                       )
                   ),
            'until_date' => $until_date
        );

        $muteUrl = $apiUrl . $muteEndpoint;

        $ch = curl_init($muteUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $muteData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        return $responseData;
    }

}
