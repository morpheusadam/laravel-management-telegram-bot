<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        set_agency_config();
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
        $affiliate_user_id = Cookie::get('affiliate_user_id');
        if($affiliate_user_id === null) $affiliate_user_id = 0;
        set_agency_config();
        $package_info = DB::table('packages')->where(['is_default'=>'1'])->first();
        $validity = isset($package_info->validity) ? $package_info->validity : 0;
        $package_id = isset($package_info->id) ? $package_info->id : 0;
        $to_date = date('Y-m-d');
        $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
        $curtime = date("Y-m-d H:i:s");
        $userdata = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type'=>'Member',
            'package_id'=>$package_id,
            'created_at'=>$curtime,
            'updated_at'=>$curtime,
            'expired_date'=>$expiry_date,
            'last_login_at'=>date('Y-m-d H:i:s'),
            'last_login_ip'=>get_real_ip(),
            'under_which_affiliate_user'=>$affiliate_user_id

        ];
        $user = User::create($userdata);

        if($user instanceof User)
        {
            event(new Registered($user));
            Auth::login($user);
            $user_id = $user->id;
            if($affiliate_user_id != 0)
            app('App\Http\Controllers\Home')->affiliate_commission($affiliate_user_id,$user_id,$event='signup',$package_price=0);
            return response()->json([
                'error' => false,
                'message' => __('You have been registered successfully'),
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => __('Something went wrong'),
        ]);
    }
}
