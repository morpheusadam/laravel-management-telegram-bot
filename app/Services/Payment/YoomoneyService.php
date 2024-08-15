<?php
namespace App\Services\Payment;
class YoomoneyService implements YoomoneyServiceInterface
{
    public $yoomoney_secret_key;
    public $yoomoney_shop_id;
    public $external_id;
    public $payer_email;
    public $description;
    public $buyer_name;
    public $amount;
    public $yoomoney_success_redirect_url;
    public $yoomoney_redirect_url;
    public $button_lang;
    public $currency;
    public $yoomoney_api_url = 'https://api.yookassa.ru/v3/payments/';
    public $payment_id;
    public $invoice_id;
    public $final_url;

    function __construct(){
    }

    function set_button(){
        
        $button_lang = "Pay with YooMoney";
        $button = "
        <a href='".$this->yoomoney_redirect_url."' class='list-group-item list-group-item-action flex-column align-items-start'>
        <div class='d-flex w-100 align-items-center'>
        <small class='text-muted'><img class='rounded' width='60' height='60' src='".asset('assets/images/yoomoney.png')."'></small>
        <h5 class='mb-1'>".$button_lang."</h5>
        </div>
        </a>";
        return $button;
    }


    function get_long_url()
    {
        $curl = curl_init();
        /**
        1. Encode the Secret Api key  above into Base64 format
        2. Make sure to include ( : ) at the end of the secret Api key
         ***/
        $yoomoney_shop_id =  $this->yoomoney_shop_id;
        $yoomoney_secret_key = $this->yoomoney_secret_key;
        $params = [
            "amount" => [
                "value" => $this->amount,
                "currency" => $this->currency
            ],
            "capture" => true,
            "confirmation" => [
                "type" => "redirect",
                "return_url" => $this->yoomoney_success_redirect_url,
            ],
            'description' => $this->description,
            "metadata" => ["order_id" => $this->order_id],

        ];
        $params = json_encode($params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->yoomoney_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_USERPWD => $this->yoomoney_shop_id . ":" . $this->yoomoney_secret_key,
            CURLOPT_POSTFIELDS =>$params,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Idempotence-key: ' . $this->external_id,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return  $response = json_decode($response,true);
        
    }


    public function success_action()
    {
        $invoice_id = $this->invoice_id;
        $yoomoney_secret_key = $this->yoomoney_secret_key.':';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->yoomoney_api_url.$invoice_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERPWD => $this->yoomoney_shop_id . ":" . $this->yoomoney_secret_key,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Idempotence-key: ' . $this->external_id,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode( $response, true );
        return $response;

    }



}
