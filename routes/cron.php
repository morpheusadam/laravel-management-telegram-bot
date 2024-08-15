<?php
use App\Http\Controllers\Cron;

Route::get('cron/telegram-group-broadcast-send/'.ENV('CRON_TOKEN'),[Cron::class,'telegram_group_broadcast_send'])->name('telegram-group-broadcast-send');
Route::get('cron/telegram-group-broadcast-delete/'.ENV('CRON_TOKEN'),[Cron::class,'telegram_group_broadcast_delete'])->name('telegram-group-broadcast-delete');

Route::get('cron/telegram-clean-junk-data/'.ENV('CRON_TOKEN'),[Cron::class,'telegram_clean_junk_data'])->name('telegram-clean-junk-data');
Route::get('cron/telegram-disable-bot-expired-users/'.ENV('CRON_TOKEN'),[Cron::class,'telegram_disable_bot_expired_users'])->name('telegram-disable-bot-expired-users');
Route::get('cron/clean-system-logs/'.ENV('CRON_TOKEN'),[Cron::class,'clean_system_logs'])->name('clean-system-logs');

Route::any('cron/subscribers/transaction/'.ENV('CRON_TOKEN'),[Cron::class,'get_paypal_subscriber_transaction'])->name('get-paypal-subscriber-transaction');

