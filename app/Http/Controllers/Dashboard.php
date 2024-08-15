<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Home
{
    public function __construct()
    {
        $this->set_global_userdata();
    }

    public function index()
    {       
        if(session('email_just_verified')=='1'){
            $settings_data = DB::table('settings')->where('user_id',$this->parent_user_id)->first();
            $auto_responder_signup_settings = $settings_data->auto_responder_signup_settings ?? '';
            if(!empty($auto_responder_signup_settings)){
                $this->sync_email_to_autoresponder(
                    $auto_responder_signup_settings,
                    $email = Auth::user()->email,
                    $first_name = Auth::user()->name,
                    $last_name = '',
                    $type='signup',
                    $this->parent_user_id
                );
            }
            session(['email_just_verified' => '0']);
        }

        if(!empty(request()->id)){
            $dashboard_user = request()->id;
            $check = DB::table('users')->where(['parent_user_id'=>$this->user_id,'id'=>$dashboard_user])->select('id')->first();
            $user_id = empty($check) ? $this->user_id : $dashboard_user;
        }
        else $user_id = $this->user_id;

        $dashboard_selected_year = (int) session('dashboard_selected_year');
        if($dashboard_selected_year==0) $dashboard_selected_year = date('Y');
        $dashboard_selected_month = session('dashboard_selected_month');
        if($dashboard_selected_month=='') $dashboard_selected_month = date('m');
        $dashboard_selected_month_year = $dashboard_selected_year.'-'.$dashboard_selected_month;
        $previous_year = ($dashboard_selected_year-1);


        //FIRST BLOCK
        $telegram_bot_count =  DB::table('telegram_bots')->select('id')->where(['user_id'=>$user_id])->count();
        $telegram_group_count = DB::table('telegram_groups')
            ->select('telegram_groups.id')
            ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
            ->where([
                ['telegram_bots.user_id', '=', $this->user_id]
            ])
            ->count();


        $total_member = DB::table('telegram_group_subscribers')
                        ->leftJoin('telegram_groups', 'telegram_groups.id', '=', 'telegram_group_subscribers.telegram_group_id')
                        ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                        ->where(['telegram_bots.user_id'=>$user_id,'is_banned'=>'0','is_left'=>'0'])->count();
        
        $join_member = DB::table('telegram_group_subscribers')
                        ->leftJoin('telegram_groups', 'telegram_groups.id', '=', 'telegram_group_subscribers.telegram_group_id')
                        ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                        ->where(['telegram_bots.user_id'=>$user_id,'is_banned'=>'0','is_left'=>'0',])
                        ->where(DB::raw("(DATE_FORMAT(updated_at,'%Y-%m'))"),'=',$dashboard_selected_month_year)
                        ->count();
        $left_member = DB::table('telegram_group_subscribers')
                        ->leftJoin('telegram_groups', 'telegram_groups.id', '=', 'telegram_group_subscribers.telegram_group_id')
                        ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                        ->where(['telegram_bots.user_id'=>$user_id,'is_left'=>'1',])
                        ->where(DB::raw("(DATE_FORMAT(updated_at,'%Y-%m'))"),'=',$dashboard_selected_month_year)
                        ->count();
                        
        $banned_member = DB::table('telegram_group_subscribers')
                        ->leftJoin('telegram_groups', 'telegram_groups.id', '=', 'telegram_group_subscribers.telegram_group_id')
                        ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                        ->where(['telegram_bots.user_id'=>$user_id,'is_banned'=>'ban',])
                        ->where(DB::raw("(DATE_FORMAT(updated_at,'%Y-%m'))"),'=',$dashboard_selected_month_year)
                        ->count();
        
        $current_time = date('Y-m-d H:i:s');

        $total_mute_member = DB::table('telegram_group_subscribers')
                        ->leftJoin('telegram_groups', 'telegram_groups.id', '=', 'telegram_group_subscribers.telegram_group_id')
                        ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                        ->where(['telegram_bots.user_id'=>$user_id,'is_banned'=>'0','is_left'=>'0',])
                        ->where('mute_time','>',$current_time)
                        ->count();                        
        
        
        $completed_campaign =  DB::table('telegram_group_message_sends')
                            ->select('id')
                            ->where(['user_id'=>$user_id, 'posting_status'=>'2'])
                            ->where(DB::raw("(DATE_FORMAT(schedule_time,'%Y-%m'))"),'=',$dashboard_selected_month_year)
                            ->count();

        $pending_campaign =  DB::table('telegram_group_message_sends')
                            ->select('id')
                            ->where(['user_id'=>$user_id, 'posting_status'=>'0'])
                            ->where(DB::raw("(DATE_FORMAT(schedule_time,'%Y-%m'))"),'=',$dashboard_selected_month_year)
                            ->count();

        $processing_campaign =  DB::table('telegram_group_message_sends')
                            ->select('id')
                            ->where(['user_id'=>$user_id, 'posting_status'=>'1'])
                            ->where(DB::raw("(DATE_FORMAT(schedule_time,'%Y-%m'))"),'=',$dashboard_selected_month_year)
                            ->count();       

        //FOURTH BLOCK
        $telegram_monthly_subscriber_data = DB::table('telegram_group_subscribers')
                        ->select(DB::raw('count(telegram_group_subscribers.id) as `data`'),DB::raw("DATE_FORMAT(updated_at, '%m') new_date"))
                        ->leftJoin('telegram_groups', 'telegram_groups.id', '=', 'telegram_group_subscribers.telegram_group_id')
                        ->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')
                        ->where(['telegram_bots.user_id'=>$user_id,'is_banned'=>'0','is_left'=>'0',])->where(DB::raw("(DATE_FORMAT(updated_at,'%Y'))"),'=',$dashboard_selected_year)
                        ->groupBy('new_date')->orderBy('new_date')->get();

        
        $data = [
            'telegram_bot_count'=>$telegram_bot_count,
            'telegram_group_count'=>$telegram_group_count,
            'total_member'=>$total_member,
            'join_member'=>$join_member,
            'left_member'=>$left_member,
            'banned_member'=>$banned_member,
            'total_mute_member'=>$total_mute_member,
            'completed_campaign'=>$completed_campaign,
            'processing_campaign'=>$processing_campaign,
            'pending_campaign'=>$pending_campaign,
            'telegram_monthly_subscriber_data'=>$telegram_monthly_subscriber_data,
            'dashboard_selected_year'=>$dashboard_selected_year,
            'dashboard_selected_month'=>$dashboard_selected_month,
        ];
        $data['body'] = 'dashboard';
        return $this->viewcontroller($data);
    }


    public function dashboard_change_data(Request $request){
        $month = $request->month;
        $year = $request->year;
        $currency = $request->currency;
        if(!empty($month)) {
            $month = str_pad($month,2,'0',STR_PAD_LEFT);
            session(['dashboard_selected_month'=>$month]);
        }
        if(!empty($year)) session(['dashboard_selected_year'=>$year]);
        if(!empty($currency)) session(['dashboard_selected_currency'=>$currency]);
    }
}
