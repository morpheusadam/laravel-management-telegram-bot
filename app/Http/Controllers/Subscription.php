<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use App\Jobs\SendEmailJob;
use App\Services\TelegramServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Subscription extends Home
{
    public function __construct()
    {
        $this->set_global_userdata(true,['Admin'],['Manager']);
    }

    public function list_package()
    {
       $payment_config = $this->get_payment_config();
       $has_team_access = has_team_access($this->is_admin);
       $data = array('body'=>'subscription/package/list-package','payment_config'=>$payment_config,'load_datatable'=>true,'has_team_access'=>$has_team_access);
       return $this->viewcontroller($data);
    }

    public function list_package_data(Request $request)
    {
        $search_value = $request->search_value;
        $search_package_type = $request->search_package_type;

        $display_columns = array("#",'id', 'package_name','package_type','price','validity','is_default');
        $search_columns = array('package_name','price','validity');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 1;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="packages";
        $query = DB::table($table)->where('user_id',$this->user_id)->where('deleted','0');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_type)) $query->where('package_type','=',$search_package_type);
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($table.'id')->where('user_id',$this->user_id)->where('deleted','0');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_type)) $query->where('package_type','=',$search_package_type);
        $total_result = $query->count();

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function create_package()
    {
        $team_package = isset(request()->type) && request()->type=='team';
        $data['has_team_access'] = has_team_access($this->is_admin);
        $data['body'] = 'subscription/package/create-package';
        $data['modules'] = $this->get_modules($team_package);
        $data['payment_config'] = $this->get_payment_config();
        $data['validity_types'] = $this->get_validity_types();
        return $this->viewcontroller($data);
    }

    public function save_package(Request $request)
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));
        $package_type = $request->package_type ?? 'team';
        $rules =
        [
            'package_type' => 'required|string',
            'package_name' => 'required|string|max:99',
            'visible' => 'nullable|sometimes|boolean',
            'highlight' => 'nullable|sometimes|boolean',
            'modules' => 'nullable|sometimes',
            'team_access' => 'nullable|sometimes'
        ];

        if($package_type!='team'){
            if($request->is_default == '1') $rules['price'] = 'required';
            else $rules['price'] = 'required|numeric|min:1';
        }
        else $rules['price'] = 'nullable|sometimes';


        if($package_type!='team' && (($request->is_default == '1' && $request->price == "Trial") || is_null($request->is_default)))
        {
            $rules['validity'] = 'required|integer|min:1';
            $rules['validity_type'] = 'required|string';
        }
        else
        {
            $rules['validity'] = 'nullable|sometimes|integer';
            $rules['validity_type'] = 'nullable|sometimes|string';
        }
        $validate_data = $request->validate($rules);

        $modules = $validate_data['modules'];
        if($package_type=='team'){
            $bot_manager_modules = [2,7,10,11];
            $team_access = $validate_data['team_access'] ?? [];
            // if module is not checked but permission checked then remove the permission
            foreach ($team_access as $k=>$v) {
                if(!in_array($k,$modules)) unset($team_access[$k]);
            }
            // if no permission allowed for a module then assign empty manually
            foreach ($modules as $k=>$v) {
                if(!isset($team_access[$v])) $team_access[$v] = [];
            }
            if(isset($team_access[14]))
            foreach ($bot_manager_modules as $v){
                $team_access[$v] = $team_access[14];
                array_push($modules,$v);
            }
            $validate_data['team_access'] = json_encode($team_access);
            $validate_data['price'] = null;
        }
        else $validate_data['team_access'] = null;

        $validate_data['visible'] = isset($_POST['visible']) ? "1" : "0";
        $validate_data['highlight'] = isset($_POST['highlight']) ? "1" : "0";
        $product_data = [
            'paypal' =>[
                'plan_id'=>$request->paypal_plan_id
            ]
        ];
        $validate_data['product_data'] = $package_type=='team' ? null : json_encode($product_data);

        $discount_apply_all = isset($_POST['discount_apply_all']) ? "1" : "0";
        $discount_data = [
            'percent' => $request->discount_percent,
            'terms' => $request->discount_terms,
            'start_date' => $request->discount_start_date,
            'end_date' => $request->discount_end_date,
            'timezone' => $request->discount_timezone,
            'status' => isset($_POST['discount_status']) ? "1" : "0"
        ];
        $validate_data['discount_data'] = $package_type=='team' ? null : json_encode($discount_data);

        $validity_type_arr = ['D' => 1,'W' => 7,'M' => 30,'Y' => 365];
        $validity = $validate_data['validity'] ?? 0;
        $validate_data['validity'] =   $package_type=='team' ? null : $validity* $validity_type_arr[$validate_data['validity_type'] ?? 'D'];
        $validate_data['validity_extra_info'] = $package_type=='team' ? null : implode(',', array( $validity,  $validate_data['validity_type']));

        $bulk_limit=array();
        $monthly_limit=array();

        foreach ($modules as $value)
        {
            $monthly_field="monthly_".$value;
            $val=$request->$monthly_field;
            if($val=="") $val=0;

            $monthly_limit[$value]=$val;

            $bulk_field="bulk_".$value;
            $val=$request->$bulk_field;
            if($val=="") $val=0;
            $bulk_limit[$value]=$val;
        }
        if(isset($validate_data['modules'])) unset($validate_data['modules']);
        if(isset($validate_data['validity_type'])) unset($validate_data['validity_type']);
        $validate_data['module_ids'] = implode(',',$modules);
        $validate_data['monthly_limit'] = $package_type=='team' ? null : json_encode($monthly_limit);
        $validate_data['bulk_limit'] = $package_type=='team' ? null : json_encode($bulk_limit);
        if(!isset($request->id)) $validate_data['user_id'] = $this->user_id;

        $query = true;
        if(isset($request->id)) DB::table("packages")->where(['id'=>$request->id,'user_id'=>$this->user_id])->update($validate_data);
        else $query = DB::table("packages")->insert($validate_data);

        if($discount_apply_all=='1'){
            DB::table("packages")->where(['user_id'=>$this->user_id,'is_default'=>'0','package_type'=>'subscription'])->update(['discount_data'=>$discount_data]);
        }

        if($query) $request->session()->flash('save_package_status', '1');
        else $request->session()->flash('save_package_status', '0');

        return redirect(route('list-package'));

    }

    public function update_package($id)
    {
        $xdata = DB::table('packages')->where(['id'=>$id,'user_id'=>$this->user_id])->first();
        if(!isset($xdata)) abort(403);
        $team_package = $xdata->package_type == 'team';

        $data['has_team_access'] = has_team_access($this->is_admin);
        $data['body'] = 'subscription/package/update-package';
        $data['modules'] = $this->get_modules($team_package);
        $data['payment_config'] = $this->get_payment_config();
        $data['validity_types'] = $this->get_validity_types();
        $data['xdata'] = $xdata;

        $validity_days = $xdata->validity;
        if ($validity_days % 365 == 0) {
            $validity_type = 'Y';
            $validity_amount = $validity_days / 365;
        }
        else if ($validity_days % 30 == 0) {
            $validity_type = 'M';
            $validity_amount = $validity_days / 30;
        }
        else if ($validity_days % 7 == 0) {
            $validity_type = 'W';
            $validity_amount = $validity_days / 7;
        }
        else {
            $validity_type = 'D';
            $validity_amount = $validity_days;
        }
        $data['validity_type'] = $validity_type;
        $data['validity_amount'] = $validity_amount;

        return $this->viewcontroller($data);
    }

    public function delete_package(Request $request)
    {
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $id = $request->id;
        $query = DB::table('packages')->where('id',$id)->where('user_id',$this->user_id)->where('is_default','0')->update(['deleted'=>'1']);
        if($query) return response()->json(['error' => false,'message' => __('Package has been deleted successfully')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong')]);
    }

    public function list_user()
    {
        $has_team_access = has_team_access($this->is_admin);
        $data = array('body'=>'subscription/user/list-user','load_datatable'=>true,'has_team_access'=>$has_team_access);
        $package_list = $this->get_packages_all();
        $packages = [''=>__('Any Package/Role')];
        if($this->is_admin) $packages['Subscribed']='--- '.__('Only Paid Subscription').' ---';
        foreach ($package_list as $k=>$v){
            $extra_text = $has_team_access && !empty($v->package_type) ? ucfirst($v->package_type).' : ' : '';
            $packages[$v->id] = $extra_text.$v->package_name;
        }
        $data['packages']=$packages;
        return $this->viewcontroller($data);
    }

    public function list_user_data(Request $request)
    {
        $search_value = $request->search_value;
        $search_package_id = $request->search_package_id;
        $search_user_type = $request->search_user_type;
        $display_columns = array("#","CHECKBOX", 'profile_pic','name', 'email','package_name', 'status', 'user_type', 'actions','expired_date', 'created_at','last_login_at','last_login_ip','user_id');
        $search_columns = array('name', 'email','agent_domain');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 13;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="users";
        $select= ["users.*","users.id as user_id","packages.package_name"];
        $query = DB::table($table)->select($select)->where('parent_user_id',$this->user_id)->where('user_type','!=','Affiliate')->where($table.'.deleted','0')->leftJoin('packages', 'users.package_id', '=', 'packages.id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_id)) {
            if($search_package_id=='Subscribed') $query->where('package_id','>','1');
            else $query->where('package_id','=',$search_package_id);
        }
        if (!empty($search_user_type)) $query->where('user_type','=',$search_user_type);

        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($table.'id')->where('parent_user_id',$this->user_id)->where('user_type','!=','Affiliate')->where($table.'.deleted','0')->leftJoin('packages', 'users.package_id', '=', 'packages.id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_id)) {
            if($search_package_id=='Subscribed') $query->where('package_id','>','1');
            else $query->where('package_id','=',$search_package_id);
        }
        if (!empty($search_user_type)) $query->where('user_type','=',$search_user_type);
        $total_result = $query->count();

        $i=0;
        foreach ($info as $key => $value)
        {
            $status_checked = ($value->status=='1') ? 'checked' : '';
            $value->status = '<div class="form-check form-switch update-status-switch d-flex justify-content-center"><input data-url="'.route('update-user-status').'" data-id="'.$value->id.'" class="form-check-input update-status" type="checkbox" '.$status_checked.' value="'.$value->status.'"></div>';

            $last_login_at = $value->last_login_at;
            if($last_login_at=='0000-00-00 00:00:00')  $value->last_login_at = __("Never");
            else  $value->last_login_at = convert_datetime_to_timezone($value->last_login_at);

            if($value->user_type=='Manager') $value->expired_date = '-';
            else{
                $expired_date =  $value->expired_date;
                if($expired_date=='0000-00-00 00:00:00' ||  $value->user_type == "Admin")  $value->expired_date = "-";
                else  $value->expired_date = convert_datetime_to_timezone($value->expired_date,'',false,'jS M y');
            }

            $value->created_at = convert_datetime_to_timezone($value->created_at,'',false,'jS M y');

            if($value->package_name=="") $value->package_name = "-";

            $user_name = $value->name;
            $user_id = $value->id;
            $edit_url = route('update-user',$value->id);
            $dash_url = route('dashboard-user').'?id='.$value->id;
            $delete_url = route('delete-user');
            $str="";

            $str=$str."<a class='btn btn-circle btn-outline-primary' target='_BLANK' href='".$dash_url."' title='".__('Dashboard')."'>".'<i class="fas fa-chart-pie"></i>'."</a>";
            $str=$str."&nbsp;&nbsp;<a class='btn btn-circle btn-outline-warning' href='".$edit_url."' title='".__('Edit')."'>".'<i class="fas fa-edit"></i>'."</a>";
            $str=$str."&nbsp;&nbsp;<a href='".$delete_url."' data-id='".$value->id."' class='delete-row btn btn-circle btn-outline-danger' title='".__('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $width = $this->is_admin ? 160 : 120;
            $value->actions = "<div style = 'min-width:".$width."px'>".$str."</div>";

            $profile_pic  = !empty($value->profile_pic) ? $value->profile_pic : asset('assets/images/avatar/avatar-1.png');
            $value->profile_pic = "<img src='".$profile_pic."' width='40px' height='40px' class='rounded-circle'>";

            if($value->user_type=='Manager') $value->user_type=__("Team");
            else if($value->user_type=='Member') $value->user_type=__("Member");
            if(config('app.is_demo')=='1') $value->email = "xxxxxx@something.com";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public  function create_user()
    {
        $team_package = isset(request()->type) && request()->type=='team';
        $data['body'] = 'subscription/user/create-user';
        $data['packages'] = $this->get_packages('*',$team_package);
        return $this->viewcontroller($data);
    }

    public function save_user(Request $request)
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));

        $limit_error = $team_limit_error = false;
        $user_password = $request->password;
        $action_type = $request->action_type;
        $user_type = $request->user_type;

        $xstatus = '0';
        $status = isset($_POST['status']) ? "1" : "0";
        if(isset($request->id)){
            $xuser_data = DB::table('users')->where('id',$request->id)->select('status')->first();
            $xstatus = $xuser_data->status ?? '0';
        }

        $rules =
        [
            'name' => 'required|string|max:99',
            'mobile' => 'nullable|sometimes|string',
            'address' => 'nullable|sometimes|string',
            'package_id' => 'required|integer',
            'status' => 'required|sometimes|boolean',
            'user_type' => 'required|string',
            'expired_date' => 'required_if:user_type,Member|date|nullable|sometimes',
        ];

        if(!isset($request->id)) {
            $rules['password'] = 'required|min:6|confirmed';
            $rules['email'] = 'required|string|email|max:99|unique:users';
        }
        else {
            $rules['password'] = 'nullable|sometimes|min:6|confirmed';
            $rules['email'] = 'required|email|max:99|unique:users,email,' . $request->id;
        }

        $validate_data = $request->validate($rules);

        $curdate = date("Y-m-d H:i:s");
        $validate_data['status'] = $status;
        $validate_data['updated_at'] = $curdate;
        if(!isset($request->id)) {
            $validate_data['parent_user_id'] = $this->user_id;
            $validate_data['created_at'] = $curdate;
            $validate_data['purchase_date'] = $curdate;
            $validate_data['password'] =  Hash::make($user_password);
        }
        else {
            if(empty($user_password)) unset($validate_data['password']);
            else $validate_data['password'] =  Hash::make($user_password);
        }

        if(!isset($validate_data['expired_date'])) $validate_data['expired_date'] = null;
        if($action_type=="user") $validate_data['user_type'] = 'Member';
      
        if(!isset($request->id) || $request->email != $request->xemail) // new user email and edited user with a new email need to be verified
        $validate_data['email_verified_at'] = null;
        $error = false;
        try {
            if (isset($request->id)) {
                DB::table("users")->where(['id' => $request->id, 'parent_user_id' => $this->user_id])->update($validate_data);
                $insert_id = $request->id;
            } else {
                DB::table("users")->insert($validate_data);
                $insert_id = DB::getPdo()->lastInsertId();
            }
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error = $e->getMessage();
        }

        if(!$error) $request->session()->flash('save_user_status', '1');
        else {
            $request->session()->flash('save_user_status', '0');
            $request->session()->flash('save_user_status_error', __($error));
        }

        return redirect(route('list-user'));

    }

    public function update_user($id)
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));
        $xdata = DB::table('users')->where(['id'=>$id,'parent_user_id'=>$this->user_id])->first();
        if(!isset($xdata)) abort(403);
        $team_package = $xdata->user_type == 'Manager';

        $data['body'] = 'subscription/user/update-user';
        $data['packages'] = $this->get_packages('*',$team_package);
        $data['xdata'] = $xdata;
        return $this->viewcontroller($data);
    }

    public function delete_user(Request $request,TelegramServiceInterface $telegram_service)
    {
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if($this->is_manager) {
            return response()->json(['error' => true,'message' => __('Access Denied')]);
        }

        $user_id = $request->id;
        if($user_id==$this->user_id){
            return response()->json(['error' => true,'message' => __('You cannot delete yourself. Please contact your service provider.')]);
        }

        $table = 'users';
        $where = ['parent_user_id'=>$this->user_id,'id'=>$user_id];
        if(!valid_to_delete($table,$where)) {
            return response()->json(['error'=>true,'message'=>__('Bad request.')]);
        }

        //finding all users
        $user_data = DB::table($table)
            ->select('id')
            ->where('user_type','!=','Admin')
            ->where(function ($query) use ($user_id){
                $query->where('id', '=', $user_id)
                    ->orWhere('parent_user_id', '=',$user_id);
            })->get();
        $user_ids = [];
        foreach ($user_data as $value) array_push($user_ids,$value->id);
        if(empty($user_ids)) {
            return response()->json(['error'=>true,'message'=>__('User not found.')]);
        }

        //finding all bots
        $telegram_bot_data = DB::table('telegram_bots')->select('bot_token')->whereIntegerInRaw('user_id',$user_ids)->get();

        $success = false;
        $error_message = '';
        try {
            DB::beginTransaction();

            DB::table($table)->whereIntegerInRaw('id',$user_ids)->delete();

            DB::commit();
            $success = true;
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error_message = $e->getMessage();
        }

        // disabling bot webhook
        if($success)
        {
            foreach ($telegram_bot_data as $value)
            {
                $telegram_service->bot_token = $value->bot_token ?? '';
                $api_response = $telegram_service->delete_webhook();
            }
            return response()->json(['error' => false,'message' => __('User has been deleted successfully.')]);
        }
        else return response()->json(['error' => true,'message' => __('Database error : ').$error_message]);

    }

    public function update_user_status(Request $request)
    {
        if(config('app.is_demo')=='1')
            return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);
        $id = $request->id;
        $status = $request->status;
        $where = ['id'=> $id];
        $query = DB::table('users')->where($where)->update(['status' => $status,'updated_at'=>date("Y-m-d H:i:s")]);
        if($query) return response()->json(['error' => false,'message' => __('User status has been updated successfully')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong')]);
    }

    public function user_send_email(Request $request)
    {
        if(config('app.is_demo')=='1') {
            echo "<b>This feature has been disabled in this demo version. We recommend to sign up as user and check.</b>";
            exit();
        }
        $subject = $request->subject;
        $message = $request->message;
        $user_ids = $request->user_ids;
        $count=0;

        $info = DB::table("users")->whereIn("id",$user_ids)->select('email','name')->get();

        foreach($info as $member)
        {
            $email = $member->email;
            $name = $member->name;
            if($message=="" || $subject=="") continue;
            $title = __("Hello").' '.$name;
            $response = $this->send_email($email,$message,$subject,$title);
            if(isset($response['error']) && !$response['error']) $count++;

        }
        echo "<b> $count / ".count($info)." : ".__("Emails have been sent successfully.")."</b>";
    }



}
