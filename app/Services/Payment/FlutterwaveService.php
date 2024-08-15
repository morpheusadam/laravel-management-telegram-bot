<?php
namespace App\Services\Payment;

class FlutterwaveService implements FlutterwaveServiceInterface
{

  public $flutterwave_api_key;
  public $purpose;
  public $amount;
  public $phone;
  public $buyer_name;
  public $email;
  public $name;
  public $payment_id;
  public $payment_request_id;
  public $redirect_url_flutterwave;
  public $success_url_flutterwave;
  public $flutterwave_api_url = "https://api.flutterwave.com/v3/payments";
  public $currency;
  public $transaction_id;


  function __construct(){

  }

  function set_button(){

    $button_lang = __('Pay with Flutterwave');
    $button = "
      <a href='".$this->redirect_url_flutterwave."' class='list-group-item list-group-item-action flex-column align-items-start'>
      <div class='d-flex w-100 align-items-center'>
      <small class='text-muted'><img class='rounded' width='60' height='60' src='".asset('assets/images/Flutterwave-logo.png')."'></small>
      <h5 class='mb-1'>".$button_lang."</h5>
      </div>
      </a>";

    return $button;

  }


  public function get_long_url()
  {



    $request = [
        'tx_ref' => time(),
        'amount' => $this->amount,
        'currency' => $this->currency,
        'payment_options' => 'card',
        'redirect_url' => $this->success_url_flutterwave,
        'customer' => [
            'email' => $this->email,
            'name' => $this->name
        ],
        'meta' => [
            'price' => $this->amount
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $this->flutterwave_api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($request),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$this->flutterwave_api_key,
        'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    
    $response = json_decode($response);

    if($response->status == 'success')
    {
        return $link = $response->data->link;
       
    }

     return false;
  }


  public function success_action()
  {

    $curl_url = "https://api.flutterwave.com/v3/transactions/{$this->transaction_id}/verify";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $curl_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Content-Type: application/json",
          'Authorization: Bearer '.$this->flutterwave_api_key,
        ),
      ));
      
      $response = curl_exec($curl);
      $response = json_decode($response,true);
      return $response;
  }
}



