<?php

use App\Http\Controllers\Webhook;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Home;
use App\Http\Controllers\Bot;
use App\Http\Controllers\Subscriber;
$auth_or_guest =  env('APP_ENV')=='local' ? 'guest' : 'auth';

Route::get('telegram/bot/connect',[Bot::class,'connect_bot'])->middleware(['auth'])->name('connect-bot');
Route::post('telegram/bot/connect',[Bot::class,'connect_bot_action'])->middleware(['auth'])->name('connect-bot-action');
Route::post('telegram/bot/sync',[Bot::class,'sync_bot'])->middleware(['auth'])->name('sync-bot');
Route::post('telegram/bot/delete',[Bot::class,'delete_bot'])->middleware(['auth'])->name('delete-bot');
Route::post('telegram/bot/update-status',[Bot::class,'update_bot_status'])->middleware(['auth'])->name('update-bot-status');
Route::post('telegram/bot/update-administrator',[Bot::class,'update_group_administrator'])->middleware(['auth'])->name('update-group-administrator');
Route::get('telegram/bot/manager',[Bot::class,'bot_manager'])->middleware(['auth'])->name('bot-manager');
Route::post('telegram/bot/switch',[Bot::class,'bot_switch'])->middleware(['auth'])->name('bot-switch');


Route::get('telegram/group/manager',[Bot::class,'group_manager'])->middleware(['auth'])->name('telegram-group-manager');
Route::post('telegram/group/edit/campaign',[Bot::class,'edit_campaign_list'])->middleware(['auth'])->name('telegram-group-edit-campaign');
Route::post('telegram/group/delete/campaign',[Bot::class,'delete_campaign'])->middleware(['auth'])->name('telegram-group-delete-campaign');
Route::post('telegram/subscriber/group/list',[Subscriber::class,'list_group_subscriber_data'])->middleware(['auth'])->name('list-group-subscriber-data');
Route::post('telegram/group/campaign/list',[Bot::class,'campaign_list_data'])->middleware(['auth'])->name('list-campaign-data');
Route::post('telegram/group/mute/chat/member',[Subscriber::class,'mute_group_chat_member'])->middleware(['auth'])->name('mute-group-chat-member');
Route::post('telegram/group/unmute/chat/member',[Subscriber::class,'unmute_group_chat_member'])->middleware(['auth'])->name('unmute-group-chat-member');
Route::post('telegram/group/banned/chat/member',[Subscriber::class,'banned_group_chat_member'])->middleware(['auth'])->name('banned-group-chat-member');
Route::post('telegram/group/unban/chat/member',[Subscriber::class,'unban_group_chat_member'])->middleware(['auth'])->name('unban-group-chat-member');
Route::post('telegram/group/manager/set-active-group-session',[Bot::class,'set_active_group_session'])->middleware(['auth'])->name('set-active-group-session');
Route::post('telegram/group/manager/set-active-group-tab-menu-session',[Bot::class,'set_active_group_tab_menu_session'])->middleware(['auth'])->name('set-active-group-tab-menu-session');
Route::any('telegram/group/manager/message/filter',[Bot::class,'group_filtering_message_data'])->middleware(['auth'])->name('group-message-filter');
Route::any('telegram/group/manager/message/send',[Bot::class,'group_send_message_data'])->middleware(['auth'])->name('group-message-send');
Route::post('telegram/group/subscriber/delete-subscribers',[Subscriber::class,'delete_group_subscribers'])->middleware(['auth'])->name('delete-group-subscribers');
Route::post('telegram/group/subscriber/message',[Subscriber::class,'group_subscriber_message'])->middleware(['auth'])->name('telegram-group-subscriber-message');

Route::any('webhook/telegram-webhook/{token}',[Webhook::class,'telegram_webhook'])->name('telegram-webhook');
Route::any('webhook/telegram-webhook-main',[Webhook::class,'telegram_webhook_main'])->name('telegram-webhook-main');
Route::post('webhook/telegram-send-webhook/'.ENV('CRON_TOKEN'),[Webhook::class,'send_message_bot_reply_curl'])->name('telegram-webhook-send-message-bot');

Route::post('common/get-email-profile-dropdown',[Home::class,'get_email_profile_dropdown'])->middleware(['auth'])->name('common-get-email-profile-dropdown');
Route::post('common/get-sms-profile-dropdown',[Home::class,'get_sms_profile_dropdown'])->middleware(['auth'])->name('common-get-sms-profile-dropdown');