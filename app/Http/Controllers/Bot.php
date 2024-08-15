<?php

namespace App\Http\Controllers;

use App\Models\Telegram_bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\TelegramServiceInterface;

class Bot extends Home
{
    public $telegram;

    public function __construct(TelegramServiceInterface $telegram_service)
    {
        $this->set_global_userdata(true,['Admin','Agent','Member']);
        $this->telegram = $telegram_service;
    }

    public function connect_bot()
    {
        if($this->is_manager && empty(Auth::user()->allowed_telegram_bot_ids)) $telegram_bots = [];
        else{
            $allowed_telegram_bot_ids = $this->is_manager && !empty(Auth::user()->allowed_telegram_bot_ids) ? json_decode(Auth::user()->allowed_telegram_bot_ids,true) : [];
            $where = array('user_id' => $this->user_id);
            $query = DB::table('telegram_bots')->where($where);
            if(!empty($allowed_telegram_bot_ids)) $query->whereIntegerInRaw('id',$allowed_telegram_bot_ids);
            $telegram_bots = $query->orderByRaw('username ASC')->get();
        }
        $data = array('body'=>'telegram/bot/connect-bot','telegram_bots'=>$telegram_bots);
        return $this->viewcontroller($data);
    }

    public function group_list()
    {
        if($this->is_manager && empty(Auth::user()->allowed_telegram_bot_ids)) $telegram_bots = [];
        else{


          $telegram_groups = DB::table('telegram_groups')
              ->select('telegram_groups.id','group_id', 'group_name', 'telegram_bot_id','is_bot_admin', 'telegram_bots.username')
              ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
              ->where([
                  ['telegram_bots.user_id', '=', $this->user_id],
              ])
              ->orderBy('group_name', 'asc')
              ->get();
          $grouped_groups = $telegram_groups->groupBy('group_id');
          // Transform the grouped groups
          $result = $grouped_groups->map(function ($group) {
              // Extract the common group information from the first item
              $firstItem = $group->first();
              $group_id = $firstItem->group_id;
              $group_name = $firstItem->group_name;
              $is_bot_admin = $firstItem->is_bot_admin;
              $id = $firstItem->id;

              // Extract the usernames of the Telegram bots and create an array of them
              $bot_usernames = $group->pluck('username','telegram_bot_id')->toArray();

              // Create the final group item with the desired structure
              return [
                  'id'=>$id,
                  'group_id' => $group_id,
                  'group_name' => $group_name,
                  'is_bot_admin' => $is_bot_admin,
                  'bots' => $bot_usernames,
              ];
          });
          // Convert the result to a plain array
          $result = $result->values()->toArray();
        }
        $data = array('body'=>'telegram/group/group-list','telegram_groups'=>$result);
        return $this->viewcontroller($data);
    }

    public function group_manager(Request $request)
    {

        if(!has_module_access($this->module_id_telegram_group,$this->module_ids,$this->is_admin,$this->is_manager)) abort(403);

        $search_time = $request->time_selection ?? '1 month';
        session(['group_activity_time'=>$search_time]);
        $data = array('body' => 'telegram/group/group-manager','load_datatable'=>true);
        $group_info = DB::table('telegram_groups')
                           ->select('telegram_groups.id','supergroup_subscriber_id','group_id', 'group_name', 'telegram_bot_id','is_bot_admin')
                           ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                           ->where([
                               ['telegram_bots.user_id', '=', $this->user_id],
                               ['telegram_groups.is_bot_admin','=','1']
                           ])
                           ->orderBy('group_name', 'asc')
                           ->get();
        
        $bot_list_not_admin = DB::table('telegram_groups')
                           ->select('telegram_groups.id','group_id', 'group_name', 'telegram_bot_id','is_bot_admin','telegram_bots.username')
                           ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                           ->where([
                               ['telegram_bots.user_id', '=', $this->user_id],
                               ['telegram_groups.is_bot_admin','=','0']
                           ])
                           ->get()
                           ->groupBy('group_name')
                              ->map(function ($items) {
                                  $bot_list = $items->map(function ($item) {
                                      return [
                                          'username' => $item->username,
                                      ];
                                  })->all();

                                  return [
                                      'group_name' => $items->first()->group_name,
                                      'bot_list' => $bot_list
                                  ];
                              })
                              ->values()
                              ->all();
        $no_group_found =0; 

        if($group_info->isEmpty() && empty($bot_list_not_admin) ) $no_group_found = 1;

        $group_list = [];
        if (!empty($group_info)) {
            $i = 1;
            foreach ($group_info as $value) {
                if($i==1 && !session()->has('bot_manager_get_group_details_telegram_group_id')) {
                    session(['bot_manager_get_group_details_telegram_group_id'=>$value->id]);
                    session(['bot_manager_get_group_details_tab_menu_id'=>'v-pills-group-subscriber-tab']);
                }
                if ($value->id == session('bot_manager_get_group_details_telegram_group_id')) {
                    $group_list[0] = $value;
                }
                else $group_list[$i] = $value;
                $i++;
            }
        }

        $telegram_group_id = session('bot_manager_get_group_details_telegram_group_id');
        $total_member = DB::table('telegram_group_subscribers')
                        ->where(['telegram_group_id'=>$telegram_group_id,'is_banned'=>'0','is_left'=>'0'])->count();

        $from_date = date('Y-m-d H:i:s', strtotime('- 30 days'));
        $to_date =date('Y-m-d H:i:s');
        $where_cart_data = [
			['telegram_group_subscribers.updated_at', '>', $from_date],
			['telegram_group_subscribers.updated_at', '<=', $to_date],
		];
        $total_member_data = DB::table('telegram_group_subscribers')
                        ->select('*')
                        ->where($where_cart_data)
                        ->where(['telegram_group_id'=>$telegram_group_id,'is_banned'=>'0','is_left'=>'0'])
                        ->get();
        $current_time = date('Y-m-d H:i:s');
        //total_mute_member
        $total_mute_member = DB::table('telegram_group_subscribers')
                        ->where(['telegram_group_id'=>$telegram_group_id,'is_banned'=>'0','is_left'=>'0',])
                        ->where('mute_time','>',$current_time)
                        ->count();

        //join member

        $result_time = date('Y-m-d H:i:s', strtotime('- ' . $search_time));
        $join_member = DB::table('telegram_group_subscribers')
                        ->where(['telegram_group_id'=>$telegram_group_id,'is_banned'=>'0','is_left'=>'0',])
                        ->where('updated_at','>=',$result_time)
                        ->count();

        //left member
        $left_member = DB::table('telegram_group_subscribers')
                        ->where(['telegram_group_id'=>$telegram_group_id,'is_left'=>'1',])
                        ->count();

        //banned members
        $banned_member = DB::table('telegram_group_subscribers')
                        ->where(['telegram_group_id'=>$telegram_group_id,'is_banned'=>'ban',])
                        ->count();


                    
        // total message


        $total_message = 0;

        ksort($group_list);


        $telegram_actual_id = DB::table('telegram_groups')->select('group_id')->where('id',$telegram_group_id)->first();
        $telegram_actual_id = $telegram_actual_id->group_id ?? '';
       
        $telegram_group_bot_list = DB::table('telegram_groups')->select("telegram_groups.*",'telegram_bots.username')
                                   ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')->where(['group_id'=>$telegram_actual_id,'is_bot_admin'=>'1'])->get();

        $filter_message_data = DB::table('telegram_group_message_filterings')->where('user_id',Auth::user()->id)->where('telegram_group_id',$telegram_group_id)->first();
        $send_message_data = DB::table('telegram_group_message_sends')->where('user_id',Auth::user()->id)->where('telegram_group_id',$telegram_group_id)->first();

        $data['bot_list_not_admin'] = $bot_list_not_admin;
        $data['no_group_found'] = $no_group_found ;
        $data['group_info'] = $group_list;
        $data['total_members'] = $total_member;
        $data['joined_members'] = $join_member;
        $data['left_members'] = $left_member;
        $data['banned_member'] = $banned_member;
        $data['muted_members'] = $total_mute_member;
        $data['total_message'] = $total_message;
        $data['total_member_data'] = $total_member_data;
        $data['total_member_data_count'] = count($total_member_data);
        $data['telegram_group_bot_list'] = $telegram_group_bot_list;
        $data['filter_message_data'] = $filter_message_data;
        $data['send_message_data'] = $send_message_data;
        return $this->viewcontroller($data);
    }

    public function group_filtering_message_data(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        $telegram_group_id = session('bot_manager_get_group_details_telegram_group_id');

        $telegram_actual_id = DB::table('telegram_groups')->select('group_id')->where('id',$telegram_group_id)->first();
        $currentDateTime = date('Y-m-d H:i:s');
        $Delete_messages_containing_bot_commands = $request->Delete_messages_containing_bot_commands;
        $Delete_image_message = $request->Delete_image_message;
        $Delete_voice_messages = $request->Delete_voice_messages;
        $Delete_documents = $request->Delete_documents;
        $Delete_stickers = $request->Delete_stickers;
        $Delete_dice = $request->Delete_dice;
        $Delete_messages_contain_links = $request->Delete_messages_contain_links;
        $remove_admin_message = $request->remove_admin_message;
        $Deleted_forwarded_messages_image = $request->Deleted_forwarded_messages_image;
        $Deleted_forwarded_messages_contain_links = $request->Deleted_forwarded_messages_contain_links;
        $Deleted_all_forwarded_messages = $request->Deleted_all_forwarded_messages;
        $Delete_user_joined_the_group_message = $request->Delete_user_joined_the_group_message;
        $Delete_user_left_the_group_message = $request->Delete_user_left_the_group_message;

        $same_message_delete_count = $request->same_message_delete_count ;
        $same_message_restrict_count = $request->same_message_restrict_count ;
        $allowed_message_per_time_count = $request->allowed_message_per_time_count ;
        $minute_allowed_message_per_time = $request->minute_allowed_message_per_time ;


        $keyword_list = $request->keyword_list ?? [];
        $censor_words= $request->censor_words ?? 'off';
        if($censor_words == 'off'){
            $keyword_list = [];
        }
        $keyword_list_json = json_encode($keyword_list);

        $mute_type = $request->restrict_time_type;
        $mute_time = $request->restrict_member_time;
        if($mute_type == ''){
          $mute_type = 'minutes';
        }
        $mute_actual_time = $mute_time.'-'.$mute_type;
        if($mute_actual_time == '-minutes' || $mute_actual_time == $mute_time.'-'){
          $mute_actual_time = '';
        }


        $restrict_type = $request->new_members_restrict_time_type;
        $restrict_time = $request->new_member_restrict_time;

        if($restrict_type == ''){
          $restrict_type = 'minutes';
        }

        $restrict_actual_time = $restrict_time.'-'.$restrict_type;
        if($restrict_actual_time == '-minutes' || $restrict_actual_time == $restrict_time.'-'){
            $restrict_actual_time = '';
        }


        $same_message_restrict_type = $request->same_message_restrict_time_type;
        $same_message_restrict_time_unit = $request->same_message_restrict_time_unit;

        if($same_message_restrict_type == ''){
          $same_message_restrict_type = 'minutes';
        }

        $same_message_restrict_actual_time = $same_message_restrict_time_unit.'-'.$same_message_restrict_type;
        if($same_message_restrict_time_unit == '-minutes' || $same_message_restrict_time_unit == $same_message_restrict_time_unit.'-'){
            $same_message_restrict_time_unit = '';
        }

        $insert_data = [
            'delete_command' => $Delete_messages_containing_bot_commands ?? '0' ,
            'delete_image' => $Delete_image_message ?? '0' ,
            'delete_voice' => $Delete_voice_messages ?? '0' ,
            'delete_document' => $Delete_documents ?? '0' ,
            'delete_sticker' => $Delete_stickers ?? '0' ,
            'delete_diceroll' => $Delete_dice ?? '0' ,
            'delete_links' => $Delete_messages_contain_links ?? '0' ,
            'user_joined_group' => $Delete_user_joined_the_group_message ?? '0' ,
            'user_left_group' => $Delete_user_left_the_group_message ?? '0' ,
            'delete_forword_image' => $Deleted_forwarded_messages_image ?? '0' ,
            'remove_admin_message' => $remove_admin_message ?? '0' ,
            'delete_forward_links' => $Deleted_forwarded_messages_contain_links ?? '0' ,
            'delete_all_forward_message' => $Deleted_all_forwarded_messages ?? '0' ,
            'restrict_member_time' => $mute_actual_time,
            'new_join_restrict_time' => $restrict_actual_time,
            'delete_containing_words' => $keyword_list_json ?? '',
            'same_message_delete_count' => $same_message_delete_count ?? null,
            'same_message_restrict_count' => $same_message_restrict_count ?? null,
            'same_message_restrict_time' => $same_message_restrict_actual_time ?? null,
            'allowed_message_per_time_count' => $allowed_message_per_time_count ?? null,
            'minute_allowed_message_per_time' => $minute_allowed_message_per_time ?? null,
            'user_id' => Auth::user()->id,
            'telegram_group_id' =>$telegram_group_id,
        ];
        if(DB::table('telegram_group_message_filterings')->updateOrInsert(['user_id'=>Auth::user()->id,'telegram_group_id'=>$telegram_group_id],$insert_data))
            $request->session()->flash('save_filtering_message', '1');

        return redirect()->route('telegram-group-manager');

    }

    public function group_send_message_data(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        if(!has_module_access($this->module_id_telegram_group,$this->module_ids,$this->is_admin,$this->is_manager)) abort(403);

        $campaign_id = $request->campaign_id ?? '';

        $telegram_group_id = session('bot_manager_get_group_details_telegram_group_id');
        $query = DB::table('telegram_groups')->select('group_id','supergroup_subscriber_id')->where('id',$telegram_group_id)->first();
        $group_data  = explode('-',$query->supergroup_subscriber_id);
        $bot_id = end($group_data);
        $telegram_full_group_id = $query->group_id;
        $currentDateTime = date('Y-m-d H:i:s');

        $campaign_name = $request->campaign_name;
        $text_message = $request->text_message;
        $pin_announcement = $request->pin_this_announcement ?? '0';
        $preview_url = ($request->preview_the_URL_in_the_text_message == 1) ? true: false ;
        $message_protection = ($request->protected_messages_no_copying_or_forwarding == 1) ? true: false ;
        $message_sound_alerts =  ($request->sound_alerts_for_messages== 1) ? true: false ;
        $timezone = $request->timezone;
        $schedule_time = $request->schedule_time ?? '';
        $convert_schedule_time = convert_datetime_to_timezone($schedule_time,'UTC',false,'Y-m-d H:i:s',$timezone);
        $delayed_automatic_deletion = $request->delayed_automatic_deletion;
        $sending_option = $request->sending_option;

        if($delayed_automatic_deletion == '0'){
            $delayed_actual_deletion_time = null;
        }
        else{
            $delayed_timeParts = explode('-', $delayed_automatic_deletion);
            $delayed_time_quantity = intval($delayed_timeParts[0]);
            $delayed_time_type = $delayed_timeParts[1];

            $delayed_actual_deletion_time = date('Y-m-d H:i:s', strtotime($convert_schedule_time . ' + ' . $delayed_time_quantity . ' ' . $delayed_time_type));
        }

        $send_message_data =[
            'chat_id'=>$telegram_full_group_id,
            'text' => strip_tags($text_message, '<b><i><u><s><a><code><pre><strong><em>'),
            'parse_mode'=>'HTML',
            'protect_content'=>$message_protection,
            'disable_web_page_preview' => $preview_url,
            'disable_notification' => $message_sound_alerts
        ];
        $send_message_data = json_encode($send_message_data);
        $insert_data = [
            'campaign_name' => $campaign_name ?? '' ,
            'user_id'=>$this->user_id,
            'message_content' => $send_message_data ,
            'telegram_group_id'=>$telegram_group_id ?? '',
            'schedule_time'=>$convert_schedule_time ,
            'timezone' => $timezone,
            'pin_post'=>$pin_announcement,
            'delete_message_time'=>$delayed_actual_deletion_time
        ];
        if($sending_option == 'later'){
            DB::table('telegram_group_message_sends')->updateOrInsert(['user_id'=>$this->user_id,'id'=>$campaign_id],$insert_data);
            $request->session()->flash('save_campaign_message', '1');
        }
        else{
            $bot_info = DB::table('telegram_bots')->select('bot_token')->where(['user_id'=>$this->user_id,'id'=>$bot_id])->first();
            $bot_token = $bot_info->bot_token;
            $this->telegram->bot_token = $bot_token;
            $insert_data['posting_status'] = '2';
            $response = $this->telegram->send('sendMessage',$send_message_data);
            $response = json_decode($response,true);
            if(isset($response['ok']) && $response['ok'] == true){
                if($pin_announcement ==1){
                    $send_pin_message_data =[
                        'chat_id'=>$telegram_full_group_id,
                        'message_id' => $response['result']['message_id'],
                        'parse_mode'=>'HTML',
                        'disable_notification' => $message_sound_alerts
                    ];
                    $send_pin_message_data = json_encode($send_pin_message_data);
                    @$this->telegram->send('pinChatMessage',$send_pin_message_data);
                }
                DB::table('telegram_group_message_sends')->updateOrInsert(['user_id'=>$this->user_id,'id'=>$campaign_id],$insert_data);
                $request->session()->flash('save_campaign_message', '1');
            }
            else{
                $message_error_code= $response['error_code'] ?? '';
                $error_msg = $response['description'] ?? '';
                $request->session()->flash('save_campaign_message', '0');
                $request->session()->flash('save_campaign_message_content', $message_error_code.':'.$error_msg);
            }

        }
        return redirect()->route('telegram-group-manager');

    }

    public function campaign_list_data(Request $request){
        $search_value = $request->search_value_send_message;
        $posting_status = $request->search_status;
        $display_columns = array("#",'campaign_name','posting_status','actions', 'schedule_time');
        $search_columns = array('campaign_name');

        $telegram_group_id = session('bot_manager_get_group_details_telegram_group_id');
        session(['bot_manager_get_bot_details_tab_menu_id'=>'v-pills-send-message-tab']);

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 4;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'created_at';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="telegram_group_message_sends";
        $select= ["telegram_group_message_sends.*"];
        $query = DB::table($table)->select($select)->where(['user_id'=>$this->user_id,'telegram_group_id'=>$telegram_group_id]);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if ($posting_status != '') $query->where('posting_status','=',$posting_status);
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($select)->where(['user_id'=>$this->user_id,'telegram_group_id'=>$telegram_group_id]);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if ($posting_status != '') $query->where('posting_status','=',$posting_status);
        $total_result = $query->count();

        $hold_message = '<a href="#" data-bs-toggle="popover" data-bs-trigger="hover" title="'.__("Campaign Status : On-hold").'" data-bs-content=""><i class="fas fa-info-circle"></i> </a>';
        foreach ($info as $key => $value)
        {
            $posting_status = $value->posting_status;
            $value->schedule_time = convert_datetime_to_timezone($value->schedule_time,$value->timezone,true);

            if($posting_status=='2') $value->posting_status = '<i class="fas fa-circle text-success"></i> '.__('Completed');
            else if($posting_status=='1') $value->posting_status = '<i class="fas fa-circle text-warning"></i> '.__('Processing');
            else $value->posting_status = '<i class="fas fa-circle text-muted"></i> '.__('Pending');

            $delete_url = route('telegram-group-delete-campaign');
            $str="";

            if($posting_status!='1' && $posting_status!='2') $str=$str."&nbsp;<a class='btn btn-circle rounded btn-outline-warning edit_telegram_group_campaign'  data-id='".$value->id."'  href='#' title='".__('Edit')."' >".'<i class="fas fa-edit"></i>'."</a>";
            if($posting_status!='1') $str=$str."&nbsp;<a href='".$delete_url."' title='".__('Delete')."' data-id='".$value->id."' data-table-name='table2' class='delete-row btn btn-circle rounded btn-outline-danger'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->actions = "<div class='min-width-100px'>".$str."</div>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function edit_campaign_list(Request $request){
       $campaign_id  = $request->campaign_id;
       $campaign_data = DB::table('telegram_group_message_sends')->select('*')->where(['id'=>$campaign_id,'user_id'=>$this->user_id])->first();

       $schedule_time = $campaign_data->schedule_time;
       $schedule_time  = convert_datetime_to_timezone($schedule_time,$campaign_data->timezone);
       $response['id'] = $campaign_data->id;
       $response['campaign_name'] = $campaign_data->campaign_name;
       $response['timezone'] = $campaign_data->timezone;
       $response['pin_post'] = $campaign_data->pin_post;
       $message_content = json_decode($campaign_data->message_content,true);
       $response['text'] = $message_content['text'];
       $response['schedule_time'] = $schedule_time;
       $response['preview_the_URL_in_the_text_message'] = ($message_content['disable_web_page_preview'] == true) ? '1' : '0';
       $response['protected_messages_no_copying_or_forwarding'] = ($message_content['protect_content'] == true) ? '1' : '0';
       $response['sound_alerts_for_messages'] = ($message_content['disable_notification'] == true) ? '1' : '0';
       return response()->json($response);

    }

    public function delete_campaign(Request $request){
       if(config('app.is_demo')=='1' && $this->is_admin) return response()->json(['error'=>true]);
       $id = $request->id;
       if(DB::table('telegram_group_message_sends')->where(['user_id'=>$this->user_id,'id'=>$id])->delete()) return response()->json(['error'=>false]);
       else return response()->json(['error'=>true]);
    }

    public function connect_bot_action(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        $limit_check = $this->print_limit_message(1,1);
        if(!$limit_check) return redirect(route('connect-bot'));
        $telegram_bot = new Telegram_bot;
        $bot_token = $request->bot_token;
        $rules =
        [
            'bot_token' => 'required|string|unique:telegram_bots,bot_token,'.$bot_token
        ];

        $validate_data = $request->validate($rules);

        $this->telegram->bot_token = $validate_data['bot_token'];
        $response = $this->telegram->get_bot_details();
        if(!$response['ok']) {
            $this->set_api_error($response['error_code'] ?? '',$response['description'] ?? '');
            $request->session()->flash('connect_bot_status', '0');
            return redirect(route('connect-bot'));
        }

        $telegram_bot->user_id = $request->user()->id;
        $telegram_bot->bot_token = $validate_data['bot_token'] ?? '';
        $telegram_bot->bot_id = $response['result']['id'] ?? '';
        $telegram_bot->is_bot = isset($response['result']['is_bot']) ? (string)$response['result']['is_bot'] : '0';
        $telegram_bot->first_name = $response['result']['first_name'] ?? '';
        $telegram_bot->last_name = $response['result']['last_name'] ?? '';
        $telegram_bot->username = $response['result']['username'] ?? '';
        $telegram_bot->can_join_groups = isset($response['result']['can_join_groups'])  ? (string)$response['result']['can_join_groups'] : '0';
        $telegram_bot->can_read_all_group_messages = isset($response['result']['can_read_all_group_messages']) ? (string)$response['result']['can_read_all_group_messages'] : '0';
        $telegram_bot->supports_inline_queries = isset($response['result']['supports_inline_queries']) ? (string)$response['result']['supports_inline_queries'] : '0';

        $response = $this->telegram->set_webhook();
        if(!$response['ok']) {
            $this->set_api_error($response['error_code'] ?? '',$response['description'] ?? '');
            $request->session()->flash('connect_bot_status', '0');
            return redirect(route('connect-bot'));
        }

        try {
            DB::beginTransaction();
            $insert = $telegram_bot->save();
            if($insert){
                $telegram_bot_id = DB::getPdo()->lastInsertId();
                $curtime = date("Y-m-d H:i:s");
                $this->insert_usage_log(1,1);
            }
            DB::commit();
            $request->session()->flash('connect_bot_status', '1');
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error = $e->getMessage();
            $request->session()->flash('connect_bot_status', '0');
            $request->session()->flash('connect_bot_error_message', $error);
        }

        return redirect(route('connect-bot'));
    }

    public function update_bot_status(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin){
            $response['error'] = true;
            $response['message'] =__('This feature has been disabled in this demo version. We recommend to sign up as user and check.');        
            return json_encode($response);
        }

        $id = $request->id;
        $status = $request->status;

        $get_bot = $this->get_bot($id,['bot_token']);
        $this->telegram->bot_token = $get_bot->bot_token ?? '';
        $response = $status=='0' ? $this->telegram->delete_webhook() : $this->telegram->set_webhook();
        if(!$response['ok'])
        {
            return $this->set_api_error($response['error_code'] ?? '',$response['description'] ?? '',true);
            exit();
        }

        $query = Telegram_bot::where('id', $id)->where('user_id',$this->user_id)->update(['status' => $status]);
        if($query) return response()->json(['error' => false,'message' => __('Bot status has been updated successfully.')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong.')]);
    }
    public function delete_bot(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin){
            $response['error'] = true;
            $response['message'] =__('This feature has been disabled in this demo version. We recommend to sign up as user and check.');        
            return json_encode($response);
        }

        $id = $request->id;
        $get_bot = $this->get_bot($id,['bot_token']);
        $this->telegram->bot_token = $get_bot->bot_token ?? '';
        $response = $this->telegram->delete_webhook();
        return $this->delete_bot_action($id,$this->user_id);
    }

    public function sync_bot(Request $request)
    {
        $id = $request->id;
        $get_bot = $this->get_bot($id,['bot_token']);
        $this->telegram->bot_token = $get_bot->bot_token ?? '';

        $response = $this->telegram->get_bot_details();
        if(!$response['ok'])
        {
            return $this->set_api_error($response['error_code'] ?? '',$response['description'] ?? '',true);
            exit();
        }
        $update_data['first_name'] = $response['result']['first_name'] ?? '';
        $update_data['last_name'] = $response['result']['last_name'] ?? '';
        $update_data['username'] = $response['result']['username'] ?? '';
        $update_data['can_join_groups'] = isset($response['result']['can_join_groups'])  ? (string)$response['result']['can_join_groups'] : '0';
        $update_data['can_read_all_group_messages'] = isset($response['result']['can_read_all_group_messages']) ? (string)$response['result']['can_read_all_group_messages'] : '0';
        $update_data['supports_inline_queries'] = isset($response['result']['supports_inline_queries']) ? (string)$response['result']['supports_inline_queries'] : '0';
        $query = Telegram_bot::where('id', $id)->where('user_id',$this->user_id)->update($update_data);

        if($query) return response()->json(['error' => false,'message' => __('Bot details has been synced successfully.')]);
        else return response()->json(['error' => false,'message' => __('Bot details are already synced.')]);
    }

    public function set_active_group_session(Request $request)
    {
        $id = $request->telegram_group_id;

        session(['bot_manager_get_group_details_telegram_group_id'=>$id]);
        session(['bot_manager_get_group_details_tab_menu_id'=>'v-pills-group-subscriber-tab']);
    }
   
    public function set_active_group_tab_menu_session(Request $request)
    {
        session(['bot_manager_get_group_details_tab_menu_id'=>$request->link_id]);
        echo session('bot_manager_get_group_details_tab_menu_id');
    }

}
