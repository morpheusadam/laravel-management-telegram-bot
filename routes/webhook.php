<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookPayment;
use App\Http\Controllers\Member;


Route::get('webhook/paypal-ipn/{paypal_mode}',[WebhookPayment::class,'paypal_ipn'])->name('paypal-ipn');


Route::any('webhook/paypal-action/{package_id}/{buyer_user_id}/{parent_user_id}',[Member::class,'paypal_action'])->middleware(['auth'])->name('payment-paypal-action');
Route::any('webhook/paypal-subscription-action/{buyer_user_id}/{parent_user_id}/{package_id}',[WebhookPayment::class,'paypal_subscription_action'])->name('paypal-subscription-action');
Route::any('webhook/paypal-subscription-cancel',[Member::class,'paypal_subscription_cancel'])->middleware(['auth'])->name('paypal-subscription-cancel');

if(str_contains(env('APP_URL'),'telegram-group.test')) Route::any('webhook/paypal-ipn-action',[WebhookPayment::class,'paypal_ipn_action'])->name('paypal-ipn-action');
else Route::post('webhook/paypal-ipn-action',[WebhookPayment::class,'paypal_ipn_action'])->name('paypal-ipn-action');

Route::any('webhook/razorpay-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'razorpay_action'])->name('payment-razorpay_action');
Route::any('webhook/paystack-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'paystack_action'])->name('payment-paystack-action');
Route::post('webhook/mercadopago-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'mercadopago_action'])->name('payment-mercadopago-action');
Route::get('webhook/myfatoorah-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'myfatoorah_success'])->middleware(['auth'])->name('payment-myfatoorah-success');
Route::get('webhook/toyyibpay-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'toyyibpay_action'])->name('payment-toyyibpay-action');
Route::get('webhook/toyyibpay-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'toyyibpay_success'])->middleware(['auth'])->name('payment-toyyibpay-success');
Route::get('webhook/xendit-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'xendit_action'])->middleware(['auth'])->name('payment-xendit-action');
Route::get('webhook/xendit-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'xendit_success'])->middleware(['auth'])->name('payment-xendit-success');
Route::get('webhook/xendit-fail',[WebhookPayment::class,'xendit_fail'])->name('payment-xendit-fail');
Route::get('webhook/myfatoorah-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'myfatoorah_action'])->middleware(['auth'])->name('payment-myfatoorah-action');
Route::get('webhook/paymaya-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'paymaya_action'])->name('payment-paymaya-action');
Route::get('webhook/paymaya-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'paymaya_success'])->middleware(['auth'])->name('payment-paymaya-success');
Route::any('webhook/mollie-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'mollie_action'])->name('payment-mollie-action');
Route::get('webhook/senangpay-action',[WebhookPayment::class,'senangpay_action'])->middleware(['auth'])->name('senangpay-action');

Route::get('webhook/instamojo-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'instamojo_action'])->name('payment-instamojo-action');
Route::get('webhook/instamojo-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'instamojo_success'])->middleware(['auth'])->name('payment-instamojo-success');

Route::get('webhook/instamojo-v2-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'instamojo_v2_action'])->name('payment-instamojo-v2-action');
Route::get('webhook/instamojo-v2-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'instamojo_v2_success'])->middleware(['auth'])->name('payment-instamojo-v2-success');


Route::post('webhook/sslcommerz-action',[WebhookPayment::class,'sslcommerz_action'])->name('payment-sslcommerz-action');
Route::post('webhook/sslcommerz-success',[WebhookPayment::class,'sslcommerz_success'])->name('payment-sslcommerz-success');
Route::get('webhook/sslcommerz-fail',[WebhookPayment::class,'sslcommerz_fail'])->name('payment-sslcommerz-fail');

Route::get('webhook/flutterwave-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'flutterwave_success'])->name('payment-flutterwave-success');

Route::any('webhook/flutterwave-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'flutterwave_action'])->name('payment-flutterwave-action');

Route::any('webhook/yoomoney-action/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'yoomoney_action'])->name('payment-yoomoney-action');
Route::get('webhook/yoomoney-success/{package_id}/{buyer_user_id}/{parent_user_id}',[WebhookPayment::class,'yoomoney_success'])->name('payment-yoomoney-success');
