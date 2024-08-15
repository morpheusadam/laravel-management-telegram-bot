<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use App\Services\SmsManagerServiceInterface;
use App\Services\TelegramServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Webhook extends Home
{
    public function __construct(TelegramServiceInterface $telegram_service,SmsManagerServiceInterface $sms_manager_service)
    {
        $this->telegram = $telegram_service;
        $this->sms_manager = $sms_manager_service;

    }

    public function telegram_webhook($bot_token=''){
        $raw_response =  file_get_contents('php://input');

        if($raw_response=='' || $bot_token=='') exit;

        $json_response = array("raw_response"=>$raw_response,"bot_token"=>$bot_token);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, route('telegram-webhook-main'));
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_response);
        curl_setopt($ch,CURLOPT_TIMEOUT, 5);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        echo $reply_response = curl_exec($ch);
    }


    public function telegram_webhook_main(Request $request)
    {
        $raw_response = $request->raw_response;
        $raw_response_array = json_decode($raw_response,true);
        if(empty($raw_response_array)) exit();
        $bot_token = $request->bot_token;
        if(empty($bot_token)) exit();
        $bot_info = DB::table('telegram_bots')->where(['bot_token'=>$bot_token,'status'=>'1'])->first();
        if(empty($bot_info)) exit();
        $telegram_bot_status = $bot_info->status ?? '0';
        if($telegram_bot_status=='0') exit();
        $telegram_bot_id = $bot_info->id ?? 0;
        $user_id = $bot_info->user_id ?? 0;
        $telegram_bot_username = $bot_info->username ?? '';
        $started_button_enabled = $bot_info->started_button_enabled ?? '0';
        $no_match_found_reply_enabled = $bot_info->no_match_found_reply_enabled ?? '0';
        $persistent_enabled = $bot_info->persistent_enabled ?? '0';
        $welcome_message = $bot_info->welcome_message ?? '';
        $chat_human_email = $bot_info->chat_human_email ?? '';
        $sms_broadcast_sequence_campaign_id = $bot_info->sms_broadcast_sequence_campaign_id ?? '';
        $email_broadcast_sequence_campaign_id = $bot_info->email_broadcast_sequence_campaign_id ?? '';
        $email_auto_responder_settings = $bot_info->email_auto_responder_settings ?? '';
        $settings_sms_id = $bot_info->settings_sms_id ?? 0;
        $sms_reply_message = $bot_info->sms_reply_message ?? '';
        $settings_email_id = $bot_info->settings_email_id ?? 0;
        $email_reply_message = $bot_info->email_reply_message ?? '';
        $email_reply_subject = $bot_info->email_reply_subject ?? '';

        set_agency_config();

        $user_info = DB::table('users')
            ->select('users.*','module_ids')
            ->leftJoin('packages','packages.id','=','users.package_id')
            ->where('users.id',$user_id)->first();
        $user_type = $user_info->user_type ?? 'Member';
        $module_ids = $user_info->module_ids ?? '';
        $module_ids = explode(',',$module_ids);
        $is_admin = $user_type=='Admin';
        $is_manager = $user_type=='Manager';
        //   TELEGRAM GROUP MANAGEMENT START
        if(has_module_access($this->module_id_telegram_group,$module_ids,$is_admin,$is_manager)){
            // Add group info in database
            $telegram_bot_name = $bot_info->username ?? '';
            $add_group_info =
            isset($raw_response_array['message']['new_chat_member']['is_bot'])  &&
            isset($raw_response_array['message']['new_chat_member']['username'])  &&
            isset($raw_response_array['message']['chat']['type'])  &&
            ($raw_response_array['message']['chat']['type'] == 'group' || $raw_response_array['message']['chat']['type'] == 'supergroup') &&
            $raw_response_array['message']['new_chat_member']['username'] == $telegram_bot_name && $raw_response_array['message']['new_chat_member']['is_bot'] == 'false' ? true : false;
            if($add_group_info){
                $group_id = $raw_response_array['message']['chat']['id'] ?? '';
                $group_name = $raw_response_array['message']['chat']['title'] ?? '';
                $supergroup_subscriber_id =  $group_id.'-'.$telegram_bot_id;
                if(!empty($group_id) && DB::table('telegram_groups')->where(['supergroup_subscriber_id'=>$supergroup_subscriber_id])->doesntExist()){
                    $group_id = (string) $group_id;
                    DB::table('telegram_groups')->insert(['group_id'=>$group_id,'telegram_bot_id'=>$telegram_bot_id,'group_name'=>$group_name,"supergroup_subscriber_id"=>$supergroup_subscriber_id]);
                }
            }

            //Update Bot administrator  status in telegram group
            $is_bot_administrator =
            isset($raw_response_array['my_chat_member']['new_chat_member']['user']['is_bot']) &&
            isset($raw_response_array['my_chat_member']['chat']['type']) &&
            isset($raw_response_array['my_chat_member']['new_chat_member']['user']['username']) &&
            ($raw_response_array['my_chat_member']['chat']['type'] == 'group' || $raw_response_array['my_chat_member']['chat']['type'] == 'supergroup') &&
            $raw_response_array['my_chat_member']['new_chat_member']['user']['username'] == $telegram_bot_name  ? true : false;
            if($is_bot_administrator){
                $group_id = $raw_response_array['my_chat_member']['chat']['id'] ?? '';
                $supergroup_subscriber_id =  $group_id.'-'.$telegram_bot_id;
                $chat_status = $raw_response_array['my_chat_member']['new_chat_member']['status'] ?? '';
                $add_bot_administrator = !empty($group_id) && $chat_status =='administrator' ? true : false;
                if($add_bot_administrator){
                    DB::table('telegram_groups')->where(['supergroup_subscriber_id'=> $supergroup_subscriber_id])->update(['is_bot_admin'=>'1']);
                }
                else{
                    DB::table('telegram_groups')->where(['supergroup_subscriber_id'=> $supergroup_subscriber_id])->update(['is_bot_admin'=>'0']);
                }
            }

            $group_id = $raw_response_array['message']['chat']['id'] ?? null;
            $userID = $raw_response_array['message']['from']['id'] ?? '';


            if(empty($group_id)) $group_id =  $raw_response_array['chat_member']['chat']['id'] ?? null;
            $supergroup_subscriber_id =  $group_id.'-'.$telegram_bot_id;
            $get_group = DB::table('telegram_groups')->select('*')->where('supergroup_subscriber_id',$supergroup_subscriber_id)->first();
            $telegram_group_id = $get_group->id ?? 0;
            $is_bot_admin = $get_group->is_bot_admin ?? false;
            $filter_message_data = DB::table('telegram_group_message_filterings')->select('*')->where('telegram_group_id',$telegram_group_id)->first();
            $remove_admin_message = $filter_message_data->remove_admin_message ?? '';
            $this->telegram->bot_token = $bot_token;
            $result_check_group_admin = $this->telegram->check_group_admin($group_id, $userID);
            $group_admin = false;

            if(isset($result_check_group_admin['ok']) && isset($result_check_group_admin['result']['status']) && ($result_check_group_admin['result']['status'] == 'administrator' || $result_check_group_admin['result']['status'] == 'creator' ) && $remove_admin_message != '1') $group_admin = true;
            if(!empty($get_group) && $is_bot_admin && !empty($group_id)){

            //Add group member in telegram_group_subscribers table when message in group
                $add_subscriber = isset($raw_response_array['message']['chat']['type']) && ($raw_response_array['message']['chat']['type'] == 'group' || $raw_response_array['message']['chat']['type'] == 'supergroup') ? true : false;
                if($add_subscriber){
                    $currentDateTime = date('Y-m-d H:i:s');
                    if(isset($raw_response_array['message']['new_chat_member'])){
                        $subscriber_id = $raw_response_array['message']['new_chat_member']['id'] ?? '';
                        $first_name =  $raw_response_array['message']['new_chat_member']['first_name'] ?? '';
                        $last_name =  $raw_response_array['message']['new_chat_member']['last_name'] ?? '';
                        $user_name =  $raw_response_array['message']['new_chat_member']['username'] ?? '';
                        $group_subscriber_id = $subscriber_id.'-'.$telegram_group_id;

                        $user_joined_group = $filter_message_data->user_joined_group ?? '';
                        if($user_joined_group == '1'){
                            $message = $raw_response_array['message'];
                            $chatID = $message['chat']['id'] ?? '';
                            $messageID = $message['message_id'] ?? '';
                            $this->telegram->bot_token = $bot_token;
                            $this->telegram->delete_message($chatID, $messageID);
                        }

                        if(isset($raw_response_array['message']['new_chat_member']['is_bot']) && $raw_response_array['message']['new_chat_member']['is_bot'] !=true){
                            if(!empty($subscriber_id) && DB::table('telegram_group_subscribers')->where(['group_subscriber_id'=> $group_subscriber_id])->doesntExist()){
                                // check subscriber limit before inserting new subscriber
                                $limit_exceed=$this->check_usage($this->module_id_bot_subscriber,1,$user_id);
                                if($limit_exceed=="2" || $limit_exceed=="3") die('Subscriber limit exceeded.');
                                $query = DB::table('telegram_group_subscribers')->insert([
                                    'telegram_group_id' => $telegram_group_id,
                                    'group_chat_id' => $subscriber_id,
                                    'group_subscriber_id' => $group_subscriber_id,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'username' => $user_name,
                                    'updated_at' => $currentDateTime
                                ]);
                                $this->insert_usage_log($this->module_id_bot_subscriber,1,$user_id);

                            }
                            else{
                                DB::table('telegram_group_subscribers')->where(['group_subscriber_id'=> $group_subscriber_id])->update(['is_left'=>'0','is_banned'=>'0','removed'=>'0']);
                            }

                            $new_join_restrict_time = $filter_message_data->new_join_restrict_time ?? '';
                            if(!empty($new_join_restrict_time) && $raw_response_array['message']['chat']['type'] == 'supergroup'){
                               $restrict_time = explode('-',$new_join_restrict_time);
                               $mute_time = $restrict_time[0] ?? 0;
                               if($mute_time>0){
                                    $mute_type = $restrict_time[1];
                                    $mute_actual_time = date('Y-m-d H:i:s', strtotime($currentDateTime . ' + ' . $mute_time .$mute_type ));
                                    $until_date = strtotime($mute_actual_time);
                                    $this->telegram->bot_token = $bot_token;
                                    $responseData= $this->telegram->mute_chat_member($group_id,$subscriber_id,$until_date);
                                    if ($responseData['ok']) {
                                          DB::table('telegram_group_subscribers')->where(['telegram_group_id'=>$telegram_group_id,'group_subscriber_id'=>$group_subscriber_id])->update(['mute_time'=>$mute_actual_time]);
                                    }
                               }
                            }

                        }

                    }
                    else if(DB::table('telegram_groups')->where(['supergroup_subscriber_id'=> $supergroup_subscriber_id])->doesntExist()){
                        DB::table('telegram_groups')->insert(['group_id'=>$group_id,'supergroup_subscriber_id'=> $supergroup_subscriber_id,'telegram_bot_id'=>$telegram_bot_id,'group_name'=> $raw_response_array['message']['chat']['title']]);
                    }
                    else{
                        $subscriber_id = $raw_response_array['message']['from']['id'] ?? '';
                        $first_name =  $raw_response_array['message']['from']['first_name'] ?? '';
                        $last_name =  $raw_response_array['message']['from']['last_name'] ?? '';
                        $user_name =  $raw_response_array['message']['from']['username'] ?? '';
                        $group_subscriber_id = $subscriber_id.'-'.$telegram_group_id;
                        if(!empty($subscriber_id) && DB::table('telegram_group_subscribers')->where(['group_subscriber_id'=> $group_subscriber_id])->doesntExist() && $user_name != $telegram_bot_username ){
                            $limit_exceed=$this->check_usage($this->module_id_bot_subscriber,1,$user_id);
                            if($limit_exceed=="2" || $limit_exceed=="3") die('Subscriber limit exceeded.');

                            DB::table('telegram_group_subscribers')->insert(['telegram_group_id'=>$telegram_group_id, 'group_chat_id'=> $subscriber_id,'group_subscriber_id'=> $group_subscriber_id,'first_name'=>$first_name,'last_name'=>$last_name,'username'=>$user_name,'updated_at'=>$currentDateTime]);
                            $this->insert_usage_log($this->module_id_bot_subscriber,1,$user_id);
                        }
                    }
                }

                // Delete Command
                if (isset($raw_response_array['message']['chat']['type']) && !isset($raw_response_array['message']['forward_from']) && ($raw_response_array['message']['chat']['type'] == 'group' || $raw_response_array['message']['chat']['type'] == 'supergroup') ) {
                    $message = $raw_response_array['message'];
                    if(!empty($filter_message_data) && $group_admin != true){

                        $group_message_is_text = isset($raw_response_array['message']['text']) ? true : false;
                        $group_message_text = $group_message_is_text ? $raw_response_array['message']['text'] : '';
                        $group_message_text = strtolower($group_message_text);

                        $subscriber_id = $raw_response_array['message']['from']['id'] ?? '';
                        $group_subscriber_id = $subscriber_id.'-'.$telegram_group_id;

                        $delete_command = $filter_message_data->delete_command ?? false;
                        $delete_image = $filter_message_data->delete_image ?? false;
                        $delete_document = $filter_message_data->delete_document ?? false;
                        $delete_voice = $filter_message_data->delete_voice ?? false;
                        $delete_sticker = $filter_message_data->delete_sticker ?? false;
                        $delete_diceroll = $filter_message_data->delete_diceroll ?? false;
                        $delete_links = $filter_message_data->delete_links ?? false;
                        $delete_containing_words = !empty($filter_message_data->delete_containing_words) ? json_decode($filter_message_data->delete_containing_words) : [];

                        $same_message_delete_count = $filter_message_data->same_message_delete_count ?? '';
                        $same_message_restrict_count = $filter_message_data->same_message_restrict_count ?? '';
                        $same_message_restrict_time = $filter_message_data->same_message_restrict_time ?? '';
                        $allowed_message_per_time_count = $filter_message_data->allowed_message_per_time_count ?? '';
                        $minute_allowed_message_per_time = $filter_message_data->minute_allowed_message_per_time ?? '';

                        $message_list_of_member = DB::table('telegram_bot_livechat_messages')->select('id','message_content')->where('telegram_group_subscriber_group_subscriber_id',$group_subscriber_id)->limit(20)->orderBy('id', 'DESC')->get();

                         // What is the maximum number of messages that a user can send within minutes?
                        if($allowed_message_per_time_count>0 && $minute_allowed_message_per_time>0){
                            $message_list =$message_list_of_member->take($allowed_message_per_time_count);
                            $time_frame = $minute_allowed_message_per_time*60;
                            $current_time = time();
                            $sms_sent_count = 0;
                            foreach ($message_list as $sms) {
                                $sms_data = json_decode($sms->message_content, true);
                                $sms_timestamp = $sms_data['message']['date'];
                                if ($current_time - $sms_timestamp <= $time_frame) {
                                    $sms_sent_count++;
                                }
                            }
                            if($sms_sent_count >= $allowed_message_per_time_count){
                                $messageID = $message['message_id'] ?? '';
                                $response = $this->telegram->delete_message($group_id, $messageID);
                                if(isset($response->ok) && $response->ok == true){
                                    dd('Message limit exceeded');
                                }

                            }
                        }

                        $restrict_time = explode('-',$same_message_restrict_time);
                        $mute_time = $restrict_time[0] ?? 0;
                        if($same_message_restrict_count >1 && !empty($mute_time) && $mute_time>0 && $group_message_is_text){
                             $lim = $same_message_restrict_count-1;
                             $message_list =$message_list_of_member->take($lim);
                             $count_message = 0;
                             if(!empty($message_list)){
                                 foreach ($message_list as $message_group) {
                                    $message_text_data = json_decode($message_group->message_content, true);
                                    if (isset($message_text_data['message']['text'])) {
                                        $message_text = strtolower($message_text_data['message']['text']);
                                    } else {
                                        $message_text = '';
                                    }
                                    $message_id = json_decode($message_group->message_content)->message->message_id ?? '';
                                    $message_database_id[] = $message_group->id;
                                    $message_list_id[] = $message_id;
                                    if ($message_text === $group_message_text) {
                                        $count_message++;
                                    }
                                }
                             }
                             if($count_message == $lim){
                                if(!empty($same_message_restrict_time)){                                   
                                   if($mute_time>0){
                                        $mute_type = $restrict_time[1];
                                        $mute_actual_time = date('Y-m-d H:i:s', strtotime($currentDateTime . ' + ' . $mute_time .$mute_type ));
                                        $until_date = strtotime($mute_actual_time);
                                        $this->telegram->bot_token = $bot_token;
                                        $responseData= $this->telegram->mute_chat_member($group_id,$subscriber_id,$until_date);
                                        if ($responseData['ok']) {
                                            DB::table('telegram_group_subscribers')->where(['telegram_group_id'=>$telegram_group_id,'group_subscriber_id'=>$group_subscriber_id])->update(['mute_time'=>$mute_actual_time]);
                                        }
                                   }
                                }
                             }
                        }
                        //  same messages continuously, the messages will be automatically deleted.
                        if(($same_message_delete_count >1 ) && $group_message_is_text){
                            $lim = $same_message_delete_count-1;
                            $message_list_id = [];
                            $message_database_id = [];
                            $message_list =$message_list_of_member->take($lim);
                            $count_message = 0;
                            if(!empty($message_list)){
                                foreach ($message_list as $message_group) {
                                       $message_text_data = json_decode($message_group->message_content, true);
                                       if (isset($message_text_data['message']['text'])) {
                                           $message_text = strtolower($message_text_data['message']['text']);
                                       } else {
                                           $message_text = '';
                                       }
                                       $message_id = json_decode($message_group->message_content)->message->message_id ?? '';
                                       $message_database_id[] = $message_group->id;
                                       $message_list_id[] = $message_id;
                                       if ($message_text === $group_message_text) {
                                           $count_message++;
                                       }
                                   }
                            }
                            if($count_message == $lim){
                                if($same_message_delete_count >1){
                                    for($i = 0; $i<$same_message_delete_count-1; $i++ ){
                                        $response = $this->telegram->delete_message($group_id, $message_list_id[$i]);
                                        $response = json_decode($response);
                                        if(isset($response->ok) && $response->ok == true){
                                            DB::table('telegram_bot_livechat_messages')->where('id',$message_database_id[$i])->delete();
                                        }
                                    }
                                   echo 'Group message deleted';
                                }
                               
                            }
                        }

                        // Check if the message contains bot commands
                        if($delete_command){
                            if (isset($message['entities']) && is_array($message['entities'])) {
                                foreach ($message['entities'] as $entity) {
                                    if ($entity['type'] === 'bot_command') {
                                        // Delete the message with bot commands
                                        $chatID = $message['chat']['id'] ?? '';
                                        $messageID = $message['message_id'] ?? '';
                                        $this->telegram->bot_token = $bot_token;
                                        $this->telegram->delete_message($chatID, $messageID);
                                    }
                                }
                            }
                        }

                        if($delete_image){
                            if (isset($message['photo'])) {
                                // Delete the image message
                                $chatID = $message['chat']['id'] ?? '';
                                $messageID = $message['message_id'] ?? '';
                                $this->telegram->bot_token = $bot_token;
                                $this->telegram->delete_message($chatID, $messageID);
                            }
                        }
                        if($delete_links){
                            // link
                            if (isset($message['text'])) {
                                $text = $message['text'];

                                $pattern = '/https?:\/\/\S+/i';
                                $link = preg_match($pattern, $text);

                                // Check if the text contains URLs
                                if ($link) {

                                    $chatID = $message['chat']['id'] ?? '';
                                    $messageID = $message['message_id'] ?? '';
                                    $this->telegram->bot_token = $bot_token;
                                    $this->telegram->delete_message($chatID, $messageID);
                                }
                            }
                        }
                        if($delete_document){
                            if (isset($message['document'])) {
                                // Delete the image message
                                $chatID = $message['chat']['id'] ?? '';
                                $messageID = $message['message_id'] ?? '';
                                $this->telegram->bot_token = $bot_token;
                                $this->telegram->delete_message($chatID, $messageID);
                            }
                        }
                        if($delete_voice){
                            if (isset($message['voice'])) {
                                // Delete the image message
                                $chatID = $message['chat']['id'] ?? '';
                                $messageID = $message['message_id'] ?? '';
                                $this->telegram->bot_token = $bot_token;
                                $this->telegram->delete_message($chatID, $messageID);
                            }
                        }
                        if($delete_sticker){
                            if (isset($message['sticker']) || isset($message['animation'])) {
                                // Delete the image message
                                $chatID = $message['chat']['id'] ?? '';
                                $messageID = $message['message_id'] ?? '';
                                $this->telegram->bot_token = $bot_token;
                                $this->telegram->delete_message($chatID, $messageID);
                            }
                        }
                        //Delete Message containing word
                        if(!empty($delete_containing_words)){
                            if(isset($message['text'])){
                                $wordsToCheck =$delete_containing_words;
                                $lowercaseText = strtolower($message['text']);
                                foreach ($wordsToCheck as $word) {
                                    // Convert the word to lowercase for case-insensitive matching
                                    $lowercaseWord = strtolower($word);

                                    // Check if the word exists in the text
                                    if (strpos($lowercaseText, $lowercaseWord) !== false) {
                                        $chatID = $message['chat']['id'] ?? '';
                                        $messageID = $message['message_id'] ?? '';
                                        $this->telegram->bot_token = $bot_token;
                                        $this->telegram->delete_message($chatID, $messageID);
                                    }
                                }
                            }
                        }
                    }
                }

                //Filter Forwarded Messages
                if (isset($raw_response_array['message']['forward_from'] )  && $group_admin != true){
                    $message = $raw_response_array['message'];
                    $filter_message_data = DB::table('telegram_group_message_filterings')->select('*')->where('telegram_group_id',$telegram_group_id)->first();
                    $delete_forword_image = $filter_message_data->delete_forword_image ?? '';
                    $delete_forward_links = $filter_message_data->delete_forward_links ?? '';
                    $delete_all_forward_message = $filter_message_data->delete_all_forward_message ?? '';
                    if($delete_all_forward_message){
                        $chatID = $message['chat']['id'] ?? '';
                        $messageID = $message['message_id'] ?? '';
                        $this->telegram->bot_token = $bot_token;
                        $this->telegram->delete_message($chatID, $messageID);
                    }
                    if($delete_forword_image){
                        //d$telegram_group_idelete forward  message containing videos stciker
                        if (isset($message['photo']) || isset($message['sticker']) || isset($message['video'])) {
                            $chatID = $message['chat']['id'] ?? '';
                            $messageID = $message['message_id'] ?? '';
                            $this->telegram->bot_token = $bot_token;
                            $this->telegram->delete_message($chatID, $messageID);
                        }
                    }
                    if($delete_forward_links){
                        //delete forward  message containing links
                        if(isset($message['text'])){
                            $text = $message['text'];
                            $pattern = '/https?:\/\/\S+/i';
                            $link = preg_match($pattern, $text);
                            if ($link){
                                $chatID = $message['chat']['id'] ?? '';
                                $messageID = $message['message_id'] ?? '';
                                $this->telegram->bot_token = $bot_token;
                                $this->telegram->delete_message($chatID, $messageID);
                            }
                        }
                    }
                }
                // Ban Chat member from group
                $ban_chat_member = isset($raw_response_array['chat_member']['chat']['type']) && ($raw_response_array['chat_member']['chat']['type'] == 'group' || $raw_response_array['chat_member']['chat']['type'] == 'supergroup') ? true : false;
                if($ban_chat_member){
                    if(isset($raw_response_array['new_chat_member']['status']) && $raw_response_array['new_chat_member']['status'] == 'left'){
                        $subscriber_id = $raw_response_array['message']['left_chat_member']['id'] ?? '';
                        $group_subscriber_id = $subscriber_id.'-'.$telegram_group_id;
                        if(!empty($subscriber_id))
                        DB::table('telegram_group_subscribers')->where(['group_subscriber_id'=> $group_subscriber_id])->update(['is_banned'=>'ban']);
                    }

                }
            }

            //remove bot or subscriber

            if(!empty($get_group)){

                $remove_bot_or_subscriber = isset($raw_response_array['message']['left_chat_member']) && isset($raw_response_array['message']['chat']['type']) && ($raw_response_array['message']['chat']['type'] == 'group' || $raw_response_array['message']['chat']['type'] == 'supergroup')  ? true : false;
                if($remove_bot_or_subscriber){


                    $user_left_group = $filter_message_data->user_left_group ?? '';
                    if($user_left_group == '1'){
                       $message = $raw_response_array['message'];
                       $chatID = $message['chat']['id'] ?? '';
                       $messageID = $message['message_id'] ?? '';
                       $this->telegram->bot_token = $bot_token;
                       $this->telegram->delete_message($chatID, $messageID);
                    }
                    $get_username = $raw_response_array['message']['left_chat_member']['username'] ?? '';
                    $remove_bot = $get_username == $telegram_bot_name ? true : false;

                    if($remove_bot){
                        DB::table('telegram_groups')->where(['id'=> $telegram_group_id])->delete();
                    }
                    //Left from himself
                    else if(
                        isset($raw_response_array['message']['left_chat_member']['id']) &&
                        isset($raw_response_array['message']['chat']['type']) &&
                        isset($raw_response_array['message']['from']['id']) &&
                        ($raw_response_array['message']['chat']['type'] == 'group' || $raw_response_array['message']['chat']['type'] == 'supergroup') &&
                        $raw_response_array['message']['left_chat_member']['id'] == $raw_response_array['message']['from']['id']
                    ) {
                        $subscriber_id = $raw_response_array['message']['left_chat_member']['id'] ?? '';
                        $group_subscriber_id = $subscriber_id.'-'.$telegram_group_id;
                        DB::table('telegram_group_subscribers')->where(['group_subscriber_id'=> $group_subscriber_id])->update(['is_left'=>'1']);

                    }
                    else if( isset($raw_response_array['message']['left_chat_member']['id']) &&
                        isset($raw_response_array['message']['chat']['type']) &&
                        ($raw_response_array['message']['chat']['type'] == 'group' || $raw_response_array['message']['chat']['type'] == 'supergroup')
                    ){
                      $subscriber_id = $raw_response_array['message']['left_chat_member']['id'] ?? '';
                      $group_subscriber_id = $subscriber_id.'-'.$telegram_group_id;
                      DB::table('telegram_group_subscribers')->where(['group_subscriber_id'=> $group_subscriber_id])->update(['removed'=>'1']);
                    }


                }
            }
        }
        //   TELEGRAM GROUP MANAGEMENT END

        $insert_livechat_data = [
            'telegram_bot_id' => $telegram_bot_id,
            'sender' => 'user',
            'message_content' => $raw_response
        ];
        if(isset($group_subscriber_id)){
            $insert_livechat_data['telegram_group_subscriber_group_subscriber_id']=$group_subscriber_id;
        }
        $this->insert_livechat_data($insert_livechat_data,$user_id);

    }
}
