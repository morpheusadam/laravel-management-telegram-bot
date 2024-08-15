<?php
namespace App\Services\Payment;

use Stripe\StripeClient;

class StripeService implements StripeServiceInterface {

    public $secret_key="";
    public $publishable_key="";

    public $description="Package Renew";
    public $amount=0;
    public $action_url="";

    public $currency="brl";
    public $secondary_button=true;
    public $button_lang='';

    function __construct(){
    }

    function set_button(){

        $button_url=asset("assets/images/stripe.png");
        $hide_me = $this->secondary_button ? 'display:none;' : '';
        $stripe_lang = __("Pay with Stripe");
        if(strtoupper($this->currency)=='JPY' || strtoupper($this->currency)=='VND')
            $amount=$this->amount;
        else
            $amount=$this->amount*100;

        $button="";

        $button.="<form action='".$this->action_url."' method='POST' style='".$hide_me."' id='stripePaymentForm01'>
			<script
		    src='https://checkout.stripe.com/checkout.js' class='stripe-button'
		    data-key='{$this->publishable_key}'
		    data-image='{$button_url}'
		    data-name=".config('app.name')."
		    data-currency='{$this->currency}'
		    data-description='{$this->description}'
		    data-amount='{$amount}'
		    data-billing-address='true'>
		  </script>
		</form>";

        if($this->secondary_button)
            $button.="
		<a href='#' class='list-group-item list-group-item-action flex-column align-items-start' id='stripe_clone' onclick=\"document.querySelector('#stripePaymentForm01 .stripe-button-el').click();\">
		    <div class='d-flex w-100 align-items-center'>
		      <small class='text-muted'><img class='rounded' width='60' height='60' src='".asset('assets/images/stripe.png')."'></small>
		      <h5 class='mb-1'>".$stripe_lang."</h5>
		    </div>
		</a>";
        return $button;
    }



    public function stripe_payment_action(){
        $response=array();
        $amount = $this->amount;
        if(strtoupper($this->currency)=='JPY' || strtoupper($this->currency)=='VND') $amount=$amount;
        else $amount=$amount*100;

        try {

            $stripe = new StripeClient($this->secret_key);

            $charge = $stripe->charges->create([
                'amount' => $amount,
                'currency' => $this->currency,
                "card" => $_POST['stripeToken'],
                "description" => $this->description
            ]);

            $email	= $_POST['stripeEmail'];

            $response['status']="Success";
            $response['email']=$email;
            $response['charge_info']=$charge;

            return $response;

        }

        catch(\Stripe\Exception\CardException $e) {
            $response['status'] ="Error";
            $response['message'] ="Stripe_CardError"." : ".$e->getMessage();
            return $response;
        }
        catch (\Stripe\Exception\RateLimitException $e) {
           $response['status'] ="Error";
           $response['message'] ="Stripe_CardError"." : ".$e->getError()->message;
        }
        catch (\Stripe\Exception\InvalidRequestException $e) {
            $response['status'] ="Error";
            $response['message'] ="Stripe_InvalidRequestError"." : ".$e->getMessage();
            return $response;
        }
        catch (\Stripe\Exception\AuthenticationException $e) {
            $response['status'] ="Error";
            $response['message'] ="Stripe_AuthenticationError"." : ".$e->getMessage();
            return $response;

        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $response['status'] ="Error";
            $response['message'] ="Stripe_ApiConnectionError"." : ".$e->getMessage();
            return $response;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $response['status'] ="Error";
            $response['message'] ="Stripe_Error"." : ".$e->getMessage();
            return $response;

        } catch (Exception $e) {
            $response['status'] ="Error";
            $response['message'] ="Stripe_Error"." : ".$e->getMessage();
            return $response;
        }

    }


}
