<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use App\Models\Telegram_bot;
use App\Services\TelegramServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use DateTime;
use DateTimeZone;

class Cron extends Home
{
    public function __construct(TelegramServiceInterface $telegram_service)
    {
        $this->telegram = $telegram_service;
    }

    protected function telegram_group_broadcast_send()
    {
        $campaign_lead = DB::table('telegram_group_message_sends')
            ->select('telegram_group_message_sends.*','bot_token','group_id')
            ->leftJoin('telegram_groups','telegram_groups.id','=','telegram_group_message_sends.telegram_group_id')
            ->leftJoin('telegram_bots','telegram_bots.id','=','telegram_groups.telegram_bot_id')
            ->where('schedule_time','<=',date('Y-m-d H:i:s'))
            ->where('posting_status','=','0')
            ->orderByRaw('schedule_time ASC')->limit(50)->get();
        if($campaign_lead->isEmpty()) dd('No pending broadcast found.');

        $campaign_ids =[];
        $i=0;
        foreach ($campaign_lead as $key=>$value){
            $campaign_ids[$i] = $value->id;
            $i++;
        }
        DB::table('telegram_group_message_sends')->whereIntegerInRaw('id',$campaign_ids)->update(array("posting_status"=>"1"));

        // send message
        foreach($campaign_lead as $key => $value)
        {
            $this->telegram->bot_token = $value->bot_token;
            $response = $this->telegram->send('sendMessage',$value->message_content);
            $response = json_decode($response,true);
            if(isset($response['ok']) && $response['ok'] == true){
                $decoded = json_decode($value->message_content,true);
                $message_id = $response['result']['message_id'] ?? null;
                $disable_notification = $decoded['disable_notification'] ?? false;
                if($value->pin_post=='1'){
                    $send_pin_message_data =[
                        'chat_id'=>$value->group_id,
                        'message_id' => $response['result']['message_id']??'',
                        'parse_mode'=>'HTML',
                        'disable_notification' => $disable_notification
                    ];
                    $send_pin_message_data = json_encode($send_pin_message_data);
                    @$this->telegram->send('pinChatMessage',$send_pin_message_data);
                }
                DB::table('telegram_group_message_sends')->where('id',$value->id)->update(array("posting_status"=>"2","message_id"=>$message_id));
            }
        }
        DB::table('telegram_group_message_sends')->whereIntegerInRaw('id',$campaign_ids)->update(array("posting_status"=>"2"));
    }

    protected function telegram_group_broadcast_delete()
    {
        $campaign_lead = DB::table('telegram_group_message_sends')
            ->select('telegram_group_message_sends.*','bot_token','group_id')
            ->leftJoin('telegram_groups','telegram_groups.id','=','telegram_group_message_sends.telegram_group_id')
            ->leftJoin('telegram_bots','telegram_bots.id','=','telegram_groups.telegram_bot_id')
            ->whereNotNull('delete_message_time')
            ->where('delete_message_time','<=',date('Y-m-d H:i:s'))
            ->where('posting_status','=','2')
            ->where('delete_status','=','0')
            ->orderByRaw('delete_message_time ASC')->limit(50)->get();
        if($campaign_lead->isEmpty()) dd('No pending campaign found to delete.');
        $campaign_ids =[];
        $i=0;
        foreach ($campaign_lead as $key=>$value){
            $campaign_ids[$i] = $value->id;
            $i++;
        }
        DB::table('telegram_group_message_sends')->whereIntegerInRaw('id',$campaign_ids)->update(array("delete_status"=>"1"));

        // send message
        foreach($campaign_lead as $key => $value)
        {
            $decoded = json_decode($value->message_content,true);
            $chatID = $decoded['chat_id'] ?? '';
            $messageID = $value->message_id ?? '';
            $this->telegram->bot_token = $value->bot_token;
            @$this->telegram->delete_message($chatID, $messageID);
        }
        DB::table('telegram_group_message_sends')->whereIntegerInRaw('id',$campaign_ids)->update(array("delete_status"=>"2"));
    }

    public function telegram_disable_bot_expired_users()
    {
        $current_date = date("Y-m-d H:i:s",strtotime("-2 day"));
        $free_package_info = DB::table('packages')->where(['price'=>'0','validity'=>'0','is_default'=>'1'])->get();
        $free_package_ids = [];
        foreach ($free_package_info as $value){
            $free_package_ids[] = $value->id;
        }

        $user_info = [];
        if(!empty($free_package_ids))
        {
            $user_info = DB::table('users')
                ->whereIntegerNotInRaw('users.package_id',$free_package_ids)
                ->where('users.user_type','!=','Admin')
                ->where('users.expired_date','<',$current_date)
                ->get()->toArray();
        }

        $user_ids = [];
        foreach ($user_info as $value){
            $user_ids[] = $value->id;
        }

        $child_user_info = [];
        if(!empty($user_ids)){
            $child_user_info = DB::table('users')->whereIntegerInRaw('users.parent_user_id',$user_ids)->get()->toArray();
        }

        foreach ($child_user_info as $value) {
            $user_ids[] = $value->id;
        }

        $bot_data = [];
        $whatsapp_bot_data = [];

        if(!empty($user_ids)) {
            $bot_data = DB::table('telegram_bots')
                ->whereIntegerInRaw('user_id',$user_ids)->where(['status'=>'1'])
                ->select('bot_token','id')->get();
        }

        foreach($bot_data as $value)
        {
            $this->telegram->bot_token = $value->bot_token ?? '';
            $response = $this->telegram->delete_webhook();
            if($response['ok'])
            {
                DB::table('telegram_bots')->where('id','=',$value->id)->update(['status'=>'0']);
            }
        }
    }

    public function telegram_clean_junk_data()
    {
        $delete_junk_data_after_how_many_days = 30;
        $delete_junk_data_after_how_many_days_long = 90;
        $delete_junk_data_after_how_many_days_too_long = 180;

        $cur_time=date('Y-m-d H:i:s');
        $last_time=date("Y-m-d H:i:s",strtotime($cur_time." -".$delete_junk_data_after_how_many_days." day"));
        $last_time_long=date("Y-m-d H:i:s",strtotime($cur_time." -".$delete_junk_data_after_how_many_days_long." day"));
        $last_time_too_long=date("Y-m-d H:i:s",strtotime($cur_time." -".$delete_junk_data_after_how_many_days_too_long." day"));
       
        DB::table('sms_email_send_logs')->where('updated_at','<=',$last_time)->delete();
        DB::table('payment_api_logs')->where('call_time','<=',$last_time_too_long)->delete();
        DB::table('telegram_bot_livechat_messages')->where('conversation_time','<=',$last_time_too_long)->delete();
    }

    public function clean_system_logs(){
        @unlink(storage_path('logs/laravel.log'));
    }

    public function get_paypal_subscriber_transaction(){
        $where = [
            ['paypal_subscriber_id', '!=',''],
            ['subscription_enabled','=','1'],
            ['paypal_next_check_time','<=',Carbon::now()->toDateTimeString()],
        ];
        $data = DB::table('users')->select('users.*','settings_payments.paypal','settings_payments.currency')->leftJoin("settings_payments","users.parent_user_id","=","settings_payments.user_id")->where($where)->whereNull('settings_payments.ecommerce_store_id')->whereNull('settings_payments.whatsapp_bot_id')->orderByRaw('paypal_next_check_time asc')->limit(10)->get();
        $paypal_processing_data = [];
        foreach ($data as $user) {
            array_push($paypal_processing_data,$user->id);
        }
        DB::table('users')->whereIn('id',$paypal_processing_data)->update(['paypal_processing'=>'1']);
        foreach ($data as $user) {
            $id = $user->id;
            $paypal_credintial = $user->paypal;
            $paypal_credintial = json_decode($paypal_credintial,true);
            $paypal_client_id = $paypal_credintial['paypal_client_id'];
            $paypal_client_secret = $paypal_credintial['paypal_client_secret'];
            $currency = $user->currency;

            $paypal_app_id = $paypal_credintial['paypal_app_id'];
            $paypal_mode = $paypal_credintial['paypal_mode'];
            $paypal_subscriber_id = $user->paypal_subscriber_id;
            $expired_date = strtotime($user->expired_date);
            $provider = new PayPalClient;
            $subscription_data = json_decode($user->subscription_data,true);
            $package_id = $subscription_data['package_id'];
            if($paypal_mode == 'sandbox'){
               $config = [
                   'mode'    => 'sandbox',
                   'sandbox' => [
                       'client_id'         => $paypal_client_id,
                       'client_secret'     => $paypal_client_secret,
                       'app_id'            => $paypal_app_id,
                   ],
                   'payment_action' => 'Sale',
                   'currency'       => $currency,
                   'notify_url'     => '',
                   'locale'         => 'en_US',
                   'validate_ssl'   => true,
               ];
               $provider->setApiCredentials($config);
            }
            else{
                $config = [
                    'mode'    => 'live',
                    'live' => [
                        'client_id'         => $paypal_client_id,
                        'client_secret'     => $paypal_client_secret,
                        'app_id'            => $paypal_app_id,
                    ],
                    'payment_action' => 'Sale',
                    'currency'       => $currency,
                    'notify_url'     => '',
                    'locale'         => 'en_US',
                    'validate_ssl'   => true,
                ];
                $provider->setApiCredentials($config);
            }
            $provider->getAccessToken();
            $timestamp = time()-(365*24*60*60);
            $one_year_ago_date = gmdate("Y-m-d\TH:i:s\Z",$timestamp);

            $response = $provider->listSubscriptionTransactions($paypal_subscriber_id,$one_year_ago_date,gmdate("Y-m-d\TH:i:s\Z",time()));
            $transaction_id = $response['transactions'][0]['id'] ?? '';
            $buyer_user_id = $user->id ?? null;
            $payment_type = "PayPal";

            $check_duplicate = DB::table("transaction_logs")->select('transaction_id')->where(['buyer_user_id'=>$buyer_user_id,'transaction_id'=>$transaction_id,'payment_method'=>$payment_type])->first();
            $previous_transaction_id = $check_duplicate->transaction_id ?? '';
            if($previous_transaction_id == $transaction_id && get_domain_only(env('APP_URL'))!='telegram-group.test') {
                echo "<h4>Transaction ID : ".$transaction_id." duplicated.</h4>";
                DB::table('users')->where('id',$id)->update(['paypal_processing'=>'0']);
                continue;
            }
            $subscription_time = strtotime($response['transactions'][0]['time']);
            $get_payment_validity_data = $this->get_payment_validity_data($user->id,$package_id);
            $cycle_start_date = $get_payment_validity_data['cycle_start_date'] ?? date("Y-m-d");
            $cycle_expired_date = $get_payment_validity_data['cycle_expired_date'] ?? date("Y-m-d");
            $insert_data=array(
                "verify_status"     => $response['transactions'][0]['status'] ?? '',
                "user_id"           => $user->parent_user_id ?? '',
                "buyer_user_id"     => $buyer_user_id,
                "first_name"        => $response['transactions'][0]['payer_name']['given_name'] ?? '',
                "last_name"         => $response['transactions'][0]['payer_name']['surname'] ?? '',
                "buyer_email"       => $response['transactions'][0]['payer_email'] ?? '',
                "paid_currency"     => $response['transactions'][0]['amount_with_breakdown']['gross_amount']['currency_code'] ?? '',
                "paid_at"           => $response['transactions'][0]['time'] ?? '',
                "payment_method"    => $payment_type ?? '',
                "transaction_id"    => $transaction_id,
                "paid_amount"       => $response['transactions'][0]['amount_with_breakdown']['gross_amount']['value'] ?? '',
                "cycle_start_date"  => $cycle_start_date,
                "cycle_expired_date"=> $cycle_expired_date,
                "paypal_next_check_time"=> $cycle_expired_date,
                "package_id"        => $package_id,
                "response_source"   => json_encode($response),
                "package_name"      => $get_payment_validity_data['package_name'] ?? '',
                "user_email"        => $get_payment_validity_data['email'] ?? '', // not for insert, for sending email
                "user_name"         => $get_payment_validity_data['name'] ?? '' // not for insert, for sending email
            );
            $is_agency = '0';
            $is_whitelabel = $is_agency;
            $this->complete_payment($insert_data,$is_agency,$is_whitelabel,$payment_type);
        }
        DB::table('users')->whereIn('id',$paypal_processing_data)->update(['paypal_processing'=>'0']);
    }

}
