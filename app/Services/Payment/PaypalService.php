<?php
namespace App\Services\Payment;
class PaypalService implements PaypalServiceInterface{
    public $mode;
    public $paypal_url;
    public $success_url;
    public $cancel_url;
    public $notify_url;
    public $business_email;
    public $product_quantity=1;
    public $product_name;
    public $product_number;
    public $amount;
    public $a3;
    public $p3;
    public $t3;
    public $src;
    public $sra;
    public $is_recurring;
    public $shipping_amount=0;
    public $currency="USD";
    public $user_id;
    public $package_id;
    public $button_lang='';
    public $secondary_button=false;
    public $plan_id = '';
    public $paypal_client_id='';
    public $paypal_client_secret='';
    public $paypal_app_id='';
    public $provider='';
    public $name='';
    public $email='';
    public $product_information =[];
    public $subscription_id;

    function __construct(){
    }
    function set_button(){

        $provider = $this->provider;
        if($this->mode == 'sandbox'){
           $config = [
               'mode'    => 'sandbox',
               'sandbox' => [
                   'client_id'         => $this->paypal_client_id,
                   'client_secret'     => $this->paypal_client_secret,
                   'app_id'            => $this->paypal_app_id,
               ],
               'payment_action' => 'Sale',
               'currency'       => $this->currency,
               'notify_url'     => $this->notify_url,
               'locale'         => 'en_US',
               'validate_ssl'   => true,
           ];
           $provider->setApiCredentials($config);
        }
        else{
            $config = [
                'mode'    => 'live',
                'live' => [
                    'client_id'         => $this->paypal_client_id,
                    'client_secret'     => $this->paypal_client_secret,
                    'app_id'            => $this->paypal_app_id,
                ],
                'payment_action' => 'Sale',
                'currency'       => $this->currency,
                'notify_url'     => $this->notify_url,
                'locale'         => 'en_US',
                'validate_ssl'   => true,
            ];
            $provider->setApiCredentials($config); 
        }
        $paypal_lang = !empty($this->button_lang) ? $this->button_lang : $this->__("Pay with PayPal");
        $hide_me = $this->secondary_button ? 'display:none;' : '';
        $button="";

        $button.= "<form action='{$this->paypal_url}' class='p-0 m-0' method='post' style='".$hide_me."' id='paypalPaymentForm01'>";

        if (isset($this->is_recurring) && $this->is_recurring == true) $button.= "<input type='hidden' name='cmd' value='_xclick-subscriptions' />";
        else $button.= "<input type='hidden' name='cmd' value='_xclick' />";

        $button.= "<input type='hidden' name='business' value='{$this->business_email}' />";
        $button.= "<input type='hidden' name='quantity' value='{$this->product_quantity}' />";
        $button.= "<input type='hidden' name='item_name' value='{$this->product_name}' />";
        $button.= "<input type='hidden' name='item_number' value='{$this->product_number}' />";

        if (!isset($this->a3)) $button.= "<input type='hidden' name='amount' value='{$this->amount}' />";
        else $button.= "<input type='hidden' name='a3' value='{$this->a3}' />";

        if (isset($this->is_recurring) && $this->is_recurring == true)
        {

            if (isset($this->p3)) $button.= "<input type='hidden' name='p3' value='{$this->p3}' />";
            if (isset($this->t3)) $button.= "<input type='hidden' name='t3' value='{$this->t3}' />";
            if (isset($this->src)) $button.= "<input type='hidden' name='src' value='{$this->src}' />";
            if (isset($this->sra)) $button.= "<input type='hidden' name='sra' value='{$this->sra}' />";
        }

        $button.= "<input type='hidden' name='shipping' value='{$this->shipping_amount}' />";
        $button.= "<input type='hidden' name='plan_id' value='{$this->plan_id}' />";
        $button.= "<input type='hidden' name='no_note' value='1' />";
        $button.= "<input type='hidden' name='notify_url' value='{$this->notify_url}'>";
        $button.= "<input type='hidden' name='currency_code' value='{$this->currency}' />";
        $button.= "<input type='hidden' name='return' value='{$this->success_url}'>";
        $button.= "<input type='hidden' name='cancel_return' value='{$this->cancel_url}'>";
        $button.= "<input type='hidden' name='custom' value='{$this->user_id}_{$this->package_id}'>";
        $button.= "<button type='submit' class='btn paypal_button_sn'>Pay with PayPal</button>";

        $button.= "</form>";

        if($this->secondary_button)
        $button.="
        <a href='#' class='list-group-item list-group-item-action flex-column align-items-start' id='paypal_clone' onclick=\"document.getElementById('paypalPaymentForm01').submit();\">
            <div class='d-flex w-100 align-items-center'>
              <small class='text-muted'><img class='rounded' width='60' height='60' src='".asset('assets/images/paypal.png')."'></small>
              <h5 class='mb-1'>".$paypal_lang."</h5>
            </div>
        </a>";
        return $button;

    }


    function run_ipn($paypal_mode="sandbox"){
        $req = 'cmd=' . urlencode('_notify-validate');
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }

        if($paypal_mode=='sandbox') $paypal_url="https://www.sandbox.paypal.com/cgi-bin/webscr";
        else $paypal_url="https://www.paypal.com/cgi-bin/webscr";
        $ch = curl_init();
        $headers = array("Content-type: application/json");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$req);
        curl_setopt($ch, CURLOPT_URL, $paypal_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
        $st=curl_exec($ch);
        curl_close($ch);
        $response['verify_status']=$st;
        $response['data']=$_POST;
        return $response;
    }

    function paypal_subscriber_url(){
       $provider = $this->provider;
       if($this->mode == 'sandbox'){
          $config = [
              'mode'    => 'sandbox',
              'sandbox' => [
                  'client_id'         => $this->paypal_client_id,
                  'client_secret'     => $this->paypal_client_secret,
                  'app_id'            => $this->paypal_app_id,
              ],
              'payment_action' => 'Sale',
              'currency'       => $this->currency,
              'notify_url'     => $this->notify_url,
              'locale'         => 'en_US',
              'validate_ssl'   => true,
          ];
          $provider->setApiCredentials($config); 
       }
       else{
           $config = [
               'mode'    => 'live',
               'live' => [
                   'client_id'         => $this->paypal_client_id,
                   'client_secret'     => $this->paypal_client_secret,
                   'app_id'            => $this->paypal_app_id,
               ],
               'payment_action' => 'Sale',
               'currency'       => $this->currency,
               'notify_url'     => $this->notify_url,
               'locale'         => 'en_US',
               'validate_ssl'   => true,
           ];
           $provider->setApiCredentials($config); 
       }
       $provider->getAccessToken();
       $timestamp = time()+(2*60);
       $data = json_decode('{
         "plan_id": "'.$this->plan_id.'",
         "start_time": "'.gmdate("Y-m-d\TH:i:s\Z",$timestamp).'",
         "shipping_amount": {
            "currency_code": "'.$this->currency.'",
            "value": "0.00"
          },
         "subscriber": {
            "name": {
               "given_name": "'.$this->name.'"
            },
            "email_address": "'.$this->email.'"
         },
         "application_context": {
            "return_url": "'.$this->success_url.'",
            "cancel_url": "'.$this->cancel_url.'"
         }
       }', true);
       $subscription = $provider->createSubscription($data);
       if(isset($subscription['error'])){
        dd($subscription['error']['details']);
       }
       if(isset($subscription['status'])){
        return redirect($subscription['links'][0]['href'])->send();
       }

   }

   public function paypal_plan_create()
   {
       $provider = $this->provider;
       if($this->mode == 'sandbox'){
          $config = [
              'mode'    => 'sandbox',
              'sandbox' => [
                  'client_id'         => $this->paypal_client_id,
                  'client_secret'     => $this->paypal_client_secret,
                  'app_id'            => $this->paypal_app_id,
              ],
              'payment_action' => 'Sale',
              'currency'       => $this->currency,
              'notify_url'     => $this->notify_url,
              'locale'         => 'en_US',
              'validate_ssl'   => true,
          ];
          $provider->setApiCredentials($config); 
       }
       else{
           $config = [
               'mode'    => 'live',
               'live' => [
                   'client_id'         => $this->paypal_client_id,
                   'client_secret'     => $this->paypal_client_secret,
                   'app_id'            => $this->paypal_app_id,
               ],
               'payment_action' => 'Sale',
               'currency'       => $this->currency,
               'notify_url'     => $this->notify_url,
               'locale'         => 'en_US',
               'validate_ssl'   => true,
           ];
           $provider->setApiCredentials($config); 
       }
       $provider->getAccessToken();
       $data =  json_decode('{
         "name": "'.$this->product_information->package_name.'",
         "description": "'.$this->product_information->package_type.'",
         "type": "SERVICE",
         "category": "SOFTWARE",
         "image_url": "https://example.com/streaming.jpg",
         "home_url": "https://example.com/home"
       }', true);
       $request_id = 'create-product-'.time();

       $product = $provider->createProduct($data, $request_id);
       $data = json_decode('{
          "product_id": "'.$product['id'].'",
          "name": "'.$this->product_information->package_name.'",
          "description": "'.$this->product_information->package_name.'",
          "status": "ACTIVE",
          "billing_cycles": [
           {
             "frequency": {
               "interval_unit": "DAY",
               "interval_count": "'.$this->product_information->validity.'"
             },
             "tenure_type": "REGULAR",
             "sequence": 1,
             "total_cycles": 999,
             "pricing_scheme": {
               "fixed_price": {
                 "value": "'.$this->product_information->price.'",
                 "currency_code": "'.$this->currency.'"
               }
             }
           }
          ],
          "payment_preferences": {
            "auto_bill_outstanding": true,
            "setup_fee_failure_action": "CONTINUE",
            "payment_failure_threshold": 3
          }
       }', true);

      return $plan = $provider->createPlan($data,rand(10,1000));
   }

   public function paypal_subscription_cancel(){

        $provider = $this->provider;
        if($this->mode == 'sandbox'){
           $config = [
               'mode'    => 'sandbox',
               'sandbox' => [
                   'client_id'         => $this->paypal_client_id,
                   'client_secret'     => $this->paypal_client_secret,
                   'app_id'            => $this->paypal_app_id,
               ],
               'payment_action' => 'Sale',
               'currency'       => $this->currency,
               'notify_url'     => $this->notify_url,
               'locale'         => 'en_US',
               'validate_ssl'   => true,
           ];
           $provider->setApiCredentials($config); 
        }
        else{
            $config = [
                'mode'    => 'live',
                'live' => [
                    'client_id'         => $this->paypal_client_id,
                    'client_secret'     => $this->paypal_client_secret,
                    'app_id'            => $this->paypal_app_id,
                ],
                'payment_action' => 'Sale',
                'currency'       => $this->currency,
                'notify_url'     => $this->notify_url,
                'locale'         => 'en_US',
                'validate_ssl'   => true,
            ];
            $provider->setApiCredentials($config); 
        }
        $provider->getAccessToken();
        return $response = $provider->cancelSubscription($this->subscription_id, 'Switched plan');

   }
   
   public function paypal_get_app_id($paypal_client_id,$paypal_client_secret,$paypal_mode){

        if($paypal_mode == 'sandbox'){
            $endpoint = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
        }
        else{
            $endpoint = 'https://api-m.paypal.com/v1/oauth2/token';
        }
        $auth = base64_encode($paypal_client_id . ":" . $paypal_client_secret);


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $paypal_client_id . ':' . $paypal_client_secret);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $result = json_decode($result);
        return $result;

   }
   
}
