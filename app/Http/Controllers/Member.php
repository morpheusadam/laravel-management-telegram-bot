<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use App\Services\Payment\PaypalServiceInterface;
use App\Services\Payment\StripeServiceInterface;
use App\Services\Payment\InstamojoServiceInterface;
use App\Services\Payment\PaymayaServiceInterface;
use App\Services\Payment\ToyyibpayServiceInterface;
use App\Services\Payment\XenditServiceInterface;
use App\Services\Payment\SenangpayServiceInterface;
use App\Services\Payment\MyfatoorahServiceInterface;
use App\Services\Payment\MercadopagoServiceInterface;
use App\Services\Payment\FlutterwaveServiceInterface;
use App\Services\Payment\RazorpayServiceInterface;
use App\Services\Payment\PaystackServiceInterface;
use App\Services\Payment\MollieServiceInterface;
use App\Services\Payment\YoomoneyServiceInterface;
use App\Services\AutoResponder\AutoResponderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Form;
use Illuminate\Validation\Rules;
use App\Models\User;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class Member extends Home
{
    public function __construct(MollieServiceInterface $mollie_service, PaystackServiceInterface $paystack_service, RazorpayServiceInterface $razorpay_service,MyfatoorahServiceInterface $myfatoorah_sevice, MercadopagoServiceInterface $mercadopago_service, InstamojoServiceInterface $instamojo_service,PaymayaServiceInterface $paymaya_service,ToyyibpayServiceInterface $toyyibpay_service,XenditServiceInterface $xendit_service,SenangpayServiceInterface $senangpay_service,PaypalServiceInterface $paypal_service,StripeServiceInterface $stripe_service,YoomoneyServiceInterface $yoomoney_service,FlutterwaveServiceInterface $flutterwave_service)
    {
        if(in_array(Route::currentRouteName(),['account','account-action'])) $this->set_global_userdata(false);
        else $this->set_global_userdata(false,['Admin','Member'],['Manager']);
        $this->paypal = $paypal_service;
        $this->stripe = $stripe_service;
        $this->instamojo = $instamojo_service;
        $this->instamojo_v2 = $instamojo_service;
        $this->paymaya = $paymaya_service;
        $this->toyyibpay = $toyyibpay_service;
        $this->xendit = $xendit_service;
        $this->senangpay = $senangpay_service;
        $this->myfatoorah = $myfatoorah_sevice;
        $this->mercadopago = $mercadopago_service;
        $this->flutterwave = $flutterwave_service;
        $this->razorpay_class_ecommerce = $razorpay_service;
        $this->paystack_class_ecommerce = $paystack_service;
        $this->mollie_class_ecommerce = $mollie_service;
        $this->yoomoney = $yoomoney_service;
        $this->provider = new PayPalClient;
    }

    public function account()
    {
      $user = Auth::user();
      $data = $this->get_usage_log_data();
      $data['body'] = 'member/settings/account';
      $data['data'] = $user;
      return $this->viewcontroller($data);
    }

    public function account_action(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        $user_email = Auth::user()->email;
        $user_password = $request->password;
        $data['email'] = $request->email;
        $user_id = $this->is_manager ? $this->manager_id: $this->user_id;

        $rules =
        [
            'name' => 'required|string|max:99',
            'mobile' => 'nullable|sometimes|string',
            'address' => 'nullable|sometimes|string',
            'timezone' => 'nullable|sometimes|string',
            'profile_pic'=>'nullable|sometimes|image|mimes:png,jpg,jpeg,webp|max:200'
        ];

        $logout=false;

        if($user_email != $data['email']){
            $rules['email'] = 'required|email|unique:users,email,' . $user_id;
            $logout = true;
        }

        if($user_password!=''){
            $rules['password'] = ['required','confirmed',Rules\Password::defaults()];
            $logout = true;
        }

        $validate_data = $request->validate($rules);
        if($user_email != $data['email']){
            $validate_data['email_verified_at'] = null;
        }

        if($request->file('profile_pic')) {

            $file = $request->file('profile_pic');
            $extension = $request->file('profile_pic')->getClientOriginalExtension();
            $filename = $user_id.'.'.$extension;
            $upload_dir_subpath = 'public/profile';

            if(env('AWS_UPLOAD_ENABLED')){
               try {
                   $upload2S3 = Storage::disk('s3')->putFileAs('profile', $file,$filename);
                   $validate_data['profile_pic'] = Storage::disk('s3')->url($upload2S3);
               }
               catch (\Exception $e){
                   $error_message = $e->getMessage();
               }
            }
            else{
                $request->file('profile_pic')->storeAs(
                    $upload_dir_subpath, $filename
                );
                $validate_data['profile_pic'] = asset('storage/profile').'/'.$filename;
            }
        }

        if($user_password!='') $validate_data['password'] =  Hash::make($user_password);
        DB::table('users')->where('id',$user_id)->update($validate_data);

        if($logout) return redirect(route('logout'));

        $request->session()->flash('save_user_profile', '1');
        return redirect(route('account'));
    }

    public function select_package()
    {
        if($this->is_admin) abort('403');
        $data['body'] = "member/payment/select-package";
        $payment_config = $this->get_payment_config_parent($this->parent_user_id);
        $data['config_data'] = $payment_config;
        $data["payment_package"] = $this->get_packages_parent($this->parent_user_id);
        $data['has_reccuring'] = Auth::user()->subscription_enabled == '1' ? '1' : '0';
        $data['last_payment_method'] = Auth::user()->last_payment_method;
        $data['format_settings'] = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? 0,'thousand_comma'=>$payment_config->thousand_comma ?? '0','currency_position'=>$payment_config->currency_position ?? 'left'];
        return $this->viewcontroller($data);
    }

    public function buy_package($id=0)
    {
        if ($this->is_admin) abort('403');
        $package_data = $this->get_package($id, $select = '*', $where = ['id' => $id, 'user_id' => $this->parent_user_id, 'deleted' => '0', 'is_default' => '0']);
        if (empty($package_data)) abort('403');

        $subscriber_limit_error = false;
        $limit_exceed=$this->check_usage($this->module_id_bot_subscriber,1,$this->user_id);
        if($limit_exceed=="2" || $limit_exceed=="3") $subscriber_limit_error = true;

        if ($subscriber_limit_error) {
            $error_message = __('You are not eligible to purchase this package');
            return $this->custom_error_page($error_title = '', $error_code = '', $error_message);
        }
    

        $package_id = $id;
        $data['body'] = "member/payment/buy-package";
        $payment_config = $this->get_payment_config_parent();

        $currency = $payment_config->currency ?? 'USD';
        $paypal_data = isset($payment_config->paypal) ? json_decode($payment_config->paypal) : [];
        $stripe_data = isset($payment_config->stripe) ? json_decode($payment_config->stripe) : [];
        
        $razorpay_data = isset($payment_config->razorpay) ? json_decode($payment_config->razorpay) : [];
        $paystack_data = isset($payment_config->paystack) ? json_decode($payment_config->paystack) : [];
        $mercadopago_data= isset($payment_config->mercadopago) ? json_decode($payment_config->mercadopago) : [];
        $myfatoorah_data= isset($payment_config->myfatoorah) ? json_decode($payment_config->myfatoorah) : [];
        $toyyibpay_data= isset($payment_config->toyyibpay) ? json_decode($payment_config->toyyibpay) : [];
        $xendit_data= isset($payment_config->xendit) ? json_decode($payment_config->xendit) : [];
        $paymaya_data = isset($payment_config->paymaya) ? json_decode($payment_config->paymaya) : [];
        $mollie_data = isset($payment_config->mollie) ? json_decode($payment_config->mollie) : [];
        $senangpay_data= isset($payment_config->senangpay) ? json_decode($payment_config->senangpay) : [];
        $instamojo_data = isset($payment_config->instamojo) ? json_decode($payment_config->instamojo) : [];
        $instamojo_v2_data = isset($payment_config->instamojo_v2) ? json_decode($payment_config->instamojo_v2) : [];

        $sslcommerz_data= isset($payment_config->sslcommerz) ? json_decode($payment_config->sslcommerz) : [];
        $flutterwave_data = isset($payment_config->flutterwave) ? json_decode($payment_config->flutterwave) : [];
        $yoomoney_data= isset($payment_config->yoomoney) ? json_decode($payment_config->yoomoney) : [];
        $manual_payment_status= isset($payment_config->manual_payment_status) ? $payment_config->manual_payment_status: "0";
        $manual_payment_instruction= isset($payment_config->manual_payment_instruction) ? $payment_config->manual_payment_instruction: "";

        $paypal_status = $paypal_data->paypal_status ?? '0';
        $stripe_status = $stripe_data->stripe_status ?? '0';
        $razorpay_status = $razorpay_data->razorpay_status??'0';
        $paystack_status = $paystack_data->paystack_status??'0';
        $mercadopago_status = $mercadopago_data->mercadopago_status??'0';
        $myfatoorah_status= $myfatoorah_data->myfatoorah_status ??'0';
        $toyyibpay_status= $toyyibpay_data->toyyibpay_status ??'0';
        $xendit_status= $xendit_data->xendit_status ??'0';
        $paymaya_status= $paymaya_data->paymaya_status ??'0';
        $mollie_status = $mollie_data->mollie_status??'0';
        $instamojo_status= $instamojo_data->instamojo_status ??'0';
        $instamojo_v2_status= $instamojo_v2_data->instamojo_v2_status ??'0';
        $senangpay_status= $senangpay_data->senangpay_status ??'0';
        $senangpay_mode= $senangpay_data->senangpay_mode ?? "0";
        $sslcommerz_status= $sslcommerz_data->sslcommerz_status ??'0';
        $yoomoney_status= $yoomoney_data->yoomoney_status ??'0';
        $sslcommerz_mode = $sslcommerz_data->sslcommerz_mode ?? 'sandbox';
        $flutterwave_status = $flutterwave_data->flutterwave_status??'0';

        $paypal_client_id = $paypal_data->paypal_client_id ?? '';
        $paypal_client_secret = $paypal_data->paypal_client_secret ?? '';
        $paypal_app_id = $paypal_data->paypal_app_id ?? '';
        $paypal_mode = $paypal_data->paypal_mode ?? 'sandbox';
        $paypal_payment_type = $paypal_data->paypal_payment_type ?? 'manual';
        $stripe_publishable_key = $stripe_data->stripe_publishable_key ?? '';

        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];

        $package_name = $package_data->package_name ?? '';
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $discount_valid = $price_raw_data->discount_valid;

        $product_data = isset($package_data->product_data) && !is_null($package_data->product_data) ? json_decode($package_data->product_data) : null;
        $discount_data = isset($package_data->discount_data) && !is_null($package_data->discount_data) ? json_decode($package_data->discount_data) : null;'';
        $paypal_plan_id = $product_data->paypal->plan_id ?? null;
        $validity_extra_info = $package_data->validity_extra_info ?? '0,D';
        $validity_extra_info = explode(',', $validity_extra_info);

        $package_validity = $package_data->validity ?? 0;

        $cancel_url = route('transaction-log')."?action=cancel";
        $success_url = route('transaction-log')."?action=success";
        $no_payment_found_error = true;

        $user_info = $this->get_user($this->user_id);
        $user_name = isset($user_info->name)? $user_info->name: '';
        $user_email = isset($user_info->email) ? $user_info->email : '';
        $user_mobile = isset($user_info->mobile) ? $user_info->mobile : '012345678901';
        $provider = $this->provider; //using the PaypalClint

        $paypal_button = '';
        if($paypal_status=='1')
        {
            if($paypal_plan_id == ''){
                $this->paypal->provider = $provider;
                $this->paypal->mode = $paypal_mode;
                $this->paypal->paypal_client_id = $paypal_client_id;
                $this->paypal->paypal_client_secret = $paypal_client_secret;
                $this->paypal->paypal_app_id = $paypal_app_id;
                $this->paypal->currency = $currency;
                $this->paypal->product_information = $package_data;
                $paypal_data = $this->paypal->paypal_plan_create();
                if(isset($paypal_data['id'])){
                 $package_data = DB::table('packages')->select('product_data')->where('id',$package_id)->first();
                 $decode_package_data = json_decode($package_data->product_data,true);
                    $product_data = [
                        'paypal' =>[
                            'plan_id'=> $paypal_data['id']
                        ]
                    ];
                    $product_data = json_encode($product_data);
                 $table = DB::table('packages')->where('id',$package_id)->update(['product_data'=>$product_data]);
                }

            }
            $package_data = $this->get_package($id,$select='*',$where=['id'=>$id,'deleted'=>'0','is_default'=>'0']);
            $product_data = isset($package_data->product_data) && !is_null($package_data->product_data) ? json_decode($package_data->product_data) : null;
            $paypal_plan_id = $product_data->paypal->plan_id ?? null;
            $this->paypal->plan_id=$paypal_plan_id;
            $no_payment_found_error = false;
            $this->paypal->mode = $paypal_mode;
            $this->paypal->cancel_url = route('transaction-log')."?action=cancel";
            $this->paypal->success_url = route('paypal-subscription-action',['buyer_user_id'=>$this->user_id,'parent_user_id'=>$this->parent_user_id,'package_id'=>$id]);
            $notify_url = get_domain_only(env('APP_URL'))=='telegram-group.test' ? 'https://ezsoci.com/botsailor-test-ipn/paypal.php' : route('paypal-ipn',$paypal_mode);
            $this->paypal->paypal_client_id = $paypal_client_id;
            $this->paypal->paypal_client_secret = $paypal_client_secret;
            $this->paypal->paypal_app_id = $paypal_app_id;
            $paypal_url = route('payment-paypal-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->paypal->paypal_url = $paypal_url;
            $this->paypal->provider = $provider;
            if($paypal_payment_type == 'recurring')
            {
                $this->paypal->a3 = $payment_amount;
                $this->paypal->p3 = $validity_extra_info[0] ?? '0';
                $this->paypal->t3 = $validity_extra_info[1] ?? 'D';
                $this->paypal->src='1';
                $this->paypal->sra='1';
                $this->paypal->is_recurring=true;
            }
            else $this->paypal->amount=$payment_amount;

            $this->paypal->user_id = $this->user_id;
            $this->paypal->currency = $currency;
            $this->paypal->secondary_button=true;
            $this->paypal->button_lang = __("Pay with PayPal");
            $this->paypal->package_id = $id;
            $this->paypal->product_name = $package_name;
            $paypal_button = $this->paypal->set_button();
            $paypal_button = $paypal_button;
        }

        $stripe_button = '';
        if($stripe_status == '1')
        {
            $no_payment_found_error = false;
            $this->stripe->currency = $currency;
            $this->stripe->amount = $payment_amount;
            $this->stripe->action_url = route('stripe-ipn',['buyer_user_id'=>$this->user_id,'parent_user_id'=>$this->parent_user_id,'package_id'=>$id]);
            $this->stripe->description = $package_name;
            $this->stripe->publishable_key = $stripe_publishable_key;
            $stripe_button = $this->stripe->set_button();
        }

        $razorpay_button = '';
        if($razorpay_status=='1' && !empty($razorpay_status)){
            $no_payment_found_error = false;
            $razorpay_key_id = $razorpay_data->razorpay_key_id;
            $razorpay_key_secret = $razorpay_data->razorpay_key_secret;
            $this->razorpay_class_ecommerce->key_id=$razorpay_key_id;
            $this->razorpay_class_ecommerce->key_secret=$razorpay_key_secret;
            $this->razorpay_class_ecommerce->title=$package_name;
            $this->razorpay_class_ecommerce->description=config("app.name")." : ".$package_name." (".$package_validity." days)";
            $this->razorpay_class_ecommerce->amount=$payment_amount;
            $this->razorpay_class_ecommerce->action_url=route("payment-razorpay_action",[$package_id,$this->user_id,$this->parent_user_id]).'?order_id=';
            $this->razorpay_class_ecommerce->currency=$currency;
            $store_favicon = config('app.logo');
            $this->razorpay_class_ecommerce->img_url=$store_favicon;
            $this->razorpay_class_ecommerce->customer_name=$user_name;
            $this->razorpay_class_ecommerce->customer_email=$user_email;
            $this->razorpay_class_ecommerce->secondary_button=true;
            $this->razorpay_class_ecommerce->button_lang= __('Pay with Razorpay');

            // for action function, because it's not web hook based, it's js based
            session(['razorpay_payment_package_id' => $package_id]);
            session(['razorpay_payment_amount' => $payment_amount]);
            $razorpay_button =  $this->razorpay_class_ecommerce->set_button();
        }

        $paystack_button = '';
        if($paystack_status=='1' && !empty($paystack_status)){
            $no_payment_found_error = false;
            $paystack_secret_key = $paystack_data->paystack_secret_key;
            $paystack_public_key = $paystack_data->paystack_public_key;
            $this->paystack_class_ecommerce->secret_key=$paystack_secret_key;
            $this->paystack_class_ecommerce->public_key=$paystack_public_key;
            $this->paystack_class_ecommerce->title=$package_name;
            $this->paystack_class_ecommerce->description=config("app.name")." : ".$package_name." (".$package_validity." days)";
            $this->paystack_class_ecommerce->amount=$payment_amount;
            $this->paystack_class_ecommerce->action_url=route("payment-paystack-action",[$package_id,$this->user_id,$this->parent_user_id]).'?reference=';
            $this->paystack_class_ecommerce->currency=$currency;
            $this->paystack_class_ecommerce->img_url=config('app.logo');
            $this->paystack_class_ecommerce->customer_first_name=$user_name;
            $this->paystack_class_ecommerce->customer_email=$user_email;
            $this->paystack_class_ecommerce->secondary_button=true;
            $this->paystack_class_ecommerce->button_lang=__("Pay with Paystack");

            // for action function, because it's not web hook based, it's js based
            session(['paystack_payment_package_id' => $package_id]);
            session(['paystack_payment_amount' => $payment_amount]);
            $paystack_button =  $this->paystack_class_ecommerce->set_button();
        }

        $mercadopago_button ='';
        if($mercadopago_status=='1'){
            $no_payment_found_error = false;
            $mercadopago_public_key = $mercadopago_data->mercadopago_public_key;
            $mercadopago_access_token = $mercadopago_data->mercadopago_access_token;
            $mercadopago_country = $mercadopago_data->mercadopago_country;
            $this->mercadopago->public_key=$mercadopago_public_key;
            $this->mercadopago->mercadopago_url = 'https://www.mercadopago.com.'.$mercadopago_country;
            $this->mercadopago->redirect_url=route("payment-mercadopago-action",[$package_id,$this->user_id,$this->parent_user_id]);
            $this->mercadopago->transaction_amount=$payment_amount;
            $this->mercadopago->secondary_button=true;
            $this->mercadopago->button_lang=__('Pay with Mercadopago');

            $mercadopago_button =  $this->mercadopago->set_button();
        }

        $myfatoorah_button ='';
        if($myfatoorah_status=='1'){
            $no_payment_found_error = false;
            $redirect_url_myfatoorah = route('payment-myfatoorah-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->myfatoorah->redirect_url = $redirect_url_myfatoorah;
            $this->myfatoorah->button_lang = __('Pay With myfatoorah');
            $myfatoorah_button = $this->myfatoorah->set_button();
        }

        $toyyibpay_button ='';
        if($toyyibpay_status=='1'){
            $no_payment_found_error = false;
            $redirect_url_toyyibpay = route('payment-toyyibpay-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->toyyibpay->redirect_url = $redirect_url_toyyibpay;
            $this->toyyibpay->button_lang = __('Pay With Paymaya');
            $toyyibpay_button = $this->toyyibpay->set_button();

        }

        $xendit_button ='';
        if($xendit_status=='1'){
            $no_payment_found_error = false;
            $xendit_redirect_url = route('payment-xendit-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->xendit->xendit_redirect_url = $xendit_redirect_url;
            $this->xendit->button_lang = __('Pay With Xendit');
            $xendit_button = $this->xendit->set_button();

        }

        $paymaya_button ='';
        if($paymaya_status=='1'){
            $no_payment_found_error = false;
            $redirect_url_paymaya = route('payment-paymaya-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->paymaya->redirect_url = $redirect_url_paymaya;
            $this->paymaya->button_lang = __('Pay With Paymaya');
            $paymaya_button = $this->paymaya->set_button();
        }

        $mollie_button='';

        if($mollie_status=='1' && !empty($mollie_status)){
            $no_payment_found_error = false;
            $unique_id = $this->user_id.time();
            $mollie_api_key = $mollie_data->mollie_api_key;

            $this->mollie_class_ecommerce->api_key=$mollie_api_key;
            $this->mollie_class_ecommerce->title=$package_name;
            $this->mollie_class_ecommerce->description=config("app.name")." : ".$package_name." (".$package_validity." days)";
            $this->mollie_class_ecommerce->amount=$payment_amount;
            $this->mollie_class_ecommerce->action_url=route("payment-mollie-action",[$package_id,$this->user_id,$this->parent_user_id]).'?reference=';
            $this->mollie_class_ecommerce->currency=$currency;
            $this->mollie_class_ecommerce->img_url=config('app.logo');
            $this->mollie_class_ecommerce->customer_name=$user_name;
            $this->mollie_class_ecommerce->customer_email=$user_email;
            $this->mollie_class_ecommerce->ec_order_id=$unique_id;
            $this->mollie_class_ecommerce->secondary_button=true;
            $this->mollie_class_ecommerce->button_lang=__("Pay with Mollie");

            // for action function, because it's not web hook based, it's js based
            session(['mollie_payment_package_id' => $package_id]);
            session(['mollie_payment_amount' => $payment_amount]);
            session(['mollie_unique_id' => $payment_amount]);
            $mollie_button =  $this->mollie_class_ecommerce->set_button();
        }


        $instamojo_button = '';
        if($instamojo_status == '1')
        {
            $no_payment_found_error = false;
            $redirect_url_instamojo = route('payment-instamojo-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->instamojo->redirect_url = $redirect_url_instamojo;
            $this->instamojo->button_lang = __('Pay With Instamojo');
            $instamojo_button = $this->instamojo->set_button();
        }


         $instamojo_v2_button = '';
        if($instamojo_v2_status == '1')
        {
            $no_payment_found_error = false;
            $redirect_url_instamojo_v2 = route('payment-instamojo-v2-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->instamojo_v2->redirect_url_v2 = $redirect_url_instamojo_v2;
            $this->instamojo_v2->button_lang_v2 = __('Pay With Instamojo v2');
            $instamojo_v2_button = $this->instamojo_v2->set_button_v2();
        }


        $senangpay_button ='';
        if($senangpay_status=='1'){
            $no_payment_found_error = false;
            $senangpay_secret_key =  $senangpay_data->senangpay_secret_key;
            $senangpay_order_id = $package_id.'_'.$this->user_id;
            $hashed_string = hash_hmac('sha256', $senangpay_secret_key.urldecode($package_name).urldecode($payment_amount).urldecode($senangpay_order_id), $senangpay_secret_key);
            $merchant_id =  $senangpay_data->senangpay_merchent_id;
            $this->senangpay->secretkey = $senangpay_secret_key;
            $this->senangpay->merchant_id = $merchant_id;
            $this->senangpay->detail =$package_name;
            $this->senangpay->amount = $payment_amount;
            $this->senangpay->order_id = $senangpay_order_id;
            $this->senangpay->name = $user_name;
            $this->senangpay->email = $user_email;
            $this->senangpay->phone = $user_mobile;
            $this->senangpay->senangpay_mode = $senangpay_mode;
            $this->senangpay->hashed_string = $hashed_string;
            $this->senangpay->secondary_button = true;
            $this->senangpay->button_lang = __('Pay With Senangpay');
            $senangpay_button = $this->senangpay->set_button();

        }

        $flutterwave_button='';

        if($flutterwave_status=='1' && !empty($flutterwave_status)){
            $no_payment_found_error = false;
            $flutterwave_api_key = $flutterwave_data->flutterwave_api_key;
            $redirect_url_flutterwave = route('payment-flutterwave-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->flutterwave->redirect_url_flutterwave = $redirect_url_flutterwave;
            $no_payment_found_error = false;

            $this->flutterwave->button_lang = __('Pay With Flutterwave');
            $flutterwave_button = $this->flutterwave->set_button();
        }


        $sslcommerz_button = '';
        if($sslcommerz_status == '1'){
            $no_payment_found_error = false;
            $sslcommerz_store_id = $sslcommerz_data->sslcommerz_store_id;
            $sslcommerz_store_password = $sslcommerz_data->sslcommerz_store_password;
            $package_data = $this->get_package($package_id);
            $package_name = $package_data->package_name ?? '';
            $payment_amount = $package_data->price ?? 0;
            $postdata_array = [
                'total_amount' => $payment_amount,
                'currency' => $currency,
                'product_name' => $package_name,
                'product_category' => $package_name,
                'cus_name' => $user_name,
                'cus_email' => $user_email,
                'package_id' => $package_id,
                'user_id' => $this->user_id,
            ];
            $endpoint_url = route('payment-sslcommerz-action');
            $sslcommerz_button = '<button class="your-button-class d-none" id="sslczPayBtn"
                                     token="if you have any token validation"
                                     postdata=""
                                     order="If you already have the transaction generated for current order"
                                     endpoint="'.$endpoint_url.'">'. __("Pay With SSLCOMMERZ").'
                               </button>';

            $sslcommerz_button .= "
                                <a href='#' class='list-group-item list-group-item-action flex-column align-items-start' onclick=\"document.getElementById('sslczPayBtn').click();\">
                                    <div class='d-flex w-100 align-items-center'>
                                      <small class='text-muted'><img class='rounded' width='60' height='60' src='".asset('assets/images/sslcommerz.png')."'></small>
                                      <h6 class='mb-1'>".__('Pay With SSLCOMMERZ')."</h6>
                                    </div>
                                </a>";

        }

        $yoomoney_button = '';
        if($yoomoney_status == '1')
        {
            $no_payment_found_error = false;
            $redirect_url_yoomoney = route('payment-yoomoney-action',[$package_id,$this->user_id,$this->parent_user_id]);
            $this->yoomoney->yoomoney_redirect_url = $redirect_url_yoomoney;
            $this->yoomoney->button_lang = __('Pay With YooMoney');
            $yoomoney_button = $this->yoomoney->set_button();
        }

        $manual_payment_button='';
        if($manual_payment_status=='1') {
            $no_payment_found_error = false;
            $manual_payment_button = '
            <div class="col-12 col-md-4 pt-3">
                <a href="" class="list-group-item list-group-item-action flex-column align-items-start" id="manual-payment-button">
                    <div class="d-flex w-100 align-items-center">
                      <small class="text-muted"><img class="rounded" width="60" height="60" src="'.asset('assets/images/manual.png').'"></small>
                      <h5 class="mb-1">'.__("Manual Payment").'</h5>
                    </div>
                </a>
            </div>';;
        }

        $buttons_html = '<div class="row" id="payment_options">';
        if($paypal_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$paypal_button.'</div>';
        if($stripe_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$stripe_button.'</div>';
        if($razorpay_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$razorpay_button.'</div>';
        if($paystack_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$paystack_button.'</div>';
        if($mercadopago_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$mercadopago_button.'</div>';
        if($myfatoorah_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$myfatoorah_button.'</div>';
        if($toyyibpay_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$toyyibpay_button.'</div>';
        if($xendit_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$xendit_button.'</div>';
        if($paymaya_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$paymaya_button.'</div>';
        if($mollie_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$mollie_button.'</div>';
        if($instamojo_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$instamojo_button.'</div>';
        if($instamojo_v2_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$instamojo_v2_button.'</div>';
        if($senangpay_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$senangpay_button.'</div>';
        if($sslcommerz_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$sslcommerz_button.'</div>';
         if($yoomoney_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$yoomoney_button.'</div>';
        if($flutterwave_button != '') $buttons_html .= '<div class="col-12 col-md-4 pt-3">'.$flutterwave_button.'</div>';
        if($manual_payment_button != '') $buttons_html .= $manual_payment_button;
        $buttons_html .= '</div>';

        $data['buttons_html'] = $buttons_html;
        $data['no_payment_found_error'] = $no_payment_found_error;
        $data['payment_config'] = $payment_config;
        $data['buy_package_package_id'] = $package_id;
        $data['currency'] = $currency;
        $data['manual_payment_instruction'] = $manual_payment_instruction;
        $data['currency_list'] = get_country_iso_phone_currency_list("currency_name");
        return $this->viewcontroller($data);
    }

    public function transaction_log()
    {
        if($this->is_manager) abort('403');
        $action = isset($_GET['action']) ? $_GET['action'] : ""; // if redirect after purchase
        if($action!="")
        {
            if($action=="cancel") session(['payment_success' => '0']);
            else if($action=="success") session(['payment_success' => '1']);
        }
        $data = array('body'=>'member/payment/transaction-log','load_datatable'=>true);

        if($this->is_admin) {
            $payment_config = $this->get_payment_config();
            $currency = isset($payment_config->paid_currency) ? $payment_config->paid_currency : "USD";

            $year = date("Y");
            $lastyear = $year - 1;
            $month = date("m");
            $date = date("Y-m-d");

            $user_count = DB::table("users")->where(['parent_user_id' => $this->user_id, 'status' => '1', 'deleted' => '0'])->count();

            $query = DB::table("transaction_logs")
                        ->where('user_id', $this->user_id)
                        ->where('paid_currency',$currency);

            $query->where(function($query) use ($year,$lastyear){
                $query->whereRaw("DATE_FORMAT(paid_at,'%Y')='" . $year."'");
                $query->orWhereRaw("DATE_FORMAT(paid_at,'%Y')='" . $lastyear."'");
            });
            $payment_data = $query->orderByRaw('paid_at DESC')->get();

            $payment_today = $payment_month = $payment_month_previous = $payment_year = $payment_life = 0;
            $array_month = array();
            $array_year = array();
            $this_year_earning = array();
            $last_year_earning = array();
            $this_year_top = array();
            $last_year_top = array();
            $month_names = array();
            for ($m = 1; $m <= 12; ++$m) {
                $name = date('M', mktime(0, 0, 0, $m, 1));
                $month_names[] = __($name);
                $this_year_earning[] = 0;
                $last_year_earning[] = 0;
            }
            foreach ($payment_data as $key => $value) {
                $mon = date("F", strtotime($value->paid_at));
                $mon2 = date("m", strtotime($value->paid_at));

                if (strtotime(date('Y-m-d',strtotime($value->paid_at))) == strtotime($date)) $payment_today += $value->paid_amount;

                if (date("m", strtotime($value->paid_at)) == $month && date("Y", strtotime($value->paid_at)) == $year) {
                    $payment_month += $value->paid_amount;
                    $payment_date = date("jS M y", strtotime($value->paid_at));

                    if (!isset($array_month[$payment_date])) $array_month[$payment_date] = 0;
                    $array_month[$payment_date] += $value->paid_amount;
                }

                $prev_month = Date('m', strtotime(date('F') . " last month"));
                if (date("m", strtotime($value->paid_at)) == $prev_month) {
                    $payment_month_previous += $value->paid_amount;
                    $payment_date = date("jS M y", strtotime($value->paid_at));

                    if (!isset($array_month[$payment_date])) $array_month[$payment_date] = 0;
                    $array_month[$payment_date] += $value->paid_amount;
                }

                if (date("Y", strtotime($value->paid_at)) == $year) {
                    $payment_year += $value->paid_amount;
                    $payment_life += $value->paid_amount;
                    if (!isset($array_year[$mon])) $array_year[$mon] = 0;
                    $array_year[$mon] += $value->paid_amount;

                    if (isset($this_year_earning[$mon2 - 1])) $this_year_earning[$mon2 - 1] += $value->paid_amount;

                    if (!isset($this_year_top[$value->country])) $this_year_top[$value->country] = 0;
                    $this_year_top[$value->country] += $value->paid_amount;
                }
                if (date("Y", strtotime($value->paid_at)) == $lastyear) {
                    if (isset($last_year_earning[$mon2 - 1])) $last_year_earning[$mon2 - 1] += $value->paid_amount;

                    if (!isset($last_year_top[$value->country])) $last_year_top[$value->country] = 0;
                    $last_year_top[$value->country] += $value->paid_amount;
                }
            }
            arsort($this_year_top);
            arsort($last_year_top);

            $user_data = DB::table('users')
                ->where(['parent_user_id'=>$this->user_id])
                ->where('user_type','!=','Team')
                ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m'))"),'=',date('Y-m'))
                ->get();

            $currency_icons = get_country_iso_phone_currency_list('currency_icon');
            $data["currency_icon"] = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
            $data['user_data'] = $user_data;
            $data['user_count'] = $user_count;
            $data['payment_today'] = $payment_today;
            $data['payment_month'] = $payment_month;
            $data['payment_month_previous'] = $payment_month_previous;
            $data['payment_year'] = $payment_year;
            $data['payment_life'] = $payment_life;
            $data['array_month'] = $array_month;
            $data['array_year'] = $array_year;
            $data['month_names'] = $month_names;
            $data['this_year_earning'] = $this_year_earning;
            $data['last_year_earning'] = $last_year_earning;
            $data['year'] = $year;
            $data['lastyear'] = $lastyear;
            $data['this_year_top'] = $this_year_top;
            $data['last_year_top'] = $last_year_top;
            $data['country_names'] = get_country_iso_phone_currency_list('country');
        }

        return $this->viewcontroller($data);
    }

    public function transaction_log_data(Request $request)
    {
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $date_range = !is_null($request->input('date_range')) ? $request->input('date_range') : '';
        $display_columns = array("#","CHECKBOX",'id','buyer_email','first_name', 'last_name','payment_method', 'paid_amount', 'package_name', 'billing_cycle','paid_at');
        if(Auth::user()->parent_user_id || $this->is_admin) array_push($display_columns,'invoice_url');
        $search_columns = array('buyer_email', 'first_name','last_name','transaction_id');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 10;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'paid_at';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $from_date = $to_date = "";
        if($date_range!="")
        {
            $exp = explode('-', $date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
        }

        $user_id = $this->user_id;
        $table="transaction_logs";
        $select= ["transaction_logs.*"];
        $query = DB::table($table)->select($select);
        if($from_date!='') $query->where('paid_at','>=',$from_date);
        if($to_date!='') $query->where('paid_at','<=',$to_date);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $query->where(function($query) use ($user_id){
            $query->orWhere('transaction_logs.user_id', '=', $user_id);
            $query->orWhere('buyer_user_id', '=', $user_id);
        });
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select('transaction_logs.id');
        if($from_date!='') $query->where('paid_at','>=',$from_date);
        if($to_date!='') $query->where('paid_at','<=',$to_date);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $query->where(function($query) use ($user_id){
            $query->orWhere('transaction_logs.user_id', '=', $user_id);
            $query->orWhere('buyer_user_id', '=', $user_id);
        });
        $total_result = $query->count();

        $currency_icons = get_country_iso_phone_currency_list('currency_icon');

        foreach ($info as $key => $value)
        {
            $value->billing_cycle = convert_datetime_to_timezone($value->cycle_start_date,'','','jS M y')." - ".convert_datetime_to_timezone($value->cycle_expired_date,'','','jS M y');
            $value->paid_at = convert_datetime_to_timezone($value->paid_at);
            $curreny_icon = isset($currency_icons[$value->paid_currency]) ? $currency_icons[$value->paid_currency] : $value->paid_currency;
            $paid_amount = number_format($value->paid_amount,2,'.','');
            $style = ($value->buyer_user_id==$this->user_id) ? "<span class='text-danger'>" :  "<span class='text-success'>";
            $sign = ($value->buyer_user_id==$this->user_id) ? "-" : "+";
            $value->paid_amount = $style.$sign.' '.$curreny_icon.''.$paid_amount."</span>";
            if(Auth::user()->parent_user_id || $this->is_admin){
                $value->invoice_url = "<a target='_BLANK' href='".$value->invoice_url."'>".__('Invoice')."</a>";
            }
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function list_payment_api_log_data(Request $request)
    {
        if(!$this->is_admin) abort(403);
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $date_range = !is_null($request->input('date_range')) ? $request->input('date_range') : '';
        $display_columns = array("#","CHECKBOX",'id','buyer_email','buyer_username','seller_email','seller_username','payment_method', 'call_time', 'request_data', 'error',);
        $search_columns = array('buyer_users.email', 'seller_users.email','payment_method');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 2;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $from_date = $to_date = "";
        if($date_range!="")
        {
            $exp = explode('-', $date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
        }

        $table="payment_api_logs";
        $select= ["payment_api_logs.*","buyer_users.parent_user_id as buyer_parent_user_id","buyer_users.name as buyer_username","buyer_users.email as buyer_email","seller_users.id as seller_user_id","seller_users.name as seller_username","seller_users.email as seller_email"];
        $query = DB::table($table)->select($select);
        $query->leftJoin('users as buyer_users','buyer_users.id','=','payment_api_logs.buyer_user_id');
        $query->leftJoin('users as seller_users','seller_users.id','=','buyer_users.parent_user_id');
        if($from_date!='') $query->where('call_time','>=',$from_date);
        if($to_date!='') $query->where('call_time','<=',$to_date);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select('payment_api_logs.id');
        $query->leftJoin('users as buyer_users','buyer_users.id','=','payment_api_logs.buyer_user_id');
        $query->leftJoin('users as seller_users','seller_users.id','=','buyer_users.parent_user_id');
        if($from_date!='') $query->where('call_time','>=',$from_date);
        if($to_date!='') $query->where('call_time','<=',$to_date);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $total_result = $query->count();

        foreach ($info as $key => $value)
        {
            $value->call_time = convert_datetime_to_timezone($value->call_time);
            $value->request_data = "<textarea class='hide-scroll payment-api-style1'>".$value->api_response."</textarea>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function usage_log()
    {
        if($this->is_admin) abort('403');
        $data = $this->get_usage_log_data();
        $data['body'] = 'member/payment/usage-log';
        return $this->viewcontroller($data);
    }

    protected function get_usage_log_data(){
        if($this->is_admin) return [];
        $current_month = date("n");
        $current_year = date("Y");
        // getting count of fixed modules usage
        $fixed_usage_log = DB::table('usage_logs')->select('usage_logs.*',DB::raw('sum(usage_count) as usage_count'))
            ->leftJoin('modules','modules.id','=','usage_logs.module_id')
            ->where(['user_id'=>$this->user_id,'extra_text'=>''])
            ->groupByRaw('module_id')->get();

        // getting count of monthly modules usage
        $monthly_usage_log = DB::table('usage_logs')->select('usage_logs.*',DB::raw('sum(usage_count) as usage_count'))
            ->leftJoin('modules','modules.id','=','usage_logs.module_id')
            ->where(['user_id'=>$this->user_id,'usage_month' => $current_month,'usage_year' => $current_year])
            ->where('extra_text','!=','')
            ->groupByRaw('module_id')->get();


        $usage_info = [];
        foreach ($fixed_usage_log as $key=>$value){
            $usage_info[$value->module_id] = (array) $value;
        }
        foreach ($monthly_usage_log as $key=>$value){
            $usage_info[$value->module_id] = (array) $value;
        }
        
        $get_modules = $this->get_modules();
        $modules = [];
        foreach ($get_modules as $key=>$value){
            $modules[$value->id] = $value;
        }

        $package_info = $this->get_package();

        $monthly_limit='';
        $bulk_limit='';

        if(isset($package_info->monthly_limit))  $monthly_limit=$package_info->monthly_limit;
        if(isset($package_info->bulk_limit))  $bulk_limit=$package_info->bulk_limit;
        $package_name= __("No Package");
        if(isset($package_info->package_name))  $package_name=$package_info->package_name;
        $validity="0";
        if(isset($package_info->validity))  $validity=$package_info->validity;
        $price="0";
        if(isset($package_info->price))  $price=$package_info->price;
        $limit_subscriber_agent = 0;
        if(isset($package_info->subscriber_limit))  $limit_subscriber_agent=$package_info->subscriber_limit;
        $limit_user_agent = 0;
        if(isset($package_info->user_limit))  $limit_user_agent=$package_info->user_limit;
        $user_count = 0;
        $team_count = 0;


        $data['limit_subscriber_agent']=$limit_subscriber_agent;
        $data['limit_user_agent']=$limit_user_agent;
        $data['user_count']=$user_count;
        $data['team_count']=$team_count;
        $data['usage_info']=$usage_info;
        $data['modules']=$modules;
        $data['monthly_limit'] = json_decode($monthly_limit,true);
        $data['bulk_limit'] = json_decode($bulk_limit,true);
        $data['package_name']=$package_name;
        $data['validity']=$validity;
        $data['price']=$price;
        $data['message_used'] = null;
        $config_data = $this->get_payment_config();
        $currency = $config_data->currency ?? "USD";
        $currency_icons =  get_country_iso_phone_currency_list('currency_icon');
        $data["currency"]=$currency;
        $data["curency_icon"]= isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
        return $data;
    }

    public function notification_mark_seen(Request $request)
    {
        $id = $request->id;
        $user_id = $this->user_id;

        $notification_info = DB::table("notifications")->where(['id'=>$id])->first();
        if(empty($notification_info))
        {
            return response()->json(array("status"=>"0","message"=>__("No data found")));
        }

        if ($notification_info->user_id != '0' && $notification_info->user_id!=$user_id)
        {
            return response()->json(array("status"=>"0","message"=>__("Access denied")));
        }

        if ($notification_info->user_id != '0')
        {
            $data =
                array
                (
                    'is_seen' => '1',
                    'last_seen_at' => date('Y-m-d H:i:s')
                );
            DB::table("notifications")->where(array('id' => $id))->update($data);
        }
        else
        {
            $data = array('last_seen_at' => date('Y-m-d H:i:s'));
            $temp = explode(',', $notification_info->seen_by);
            array_push($temp, $user_id);
            $temp = array_unique($temp);
            $temp = implode(',', $temp);
            $data['seen_by'] = trim($temp,',');
            DB::table("notifications")->where(array('id' => $id))->update($data);
        }
        return response()->json(array("status"=>"1","message"=>__("Notification has been marked as seen successfully.")));
    }

    public function payment_settings($ecommerce_store_id=0,$whatsapp_bot_id=0)
    {
        if(!$this->is_admin) abort('403');
        $data['body'] = 'member/settings/payment-settings';
        $data['ecommerce_store_id'] = 0;
        $data['whatsapp_bot_id'] = 0;
        
        $data['xdata'] = DB::table('settings_payments')->where(['user_id'=>$this->user_id])->whereNull('ecommerce_store_id')->whereNull('whatsapp_bot_id')->first();        

        $data['iframe'] = $ecommerce_store_id==0 ? false : true;
        $data['load_datatable'] = true;
        return $this->viewcontroller($data);
    }

    public function payment_settings_action(Request $request)
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));
        if(!$this->is_admin) abort('403');

        if(check_build_version()=='double'){
            $rules =
            [
                'currency' => 'required',
                'paypal_client_id' => 'required_if:paypal_status,1',
                'paypal_client_secret' => 'required_if:paypal_status,1',
                'stripe_secret_key' => 'required_if:stripe_status,1',
                'stripe_publishable_key' => 'required_if:stripe_status,1',
                'razorpay_key_id' => 'required_if:razorpay_status,1',
                'razorpay_key_secret' => 'required_if:razorpay_status,1',
                'paystack_secret_key' => 'required_if:paystack_status,1',
                'paystack_public_key' => 'required_if:paystack_status,1',
                'mercadopago_public_key' => 'required_if:mercadopago_status,1',
                'mercadopago_access_token' => 'required_if:mercadopago_status,1',
                'mercadopago_country' => 'required_if:mercadopago_status,1',
                'mollie_api_key' => 'required_if:mollie_status,1',
                'sslcommerz_store_id' => 'required_if:sslcommerz_status,1',
                'sslcommerz_store_password' => 'required_if:sslcommerz_status,1',
                'senangpay_merchent_id' => 'required_if:senangpay_status,1',
                'senangpay_secret_key' => 'required_if:senangpay_status,1',
                'instamojo_api_key' => 'required_if:instamojo_status,1',
                'instamojo_auth_token' => 'required_if:instamojo_status,1',
                'instamojo_client_id' => 'required_if:instamojo_v2_status,1',
                'instamojo_client_secret' => 'required_if:instamojo_v2_status,1',
                'toyyibpay_secret_key' => 'required_if:toyyibpay_status,1',
                'toyyibpay_category_code' => 'required_if:toyyibpay_status,1',
                'xendit_secret_api_key' => 'required_if:xendit_status,1',
                'myfatoorah_api_key' => 'required_if:myfatoorah_status,1',
                'paymaya_public_key' => 'required_if:paymaya_status,1',
                'paymaya_secret_key' => 'required_if:paymaya_status,1',
                'yoomoney_shop_id' => 'required_if:yoomoney_status,1',
                'yoomoney_secret_key' => 'required_if:yoomoney_status,1',
                'currency_position' => 'required|string',
                'decimal_point' => 'required|integer|min:0',
                'manual_payment_status' => 'nullable|sometimes|boolean',
                'manual_payment_instruction' => 'required_if:manual_payment_status,1|nullable|sometimes',
                'flutterwave_api_key' => 'required_if:flutterwave_status,1'
            ];
        }
        else {
            $rules =
            [
                'currency' => 'required',
                'currency_position' => 'required|string',
                'decimal_point' => 'required|integer|min:0',
                'manual_payment_status' => 'nullable|sometimes|boolean',
                'manual_payment_instruction' => 'required_if:manual_payment_status,1|nullable|sometimes'
            ];
        }

        $validate_data = $request->validate($rules);

        $validate_data['paypal_mode'] = isset($_POST['paypal_mode']) ? "sandbox" : "live";
        $validate_data['paypal_status'] = isset($_POST['paypal_status']) ? "1" : "0";
        $validate_data['paypal_app_id'] = '';
        if($validate_data['paypal_status']=='1'){
            $paypal_app_id_result = $this->paypal->paypal_get_app_id($validate_data['paypal_client_id'],$validate_data['paypal_client_secret'],$validate_data['paypal_mode']);
            if(isset($paypal_app_id_result->error)){
                  session()->flash('paypal_error', $paypal_app_id_result->error_description);
                  return redirect()->back();
            }
            else{
                $paypal_app_id = $paypal_app_id_result->app_id;
                $validate_data['paypal_app_id'] = $paypal_app_id;
            }
        }
        $validate_data['stripe_status'] = isset($_POST['stripe_status']) ? "1" : "0";
        $validate_data['razorpay_status'] = isset($_POST['razorpay_status']) ? "1" : "0";
        $validate_data['paystack_status'] = isset($_POST['paystack_status']) ? "1" : "0";
        $validate_data['mercadopago_status'] = isset($_POST['mercadopago_status']) ? "1" : "0";
        $validate_data['mollie_status'] = isset($_POST['mollie_status']) ? "1" : "0";
        $validate_data['sslcommerz_status'] = isset($_POST['sslcommerz_status']) ? "1" : "0";
        $validate_data['senangpay_status'] = isset($_POST['senangpay_status']) ? "1" : "0";
        $validate_data['instamojo_status'] = isset($_POST['instamojo_status']) ? "1" : "0";
        $validate_data['instamojo_v2_status'] = isset($_POST['instamojo_v2_status']) ? "1" : "0";
        $validate_data['toyyibpay_status'] = isset($_POST['toyyibpay_status']) ? "1" : "0";
        $validate_data['xendit_status'] = isset($_POST['xendit_status']) ? "1" : "0";
        $validate_data['myfatoorah_status'] = isset($_POST['myfatoorah_status']) ? "1" : "0";
        $validate_data['paymaya_status'] = isset($_POST['paymaya_status']) ? "1" : "0";
        $validate_data['yoomoney_status'] = isset($_POST['yoomoney_status']) ? "1" : "0";
        $validate_data['flutterwave_status'] = isset($_POST['flutterwave_status']) ? "1" : "0";
        $validate_data['manual_payment_status'] = isset($_POST['manual_payment_status']) ? "1" : "0";
        $validate_data['manual_payment_instruction'] = isset($_POST['manual_payment_instruction']) ? $_POST['manual_payment_instruction'] : "";
        $validate_data['cod_enabled'] = isset($_POST['cod_enabled']) ? "1" : "0";

        $validate_data['paypal_payment_type'] = isset($_POST['paypal_payment_type']) ? "recurring" : "manual";
        $validate_data['sslcommerz_mode'] = isset($_POST['sslcommerz_mode']) ? "sandbox" : "live";
        $validate_data['senangpay_mode'] = isset($_POST['senangpay_mode']) ? "sandbox" : "live";
        $validate_data['instamojo_mode'] = isset($_POST['instamojo_mode']) ? "sandbox" : "live";
        $validate_data['instamojo_v2_mode'] = isset($_POST['instamojo_v2_mode']) ? "sandbox" : "live";
        $validate_data['toyyibpay_mode'] = isset($_POST['toyyibpay_mode']) ? "sandbox" : "live";
        $validate_data['myfatoorah_mode'] = isset($_POST['myfatoorah_mode']) ? "sandbox" : "live";
        $validate_data['paymaya_mode'] = isset($_POST['paymaya_mode']) ? "sandbox" : "live";
        $validate_data['thousand_comma'] = isset($_POST['thousand_comma']) ? "1" : "0";
        

        $insert_data =
            array
            (
                'updated_at'=>date('Y-m-d H:i:s'),
                'manual_payment_status'=>$validate_data['manual_payment_status'],
                'manual_payment_instruction'=>$validate_data['manual_payment_instruction'],
                'currency'=>$validate_data['currency'],
                'decimal_point'=>$validate_data['decimal_point'],
                'thousand_comma'=>$validate_data['thousand_comma'],
                'currency_position'=>$validate_data['currency_position'],
                'user_id'=>$this->user_id,
                'cod_enabled' => $validate_data['cod_enabled'],
                'manual_payment_status' => $validate_data['manual_payment_status'],
                'manual_payment_instruction' => $validate_data['manual_payment_instruction'],

            );
        if(check_build_version()=='double'){
            $insert_data['yoomoney'] = json_encode(['yoomoney_shop_id'=>$validate_data['yoomoney_shop_id'],'yoomoney_secret_key'=>$validate_data['yoomoney_secret_key'],'yoomoney_status'=>$validate_data['yoomoney_status']]);
            $insert_data['paypal'] = json_encode(['paypal_client_id'=>$validate_data['paypal_client_id'],'paypal_client_secret'=>$validate_data['paypal_client_secret'],'paypal_app_id'=>$validate_data['paypal_app_id'],'paypal_status'=>$validate_data['paypal_status'],'paypal_mode'=>$validate_data['paypal_mode'],'paypal_payment_type'=>$validate_data['paypal_payment_type']]);
            $insert_data['stripe'] = json_encode(['stripe_secret_key'=>$validate_data['stripe_secret_key'],'stripe_publishable_key'=>$validate_data['stripe_publishable_key'],'stripe_status'=>$validate_data['stripe_status']]);
            $insert_data['razorpay'] = json_encode(['razorpay_key_id'=>$validate_data['razorpay_key_id'],'razorpay_key_secret'=>$validate_data['razorpay_key_secret'],'razorpay_status'=>$validate_data['razorpay_status']]);
            $insert_data['paystack'] = json_encode(['paystack_secret_key'=>$validate_data['paystack_secret_key'],'paystack_public_key'=>$validate_data['paystack_public_key'],'paystack_status'=>$validate_data['paystack_status']]);
            $insert_data['mercadopago'] = json_encode(['mercadopago_public_key'=>$validate_data['mercadopago_public_key'],'mercadopago_access_token'=>$validate_data['mercadopago_access_token'],'mercadopago_country'=>$validate_data['mercadopago_country'],'mercadopago_status'=>$validate_data['mercadopago_status']]);
            $insert_data['mollie'] = json_encode(['mollie_api_key'=>$validate_data['mollie_api_key'],'mollie_status'=>$validate_data['mollie_status']]);
            $insert_data['instamojo'] = json_encode(['instamojo_api_key'=>$validate_data['instamojo_api_key'],'instamojo_auth_token'=>$validate_data['instamojo_auth_token'],'instamojo_status'=>$validate_data['instamojo_status'],'instamojo_mode'=>$validate_data['instamojo_mode']]);
            $insert_data['instamojo_v2'] = json_encode(['instamojo_client_id'=>$validate_data['instamojo_client_id'],'instamojo_client_secret'=>$validate_data['instamojo_client_secret'],'instamojo_v2_status'=>$validate_data['instamojo_v2_status'],'instamojo_v2_mode'=>$validate_data['instamojo_v2_mode']]);
            $insert_data['sslcommerz'] = json_encode(['sslcommerz_store_id'=>$validate_data['sslcommerz_store_id'],'sslcommerz_store_password'=>$validate_data['sslcommerz_store_password'],'sslcommerz_status'=>$validate_data['sslcommerz_status'],'sslcommerz_mode'=>$validate_data['sslcommerz_mode']]);
            $insert_data['senangpay'] = json_encode(['senangpay_merchent_id'=>$validate_data['senangpay_merchent_id'],'senangpay_secret_key'=>$validate_data['senangpay_secret_key'],'senangpay_status'=>$validate_data['senangpay_status'],'senangpay_mode'=>$validate_data['senangpay_mode']]);
            $insert_data['toyyibpay'] = json_encode(['toyyibpay_secret_key'=>$validate_data['toyyibpay_secret_key'],'toyyibpay_category_code'=>$validate_data['toyyibpay_category_code'],'toyyibpay_status'=>$validate_data['toyyibpay_status'],'toyyibpay_mode'=>$validate_data['toyyibpay_mode']]);
            $insert_data['xendit'] = json_encode(['xendit_secret_api_key'=>$validate_data['xendit_secret_api_key'],'xendit_status'=>$validate_data['xendit_status']]);
            $insert_data['myfatoorah'] = json_encode(['myfatoorah_api_key'=>$validate_data['myfatoorah_api_key'],'myfatoorah_status'=>$validate_data['myfatoorah_status'],'myfatoorah_mode'=>$validate_data['myfatoorah_mode']]);
            $insert_data['paymaya'] = json_encode(['paymaya_public_key'=>$validate_data['paymaya_public_key'],'paymaya_secret_key'=>$validate_data['paymaya_secret_key'],'paymaya_status'=>$validate_data['paymaya_status'],'paymaya_mode'=>$validate_data['paymaya_mode']]);

            $insert_data['flutterwave'] = json_encode(['flutterwave_api_key'=>$validate_data['flutterwave_api_key'],'flutterwave_status'=>$validate_data['flutterwave_status']]);
        }
    
        $update_data = $insert_data;
        
        $xpayment_settings = $this->get_payment_config();
        $id = $xpayment_settings->id ?? 0;
        if($id>0) $query = DB::table('settings_payments')->where(['id'=>$id])->update($insert_data);
        else $query = DB::table('settings_payments')->insert($insert_data);
        

        $request->session()->flash('save_payment_accounts_status', '1');
        $params = ['ecommerce_store_id'=>0];       
        return redirect(route('payment-settings',$params));
    }

    public function general_settings()
    {
        if($this->is_manager) abort('403');
        $data['body'] = 'member/settings/general-settings';
        $data['load_datatable'] = true;
        $xdata = DB::table('settings')->where('user_id',$this->user_id)->first();
        $data['xdata'] = $xdata;
        $data['xdata_user'] = Auth::user();
        $data['language_list'] =$this->get_available_language_list();

        $autoresponder_info = $this->get_autoresponder_list();

        $dropdown_values = array();
        $i=0;
        foreach($autoresponder_info as $key => $value)
        {
            $dropdown_values[$value->api_name][$value->settings_email_autoresponder_id]["api_name"] = $value->api_name;
            $dropdown_values[$value->api_name][$value->settings_email_autoresponder_id]["profile_name"] = $value->profile_name;
            $dropdown_values[$value->api_name][$value->settings_email_autoresponder_id]["data"][$i]["list_name"] = $value->list_name;
            $dropdown_values[$value->api_name][$value->settings_email_autoresponder_id]["data"][$i]["list_id"] = $value->list_id;
            $dropdown_values[$value->api_name][$value->settings_email_autoresponder_id]["data"][$i]["table_id"] = $value->id;
            $i++;
        }

        foreach ($this->availatble_autoresponder_names as $key=>$value){
            if(!isset($dropdown_values[$value])) $dropdown_values[$value] = [];
        }

        $xauto_responder_settings = isset($xdata->auto_responder_signup_settings) ? json_decode($xdata->auto_responder_signup_settings) : [];

        if(!empty($xauto_responder_settings))
        foreach($xauto_responder_settings as $key => $value) if(!empty($value)) $dropdown_values[$key]["selected"] = $value;

        $data['autoresponser_dropdown_values'] = $dropdown_values;
        return $this->viewcontroller($data);
    }

    public function general_settings_action(Request $request)
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));
        if($this->is_manager) abort('403');
        $rules =
        [
            'default_email' => 'nullable|sometimes',
            'sender_email' => 'required|email',
            'sender_name' => 'required'
        ];
       
        $rules['logo'] = 'nullable|sometimes|image|mimes:png,jpg,jpeg,webp|max:1024';
        $rules['logo_alt'] = 'nullable|sometimes|image|mimes:png,jpg,jpeg,webp|max:1024';
        $rules['favicon'] = 'nullable|sometimes|image|mimes:png,jpg,jpeg,webp|max:100';
        $rules['app_name'] = 'required';
        $rules['timezone'] = 'required';
        $rules['language'] = 'required';

        $rules['fb_pixel_id'] = 'nullable|sometimes';
        $rules['google_analytics_id'] = 'nullable|sometimes';

        $validate_data = $request->validate($rules);
        $insert_data = ['updated_at'=>date('Y-m-d H:i:s')];

        if ($request->file('logo')) {

            $file = $request->file('logo');
            $extension = $request->file('logo')->getClientOriginalExtension();
            $filename =  $this->is_admin ? 'logo.' . $extension : $this->user_id . '-logo.' . $extension;
            $upload_dir_subpath = 'public/agency';

            if(env('AWS_UPLOAD_ENABLED')){
               try {
                   $upload2S3 = Storage::disk('s3')->putFileAs('agency', $file,$filename);
                   $insert_data['logo'] = Storage::disk('s3')->url($upload2S3);
               }
               catch (\Exception $e){
                   $error_message = $e->getMessage();
               }
            }
            else{
                $request->file('logo')->storeAs(
                    $upload_dir_subpath, $filename
                );
                $insert_data['logo'] = asset('storage/agency').'/'.$filename;
            }

        }

        if ($request->file('logo_alt')) {

            $file = $request->file('logo_alt');
            $extension = $request->file('logo_alt')->getClientOriginalExtension();
            $filename =  $this->is_admin ? 'logo-white.' . $extension : $this->user_id . '-logo-white.' . $extension;
            $upload_dir_subpath = 'public/agency';

            if(env('AWS_UPLOAD_ENABLED')){
               try {
                   $upload2S3 = Storage::disk('s3')->putFileAs('agency', $file,$filename);
                   $insert_data['logo_alt'] = Storage::disk('s3')->url($upload2S3);
               }
               catch (\Exception $e){
                   $error_message = $e->getMessage();
               }
            }
            else{
                $request->file('logo_alt')->storeAs(
                    $upload_dir_subpath, $filename
                );
                $insert_data['logo_alt'] = asset('storage/agency').'/'.$filename;
            }

        }

        if ($request->file('favicon')) {

            $file = $request->file('favicon');
            $extension = $request->file('favicon')->getClientOriginalExtension();
            $filename =  $this->is_admin ? 'favicon.' . $extension : $this->user_id . '-favicon.' . $extension;
            $upload_dir_subpath = 'public/agency';

            if(env('AWS_UPLOAD_ENABLED')){
               try {
                   $upload2S3 = Storage::disk('s3')->putFileAs('agency', $file,$filename);
                   $insert_data['favicon'] = Storage::disk('s3')->url($upload2S3);
               }
               catch (\Exception $e){
                   $error_message = $e->getMessage();
               }
            }
            else{
                $request->file('favicon')->storeAs(
                    $upload_dir_subpath, $filename
                );
                $insert_data['favicon'] = asset('storage/agency').'/'.$filename;
            }
        }

        $insert_data['app_name'] = $validate_data['app_name'];

        $upload_settings = [
            'bot' => [
                'image' => 0,
                'video' => 0,
                'audio' => 0,
                'file' => 0
            ]
        ];
        $insert_data['upload_settings'] = json_encode($upload_settings);

        $analytics_code_data = [
            'fb_pixel_id' => $validate_data['fb_pixel_id'],
            'google_analytics_id' => $validate_data['google_analytics_id'],
            'tme_widget_id' => '',
            'whatsapp_widget_id' =>'',
        ];

        $insert_data['analytics_code'] = json_encode($analytics_code_data);

        
        $insert_data['timezone'] = $validate_data['timezone'];
        $insert_data['language'] = $validate_data['language'];        

        // we are using sender_email + sender_name from settings, they are set in custom.php
        $email_settings = [
            'default' => $validate_data['default_email'] ?? null,
            'sender_email' => null,
            'sender_name' => null
        ];
        if(isset($validate_data['default_email']) && !empty($validate_data['default_email'])){
            $email_settings['sender_email'] = $validate_data['sender_email'] ?? null;
            $email_settings['sender_name'] = $validate_data['sender_name'] ?? null;
        }
        $insert_data['email_settings'] = json_encode($email_settings);

        $insert_auto_responder_signup_settings = [];
        if(!$this->is_member){
            $auto_responder_signup_settings = $request->auto_responder_signup_settings;
            $i=0;
            if(!empty($auto_responder_signup_settings))
            foreach ($auto_responder_signup_settings as $key=>$value){
                $exp = explode('-',$value);
                $settings_id = $exp[0] ?? 0;
                $api_name = $exp[1] ?? 0;
                if(!isset($insert_auto_responder_signup_settings[$api_name])) $insert_auto_responder_signup_settings[$api_name] = [];
                array_push($insert_auto_responder_signup_settings[$api_name],$settings_id);
            }
            foreach ($this->availatble_autoresponder_names as $key=>$value) if(!isset($insert_auto_responder_signup_settings[$value])) $insert_auto_responder_signup_settings[$value] = [];
            $insert_data['auto_responder_signup_settings'] = json_encode($insert_auto_responder_signup_settings);
        }

        $update_data = $insert_data;
        $insert_data['user_id'] = $this->user_id;

        $query = DB::table('settings')->upsert($insert_data,['user_id'],$update_data);

        $request->session()->flash('save_agency_account_status', '1');
        return redirect(route('general-settings'));
    }

    public function set_session_active_tab(Request $request){
        $link_id = $request->link_id;
        session(['general_settings_active_tab_id' => $link_id]);
    }

    public function api_settings_data(Request $request) // works for both email+sms and autoresponder
    {
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $is_autoresponder = $request->is_autoresponder ?? '0';
        $is_sms = $request->is_sms ?? '0';
        $is_thirdparty_api = $request->is_thirdparty_api ?? '0';
        $display_columns = array("#","CHECKBOX",'profile_name','api_name','updated_at','actions');
        $search_columns = array('profile_name');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 4;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'updated_at';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $user_id = $this->user_id;
        $table = $is_autoresponder=='0' ? "settings_sms_emails" : "settings_email_autoresponders";
        $where = ['user_id'=>$user_id];

        if($is_autoresponder=='0' && $is_thirdparty_api=='0'){
            if($is_sms=='0') $where['api_type'] = 'email';
            else $where['api_type'] = 'sms';
        }

        $query = DB::table($table)->select('*')->where($where);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select('id')->where($where);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $total_result = $query->count();

        $sms_email_class = ($is_sms=='1') ? ' is_sms' : '';
        $sms_email_class = ($is_thirdparty_api=='1') ? ' is_thirdparty_api' : $sms_email_class;
        $edit_class = $is_autoresponder=='0' ? 'update-api-settings-row'.$sms_email_class : 'update-api-settings-row is_autoresponder';
        $delete_route = $is_autoresponder=='0' ? route('delete-api-settings-action') : route('delete-email-auto-settings-action');
        $delete_route = $is_thirdparty_api=='1' ? route('delete-thirdparty-api-settings-action') : $delete_route;

        $table_name = $is_sms=='0' ? 'table' : 'table3';
        $table_name = $is_thirdparty_api=='1' ? 'table4' : $table_name;
        $datatable_name = $is_autoresponder=='0' ? $table_name : 'table2';

        $icon = $is_autoresponder=='0' ? '<i class="fa fa-edit"></i>' : '<i class="fa fa-sync"></i>';
        foreach ($info as $key => $value)
        {
            $value->updated_at = date("jS M y H:i:s",strtotime($value->updated_at));
            $str="";
            if(config('app.is_demo')!='1')
            $str=$str."<a class='btn btn-circle btn-outline-warning ".$edit_class."' data-id='".$value->id."' href='#'>".$icon."</a>";
            $str=$str."&nbsp;<a href='".$delete_route."' data-id='".$value->id."' data-table-name='".$datatable_name."' class='delete-row btn btn-circle btn-outline-danger'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->actions = "<div class='min-width-50px'>".$str."</div>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }


    public function save_api_settings(Request $request,AutoResponderServiceInterface $autoresponder_service) // works for both email/sms and autoresponder (insert+update)
    {
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $form_data = $request->all();
        $form_data_pass = $form_data;
        $not_required = ['encryption'];
        $missing_input = false;

        foreach ($form_data as $key=>$value)
        {
            if( ($value=='' || is_null($value)) && !in_array($key,$not_required)) {
                $missing_input = true;
                break;
            }
        }
        if($missing_input) return response()->json(['error' => true,'message' => __('Please fill the required fields.')]);

        $is_autoresponder = $form_data['is_autoresponder'] ?? '0';
        $is_sms = $form_data['is_sms'] ?? '0';
        $is_thirdparty_api = $form_data['is_thirdparty_api'] ?? '0';
        $update_id = $form_data['update_id'] ?? 0;
        $profile_name = $form_data['profile_name'] ?? '';
        $api_name = $form_data['api_name'] ?? '';
        if(isset($form_data['update_id'])) unset($form_data['update_id']);
        if(isset($form_data['profile_name'])) unset($form_data['profile_name']);
        if(isset($form_data['api_name'])) unset($form_data['api_name']);
        if(isset($form_data['is_autoresponder'])) unset($form_data['is_autoresponder']);
        if(isset($form_data['is_sms'])) unset($form_data['is_sms']);
        if(isset($form_data['is_thirdparty_api'])) unset($form_data['is_thirdparty_api']);

        $insert_data['api_name'] = $api_name;
        $insert_data['profile_name'] = $profile_name;
        $insert_data['settings_data'] = json_encode($form_data);
        $insert_data['updated_at'] = date("Y-m-d H:i:s");

        $table = 'settings_sms_emails';
        if($is_autoresponder=='0'){
            if($is_sms=='1') $insert_data['api_type'] = 'sms';
            else $insert_data['api_type'] = 'email';
        }
        if($is_autoresponder=='1'){
            $response = $this->upsert_autoresponser_data($form_data_pass,$update_id,$autoresponder_service);
            return response()->json($response);
        }
        else
        {
            if($update_id==0){
                $insert_data['user_id'] = $this->user_id;
                DB::table($table)->insert($insert_data);
            }
            else DB::table($table)->where(['id'=>$update_id,'user_id'=>$this->user_id])->update($insert_data);
            return response()->json(['error' => false,'message' => __('Data has been saved successfully.')]);
        }

    }

    protected function upsert_autoresponser_data($form_data=[],$update_id=0,$autoresponder_service){
        $now = date("Y-m-d H:i:s");
        $update_id = $form_data['update_id'] ?? 0;
        $profile_name = $form_data['profile_name'] ?? '';
        $api_name = $form_data['api_name'] ?? 'mailchimp';

        if(isset($form_data['update_id'])) unset($form_data['update_id']);
        if(isset($form_data['profile_name'])) unset($form_data['profile_name']);
        if(isset($form_data['api_name'])) unset($form_data['api_name']);
        if(isset($form_data['is_autoresponder'])) unset($form_data['is_autoresponder']);

        $insert_data['api_name'] = $api_name;
        $insert_data['profile_name'] = $profile_name;
        $insert_data['settings_data'] = json_encode($form_data);
        $insert_data['updated_at'] = $now;

        if($api_name=='mailchimp') {
            $response = $autoresponder_service->mailchimp_segment_list($form_data['api_key'] ?? '');
            $catch_error = $response['detail'] ?? '';

        }
        else if($api_name=='sendinblue') {
            $response = $autoresponder_service->sendinblue_segment_list($form_data['api_key'] ?? '');
            $catch_error = $response['message'] ?? '';
        }
        else if($api_name=='activecampaign')  $response = $autoresponder_service->activecampaign_segment_list($form_data['api_url'] ?? '',$form_data['api_key'] ?? '');
        else if($api_name=='mautic') {
            $username = $form_data['username'] ?? '';
            $password = $form_data['password'] ?? '';
            $base64 = base64_encode($username . ":" . $password);
            $response = $autoresponder_service->mautic_segment_list($base64, $form_data['base_url'] ?? '');
        }
        else if($api_name=='acelle') $response = $autoresponder_service->acelle_segment_list($form_data['api_Key'] ?? '',$form_data['app_url'] ?? '');
        else $response = [];

        if(isset($response['ok']) && $response['ok']==false)
        {
            $message = $response['error_code'].' : ' ?? __('Baq request').' : ';
            $message .= isset($response['description']) && !empty($response['description']) ? $response['description'] :  __('Invalid API data provided or it could be a curl connection error.');
            return ['error' => true,'message' => $message];
        }

        if (null === $response  || ! is_array($response) || ($api_name!='acelle' && ! array_key_exists('lists', $response)) || empty($response)) {

            if(isset($catch_error) && !empty($catch_error)) $message = $catch_error;
            else $message = __('Unable to pull in data from your auto responder account or may be there no data to pull.');
            return ['error' => true,'message' => $message];
        }

        $settings_email_autoresponder_id = $update_id;
        try {
            DB::beginTransaction();
            if($update_id==0){
                $insert_data['user_id'] = $this->user_id;
                DB::table("settings_email_autoresponders")->insert($insert_data);
                $settings_email_autoresponder_id = DB::getPdo()->lastInsertId();
            }
            else DB::table("settings_email_autoresponders")->where(['id'=>$update_id,'user_id'=>$this->user_id])->update(['profile_name'=>$profile_name]);

            $loop_data = $api_name!='acelle' ? $response['lists'] : $response;
            foreach ($loop_data as $key => $list) {
                $list_id = $list['id'] ?? '';
                $list_uid = $list['uid'] ?? '';
                $list_id_data = $api_name!='acelle' ? $list_id : $list_uid;
                $upsert = [
                    'settings_email_autoresponder_id' => $settings_email_autoresponder_id,
                    'list_name' => $list['name'] ?? '',
                    'list_id' => $list_id_data,
                    'string_id' => $list['stringid'] ?? '',
                    'list_folder_id' => $list['folderId'] ?? 0,
                    'list_total_subscribers' => $list['totalSubscribers'] ?? 0,
                    'list_total_blacklisted' => $list['totalBlacklisted'] ?? 0,
                    'updated_at' => $now,
                ];
                DB::table('settings_email_autoresponder_lists')->upsert($upsert, ['settings_email_autoresponder_id','list_id'], $upsert);
            }
            DB::commit();
            return ['error' => false,'message' =>__('Auto responder data has been saved & synchronized successfully.')];
        }
        catch (\Throwable $e){
            DB::rollBack();
            return ['error' => true,'message' =>$e->getMessage()];
        }
    }

    protected function get_autoreponder_list_ids($api_name='mailchimp',$user_id='') // isnt used, no need
    {
        if($user_id=='') $user_id = $this->user_id;
        $results = DB::table("settings_email_autoresponder_lists")
            ->select('settings_email_autoresponder_lists.list_id')->where(['settings_email_autoresponders.user_id'=>$user_id,'api_name'=>$api_name])
            ->leftJoin('settings_email_autoresponders','settings_email_autoresponder_lists.settings_email_autoresponder_id','=','settings_email_autoresponders.id')->get();

        $ids = [];
        foreach ($results as $key=>$value){
            $ids[] = $value['list_id'] ?? '';
        }
        return $ids;
    }

    public function update_api_settings(Request $request) // works for both email and autoresponder
    {
        if(config('app.is_demo')=='1') abort('403');
        $id = $request->id;
        $is_autoresponder = $request->is_autoresponder ?? '0';
        $is_thirdparty_api = $request->is_thirdparty_api ?? '0';
        $table = $is_autoresponder=='0' ? 'settings_sms_emails' : 'settings_email_autoresponders';
        $email_settings_data = DB::table($table)->where(['id'=>$id,'user_id'=>$this->user_id])->first();

        $response = json_decode($email_settings_data->settings_data) ?? [];
        $response->profile_name = $email_settings_data->profile_name ?? '';
        $response->api_name = $email_settings_data->api_name ?? '';
        return json_encode($response);
    }

    public function delete_api_settings_action(Request $request){
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);$id = $request->id;

        $table = 'settings_sms_emails';
        $where = ['user_id'=>$this->user_id,'id'=>$id];
        if(!valid_to_delete($table,$where)) {
            return response()->json(['error'=>true]);
        }
        unset($where['user_id']);

        if(DB::table($table)->where($where)->delete()) return response()->json(['error'=>false]);
        else return response()->json(['error'=>true]);
    }

    public function delete_email_auto_settings_action(Request $request){
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);
    
        $id = $request->id;

        $table = 'settings_email_autoresponders';
        $where = ['user_id'=>$this->user_id,'id'=>$id];
        if(!valid_to_delete($table,$where)) {
            return response()->json(['error'=>true]);
        }
        unset($where['user_id']);

        if(DB::table($table)->where($where)->delete()) return response()->json(['error'=>false]);
        else return response()->json(['error'=>true]);
    }

    public function manual_transaction_log()
    {
        $data = array('body'=>'member/payment/transaction-log-manual','load_datatable'=>true);
        $data['currency_list'] = get_country_iso_phone_currency_list("currency_name");
        return $this->viewcontroller($data);
    }

    public function manual_transaction_log_data(Request $request)
    {
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $date_range = !is_null($request->input('date_range')) ? $request->input('date_range') : '';
        $display_columns = array('id', 'name', 'email','paid_amount', 'attachment', 'status','actions','created_at','additional_info');
        $search_columns = array('name', 'email');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 5;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'transaction_history_manual.created_at';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $from_date = $to_date = "";
        if($date_range!="")
        {
            $exp = explode('-', $date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
        }

        $user_id = $this->user_id;
        $table="transaction_manual_logs";
        $select= ["transaction_manual_logs.*","packages.package_name","users.name","users.email"];
        $query = DB::table($table)->select($select)->leftJoin('packages', 'transaction_manual_logs.package_id', '=', 'packages.id')->leftJoin('users', 'transaction_manual_logs.buyer_user_id', '=', 'users.id');
        if($from_date!='') $query->where('created_at','>=',$from_date);
        if($to_date!='') $query->where('created_at','<=',$to_date);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $query->where(function($query) use ($user_id){
            $query->orWhere('transaction_manual_logs.user_id', '=', $user_id);
            $query->orWhere('buyer_user_id', '=', $user_id);
        });
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($select)->leftJoin('packages', 'transaction_manual_logs.package_id', '=', 'packages.id')->leftJoin('users', 'transaction_manual_logs.buyer_user_id', '=', 'users.id');
        if($from_date!='') $query->where('created_at','>=',$from_date);
        if($to_date!='') $query->where('created_at','<=',$to_date);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $query->where(function($query) use ($user_id){
            $query->orWhere('transaction_manual_logs.user_id', '=', $user_id);
            $query->orWhere('buyer_user_id', '=', $user_id);
        });
        $total_result = $query->count();

        $currency_icons = get_country_iso_phone_currency_list('currency_icon');

        foreach ($info as $key => $value)
        {
            $status = isset($value->status) ? $value->status : '2';
            if ('0' == $status) {
                $value->status = '<span class="text-warning"><i class="fa fa-spinner"></i> ' . __('Pending') . '</span>';
            } elseif ('1' == $status) {
                $value->status = '<span class="text-success"><i class="far fa-check-circle"></i> ' . __('Approved') . '</span>';
            } elseif ('2' == $status) {
                $value->status = '<span class="text-danger"><i class="far fa-check-circle"></i> ' . __('Rejected') . '</span>';
            }
            $file = $value->filename;
            $value->attachment = $this->handle_attachment($value->id, $file);

            if (!$this->is_member) $value->name = '<a href="' . route('update-user',$value->buyer_user_id) . '" target="_blank">' . $value->name . '</a>';

            if (! isset($value->actions)) {
                $action_width = (2*47)+20;
                $is_disabled = ('1' == $status || '2' == $status) ? 'disabled' : '';

                if ($this->is_admin)
                {
                    $approve_btn = '<a href="#" id="mp-approve-btn" class="btn btn-circle btn-outline-success ' . $is_disabled . '" data-id="' . $value->id . '"><i class="fas fa-check-circle"></i></a>';
                    $reject_btn = '&nbsp;<a href="#" id="mp-reject-btn" class="btn btn-circle btn-outline-danger ' . $is_disabled . '" data-id="' . $value->id . '"><i class="fas fa-times-circle"></i></a>';
                    $output = $approve_btn.$reject_btn;
                    $value->actions = $output;
                }
                else
                {
                    if ('0' == $status) $value->actions = '<i class="fas fa-spinner text-warning" data-toggle="tooltip" title="' . __('In progress') . '"></i>';
                    elseif ('1' == $status) $value->actions = '<i class="fas fa-check-circle text-success" data-toggle="tooltip" title="' . __('No action required') . '"></i>';
                    elseif ('2' == $status) $value->actions = '<i class="fas fa-times-circle text-danger" data-toggle="tooltip" title="' . __('Rejected') . '"></i>';
                }
            }
            $value->created_at = date("jS M y H:i:s",strtotime($value->created_at));

            $curreny_icon = isset($currency_icons[$value->paid_currency]) ? $currency_icons[$value->paid_currency] : $value->paid_currency;
            $paid_amount = number_format($value->paid_amount,2,'.','');
            $style = ($value->buyer_user_id==$this->user_id) ? "<span class='text-danger font-bold'>" :  "<span class='text-success font-bold'>";
            $sign = ($value->buyer_user_id==$this->user_id) ? "-" : "+";
            $value->paid_amount = $style.$sign.' '.$curreny_icon.' '.$paid_amount."</span>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function manual_payment_handle_actions(Request $request)
    {
        if (! $request->ajax()) {
            $message = __('Bad Request.');
            return Response::json(['msg'=>$message]);
            exit;
        }


        $rules = [
            "id"=>"required|integer",
            "action_type"=>"required|in:mp-approve-btn,mp-reject-btn",
            "rejected_reason"=>"nullable",
        ];

        $validate_data = Validator::make($request->all(),$rules);

        if($validate_data->fails()){
            $errors = $validate_data->errors();

            if($errors->has('id')) {
                $message = $errors->first('id');
            } else if($errors->has('action_type')) {
                $message = $errors->first('action_type');
            } else if($errors->has('rejected_reason')) {
                $message = $errors->first('rejected_reason');
            }

            return Response::json([
                'error'=> strip_tags($message)
            ]);
        }

        $id = $request->input('id');
        $action_type = $request->input('action_type');
        $rejected_reason = $request->input('rejected_reason');

        switch ($action_type) {
            case 'mp-approve-btn':
                $this->manual_payment_approve($id);
                return;

            case 'mp-reject-btn':
                $this->manual_payment_reject($id, $rejected_reason);
                return;

            default:
                $message = __('The action type was not valid.');
                return Response::json(['error' => $message]);
                exit;
        }
    }

    public function manual_payment_approve($transaction_id)
    {
        if (! request()->ajax() || ('Admin' != $this->user_type && 'Agent' != $this->user_type)) {
            $message = __('Bad Request.');
            return Response::json(['msg' => $message]);
            exit;
        }

        $man_select = [
            'transaction_manual_logs.id as thm_id',
            'transaction_manual_logs.user_id',
            'transaction_manual_logs.buyer_user_id',
            'transaction_manual_logs.package_id',
            'transaction_manual_logs.transaction_id',
            'transaction_manual_logs.paid_amount',
            'transaction_manual_logs.status',
            'transaction_manual_logs.created_at',
            'users.name',
            'users.email',
            'packages.price',
            'packages.validity',
        ];

        $man_where = [
                'transaction_manual_logs.id' => $transaction_id,
                // 'transaction_history_manual.status' => '0',
            ];

        $manual_transaction = DB::table("transaction_manual_logs")
                                ->select($man_select)
                                ->leftJoin("users","transaction_manual_logs.buyer_user_id","=","users.id")
                                ->leftJoin("packages","transaction_manual_logs.package_id","=","packages.id")
                                ->where($man_where)
                                ->get();
        if (1 != sizeof($manual_transaction)) {
            $message = __('Bad request.');
            return Response::json(['error' => $message]);
            exit;
        }

        // Manual transaction info
        $manual_transaction = $manual_transaction[0];

        // Payment status
        $status = $manual_transaction->status;
        if ('1' == $status) {
            $message = __('The transaction had already been approved.');
            echo json_encode(['error'=>$message]);
            return;
        } elseif ('2' == $status) {
            $message = __('The transaction had been rejected and you can not approve it.');
            echo json_encode(['error'=>$message]);
            return;
        }

        // Prepares some vars
        $name = explode(' ', $manual_transaction->name);
        $first_name = isset($name[0]) ? $name[0] : '';
        $last_name = isset($name[1]) ? $name[1] : '';
        $name = $first_name . ' ' . $last_name;
        $buyer_email = $manual_transaction->email;
        $user_id = $manual_transaction->user_id;
        $package_owner = $this->get_user($user_id,"email");
        $package_owner_email = $package_owner->email ?? "";
        $buyer_user_id = $manual_transaction->buyer_user_id;
        $package_id = $manual_transaction->package_id;
        $paid_amount = $manual_transaction->paid_amount;
        $transaction_id = $manual_transaction->transaction_id;

        // Prepares sql for 'transaction_history' table
        $prev_where = ['buyer_user_id' => $buyer_user_id];
        $prev_select = ['cycle_start_date', 'cycle_expired_date'];

        $prev_payment_info = DB::table("transaction_logs")
                                ->select($prev_select)
                                ->where($prev_where)
                                ->offset(0)
                                ->limit(1)
                                ->orderBy("ID","DESC")
                                ->get();

        // Previous payment info
        $prev_payment = isset($prev_payment_info[0]) ? $prev_payment_info[0] : [];

        // Prepares cycle start and end date
        $prev_cycle_expired_date = '';
        if (1 == sizeof($prev_payment_info)) {
            $prev_cycle_expired_date = $prev_payment->cycle_expired_date;
        }

        $validity_str = '+' . $manual_transaction->validity . ' day';
        if ('' == $prev_cycle_expired_date || strtotime($prev_cycle_expired_date) == strtotime(date('Y-m-d'))) {
            $cycle_start_date = date('Y-m-d');
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        } elseif (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))) {
            $cycle_start_date = date('Y-m-d');
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        } elseif (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))) {
            $cycle_start_date = date("Y-m-d",strtotime('+1 day', strtotime($prev_cycle_expired_date)));
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        }

        // Data for 'transaction_history' table
        $transaction_history_data = [
            'verify_status'     => '',
            'first_name'        => $first_name,
            'last_name'         => $last_name,
            'buyer_email'      => $buyer_email,
            // 'receiver_email'    => $email,
            'country'           => '',
            'paid_at'      => date('Y-m-d H:i:s', strtotime($manual_transaction->created_at)),
            'payment_method'      => 'manual',
            'transaction_id'    => $transaction_id,
            'user_id'           => $user_id,
            'buyer_user_id'     => $buyer_user_id,
            'package_id'        => $package_id,
            'cycle_start_date'  => $cycle_start_date,
            'cycle_expired_date'=> $cycle_expired_date,
            'paid_amount'       => $paid_amount,
        ];

        // Data form 'users' table
        $user_where = ['id' => $buyer_user_id];
        $user_data = [
            'expired_date' => $cycle_expired_date,
            'package_id' => $package_id,
            'bot_status' => '1'
        ];
        $has_error = false;
        try {
            DB::beginTransaction();

            DB::table("transaction_logs")->insert($transaction_history_data);
            DB::table("users")->where($user_where)->update($user_data);
            DB::table("transaction_manual_logs")->where("id",$manual_transaction->thm_id)->update(['status' => '1','updated_at' => date('Y-m-d H:i:s')]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $has_error = true;
        }

        if ($has_error) {
            echo json_encode(['status'=>'0','message'=> __('Something went wrong, please try again.')]);
            return;

        } else {
            echo json_encode(['status'=>'ok','message'=> __('Your transaction approved successfully.')]);
        }

        // Send email to package owner
        $email  = $package_owner_email;
        $email_reply_subject = __("New Payment Made");
        $email_reply_message = __("New payment has been made by").' '.$first_name.' '.$last_name;

        $this->send_email($email, $email_reply_message, $email_reply_subject);

        // Send email to buyer
        $buyer_email  = $buyer_email;
        $email_reply_subject = __("Payment Confirmation");
        $email_reply_message = __("Congratulation,<br/> we have received your payment successfully. Now you are able to use").' '.config('app.name').' '.__('system till').' '.$cycle_expired_date.'. '."\r\nThank your\r\n<a href='".url('')."'>".config('app.name')."</a>";

        $this->send_email($buyer_email, $email_reply_message, $email_reply_subject);

    }

    public function manual_payment_reject($id, $rejected_reason)
    {
        if (! request()->ajax()
            || ('Admin' != $this->user_type
                && 'Agent' != $this->user_type)
        ) {
            $message = __('Bad Request.');
            echo json_encode(['msg' => $message]);
            exit;
        }

        $man_select = [
            'transaction_manual_logs.id as thm_id',
            'transaction_manual_logs.user_id',
            'transaction_manual_logs.buyer_user_id',
            'transaction_manual_logs.package_id',
            'transaction_manual_logs.transaction_id',
            'transaction_manual_logs.status',
            'users.name',
            'users.email',
        ];

        $man_where = [
            'transaction_manual_logs.id' => $id,
        ];

        $manual_transaction = DB::table("transaction_manual_logs")
                                ->select($man_select)
                                ->leftJoin("users","transaction_manual_logs.buyer_user_id","=","users.id")
                                ->where($man_where)
                                ->get();

        if (1 != sizeof($manual_transaction)) {
            $message = __('Bad request.');
            echo json_encode(['error' => $message]);
            return;
        }

        // Manual transaction info
        $manual_transaction = $manual_transaction[0];

        // Holds transaction status
        $status = $manual_transaction->status;
        $transaction_id = $manual_transaction->transaction_id;

        if ('1' == $status) {
            $message = __('The transaction had already been approved.');
            echo json_encode(['error' => $message]);
            return;
        } elseif ('2' == $status) {
            $message = __('The transaction had already been rejected.');
            echo json_encode(['error' => $message]);
            return;
        }

        if (empty($rejected_reason)) {
            $message = __('Please describe the reason of the rejection of this payment.');
            echo json_encode(['error' => $message]);
            exit;
        }

        // Prepares some vars
        $thm_id = $manual_transaction->thm_id;
        $buyer_email = $manual_transaction->email;
        $owner_user_id = $manual_transaction->user_id;
        $get_email = $this->get_user($owner_user_id,"email");
        $owner_email = $get_email->email ?? "";

        $where = ['id' => $thm_id];
        $data = [
            'status' => '2',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (DB::table("transaction_manual_logs")->where($where)->update($data)) {
            $message = __('The transaction has been rejected.');
            echo json_encode(['status' => 'ok', 'message' => $message]);
        } else {
            $message = __('Something went wrong! Please try again later.');
            echo json_encode(['error' => $message]);
        }

        // Send email to package owner
        $email  = $owner_email;
        $mask = config('app.name');
        $email_reply_subject = __("Manual payment rejection");
        $email_reply_message = "Transaction ID: {$transaction_id} has been rejected. Please check out the following reason:\r\n \r\n{$rejected_reason}\r\n \r\nIf you are still want to use this {$mask} system, please resubmit the payment again in accordance with the description above.\r\n \r\nThank you,\r\n<a href=\"" . url('') . "\">{$mask}</a> team";

        $this->send_email($email, $email_reply_message, $email_reply_subject);

        // Send email to buyer
        $email  = $buyer_email;
        $this->send_email($email, $email_reply_message, $email_reply_subject);

    }

    private function handle_attachment($id, $file)
    {
        $info = pathinfo($file);
        if (isset($info['extension']) && ! empty($info['extension'])) {
            switch (strtolower($info['extension'])) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return $this->manual_payment_display_attachment($file);
                case 'zip':
                case 'pdf':
                case 'txt':
                    return '<a target="_blank" class="btn btn-outline-info" href="'.$file.'"><i class="fa fa-download"></i></a>';
            }
        }
    }


    private function manual_payment_display_attachment($file)
    {
        $output = '<div class="mp-display-img">';
        $output .= '<div class="mp-img-item btn btn-outline-info" data-image="' . $file . '" href="' . $file . '">';
        $output .= '<i class="fa fa-image"></i>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<script>$(".mp-display-img").Chocolat({className: "mp-display-img", imageSelector: ".mp-img-item"});</script>';

        return $output;
    }

    public function manual_payment(Request $request)
    {
        $rules = [
            'paid_amount' =>'required|integer',
            'paid_currency' =>'required',
            'additional_info' =>'nullable',
            'package_id' =>'required|integer',
            'mp_resubmitted_id' =>'integer',
        ];

        $validate_data = Validator::make($request->all(),$rules);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            if($errors->has('paid_amount')) {
                $message = $errors->first('paid_amount');
            } else if($errors->has('paid_currency')) {
                $message = $errors->first('paid_currency');
            } else if($errors->has('package_id')) {
                $message = $errors->first('package_id');
            }else if($errors->has('mp_resubmitted_id')) {
                $message = $errors->first('mp_resubmitted_id');
            }

            return Response::json([
                'error'=> strip_tags($message)
            ]);
        }

        $paid_amount = $request->input('paid_amount');
        $paid_currency = $request->input('paid_currency');
        $additional_info = strip_tags($request->input('additional_info'));
        $package_id = (int) $request->input('package_id');
        $package_data = $this->get_package($package_id);
        $package_user_id = $package_data->user_id;
        $filename = session('manual_payment_uploaded_file');
        $mp_resubmitted_id = (int) $request->input('mp_resubmitted_id');

        if (! empty($mp_resubmitted_id)) {

            $mp_resubmitted_data = DB::table("transaction_manual_logs")->select(['id', 'user_id', 'filename'])->where('id',$mp_resubmitted_id)->get();

            if (1 != sizeof($mp_resubmitted_data)) {
                $message = __('Bad request.');
                return Response::json(['error'=>$message]);
            }

            $mp_resubmitted_data = $mp_resubmitted_data[0];
            if ($mp_resubmitted_data->user_id != $this->user_id) {
                $message = __('Bad request.');
                return Response::json(['error'=>$message]);
            }

            $updated_at = date('Y-m-d H:i:s');
            $update_where = ['id' => $mp_resubmitted_id];
            $update_data = [
                'status' => '0',
                'paid_amount' => $paid_amount,
                'paid_currency' => $paid_currency,
                'additional_info' => $additional_info,
                'updated_at' => $updated_at,
            ];

            // Deletes previous attachement if new one exists
            if (! empty($filename)) {
                // Updates filename in the db
                $update_data['filename'] = $filename;

                // Upload dir path
                $upload_dir = 'upload/manual_payment';

                // Prepares file path
                $filepath = storage_path('app/public/').$upload_dir.DIRECTORY_SEPARATOR. $mp_resubmitted_data->filename;

                // Tries to remove previously uploaded file
                if (!is_dir($filepath) && file_exists($filepath)) {
                    // Deletes file from disk
                    unlink($filepath);
                }
            }

            if (DB::table('transaction_manual_logs')->where($update_where)->update($update_data)
            ) {

                // Deletes file from session
                Session::forget('manual_payment_uploaded_file');

                $message = __('Your manual transaction has been successfully re-submitted and is now being reviewed. We would let you know once it has been approved.');
                return Response::json(["success"=>$message]);
                exit;
            }

            $message = __('Something went wrong while re-submitting your information. Please try again later or contact the administrator!');
            return Response::json(["success"=>$message]);
            exit;
        }

        // Checks whether the attachment is attached
        $filename = session('manual_payment_uploaded_file');
        if (empty($filename)) {
            $message = __('The attachment must be provided.');
            return Response::json(['error'=>$message]);
            exit;
        }

        $transaction_id = 'mp_' . hash_pbkdf2('sha512', $paid_amount, mt_rand(19999999, 99999999), 1000, 24);
        $data = [
            'paid_amount' => $paid_amount,
            'paid_currency' => $paid_currency,
            'additional_info' => $additional_info,
            'package_id' => $package_id,
            'user_id' => $package_user_id,
            'buyer_user_id' => $this->user_id,
            'transaction_id' => $transaction_id,
            'filename' => $filename,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if(DB::table('transaction_manual_logs')->insert($data)) {
            $message = __('Your manual transaction has been successfully submitted and is now being reviewed. We would let you konw once it has been approved.');

            // Deletes file from session
            Session::forget('manual_payment_uploaded_file');
            return Response::json(['success'=>$message]);
            exit;
        }

        $message = __('Something went wrong while saving your information. Please try again later or contact the administrator!');
        return Response::json(['error'=>$message]);
        exit;

    }

    public function manual_payment_upload_file(Request $request)
    {
        $rules = (['file' => 'mimes:pdf,doc,txt,png,jpg,jpeg,zip|max:5120']);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ]);
        }

        $upload_dir_subpath = 'upload/manual_payment';

        $file = $request->file('file');
        $extension = $request->file('file')->extension();
        $filename = "mp_". $this->user_id . '_' . time() . '.' . $extension;

        if(env('AWS_UPLOAD_ENABLED')){
            try {
                $upload2S3 = Storage::disk('s3')->putFileAs($upload_dir_subpath, $file,$filename);
                session(['manual_payment_uploaded_file'=>Storage::disk('s3')->url($upload2S3)]);
                return response()->json([
                    'error' => false,
                    'filename' =>  Storage::disk('s3')->url($upload2S3)
                ]);
            }
            catch (\Exception $e){
                $error_message = $e->getMessage();
                if(empty($error_message)) $error_message =  __('Something went wrong.');
                return response()->json([
                    'error' => true,
                    'message' => $error_message
                ]);
            }
        }
        else{

            if ($request->file('file')->storeAs('public/'.$upload_dir_subpath, $filename)) {
                session(['manual_payment_uploaded_file'=>asset('storage').'/'.$upload_dir_subpath.'/'.$filename]);
                return Response::json([
                    'error' => false,
                    'filename' =>  asset('storage').'/'.$upload_dir_subpath.'/'.$filename
                ]);
            } else {
                return Response::json([
                    'error' => true,
                    'message' => __('Something went wrong.'),
                ]);
            }
        }
    }

    public function manual_payment_delete_file(Request $request)
    {
        $filename = filter_var($request->filename, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        $filename = str_replace('storage/','public/',$filename);
        $file_paths = explode('/',$filename);
        $filename = array_pop($file_paths);

        $upload_dir_subpath = 'upload/manual_payment';

        if(env('AWS_UPLOAD_ENABLED')){
            try {
                $s3_path = $upload_dir_subpath.'/'.$filename;
                if(Storage::disk('s3')->exists($s3_path)) {
                    Storage::disk('s3')->delete($s3_path);
                    Session::forget("manual_payment_uploaded_file");
                    return Response::json(['deleted' => 'yes']);
                }
                else return Response::json(['deleted' => 'no']);

            }
            catch (\Exception $e){
                $error_message = $e->getMessage();
                if(empty($error_message)) $error_message =  __('Something went wrong.');
                return response()->json(['deleted' => 'no']);
            }
        }
        else{
            $absolute_file_path = storage_path('app/public/').$upload_dir_subpath.DIRECTORY_SEPARATOR.$filename;

            if (! is_dir($absolute_file_path) && file_exists($absolute_file_path) && unlink($absolute_file_path)) {
                Session::forget("manual_payment_uploaded_file");
                return Response::json(['deleted' => 'yes']);
            }
            else return Response::json(['deleted' => 'no']);
        }
    }


    public function paypal_action(Request $request,$package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $package_data = $this->get_package($package_id);
        $payment_amount = $package_data->price ?? 0;
        $buyer_information = DB::table('users')->select('name','email')->where('id',$buyer_user_id)->first();
        $name = $buyer_information->name;
        $email = $buyer_information->email;
        $package_name = $package_data->package_name;
        $payment_config = $this->get_payment_config_parent();
        $paypal_data = isset($payment_config->paypal) ? json_decode($payment_config->paypal) : [];
        $paypal_client_id = $paypal_data->paypal_client_id ?? '';
        $paypal_client_secret = $paypal_data->paypal_client_secret ?? '';
        $paypal_app_id = $paypal_data->paypal_app_id ?? '';
        $paypal_mode = $paypal_data->paypal_mode ?? 'sandbox';
        $paypal_payment_type = $paypal_data->paypal_payment_type ?? 'manual';
        $notify_url = get_domain_only(env('APP_URL'))=='telegram-group.test' ? 'https://ezsoci.com/botsailor-test-ipn/paypal.php' : route('paypal-ipn',$paypal_mode);

        $this->paypal->paypal_client_id = $paypal_client_id;
        $this->paypal->paypal_client_secret = $paypal_client_secret;
        $this->paypal->paypal_app_id = $paypal_app_id;
        $this->paypal->notify_url = $notify_url;
        $this->paypal->name = $name;
        $this->paypal->email = $email;
        $this->paypal->mode = $paypal_mode;
        $provider = $this->provider;// call PypalClient
        $this->paypal->provider = $provider;
        $this->paypal->plan_id = $request->plan_id;
        $this->paypal->currency = $request->currency_code;
        $this->paypal->success_url = $request->return;
        $this->paypal->cancel_url = $request->cancel_return;
        $this->paypal->package_name = $request->package_name;
        $this->paypal->paypal_subscriber_url();
    }

    public function paypal_subscription_cancel()
    {
        $buyer_user_id = Auth::user()->id;
        $payment_config = $this->get_payment_config_parent();
        $paypal_data = isset($payment_config->paypal) ? json_decode($payment_config->paypal) : [];
        $paypal_client_id = $paypal_data->paypal_client_id ?? '';
        $paypal_client_secret = $paypal_data->paypal_client_secret ?? '';
        $paypal_app_id = $paypal_data->paypal_app_id ?? '';
        $paypal_mode = $paypal_data->paypal_mode ?? 'sandbox';

        $data = DB::table('users')->select('paypal_subscriber_id')->where('id',$buyer_user_id)->first();
        $subscription_id = $data->paypal_subscriber_id;

        $this->paypal->paypal_client_id = $paypal_client_id;
        $this->paypal->paypal_client_secret = $paypal_client_secret;
        $this->paypal->paypal_app_id = $paypal_app_id;
        $this->paypal->mode = $paypal_mode;
        $provider = $this->provider;// call PypalClient
        $this->paypal->provider = $provider;
        $this->paypal->subscription_id = $subscription_id;
        $response = $this->paypal->paypal_subscription_cancel();
        if($response == ''){
             DB::table('users')->where(['id'=>$buyer_user_id])->update(['subscription_enabled'=>'0','subscription_data'=>NULL,'paypal_subscriber_id'=>'','paypal_next_check_time'=>NULL]);
            return redirect()->back();
        }
        else dd($response);

    }

}
