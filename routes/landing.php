<?php
use App\Http\Controllers\Landing;


Route::any('/policy/privacy', [Landing::class,'policy_privacy'])->name('policy-privacy');
Route::any('/policy/terms', [Landing::class,'policy_terms'])->name('policy-terms');
Route::any('/policy/refund', [Landing::class,'policy_refund'])->name('policy-refund');
Route::any('/policy/gdpr', [Landing::class,'policy_gdpr'])->name('policy-gdpr');
Route::any('/pricing', [Landing::class,'pricing_plan'])->name('pricing-plan');
Route::any('/pricing/pay-as-you-go', [Landing::class,'pricing_plan_ppu'])->name('pricing-plan-ppu');
Route::any('/accept-cookie', [Landing::class,'accept_cookie'])->name('accept-cookie');
Route::post('/installation-submit', [Landing::class,'installation_submit'])->name('installation-submit');
