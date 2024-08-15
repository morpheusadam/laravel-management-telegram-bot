<?php

namespace App\Http\Controllers;

use stdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Home;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\TelegramServiceInterface;

class Subscriber extends Home
{

    public function __construct(TelegramServiceInterface $telegram_service)
    {
        $this->set_global_userdata(true,['Admin','Agent','Member']);
        $this->telegram = $telegram_service;
    }

    public function list_group_subscriber_data(Request $request)
    {
        $search_value = $request->search_value;
        $telegram_group_id = session('bot_manager_get_group_details_telegram_group_id');
        $label_id = $request->label_id;
        $subscriber_status = $request->subscriber_status;
        $is_subscribed = $request->is_subscribed;
        $is_banned = $is_left = $removed = '0';
        if($subscriber_status == 'active'){
            $is_banned = '0';
            $is_left = '0';
        }
        else if($subscriber_status == 'ban'){
            $is_banned = 'ban';
            $is_left = '0';
        }
        else if($subscriber_status == 'unban'){
            $is_banned = 'unban';
            $is_left = '0';
        }
        else if($subscriber_status == 'left'){
            $is_banned = '0';
            $is_left = '1';
        }
        else if($subscriber_status == 'removed'){
            $removed = '1';
        }

        session(['telegram_subscriber_status'=>$subscriber_status]);
        $display_columns = array("#","CHECKBOX",'avatar', 'chat_id','first_name', 'last_name','username' ,'is_banned','is_left','updated_at', 'actions');
        $search_columns = array('telegram_group_subscribers.first_name', 'telegram_group_subscribers.last_name','group_subscriber_id');

        $table="telegram_group_subscribers";
        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 7;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : $table.'.updated_at';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $select= ["telegram_group_subscribers.*"];
        $query = DB::table($table)->select($select)->leftJoin('telegram_groups', 'telegram_group_subscribers.telegram_group_id','=','telegram_groups.id')
            ->leftJoin('telegram_bots','telegram_bots.id','=','telegram_groups.telegram_bot_id')
            -> where(['telegram_bots.user_id'=>$this->user_id,'is_banned'=>$is_banned,'removed'=>$removed,'is_left'=> $is_left,'telegram_group_subscribers.telegram_group_id'=>$telegram_group_id]);

        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }

        $info = $query->orderByRaw($order_by)->groupByRaw($table.'.id')->offset($start)->limit($limit)->get();

        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }

        $total_result_query = $query->groupByRaw($table.'.id')->get();
        $total_result = count($total_result_query);

        $i=0;
        $currentDateTime =date('Y-m-d H:i:s');
        $currentDateTime = strtotime($currentDateTime);
        foreach ($info as $key => $value)
        {

            if(config('app.is_demo')=='1' && $this->is_admin) {
                $value->first_name = "XXXXXXXX";
                $value->last_name = "XXXX";
            }
            $chat_id = $value->group_subscriber_id;
            $value->chat_id = $chat_id;
            $value->updated_at = convert_datetime_to_timezone($value->updated_at);
            $mute_class = '';
            $show_mute_time = '';
            if($value->mute_time != 'null'){
                $mute_datbase_time = strtotime($value->mute_time);
                if ( $mute_datbase_time > $currentDateTime ) {
                    $mute_class = "bg-success";
                     $show_mute_time = $value->mute_time;
                }
            }

            $ban_class = 'ban_member';
            $title = __('Ban Subscriber');
            if($value->is_banned == 'ban'){
                $ban_class = "bg-warning unban_member";
                $title = __('Unban Subscriber');
            }
            $str="";
            $str=$str."<a class='btn btn-circle btn-outline-success total_message' title='".__('Total Message')."' href='#' data-id='" . $value->telegram_group_id . " " . $value->chat_id . "'  >".'<i class="fas fa-comment-alt"></i>'."</a>";      
            $str=$str."&nbsp;<a class='btn btn-circle btn-outline-warning mute_member ".$mute_class."' title='".__('Mute Subscriber')."' href='#' data-muted-time='".$show_mute_time."'  data-id='" . $value->telegram_group_id . " " . $value->chat_id . "'  >".'<i class="fas fa-volume-mute"></i>'."</a>";
            $str=$str."&nbsp;<a href='#' title='".$title."' data-id='".$value->id."' class='btn btn-circle btn-outline-danger ".$ban_class." '>".'<i class="fas fa-solid fas fa-ban"></i></i>'."</a>";
            $value->actions = "<div class='min-width-40px'>".$str."</div>";;
            $default = asset('assets/images/avatar/avatar-'.rand(1,5).'.png');
            $value->avatar = '<img src="'.$default.'" class="rounded-circle" width="45" height="45">';
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function mute_group_chat_member(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return response()->json(['ok'=>false,'error_code'=>'403','description'=>__('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $currentDateTime = date('Y-m-d H:i:s');
        $telegram_group_id = $request->telegram_group_id;
        $telegram_group_subscriber_id = $request->telegram_group_subscriber_id;
        $mute_type = $request->mute_type;
        $mute_time = $request->mute_time;

        if($mute_type == ''){
          $mute_type = 'minutes';
        }

        $mute_actual_time = date('Y-m-d H:i:s', strtotime($currentDateTime . ' + ' . $mute_time .$mute_type ));
        $until_date = strtotime($mute_actual_time);
        $info = DB::table('telegram_groups')->select('group_id','bot_token')->leftJoin('telegram_bots', 'telegram_bots.id','=','telegram_groups.telegram_bot_id')->where('telegram_groups.id',$telegram_group_id)->first();
        $group_id = $info->group_id;
        $bot_token = $info->bot_token;
        $this->telegram->bot_token = $bot_token;
        $responseData = $this->telegram->mute_chat_member($group_id,$telegram_group_subscriber_id,$until_date);

        if ($responseData['ok']) {
              DB::table('telegram_group_subscribers')->where(['telegram_group_id'=>$telegram_group_id,'group_subscriber_id'=>$telegram_group_subscriber_id])->update(['mute_time'=>$mute_actual_time]);
        }
        return $responseData;
    }
    public function unmute_group_chat_member(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return response()->json(['ok'=>false,'error_code'=>'403','description'=>__('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $currentDateTime = date('Y-m-d H:i:s');
        $telegram_group_id = $request->telegram_group_id;
        $telegram_group_subscriber_id = $request->telegram_group_subscriber_id;
        $info = DB::table('telegram_groups')->select('group_id','bot_token')->leftJoin('telegram_bots', 'telegram_bots.id','=','telegram_groups.telegram_bot_id')->where('telegram_groups.id',$telegram_group_id)->first();
        $group_id = $info->group_id;
        $bot_token = $info->bot_token;


        $apiUrl = 'https://api.telegram.org/bot' . $bot_token . '/';

        $muteEndpoint = 'restrictChatMember';

       $postData = array(
                'chat_id' => $group_id,
                'user_id' => $telegram_group_subscriber_id,
                'can_send_messages' => true,
                'can_send_media_messages' => true,
                'can_send_other_messages' => true,
                'can_add_web_page_previews' => true,
            );

        $muteUrl = $apiUrl . $muteEndpoint;

        $ch = curl_init($muteUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        if ($responseData['ok']) {
              DB::table('telegram_group_subscribers')->where(['telegram_group_id'=>$telegram_group_id,'group_subscriber_id'=>$telegram_group_subscriber_id])->update(['mute_time'=>$currentDateTime]);
        }
        return $responseData;
    }

    public function banned_group_chat_member(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return response()->json(['ok'=>false,'error_code'=>'403','description'=>__('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $id = $request->id;
        $query = DB::table('telegram_group_subscribers')->select('group_id','group_subscriber_id','bot_token')->leftJoin('telegram_groups', 'telegram_group_subscribers.telegram_group_id','=','telegram_groups.id')
            ->leftJoin('telegram_bots','telegram_bots.id','=','telegram_groups.telegram_bot_id')
            -> where(['telegram_bots.user_id'=>$this->user_id,'telegram_group_subscribers.id'=>$id])->first();
        $group_id = $query->group_id;
        $group_subscriber_id = $query->group_subscriber_id;
        $bot_token = $query->bot_token;

        $this->telegram->bot_token = $bot_token;
        $method = 'banChatMember';
        $response = $this->telegram->Ban_UnbanChatMember( $method,$group_id,$group_subscriber_id );
        if ($response === false) {
            echo 'Error: Failed to send API request.';
        } else {
            $responseData = json_decode($response, true);
            if ($responseData['ok']) {
                  DB::table('telegram_group_subscribers')->where(['id'=>$id])->update(['is_banned'=>'ban']);
            }
            return $responseData;
        }

    }
    public function unban_group_chat_member(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return response()->json(['ok'=>false,'error_code'=>'403','description'=>__('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $id = $request->id;
        $query = DB::table('telegram_group_subscribers')->select('group_id','group_subscriber_id','bot_token')->leftJoin('telegram_groups', 'telegram_group_subscribers.telegram_group_id','=','telegram_groups.id')
            ->leftJoin('telegram_bots','telegram_bots.id','=','telegram_groups.telegram_bot_id')
            -> where(['telegram_bots.user_id'=>$this->user_id,'telegram_group_subscribers.id'=>$id])->first();
        $group_id = $query->group_id;
        $group_subscriber_id = $query->group_subscriber_id;
        $bot_token = $query->bot_token;
        $this->telegram->bot_token = $bot_token;
        $method = 'unbanChatMember';
        $response = $this->telegram->Ban_UnbanChatMember( $method,$group_id,$group_subscriber_id );

        // Check the API response
        if ($response === false) {
            echo 'Error: Failed to send API request.';
        } else {
            $responseData = json_decode($response, true);
            if ($responseData['ok']) {
                  DB::table('telegram_group_subscribers')->where(['id'=>$id])->update(['is_banned'=>'unban','is_left'=>'0']);
            }
            return $responseData;
        }

    }

    public function group_subscriber_message(Request $request)
    {
        $search_value = $request->search['value'];
        $telegram_group_subscriber_group_subscriber_id = $request->telegram_group_subscriber_id ?? "";
        $display_columns = array("#",'id','message_content','conversation_time');
        $search_columns = array('message_content');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 1;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="telegram_bot_livechat_messages";
        $select= ["telegram_bot_livechat_messages.*"];
        $query = DB::table($table)->select($select)->where('telegram_group_subscriber_group_subscriber_id',$telegram_group_subscriber_group_subscriber_id);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($select)->where('telegram_group_subscriber_group_subscriber_id',$telegram_group_subscriber_group_subscriber_id);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $total_result = $query->count();
        if(!empty($info)){
            $message_list = [];
            foreach ($info as $key => $value)
            {
                $message_content = json_decode($value->message_content,true);
                    if(isset($message_content['message']['text'])){
                        $date = $message_content['message']['date'];
                        $message_date = date('j F g:i A',$date);
                        $message_list[] =[
                                'id'=>$value->id,
                                'message_content' => $message_content['message']['text'],
                                'conversation_time' => $message_date
                        ];
                    }
            }
        }
        $outputArray = [];
        foreach ($message_list as $item) {
            $outputItem = new stdClass();
            $outputItem->id = $item['id'];
            $outputItem->message_content = $item['message_content'];
            $outputItem->conversation_time = $item['conversation_time'];

            $outputArray[] = $outputItem;
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($outputArray, $display_columns ,$start);
        echo json_encode($data);

    }

    public function delete_group_subscribers(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return response()->json(['status'=>'0','message'=>__('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $ids = $request->ids;
        if(empty($ids))
        {
            return response()->json(['status'=>'0']);
        }
        try {
            DB::table('telegram_group_subscribers')->whereIntegerInRaw('id',$ids)->delete();
            DB::commit();
            return response()->json(['status'=>'1']);
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error = $e->getMessage();
            return response()->json(['status'=>'0','message'=>$error]);
        }

    }

}
