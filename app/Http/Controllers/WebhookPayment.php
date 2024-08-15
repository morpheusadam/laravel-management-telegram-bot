<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use App\Services\Payment\PaypalServiceInterface;
use App\Services\Payment\StripeServiceInterface;
use App\Services\Payment\InstamojoServiceInterface;
use App\Services\Payment\PaymayaServiceInterface;
use App\Services\Payment\ToyyibpayServiceInterface;
use App\Services\Payment\XenditServiceInterface;
use App\Services\Payment\MyfatoorahServiceInterface;
use App\Services\Payment\MercadopagoServiceInterface;
use App\Services\Payment\RazorpayServiceInterface;
use App\Services\Payment\PaystackServiceInterface;
use App\Services\Payment\YoomoneyServiceInterface;
use App\Services\Payment\MollieServiceInterface;
use App\Services\Payment\FlutterwaveServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookPayment extends Home
{
    public function __construct(MollieServiceInterface $mollie_service,PaystackServiceInterface $paystack_service,RazorpayServiceInterface $razorpay_service, MercadopagoServiceInterface $mercadopago_service,MyfatoorahServiceInterface $myfatoorah_sevice, InstamojoServiceInterface $instamojo_service,PaymayaServiceInterface $paymaya_service,ToyyibpayServiceInterface $toyyibpay_service,XenditServiceInterface $xendit_service, PaypalServiceInterface $paypal_service,StripeServiceInterface $stripe_service,YoomoneyServiceInterface $yoomoney_service,FlutterwaveServiceInterface $flutterwave_service)
    {
        $this->paypal = $paypal_service;
        $this->stripe = $stripe_service;
        $this->api_key = "md6EEvAfqFjHgNmVdxmhvFWchS7ERsX5TYaBEXt9f6BNn";
        $this->instamojo = $instamojo_service;
        $this->instamojo_v2 = $instamojo_service;
        $this->paymaya = $paymaya_service;
        $this->toyyibpay = $toyyibpay_service;
        $this->xendit = $xendit_service;
        $this->myfatoorah = $myfatoorah_sevice;
        $this->mercadopago = $mercadopago_service;
        $this->razorpay_class_ecommerce = $razorpay_service;
        $this->paystack_class_ecommerce = $paystack_service;
        $this->mollie_class_ecommerce = $mollie_service;
        $this->yoomoney = $yoomoney_service;
        $this->flutterwave = $flutterwave_service;
    }

    public function paypal_ipn($paypal_mode="sandbox")
    {
        $payment_info = $this->paypal->run_ipn($paypal_mode);
        if(empty($payment_info)) $payment_info = [];
        $payment_info['api_key'] = $this->api_key;

        $user_id_package_id = isset($payment_info['data']['custom']) ? explode('_',$payment_info['data']['custom']) : '';
        $buyer_user_id = $user_id_package_id[0] ?? null;

        $payment_info_json = json_encode($payment_info);
        $post_data_payment_info = array("response_raw"=>$payment_info_json);

        $url = env('NGROK_URL')!='' ? env('NGROK_URL').'/webhook/paypal-ipn-action' : route('paypal-ipn-action');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_payment_info);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $curl_information =  curl_getinfo($ch);

        $curl_error = '';
        if($curl_information['http_code']!='200') {
            $curl_error = curl_error($ch);
            $curl_error = $curl_information['http_code'] . " : " . $curl_error;
        }
        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = $payment_info_json;
        $log_insert_data['payment_method'] = 'PayPal';
        if(!empty($curl_error)) $log_insert_data['error'] = $curl_error;
        DB::table("payment_api_logs")->insert($log_insert_data);

    }

    public function paypal_ipn_action(Request $request)
    {
        $response_raw = get_domain_only(env('APP_URL'))=='telegram-group.test' ? file_get_contents('https://ezsoci.com/botsailor-test-ipn/paypal-response.txt') : $request->response_raw;;
        $payment_info = json_decode($response_raw,TRUE);
        $post_api_from_ipn = $payment_info['api_key'] ?? "";
        if($post_api_from_ipn != $this->api_key && get_domain_only(env('APP_URL'))!='telegram-group.test') exit();

        $user_id_package_id = isset($payment_info['data']['custom']) ? explode('_',$payment_info['data']['custom']) : '';
        $buyer_user_id =  $user_id_package_id[0] ?? null; // buyer user id
        $package_id = $user_id_package_id[1] ?? null;

        $verify_status = $payment_info['verify_status'] ?? "";
        $first_name = $payment_info['data']['first_name'] ?? "";
        $last_name = $payment_info['data']['last_name'] ?? "";
        $buyer_email = $payment_info['data']['payer_email'] ?? "";
        $country = $payment_info['data']['address_country_code'] ?? "";
        $paid_currency = $payment_info['data']['mc_currency'] ?? "USD";

        $transaction_id = $payment_info['data']['txn_id'] ?? "";
        $transaction_type = $payment_info['data']['txn_type'] ?? "";
        $payment_amount = $payment_info['data']['mc_gross'] ?? 0;
        $is_recurring = isset($payment_info['data']['recurring']) && $payment_info['data']['recurring']=='1' ? true : false;
        $payment_type = "PayPal";

        set_agency_config($buyer_user_id);
        $get_payment_validity_data = $this->get_payment_validity_data($buyer_user_id,$package_id);

        if($is_recurring && $transaction_type=='subscr_signup'){ //subscription created
            $recurring_amount = $payment_info['data']['mc_amount3'] ?? '';

            $subscribed_period_raw = $payment_info['data']['period3'] ?? '1 D';
            $explode_subscribed_period = explode(' ',$subscribed_period_raw);
            $explode_subscribed_period1 = isset($explode_subscribed_period[0]) ? (int) $explode_subscribed_period[0] : 1;
            $explode_subscribed_period2 = $explode_subscribed_period[1] ?? 'D';
            $subscribed_period_unit = (int) str_replace(['D','W','M','Y'],['1','7','30','365'],$explode_subscribed_period2);
            $subscribed_period = $explode_subscribed_period1*$subscribed_period_unit;

            if(empty($recurring_amount)) $recurring_amount = $payment_info['data']['amount3'] ?? 0;
            $subscription_data = [
                'unique_id' => $payment_info['data']['subscr_id'] ?? "",
                'package_id' => $package_id,
                'time' => date('Y-m-d H:i:s'),
                'amount' => $recurring_amount,
                'currency'=>$paid_currency,
                'validity' => (int) $subscribed_period,
                'method' => $payment_type
            ];

            DB::table('users')->where(['id'=>$buyer_user_id])->update(['subscription_enabled'=>'1','subscription_data'=>$subscription_data]);
            exit();
        }
        else if(in_array($transaction_type,['subscr_eot','subscr_cancel','recurring_payment_profile_cancel','recurring_payment_suspended'])){
            DB::table('users')->where(['id'=>$buyer_user_id])->update(['subscription_enabled'=>'0','subscription_data'=>null]);
            exit();
        }

        $check_duplicate = DB::table("transaction_logs")->select('transaction_id')->where(['buyer_user_id'=>$buyer_user_id,'transaction_id'=>$transaction_id,'payment_method'=>$payment_type])->first();
        $previous_transaction_id = $check_duplicate->transaction_id ?? '';
        if($previous_transaction_id == $transaction_id && get_domain_only(env('APP_URL'))!='telegram-group.test') dd("Transaction ID duplicated.");

        $package_name = $get_payment_validity_data['package_name'] ?? '';
        $user_email = $get_payment_validity_data['email'] ?? '';
        $user_name = $get_payment_validity_data['name'] ?? '';
        $parent_user_id = $get_payment_validity_data['parent_user_id'] ?? null;
        $price = (float) $get_payment_validity_data['price'] ?? 0;
        $is_agency = '0';
        $cycle_start_date = $get_payment_validity_data['cycle_start_date'] ?? date("Y-m-d");
        $cycle_expired_date = $get_payment_validity_data['cycle_expired_date'] ?? date("Y-m-d");
        $paid_at = date("Y-m-d H:i:s");

        $payment_config = $this->get_payment_config_parent($parent_user_id,['currency','decimal_point','thousand_comma','currency_position']);
        $discount_data = $get_payment_validity_data['discount_data'] ?? null;
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $price = (float) format_price($price,$format_settings,$discount_data,['which_price'=>'sale','display_currency'=>false,'return_raw_array'=>false]);

        $buyer_data = DB::table('users')->where(['id'=>$buyer_user_id])->select(['subscription_enabled','subscription_data'])->first();
        $xsubscription_data = isset($buyer_data->subscription_data) ? json_decode($buyer_data->subscription_data) : null;
        $xpayment_type = $xsubscription_data->payment_type ?? null;
        if(isset($buyer_data->subscription_enabled) && $buyer_data->subscription_enabled=='1' && strtolower($xpayment_type)==strtolower($payment_type)){
            $subscription_data = !empty($buyer_data->subscription_data) ? json_decode($buyer_data->subscription_data) : [];
            $subscription_data_method = $subscription_data->method ?? '';
            $subscription_data_package_id = $subscription_data->package_id ?? 0;
            $subscription_data_amount = $subscription_data->amount ?? 0;
            $subscription_data_validity = $subscription_data->validity ?? 0;
            if(strtolower($subscription_data_method)==strtolower($payment_type) && $subscription_data_package_id==$package_id){
                $price = (float) $subscription_data_amount;
                $validity_str='+'.$subscription_data_validity.' day';
                $cycle_expired_date = date("Y-m-d",strtotime($validity_str,strtotime(date('Y-m-d'))));
            }
        }

        if(round($payment_amount) < round($price)) dd(__('You did not paid the correct amount.'));

        /** insert the transaction into database ***/

        $insert_data=array(
            "verify_status"     => $verify_status,
            "user_id"           => $parent_user_id,
            "buyer_user_id"     => $buyer_user_id ,
            "first_name"        => $first_name,
            "last_name"         => $last_name,
            "buyer_email"       => $buyer_email,
            "country"           => $country,
            "paid_currency"     => $paid_currency,
            "paid_at"           => $paid_at,
            "payment_method"    => $payment_type,
            "transaction_id"    => $transaction_id,
            "paid_amount"       => $payment_amount,
            "cycle_start_date"  => $cycle_start_date,
            "cycle_expired_date"=> $cycle_expired_date,
            "package_id"        => $package_id,
            "response_source"   => $response_raw,
            "package_name"      => $package_name,
            "user_email"        => $user_email, // not for insert, for sending email
            "user_name"         => $user_name // not for insert, for sending email
        );
        $is_whitelabel = $is_agency;
        $this->complete_payment($insert_data,$is_agency,$is_whitelabel,$payment_type);
    }

    public function paypal_subscription_action(Request $request,$buyer_user_id=null,$parent_user_id=null,$package_id=null)
    {
        $package_data = $this->get_package($package_id,$select='*',$where=['id'=>$package_id,'deleted'=>'0','is_default'=>'0']);
        $payment_config = $this->get_payment_config_parent();
        $currency = $payment_config->currency ?? 'USD';
        $payment_type = "PayPal";
        $buyer_information = DB::table('users')->select('*')->where('id',$buyer_user_id)->first();
        $name = $buyer_information->name;
        $email = $buyer_information->email;
        $timestamp = date('Y-m-d H:i:s');
        $time = date('Y-m-d H:i:s');
        if(isset($request->subscription_id)){

             $subscription_data = [
                'subscription_id' => $request->subscription_id,
                'package_id' => $package_id,
                'time' => date('Y-m-d H:i:s'),
                'amount' => $package_data->price,
                'currency'=>$currency,
                'validity' => $package_data->validity,
                'method' => $payment_type,
                'ba_token'=>$request->ba_token,
                'token'=>$request->token
            ];
            DB::table('users')->where(['id'=>$buyer_user_id])->update(['subscription_enabled'=>'1','subscription_data'=>$subscription_data,'paypal_subscriber_id'=>$request->subscription_id,'paypal_next_check_time'=>$time]);
            $route = route('transaction-log').'?action=success';
            return redirect($route);
        }
        else{
            $route = route('transaction-log').'?action=cancel';
            return redirect($route);
        }
    }

    public function stripe_ipn($buyer_user_id=null,$parent_user_id=null,$package_id=null)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id,['currency','stripe','currency','decimal_point','thousand_comma','currency_position']);
        $currency = $payment_config->currency ?? 'USD';
        $stripe_data = isset($payment_config->stripe) ? json_decode($payment_config->stripe) : [];
        $package_data = $this->get_package($package_id,['package_name','price']);
        $package_name = $package_data->package_name ?? '';
        $payment_amount = $package_data->price ?? 0;
        $payment_type = 'Stripe';
        $stripe_secret_key = $stripe_data->stripe_secret_key ?? '';
        $this->stripe->secret_key = $stripe_secret_key;
        $this->stripe->amount = $payment_amount;
        $this->stripe->currency = $currency;
        $this->stripe->description = $package_name;
        $response = $this->stripe->stripe_payment_action();

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['message'] ?? null;
        $log_insert_data['payment_method'] = 'Stripe';
        DB::table("payment_api_logs")->insert($log_insert_data);

        if($response['status']=='Error') {
            dd($response['message']);
        }

        set_agency_config($buyer_user_id);
        $get_payment_validity_data = $this->get_payment_validity_data($buyer_user_id,$package_id);

        $buyer_email =  $response['email'] ?? '';
        $response_child =  $response['charge_info'] ?? null;
        $payment_amount = $response_child->amount ?? 0;
        $paid_currency = $response_child->currency ?? "USD";
        $paid_currency=strtoupper($paid_currency);
        if($paid_currency!='JPY' && $paid_currency!='VND') $payment_amount = $payment_amount/100;
        $transaction_id = $response_child->balance_transaction ?? '';
        $country = $response_child->source->country ?? '';
        $stripe_card_source = $response_child->source ?? "";
        $response_raw = json_encode($stripe_card_source);

        $package_name = $get_payment_validity_data['package_name'] ?? '';
        $user_email = $get_payment_validity_data['email'] ?? '';
        $user_name = $get_payment_validity_data['name'] ?? '';
        $price = (float) $get_payment_validity_data['price'] ?? 0;
        $is_agency = '0';
        $cycle_start_date = $get_payment_validity_data['cycle_start_date'] ?? date("Y-m-d");
        $cycle_expired_date = $get_payment_validity_data['cycle_expired_date'] ?? date("Y-m-d");
        $paid_at = date("Y-m-d H:i:s");

        $discount_data = $get_payment_validity_data['discount_data'] ?? null;
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $price = (float) format_price($price,$format_settings,$discount_data,['which_price'=>'sale','display_currency'=>false,'return_raw_array'=>false]);

        if(round($payment_amount) < round($price)) dd(__('You did not paid the correct amount.'));

        /** insert the transaction into database ***/
        $insert_data=array(
            "verify_status"     => 'VERIFIED',
            "user_id"           => $parent_user_id,
            "buyer_user_id"     => $buyer_user_id ,
            "first_name"        => $user_name,
            "last_name"         => '',
            "buyer_email"       => $buyer_email,
            "country"           => $country,
            "paid_currency"     => $paid_currency,
            "paid_at"           => $paid_at,
            "payment_method"    => $payment_type,
            "transaction_id"    => $transaction_id,
            "paid_amount"       => $payment_amount,
            "cycle_start_date"  => $cycle_start_date,
            "cycle_expired_date"=> $cycle_expired_date,
            "package_id"        => $package_id,
            "response_source"   => $response_raw,
            "package_name"      => $package_name,
            "user_email"        => $user_email, // not for insert, for sending email
            "user_name"         => $user_name // not for insert, for sending email
        );
        $is_whitelabel = $is_agency;
        if($this->complete_payment($insert_data,$is_agency,$is_whitelabel,$payment_type)) session(['payment_success' => '1']);
        else session(['payment_success' => '0']);
        return redirect()->route('transaction-log');
    }


    public function razorpay_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $raz_order_id_session = ltrim(request()->order_id,'/');
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $razorpay_data = isset($payment_config->razorpay) ? json_decode($payment_config->razorpay) : [];

        if(!empty($razorpay_data))
        {
            $razorpay_key_id = $razorpay_data->razorpay_key_id;
            $razorpay_key_secret = $razorpay_data->razorpay_key_secret;
        }

        $this->razorpay_class_ecommerce->key_id=$razorpay_key_id;
        $this->razorpay_class_ecommerce->key_secret=$razorpay_key_secret;
        $response= $this->razorpay_class_ecommerce->razorpay_payment_action($raz_order_id_session);

        $currency = isset($response['charge_info']['currency'])?$response['charge_info']['currency']:"INR";
        $currency = strtoupper($currency);
        $payment_amount = isset($response['charge_info']['amount_paid'])?($response['charge_info']['amount_paid']/100):"0";
        $transaction_id = isset($response['charge_info']['id'])?$response['charge_info']['id']:"";
        $buyer_email =  isset($response['email']) ? $response['email']: Auth::user()->email;

        $payment_type = 'razorpay';

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if(isset($response['status']) && $response['status']=='Error'){
            return redirect()->route('transaction-log',['action'=>'cancel']);
        }

        $data = [];

        $data['package_id'] = $package_id;
        $data['buyer_user_id'] = $buyer_user_id;
        $data['parent_user_id'] = $parent_user_id;
        $data['transaction_id'] = $transaction_id;
        $data['payment_type'] = $payment_type;
        $data['paid_amount'] = $payment_amount;
        $data['currency'] = $currency;
        $data['buyer_email'] = $buyer_email;

        $this->success_action($data);

        return redirect()->route('transaction-log');
    }

    public function paystack_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $reference=  ltrim(request()->reference,'/');
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $paystack_data = isset($payment_config->paystack) ? json_decode($payment_config->paystack) : [];
        $paystack_secret_key = $paystack_data->paystack_secret_key;
        $this->paystack_class_ecommerce->secret_key=$paystack_secret_key;
        $response= $this->paystack_class_ecommerce->paystack_payment_action($reference);

        $currency = isset($response['charge_info']['data']['currency'])?$response['charge_info']['data']['currency']:"NGN";
        $currency = strtoupper($currency);
        $payment_amount = isset($response['charge_info']['data']['amount'])?($response['charge_info']['data']['amount']/100):"0";
        $transaction_id = isset($response['charge_info']['data']['id'])?$response['charge_info']['data']['id']:"";
        $buyer_email =  isset($response['email']) ? $response['email']: Auth::user()->email;
        $payment_type = 'paystack';

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if(isset($response['status']) && $response['status']=='Error'){
            return redirect()->route('transaction-log',['action'=>'cancel']);
        }

        $data = [];

        $data['package_id'] = $package_id;
        $data['buyer_user_id'] = $buyer_user_id;
        $data['parent_user_id'] = $parent_user_id;
        $data['transaction_id'] = $transaction_id;
        $data['payment_type'] = $payment_type;
        $data['paid_amount'] = $payment_amount;
        $data['currency'] = $currency;
        $data['buyer_email'] = $buyer_email;

        $this->success_action($data);

        return redirect()->route('transaction-log');
    }


    public function mercadopago_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {

        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $user_info = $this->get_user($buyer_user_id);
        $user_email = isset($user_info->email) ? $user_info->email : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $issuer_id = isset($_POST['issuer_id']) ? $_POST['issuer_id'] : '';
        $installments = isset($_POST['installments']) ? $_POST['installments'] : '';
        $payment_method_id = isset($_POST['payment_method_id']) ? $_POST['payment_method_id'] : '';
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $mercadopago_data= isset($payment_config->mercadopago) ? json_decode($payment_config->mercadopago) : [];
        $mercadopago_access_token = $mercadopago_data->mercadopago_access_token;
        $description = $package_name;
        $this->mercadopago->accesstoken=$mercadopago_access_token;
        $this->mercadopago->transaction_amount=$payment_amount;
        $this->mercadopago->token=$token;
        $this->mercadopago->description=$description;
        $this->mercadopago->installments=$installments;
        $this->mercadopago->payment_method_id=$payment_method_id;
        $this->mercadopago->issuer_id=$issuer_id;
        $this->mercadopago->payer_email=$user_email;
        if(get_domain_only(env("APP_URL")) == "telegram-group.test") {
            $this->mercadopago->payer_email = "test_user_123456@testuser.com";
        }
        $response = $this->mercadopago->payment_action();
        $payment_type = 'mercadopago';
        $buyer_email =  isset($response['email']) ? $response['email']: $user_email;

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if(isset($response['status']) && $response['status']=='approved'){
            $transaction_id = '';
            $data = [];

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = $buyer_email;

            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');

    }



    public function flutterwave_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $currency = $payment_config->currency ?? 'NGN';
        $flutterwave_data= isset($payment_config->flutterwave) ? json_decode($payment_config->flutterwave) : [];
        $flutterwave_api_key = $flutterwave_data->flutterwave_api_key;
        $user_info = $this->get_user($buyer_user_id);
        $user_name = isset( $user_info->name) ? $user_info->name : '';
        $user_email =  $user_email = isset($user_info->email) ? $user_info->email : '';
        $user_mobile = isset($user_info->mobile) ? $user_info->mobile : '012345678901';


        $success_url_flutterwave = route('payment-flutterwave-success',[$package_id,$buyer_user_id,$parent_user_id]);
        $this->flutterwave->flutterwave_api_key = $flutterwave_api_key;
        $this->flutterwave->purpose =$package_name;
        $this->flutterwave->amount = $payment_amount;
        $this->flutterwave->success_url_flutterwave = $success_url_flutterwave;
        $this->flutterwave->buyer_name = $user_name;
        $this->flutterwave->email = $user_email;
        $this->flutterwave->phone = $user_mobile;
        $this->flutterwave->currency = $currency;
        $this->flutterwave->button_lang = __('Pay with Flutterwave');
        $checkout_url=$this->flutterwave->get_long_url();
        return redirect()->to($checkout_url);
    }

    public function flutterwave_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {

        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $payment_type = 'flutterwave';
        $response = $_GET;
        $transaction_id = $response['transaction_id'];
        $status = $response['status'];
        $currency = $payment_config->currency ?? 'USD';
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $flutterwave_data= isset($payment_config->flutterwave) ? json_decode($payment_config->flutterwave) : [];
        $flutterwave_api_key = $flutterwave_data->flutterwave_api_key;
        $this->flutterwave->transaction_id =$transaction_id;
        $this->flutterwave->flutterwave_api_key =$flutterwave_api_key;
        $response_flutterwave = $this->flutterwave->success_action();

        $status = $response_flutterwave['status'];
        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response_flutterwave);
        $log_insert_data['error'] = "status_id: ".$status ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if($status == 'success'){
            $data = [];

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = Auth::user()->email;
            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');
    }


    public function myfatoorah_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $user_info = $this->get_user($buyer_user_id);
        $user_name = isset( $user_info->name) ? $user_info->name : '';
        $user_email = isset($user_info->email) ? $user_info->email : '';
        $user_mobile = isset($user_info->mobile) ? $user_info->mobile : '01234567890';
        $myfatoorah_data= isset($payment_config->myfatoorah) ? json_decode($payment_config->myfatoorah) : [];
        $this->myfatoorah->myfatoorah_api_key = $myfatoorah_data->myfatoorah_api_key;
        $this->myfatoorah->myfatoorah_mode = $myfatoorah_data->myfatoorah_mode ?? 'sandbox';
        $this->myfatoorah->myfatoorah_currency = $payment_config->currency ?? 'KWD';
        $redirect_url_myfatoorah = route('payment-myfatoorah-success',[$package_id,$buyer_user_id,$parent_user_id]);
        $this->myfatoorah->purpose =$package_name;
        $this->myfatoorah->amount = $payment_amount;
        $this->myfatoorah->callbackurl = $redirect_url_myfatoorah;
        $this->myfatoorah->errorUrl = $redirect_url_myfatoorah;
        $this->myfatoorah->buyer_name = $user_name;
        $this->myfatoorah->email = $user_email;
        $this->myfatoorah->phone = $user_mobile;
        $this->myfatoorah->button_lang = __('Pay With myfatoorah');
        $checkout_url = $this->myfatoorah->get_long_url();
        return redirect()->to($checkout_url);
    }

    public function myfatoorah_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $currency = $payment_config->currency ?? 'USD';
        $myfatoorah_data= isset($payment_config->myfatoorah) ? json_decode($payment_config->myfatoorah) : [];
        $this->myfatoorah->myfatoorah_api_key = $myfatoorah_data->myfatoorah_api_key;
        $this->myfatoorah->myfatoorah_mode = $myfatoorah_data->myfatoorah_mode ?? 'sandbox';
        $payment_id = $_GET['paymentId'];
        $this->myfatoorah->payment_id =$payment_id;
        $response = $this->myfatoorah->success_action();
        $payment_type = "myfatoorah";
        $buyer_email =  isset($response['email']) ? $response['email']: Auth::user()->email;

        if(isset($response['Data']['InvoiceStatus']) && $response['Data']['InvoiceStatus'] == "Paid"){
            $transaction_id = isset($response['Data']['InvoiceTransactions'][0]['TransactionId']) ? $response['Data']['InvoiceTransactions'][0]['TransactionId'] : 0;
            $data = [];
            $log_insert_data['buyer_user_id'] = $buyer_user_id;
            $log_insert_data['call_time'] = date("Y-m-d H:i:s");
            $log_insert_data['api_response'] = json_encode($response);
            $log_insert_data['error'] = $response['Data']['InvoiceStatus'] ?? null;
            $log_insert_data['payment_method'] = $payment_type;
            DB::table("payment_api_logs")->insert($log_insert_data);

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = $buyer_email;
            $data['first_name'] = $response['Data']['CustomerName'];

            $this->success_action($data);
        }
        else{

            $this->cancel_action();
        }

        return redirect()->route('transaction-log');

    }

    public function senangpay_action()
    {

        $status_id = request()->status_id ?? 0;
        $order_id = request()->order_id ?? 0;
        $transaction_id = request()->transaction_id ?? 0;
        $response = request()->all();

        if($status_id == 1){
            $ex_order_id = explode("_", $order_id);
            $package_id = isset($ex_order_id[0]) ? $ex_order_id[0]:0;
            $payment_config = $this->get_payment_config_parent();
            $package_data = $this->get_package($package_id);

            $user_id = isset($ex_order_id[1]) ? $ex_order_id[1]:0;
            $payment_type = 'senangpay';
            $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
            $payment_amount = (float) $package_data->price ?? 0;
            $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
            $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
            $currency = $payment_config->currency ?? 'USD';
            $buyer_email =  Auth::user()->email;

            $log_insert_data['buyer_user_id'] = $user_id;
            $log_insert_data['call_time'] = date("Y-m-d H:i:s");
            $log_insert_data['api_response'] = json_encode($response);
            $log_insert_data['error'] = $response['status'] ?? null;
            $log_insert_data['payment_method'] = $payment_type;
            DB::table("payment_api_logs")->insert($log_insert_data);

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $user_id;
            $data['parent_user_id'] = Auth::user()->parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = $buyer_email;

            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');

    }


    public function mollie_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $reference=  ltrim(request()->reference,'/');
        $payment_type = 'mollie';
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $mollie_data = isset($payment_config->mollie) ? json_decode($payment_config->mollie) : [];
        $mollie_api_key = $mollie_data->mollie_api_key;
        $this->mollie_class_ecommerce->ec_order_id=session('mollie_unique_id');
        $this->mollie_class_ecommerce->api_key=$mollie_api_key;
        $response= $this->mollie_class_ecommerce->mollie_payment_action($reference);

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if(isset($response['status']) && $response['status']=='Error'){
            session(['payment_success' => '0']);
            return redirect()->route('transaction-log');
        }

        $currency = isset($response['charge_info']['amount']['currency']) ? $response['charge_info']['amount']['currency'] : "EUR";
        $currency = strtoupper($currency);
        $payment_amount = isset($response['charge_info']['amount']['value']) ? $response['charge_info']['amount']['value'] : "0";
        $transaction_id = isset($response['charge_info']['id']) ? $response['charge_info']['id'] : "";

        $data['package_id'] = $package_id;
        $data['buyer_user_id'] = $buyer_user_id;
        $data['parent_user_id'] = $parent_user_id;
        $data['transaction_id'] = $transaction_id;
        $data['payment_type'] = $payment_type;
        $data['paid_amount'] = $payment_amount;
        $data['currency'] = $currency;
        $data['buyer_email'] =  Auth::user()->email;;

        $this->success_action($data);

        return redirect()->route('transaction-log');
    }

    public function paymaya_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $user_info = $this->get_user($buyer_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $paymaya_data = isset($payment_config->paymaya) ? json_decode($payment_config->paymaya) : [];
        $paymaya_public_key = $paymaya_data->paymaya_public_key ?? '';
        $paymaya_secret_key = $paymaya_data->paymaya_secret_key ?? '';
        $paymaya_mode = $paymaya_data->paymaya_mode ?? 'sandbox';
        $success_url_paymaya= route('payment-paymaya-success', [$package_id,$buyer_user_id,$parent_user_id]);
        $failure_url_paymaya = route('payment-paymaya-success', [$package_id,$buyer_user_id,$parent_user_id]);
        $cancel_url_paymaya = route('payment-paymaya-success', [$package_id,$buyer_user_id,$parent_user_id]);
        $user_name = isset( $user_info->name) ? $user_info->name : '';
        $user_email = isset($user_info->email) ? $user_info->email : '';
        $user_mobile = isset($user_info->mobile) ? $user_info->mobile : '';
        $this->paymaya->paymaya_public_key = $paymaya_public_key;
        $this->paymaya->paymaya_secret_key = $paymaya_secret_key ;
        $this->paymaya->paymaya_mode = $paymaya_mode;
        $this->paymaya->purpose =$package_name;
        $this->paymaya->amount = $payment_amount;
        $this->paymaya->success_url = $success_url_paymaya;
        $this->paymaya->failure_url = $failure_url_paymaya;
        $this->paymaya->cancel_url = $cancel_url_paymaya;
        $this->paymaya->buyer_name = $user_name;
        $this->paymaya->email = $user_email;
        $this->paymaya->button_lang = __('Pay with Paymaya');
        $response = $this->paymaya->checkout_url();
        $checkout_id =  $response['checkoutId'];
        $checkout_url = $response['redirectUrl'];
        session(['paymaya_checkoutId' => $checkout_id]);
        return redirect()->to($checkout_url);
    }

    public function paymaya_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $user_id = $buyer_user_id;
        $payment_type = 'paymaya';
        $paymaya_checkoutId = session('paymaya_checkoutId');
        $currency = $payment_config->currency ?? 'USD';

        $paymaya_data = isset($payment_config->paymaya) ? json_decode($payment_config->paymaya) : [];
        $paymaya_secret_key = $paymaya_data->paymaya_secret_key ?? '';
        $paymaya_mode = $paymaya_data->paymaya_mode ?? 'sandbox';
        $this->paymaya->paymaya_secret_key = $paymaya_secret_key ;
        $this->paymaya->paymaya_mode = $paymaya_mode;
        $response = $this->paymaya->get_checkoutid($paymaya_checkoutId);

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['paymentStatus'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if(isset($response['paymentStatus']) == "PAYMENT_SUCCESS"){
            $data = [];
            $transaction_id = isset($response['paymentDetails']['responses']['efs']['receipt']['transactionId']) ? $response['paymentDetails']['responses']['efs']['receipt']['transactionId'] : 0;

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = Auth::user()->email;;
            $data['first_name'] = $response['buyer']['firstName'];
            $data['last_name'] =  $response['buyer']['lastName'];

            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');
    }

    public function toyyibpay_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $toyyibpay_data= isset($payment_config->toyyibpay) ? json_decode($payment_config->toyyibpay) : [];
        $toyyibpay_secret_key = $toyyibpay_data->toyyibpay_secret_key ?? '';
        $toyyibpay_category_code = $toyyibpay_data->toyyibpay_category_code ?? '';
        $toyyibpay_mode = $toyyibpay_data->toyyibpay_mode ?? 'sandbox';
        $user_info = $this->get_user($buyer_user_id);
        $user_name = isset( $user_info->name) ? $user_info->name : '';
        $user_email =  $user_email = isset($user_info->email) ? $user_info->email : '';
        $user_mobile = isset($user_info->mobile) ? $user_info->mobile : '012345678901';
        $redirect_url_toyyibpay = route('payment-toyyibpay-success',[$package_id,$buyer_user_id,$parent_user_id]);
        $this->toyyibpay->toyyibpay_secret_key = $toyyibpay_secret_key;
        $this->toyyibpay->toyyibpay_category_code = $toyyibpay_category_code;
        $this->toyyibpay->toyyibpay_mode = $toyyibpay_mode;
        $this->toyyibpay->purpose =$package_name;
        $this->toyyibpay->amount = $payment_amount;
        $this->toyyibpay->redirect_url = $redirect_url_toyyibpay;
        $this->toyyibpay->buyer_name = $user_name;
        $this->toyyibpay->email = $user_email;
        $this->toyyibpay->phone = $user_mobile;
        $this->toyyibpay->button_lang = __('Pay with toyyibpay');
        $checkout_url=$this->toyyibpay->get_billcode();

        return redirect()->to($checkout_url);
    }

    public function toyyibpay_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $payment_type = 'toyyibpay';
        $response = $_GET;
        $billcode = $response['billcode'];
        $transaction_id = $response['transaction_id'];
        $this->toyyibpay->billcode = $billcode;
        $currency = $payment_config->currency ?? 'USD';
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $toyyibpay_data= isset($payment_config->toyyibpay) ? json_decode($payment_config->toyyibpay) : [];
        $this->toyyibpay->toyyibpay_secret_key = $toyyibpay_data->toyyibpay_secret_key ?? '';
        $this->toyyibpay->toyyibpay_category_code = $toyyibpay_data->toyyibpay_category_code ?? '';
        $this->toyyibpay->toyyibpay_mode = $toyyibpay_data->toyyibpay_mode ?? 'sandbox';
        $response_toyyib = $this->toyyibpay->success_action();
        $second_response = $_GET;
        $status_id = $second_response['status_id'];

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response_toyyib);
        $log_insert_data['error'] = "status_id: ".$status_id ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if($status_id == 1 || $status_id == 2){
            $data = [];

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = Auth::user()->email;

            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');
    }


    public function xendit_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {

        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $payment_amount = $package_data->price ?? 0;
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $xendit_data= isset($payment_config->xendit) ? json_decode($payment_config->xendit) : [];
        $xendit_secret_api_key = $xendit_data->xendit_secret_api_key;
        $currency = $payment_config->currency ?? 'USD';
        $user_info = $this->get_user($buyer_user_id);
        $user_name = isset( $user_info->name) ? $user_info->name : '';
        $user_email =  $user_email = isset($user_info->email) ? $user_info->email : '';
        $user_mobile = isset($user_info->mobile) ? $user_info->mobile : '012345678901';

        $xendit_success_redirect_url = route('payment-xendit-success',[$package_id,$buyer_user_id,$parent_user_id]);
        $xendit_failure_redirect_url = route('payment-xendit-fail',[$package_id,$buyer_user_id,$parent_user_id]);
        $external_id = 'xendit_'.uniqid();
        $this->xendit->external_id =$external_id;
        $this->xendit->payer_email =$user_email;
        $this->xendit->description =$package_name;
        $this->xendit->amount = $payment_amount;
        $this->xendit->xendit_secret_api_key = $xendit_secret_api_key;
        $this->xendit->xendit_success_redirect_url = $xendit_success_redirect_url;
        $this->xendit->xendit_failure_redirect_url = $xendit_failure_redirect_url;
        $this->xendit->currency = $currency ;
        $this->xendit->button_lang = __('Pay With Xendit');

        $response = $this->xendit->get_long_url();
        if(!isset($response['invoice_url'])){
            dd($response);
        }
        $checkout_url = $response['invoice_url'];
        return redirect()->to($checkout_url);
    }

    public function xendit_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $payment_type = 'xendit';
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = $package_data->price ?? 0;
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $currency = $payment_config->currency ?? 'USD';
        $xendit_data= isset($payment_config->xendit) ? json_decode($payment_config->xendit) : [];
        $this->xendit->xendit_secret_api_key = $xendit_data->xendit_secret_api_key;
        $response = $this->xendit->success_action();
        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        $status_found =  $response[0]['status'] ?? '';
        $status_found = strtolower($status_found);

        if(in_array($status_found,['paid','settled','completed'])){
            $transaction_id = isset($response[0]['external_id']) ? $response[0]['external_id'] : 0;
            $data = [];

            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = Auth::user()->email;

            $this->success_action($data);

        }
        else $this->cancel_action();

        return redirect()->route('transaction-log');
    }

    public function xendit_fail()
    {
        return redirect()->route('transaction-log',['action'=>'cancel']);
    }

    public function sslcommerz_action()
    {

        $payment_config = $this->get_payment_config_parent();
        $sslcommerz_data= isset($payment_config->sslcommerz) ? json_decode($payment_config->sslcommerz) : [];
        $store_id =  $sslcommerz_data->sslcommerz_store_id;
        $store_passwd =$sslcommerz_data->sslcommerz_store_password;;
        $sslcommerz_mode = $sslcommerz_data->sslcommerz_mode ?? 'sandbox';

        $response = $_REQUEST['cart_json'];
        $response = json_decode($response, true);
        $total_amount = isset($response['total_amount']) ? $response['total_amount'] : '';
        $currency = isset($response['currency']) ? $response['currency'] : '';
        $product_name = isset($response['product_name']) ? $response['product_name'] : '';
        $product_category = isset($response['product_category']) ? $response['product_category'] : '';
        $cus_name = isset($response['cus_name']) ? $response['cus_name'] : '';
        $cus_email = isset($response['cus_email']) ? $response['cus_email'] : '';
        $package_id = isset($response['package_id']) ? $response['package_id'] : 0;
        $user_id = isset($response['user_id']) ? $response['user_id'] : 0;
        $post_data = array();
        $post_data['value_a'] = $user_id;
        $post_data['value_b'] = $package_id;
        $post_data['value_c'] = Auth::user()->parent_user_id;
        $post_data['store_id'] = $store_id;
        $post_data['store_passwd'] = $store_passwd;
        $post_data['total_amount'] = $total_amount;
        $post_data['currency'] = $currency;
        $post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
        $post_data['success_url'] = route('payment-sslcommerz-success');
        $post_data['fail_url'] = route('payment-sslcommerz-fail');
        $post_data['cancel_url'] = route('payment-sslcommerz-fail');

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $cus_name;
        $post_data['cus_email'] = $cus_email;
        $post_data['value_d'] = $cus_email;
        $post_data['cus_add1'] = "N/A";
        $post_data['cus_city'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "";
        $post_data['cus_phone'] = 'N/A';

        # SHIPMENT INFORMATION
        $post_data['shipping_method'] = "NO";
        $post_data['num_of_item'] = 1;

        #product Details
        $post_data['product_name'] = $product_name;
        $post_data['product_category'] = $product_category;
        $post_data['product_profile'] = "general";

        # REQUEST SEND TO SSLCOMMERZ
        if($sslcommerz_mode == 'live')
            $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
        else
            $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle );

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        // store log
        $log_insert_data['buyer_user_id'] = $user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($content);
        $log_insert_data['error'] = $code ?? null;
        $log_insert_data['payment_method'] = 'sslcommerz';
        DB::table("payment_api_logs")->insert($log_insert_data);

        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true );

        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="") {
            // this is important to show the popup, return or echo to sent json response back
            echo json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
        }
        else {
            $error = isset($sslcz['failedreason']) ? $sslcz['failedreason'] : __('JSON Data parsing error!');
            echo json_encode(['status' => 'fail', 'data' => null, 'message' => $error]);
        }
    }

    public function sslcommerz_success()
    {
        $user_id = isset($_POST['value_a']) ? $_POST['value_a'] : null;
        $package_id = isset($_POST['value_b']) ? $_POST['value_b'] : null;
        $email =  isset($_POST['value_d']) ? $_POST['value_d'] : '';
        $currency =  isset($_POST['currency']) ? $_POST['currency'] : 'USD';
        $total_amount =  isset($_POST['total_amount']) ? $_POST['total_amount'] : '0';
        $parent_user_id = isset($_POST['value_c']) ? $_POST['value_c'] : null;
        $transaction_id = isset($_POST['bank_tran_id']) ? $_POST['bank_tran_id'] : 0;
        $data = [];

        $data['package_id'] = $package_id;
        $data['buyer_user_id'] = $user_id;
        $data['parent_user_id'] = $parent_user_id;
        $data['transaction_id'] = $transaction_id;
        $data['payment_type'] = 'sslcommerz';
        $data['paid_amount'] = $total_amount;
        $data['currency'] = $currency;
        $data['buyer_email'] = $email;

        $this->success_action($data);

        return redirect()->route('transaction-log');
    }

    public function sslcommerz_fail(){
        return redirect()->route('transaction-log',['action'=>'cancel']);
    }

    public function instamojo_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {

        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';

        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $user_info = $this->get_user($buyer_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $instamojo_data = isset($payment_config->instamojo) ? json_decode($payment_config->instamojo) : [];
        $instamojo_api_key = $instamojo_data->instamojo_api_key ?? '';
        $instamojo_auth_token = $instamojo_data->instamojo_auth_token ?? '';
        $instamojo_mode = $instamojo_data->instamojo_mode ?? 'sandbox';
        $redirect_url_instamojo = route('payment-instamojo-success', [$package_id,$buyer_user_id,$parent_user_id]);
        $this->instamojo->instamojo_api_key = $instamojo_api_key;
        $this->instamojo->instamojo_auth_token = $instamojo_auth_token;
        $this->instamojo->instamojo_mode = $instamojo_mode;
        $this->instamojo->purpose = $package_name;
        $this->instamojo->amount = $payment_amount;
        $this->instamojo->redirect_url = $redirect_url_instamojo;
        $this->instamojo->buyer_name = isset($user_info->name) ? $user_info->name : '';
        $this->instamojo->email = isset($user_info->email) ? $user_info->email : '';

        $checkout_url = $this->instamojo->get_long_url();
        if ($checkout_url) {
            return redirect()->away($checkout_url);
        } else {
          return   redirect(route('transaction-log') . '?action=cancel');
        }
    }

    public function instamojo_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $payment_id = $_GET['payment_id'];
        $user_info = $this->get_user($buyer_user_id);
        $package_data = $this->get_package($package_id);
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $payment_request_id = $_GET['payment_request_id'];
        $currency = $payment_config->currency ?? 'USD';
        $instamojo_data = isset($payment_config->instamojo) ? json_decode($payment_config->instamojo) : [];
        $instamojo_api_key = $instamojo_data->instamojo_api_key ?? '';
        $instamojo_auth_token = $instamojo_data->instamojo_auth_token ?? '';
        $instamojo_mode = $instamojo_data->instamojo_mode ?? 'sandbox';
        $this->instamojo->instamojo_api_key = $instamojo_api_key;
        $this->instamojo->instamojo_auth_token = $instamojo_auth_token;
        $this->instamojo->instamojo_mode = $instamojo_mode;
        $this->instamojo->payment_id = $payment_id;
        $this->instamojo->payment_request_id = $payment_request_id;
        $response = $this->instamojo->success_action();
        $payment_type = 'instamojo';

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['success'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        if (isset($response['success']) && $response['success'] == 1) {
            $data = [];
            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = isset($response['payment_request']['id']) ? $response['payment_request']['id'] : 0;;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = isset($user_info->email) ? $user_info->email : '';

            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');
    }



    public function instamojo_v2_action($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {

        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $user_info = $this->get_user($buyer_user_id);
        $currency = $payment_config->currency ?? 'USD';
        $instamojo_v2_data = isset($payment_config->instamojo_v2) ? json_decode($payment_config->instamojo_v2) : [];
        $instamojo_client_id = $instamojo_v2_data->instamojo_client_id ?? '';
        $instamojo_client_secret = $instamojo_v2_data->instamojo_client_secret ?? '';
        $instamojo_v2_mode = $instamojo_v2_data->instamojo_v2_mode ?? 'sandbox';
        $redirect_url_instamojo_v2 = route('payment-instamojo-v2-success', [$package_id,$buyer_user_id,$parent_user_id]);
        $this->instamojo_v2->instamojo_client_id = $instamojo_client_id;
        $this->instamojo_v2->instamojo_client_secret = $instamojo_client_secret;
        $this->instamojo_v2->instamojo_v2_mode = $instamojo_v2_mode;
        $this->instamojo_v2->purpose = $package_name;
        $this->instamojo_v2->amount = $payment_amount;
        $this->instamojo_v2->redirect_url_v2 = $redirect_url_instamojo_v2;
        $this->instamojo_v2->buyer_name = isset($user_info->name) ? $user_info->name : '';
        $this->instamojo_v2->email = isset($user_info->email) ? $user_info->email : '';

        $checkout_url = $this->instamojo_v2->get_long_url_v2();
        if ($checkout_url) {
            return redirect()->away($checkout_url);
        } else {
          return   redirect(route('transaction-log') . '?action=cancel');
        }
    }

    public function instamojo_v2_success($package_id=0,$buyer_user_id=0,$parent_user_id=0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $payment_id = $_GET['payment_id'];
        $user_info = $this->get_user($buyer_user_id);
        $package_data = $this->get_package($package_id);
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $payment_request_id = $_GET['payment_request_id'];
        $currency = $payment_config->currency ?? 'USD';
        $instamojo_v2_data = isset($payment_config->instamojo_v2) ? json_decode($payment_config->instamojo_v2) : [];
        $instamojo_client_id = $instamojo_v2_data->instamojo_client_id ?? '';
        $instamojo_client_secret = $instamojo_v2_data->instamojo_client_secret ?? '';
        $instamojo_v2_mode = $instamojo_v2_data->instamojo_v2_mode ?? 'sandbox';
        $this->instamojo_v2->instamojo_client_id = $instamojo_client_id;
        $this->instamojo_v2->instamojo_client_secret = $instamojo_client_secret;
        $this->instamojo_v2->instamojo_v2_mode = $instamojo_v2_mode;
        $this->instamojo_v2->payment_id = $payment_id;
        $this->instamojo_v2->payment_request_id = $payment_request_id;
        $response = $this->instamojo_v2->success_action_v2();

        $payment_type = 'instamojo_v2';

        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);
        if (isset($response['status']) && $response['status'] == 'true') {
            $data = [];
            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = isset($response['id']) ? $response['id'] : 0;;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = isset($user_info->email) ? $user_info->email : '';

            $this->success_action($data);
        }
        else{
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');
    }


     public function yoomoney_action($package_id = 0, $buyer_user_id = 0, $parent_user_id = 0)
    {
        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $yoomoney_data = isset($payment_config->yoomoney) ? json_decode($payment_config->yoomoney) : [];
        $yoomoney_secret_key = $yoomoney_data->yoomoney_secret_key;
        $yoomoney_shop_id = $yoomoney_data->yoomoney_shop_id;
        $currency = $payment_config->currency ?? 'USD';
        $user_info = $this->get_user($buyer_user_id);
        $user_email =  $user_email = isset($user_info->email) ? $user_info->email : '';

        $yoomoney_success_redirect_url = route('payment-yoomoney-success', [$package_id, $buyer_user_id, $parent_user_id]);
        $external_id = uniqid('', true);
        $this->yoomoney->external_id = $external_id;
        $this->yoomoney->payer_email = $user_email;
        $this->yoomoney->description = $package_name;
        $this->yoomoney->amount = $payment_amount;
        $this->yoomoney->order_id = $package_id;
        $this->yoomoney->yoomoney_secret_key = $yoomoney_secret_key;
        $this->yoomoney->yoomoney_shop_id = $yoomoney_shop_id;
        $this->yoomoney->external_id = $external_id;
        $this->yoomoney->yoomoney_success_redirect_url = $yoomoney_success_redirect_url;
        $this->yoomoney->currency = $currency;
        $this->yoomoney->button_lang = __('Pay With YooMoney');

        $response = $this->yoomoney->get_long_url();
        $invoice_id = $response['id'];
        session([
            "yoomoney_invoice_id" => $invoice_id,
        ]);
        if(!isset($response['confirmation']['confirmation_url'])){
            dd($response);
        }
        else{
            $checkout_url = $response['confirmation']['confirmation_url'];
        }
        return redirect()->to($checkout_url);
    }
    public function yoomoney_success($package_id = 0, $buyer_user_id = 0, $parent_user_id = 0)
    {

        $payment_config = $this->get_payment_config_parent($parent_user_id);
        $payment_type = 'yoomoney';
        $package_data = $this->get_package($package_id);
        $package_name = $package_data->package_name ?? '';
        $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>'0','currency_position'=>$payment_config->currency_position ?? 'left'];
        $payment_amount = (float) $package_data->price ?? 0;
        $price_raw_data = format_price($payment_amount,$format_settings,$package_data->discount_data,['return_raw_array'=>true]);
        $payment_amount = (float) $price_raw_data->sale_price_formatted ?? 0;
        $currency = $payment_config->currency ?? 'USD';
        $this->yoomoney->invoice_id = session("yoomoney_invoice_id");
        $yoomoney_data = isset($payment_config->yoomoney) ? json_decode($payment_config->yoomoney) : [];
        $this->yoomoney->yoomoney_secret_key = $yoomoney_data->yoomoney_secret_key;
        $this->yoomoney->yoomoney_shop_id = $yoomoney_data->yoomoney_shop_id;
        $response = $this->yoomoney->success_action();
        $log_insert_data['buyer_user_id'] = $buyer_user_id;
        $log_insert_data['call_time'] = date("Y-m-d H:i:s");
        $log_insert_data['api_response'] = json_encode($response);
        $log_insert_data['error'] = $response['status'] ?? null;
        $log_insert_data['payment_method'] = $payment_type;
        DB::table("payment_api_logs")->insert($log_insert_data);

        $data = [];
        if (isset($response['status']) && $response['status'] == 'succeeded') {
            $transaction_id = isset($response['id']) ? $response['id'] : 0;
            $data['package_id'] = $package_id;
            $data['buyer_user_id'] = $buyer_user_id;
            $data['parent_user_id'] = $parent_user_id;
            $data['transaction_id'] = $transaction_id;
            $data['payment_type'] = $payment_type;
            $data['paid_amount'] = $payment_amount;
            $data['currency'] = $currency;
            $data['buyer_email'] = Auth::user()->email;
            Session::forget('yoomoney_invoice_id');
            $this->success_action($data);
        } else {
            $this->cancel_action();
        }

        return redirect()->route('transaction-log');
    }


    //  Dep-recated legacy version
    public function success_transaction($data)
    {
        $user_id = $data['buyer_user_id'];
        $where = array('user_id' => $user_id);
        $select = array('cycle_start_date', 'cycle_expired_date');
        $prev_payment_info = DB::table('transaction_logs')
            ->select($select)
            ->where($where)
            ->first();

        $prev_cycle_expired_date = "";
        $price = 0;
        $package_id = $data['package_id'];
        $get_payment_validity_data = $this->get_payment_validity_data($buyer_user_id,$package_id);
        $package_name = $get_payment_validity_data['package_name'] ?? '';
        $user_email = $get_payment_validity_data['email'] ?? '';
        $user_name = $get_payment_validity_data['name'] ?? '';
        $is_agency = '0';

        $package_data = $this->get_package($package_id);
        if (is_array($package_data) && array_key_exists(0, $package_data)) {
            $price = $package_data->price;
        }
        $validity = $package_data->validity;
        $payment_amount = isset($package_data->price) ? $package_data->price : 0;
        $validity_str = '+' . $validity . ' day';
        if($prev_payment_info){
            $prev_cycle_expired_date = $prev_payment_info->cycle_expired_date;
        }
        if ($prev_cycle_expired_date == "") {
            $cycle_start_date = date('Y-m-d');
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        } else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))) {
            $cycle_start_date = date('Y-m-d');
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        } else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))) {
            $cycle_start_date = date("Y-m-d", strtotime('+1 day', strtotime($prev_cycle_expired_date)));
            $cycle_expired_date = date("Y-m-d", strtotime($validity_str, strtotime($cycle_start_date)));
        }
         /** insert the transaction into database ***/
        $receiver_email = isset($data['email'])? $data['email'] : Auth::user()->email;
        $payment_type = isset($data['payment_type'])? $data['payment_type'] : '';

        $paid_at = date("Y-m-d H:i:s");
        $country = "";
        $payment_date = date('Y-m-d H:i:s');
        $insert_data = array(
            "verify_status" => 'VERIFIED',
            "user_id" => $data['parent_user_id'],
            "buyer_user_id" => $data['buyer_user_id'],
            "first_name" => isset($data['first_name'])? $data['first_name'] : '',
            "last_name" => isset($data['last_name'])? $data['last_name'] : '',
            "buyer_email" => $receiver_email,
            "country" => $country,
            "paid_currency" => isset($data['currency'])? $data['currency'] : '',
            "paid_at" => $paid_at,
            "payment_method" => $payment_type,
            "transaction_id" => $data['transaction_id'],
            "paid_amount" => isset($data['paid_amount'])?$data['paid_amount']:$payment_amount,
            "cycle_start_date" => $cycle_start_date,
            "cycle_expired_date" => $cycle_expired_date,
            "package_id" => $package_id,
            "response_source" => '',
            "package_name" => $package_name,
            "user_email" => $user_email, // not for insert, for sending email
            "user_name" => $user_name // not for insert, for sending email
        );


        $is_whitelabel = $is_agency;
        if($this->complete_payment($insert_data,$is_agency,$is_whitelabel,$payment_type)) session(['payment_success' => '1']);
        else session(['payment_success' => '0']);

        return redirect()->route('transaction-log');
    }



    private function success_action($data)
    {
        $buyer_user_id = $data['buyer_user_id'];
        $parent_user_id = $data['parent_user_id'];
        $currency = $data['currency'];
        $payment_type = $data['payment_type'];
        $transaction_id = $data['transaction_id'];
        $payment_amount = $data['paid_amount'];
        $package_id = $data['package_id'];
        $buyer_email = $data['buyer_email'];

        set_agency_config($buyer_user_id);
        $get_payment_validity_data = $this->get_payment_validity_data($buyer_user_id,$package_id);

        $package_name = $get_payment_validity_data['package_name'] ?? '';
        $user_email = $get_payment_validity_data['email'] ?? '';
        $user_name = $get_payment_validity_data['name'] ?? '';
        $is_agency = '0';
        $cycle_start_date = $get_payment_validity_data['cycle_start_date'] ?? date("Y-m-d");
        $cycle_expired_date = $get_payment_validity_data['cycle_expired_date'] ?? date("Y-m-d");
        $paid_at = date("Y-m-d H:i:s");
        $country =  '';

        $insert_data=array(
            "verify_status"     => 'VERIFIED',
            "user_id"           => $parent_user_id,
            "buyer_user_id"     => $buyer_user_id ,
            "first_name"        => isset($data['first_name']) ? $data['first_name']:$user_name,
            "last_name"         => isset($data['last_name']) ? $data['last_name']:'',
            "buyer_email"       => $buyer_email,
            "country"           => $country,
            "paid_currency"     => $currency,
            "paid_at"           => $paid_at,
            "payment_method"    => $payment_type,
            "transaction_id"    => $transaction_id,
            "paid_amount"       => $payment_amount,
            "cycle_start_date"  => $cycle_start_date,
            "cycle_expired_date"=> $cycle_expired_date,
            "package_id"        => $package_id,
            "response_source"   => '',
            "package_name"      => $package_name,
            "user_email"        => $user_email, // not for insert, for sending email
            "user_name"         => $user_name // not for insert, for sending email
        );
        $is_whitelabel = $is_agency;
        if($this->complete_payment($insert_data,$is_agency,$is_whitelabel,$payment_type)) session(['payment_success' => '1']);
        else session(['payment_success' => '0']);
    }

    private function cancel_action()
    {
        if (config("app.auto_relogin_after_purchase") == '1') {
            Session::forget('user_type');
            Session::forget('logged_in');
            Session::forget('username');
            Session::forget('user_id');
            Session::forget('download_id');
            Session::forget('user_login_email');
            Session::forget('expiry_date');
            Session::forget('brand_logo');
        }
    }


}
