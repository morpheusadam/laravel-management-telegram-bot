<?php
    if(!isset($is_admin)) $is_admin = '0';
    if(!isset($is_agent)) $is_agent = '0';
    if(!isset($is_member)) $is_member = '0';
    if(!isset($is_manager)) $is_manager = '0';
    if(!isset($is_trial)) $is_trial = '0';
    if(!isset($user_module_ids)) $user_module_ids = [];
    if(!isset($team_access)) $team_access = [];
    $check_is_agency_site = check_is_agency_site() ? '1' : '0';
    $language = config('app.locale');
    $language_exp = explode('-', $language);
    $language_code = $language_exp[0] ?? 'en';
    $datatable_lang_file_path = get_public_path('assets').DIRECTORY_SEPARATOR.'vendors'.DIRECTORY_SEPARATOR.'datatables'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language_code.'.json';
    if(file_exists($datatable_lang_file_path))
    $datatable_lang_file = asset('assets/vendors/datatables/language/'.$language_code.'.json');
    else $datatable_lang_file = asset('assets/vendors/datatables/language/en.json');
?>
<script type="text/javascript">
    "use strict";
    var base_url = '{{url('/')}}';
    var site_url = base_url;
    var temp_route_variable = 1;
    var csrf_token = '{{ csrf_token() }}';
    var today = '{{ date("Y-m-d") }}';
    var is_admin = '{{(int)$is_admin}}';
    var is_agent = '{{(int)$is_agent}}';
    var is_member = '{{(int)$is_member}}';
    var is_manager = '{{(int)$is_manager}}';
    var is_team = '0';
    var check_is_agency_site = '{{(int)$check_is_agency_site}}';
    var route_name = '{{isset($route_name) && !empty($route_name) ? $route_name : ""}}';
    var language = '{{$language}}';
    var is_rtl = '{{$is_rtl??"0"}}';
    var auth_user_id = '{{Auth::user()->id ?? ''}}';
    var auth_parent_user_id = '{{Auth::user()->parent_user_id ?? ''}}';
    var auth_user_name = '{{Auth::user()->name ?? ''}}';
    var auth_user_email = '{{Auth::user()->email ?? ''}}';
    var auth_user_type = '{{Auth::user()->user_type ?? ''}}';


    var user_module_ids = '{{json_encode($user_module_ids)}}';
    var module_id_bot_subscriber = '{{$module_id_bot_subscriber}}';
    var module_id_telegram_group = '{{$module_id_telegram_group}}';


    var global_url_login = '{{ route('login') }}';
    var global_url_register = '{{ route('register') }}';
    var global_url_dashboard = '{{ route('dashboard') }}';
    var global_url_datatable_language = '{{$datatable_lang_file}}';
    var global_url_payment_success = '{{ route('transaction-log') }}'+'?action=success';
    var global_url_payment_cancel = '{{ route('transaction-log') }}'+'?action=cancel';
    var global_url_notification_mark_seen = '{{ route('notification-mark-seen') }}';


    var new_request = '{{ __('New Request') }}';
    var global_lang_loading = '{{ __('Loading') }}';
    var global_lang_save = '{{ __('Save') }}';
    var global_lang_saving = '{{ __('Saving') }}';
    var global_lang_sent = '{{ __('Sent') }}';
    var global_lang_required = '{{ __('Required') }}';
    var global_lang_ok = '{{ __('OK') }}';
    var global_lang_procced = '{{ __('Proceed') }}';
    var global_lang_success = '{{ __('Success') }}';
    var global_lang_warning = '{{ __('Warning') }}';
    var global_lang_error = '{{ __('Error') }}';
    var global_lang_remove = '{{ __('Remove') }}';
    var global_lang_confirm = '{{ __('Confirm') }}';
    var global_lang_create = '{{ __('Create') }}';
    var global_lang_create_default = '{{ __('Create Default') }}';
    var global_lang_edit = '{{ __('Edit') }}';
    var global_lang_delete = '{{ __('Delete') }}';
    var global_lang_ban_member = '{{ __('Ban') }}';
    var global_lang_unban_member = '{{ __('Unban') }}';
    var global_lang_clear_log = '{{ __('Clear Log') }}';
    var global_lang_cancel = '{{ __('Cancel') }}';
    var global_lang_apply = '{{ __('Apply') }}';
    var global_lang_understand = '{{ __('I Understand') }}';
    var global_lang_download = '{{ __('Download') }}';
    var global_lang_from = '{{ __('From') }}';
    var global_lang_to = '{{ __('To') }}';
    var global_lang_custom = '{{ __('Custom') }}';
    var global_lang_choose_data = '{{ __('Date') }}';
    var global_lang_last_30_days = '{{ __('Last 30 Days') }}';
    var global_lang_this_month = '{{ __('This Month') }}';
    var global_lang_last_month = '{{ __('Last Month') }}';
    var global_lang_something_wrong = '{{ __('Something went wrong.') }}';
    var global_lang_confirmation = '{{ __('Are you sure?') }}';
    var global_lang_delete_confirmation = '{{ __('Do you really want to delete this record? This action cannot be undone and will delete any other related data if needed.') }}';
    var global_lang_ban_confirmation = '{{ __('Do you really want to banned  this user from the group? This action cannot be undone and will delete any other related data if needed.') }}';
    var global_lang_unban_confirmation = '{{ __('Do you really want to unban this user from the group? This action cannot be undone and will delete any other related data if needed.') }}';
    var global_lang_remove_confirmation = '{{ __('Do you really want to remove this record? This will only remove the data from our system.') }}';
    var global_lang_affiliate_user_response = '{{ __('Do you really want to change the  status.') }}';
    var global_lang_affiliate_withdrawal_response = '{{ __('Do you really want to change the affliate withdrawal status.') }}';
    var global_lang_submitted_successfully = '{{ __('Data has been submitted successfully.') }}';
    var global_lang_saved_successfully = '{{ __('Data has been saved successfully.') }}';
    var global_lang_deleted_successfully = '{{ __('Data has been deleted successfully.') }}';
    var global_lang_removed_successfully = '{{ __('Data has been removed successfully.') }}';
    var global_lang_action_successfully = '{{ __('Command action has been performed successfully.') }}';
    var global_lang_fill_required_fields = '{{ __('Please fill the required fields.') }}';
    var global_lang_check_status = '{{ __('Check Status') }}';
    var global_lang_bot_reply_paused = '{{ __('Bot Reply Paused') }}';
    var global_lang_bot_reply_on = '{{ __('Bot Reply On') }}';
    var global_lang_bot_subscribed = '{{ __('Subscribed') }}';
    var global_lang_bot_unsubscribed = '{{ __('Unsubscribed') }}';
    var global_all_fields_are_required = '{{ __('All fields are required.') }}';

    var telegram_connect_bot_url_delete = '{{route('delete-bot')}}';
    var telegram_connect_bot_url_sync = '{{route('sync-bot')}}';


    var telegram_list_subscriber_lang_warning_select_bot = '{{__('Please select a bot first.')}}';
    var telegram_list_subscriber_lang_warning_select_subscriber = '{{__('Please select subscribers first.')}}';
    var telegram_list_subscriber_lang_warning_select_subscriber_limit = '{{__('You can select subscribers up to')}}';
    var telegram_list_subscriber_lang_warning_select_label = '{{__('Please select labels.')}}';
    var telegram_list_subscriber_lang_success_assign_label = '{{__('Labels have been assigned successfully.')}}';
    var telegram_list_subscriber_lang_success_assign_sequence = '{{__('Sequences have been assigned successfully.')}}';
    var telegram_list_subscriber_lang_success_delete_subscriber = '{{__('Subscribers have been deleted successfully.')}}';
    var telegram_list_subscriber_lang_label_name = '{{__('Label Name')}}';
    var telegram_list_subscriber_lang_create_and_assign = '{{__('Create & Assign')}}';
    var telegram_list_subscriber_var_selected_subscriber = '{{$auto_selected_subscriber??''}}';
    var telegram_list_subscriber_var_selected_flow_id = '{{$auto_selected_flow_id??''}}';
    var telegram_list_subscriber_var_selected_bot_id = '{{$auto_selected_bot_id??''}}';

    var common_function_url_get_email_profile_dropdown = '{{route('common-get-email-profile-dropdown')}}';

    var telegram_group_manager_telegram_group_id_session = '{{session('bot_manager_get_group_details_telegram_group_id')}}';
    var telegram_group_manager_telegram_group_tab_menu_id_session = '{{session('bot_manager_get_group_details_tab_menu_id')}}';
    var telegram_group_manager_url_set_active_group_tab_menu_session = '{{route('set-active-group-tab-menu-session')}}';
    var telegram_group_manager_url_set_active_bot_session = '{{route('set-active-group-session')}}';
    var telegram_group_mute_chat_member_lang = '{{__('Member Has been muted successfully')}}';
    var telegram_group_unmute_chat_member_lang = '{{__('Member Has been unmuted successfully')}}';
    var telegram_group_banned_chat_member_lang = '{{__('Member Has been Banned successfully')}}';
    var telegram_group_unban_chat_member_lang = '{{__('Member Has been Unban successfully')}}';
    var telegram_group_select_mute_time_lang = '{{__('Mute Time Is Empty')}}';
    var telegram_group_manager_lang_active = '{{__('Group Active Subscribers')}}';
    var telegram_group_message_filtering = '{{__('Group Message Filtering')}}';
    var telegram_group_activity = '{{__('Group Activity')}}';
    var telegram_group_live_chat = '{{__('Group Live Chat')}}';
    var telegram_muted_date_text_lang = '{{ __('Member Has been muted Untill ') }}'
    var telegram_group_message_send = '{{__('Group Message Send')}}';
    var telegram_group_list_subscriber_url_data = '{{route('list-group-subscriber-data')}}';
    var telegram_campaign_list_data = '{{route('list-campaign-data')}}';
    var telegram_group_filtering_message = '{{route('group-message-filter')}}';
    var telegram_group_list_subscriber_url_delete_subscriber = '{{route('delete-group-subscribers')}}';
    var telegram_group_activity_chart_title = '{{ __("Members") }}';

    var telegram_group_mute_chat_member = '{{route('mute-group-chat-member')}}';
    var telegram_group_banned_chat_member = '{{route('banned-group-chat-member')}}';
    var telegram_group_unban_chat_member = '{{route('unban-group-chat-member')}}';
    var telegram_group_unmute_chat_member = '{{route('unmute-group-chat-member')}}';
    var telegram_group_edit_campaign = '{{route('telegram-group-edit-campaign')}}';
    var telegram_group_show_subscriber_message = '{{route('telegram-group-subscriber-message')}}';

    var subscription_list_package_url_data = '{{route('list-package-data')}}';
    var subscription_list_package_url_update = '{{route('update-package',':id')}}';
    var subscription_list_package_url_delete = '{{route('delete-package')}}';
    var subscription_list_user_url_data = '{{route('list-user-data')}}';
    var subscription_list_user_url_update = '{{route('update-user',':id')}}';
    var subscription_list_user_url_delete = '{{route('delete-user')}}';
    var subscription_list_user_url_send_email = '{{route('user-send-email')}}';
    var subscription_list_user_lang_send_email = '{{__('Send Email')}}';
    var subscription_list_user_lang_email = '{{__('Email')}}';
    var subscription_list_user_lang_warning_select_user = '{{__('You have to select users to send email.')}}';


    var member_transaction_log_url_data= '{{route('transaction-log-data')}}';
    var member_transaction_log_manual_url_data= '{{route('transaction-log-manual-data')}}';
    var member_payment_buy_package_url = '{{route('buy-package',':id')}}';
    var member_payment_select_package_lang_already_subscribed = '{{__('Already Subscribed')}}';
    var member_payment_select_package_lang_already_subscribed_lang = '{{__('You already have a subscription set up. If you want to switch to a new payment method or subscription, please sure to cancel your current one first.')}}';
    var member_payment_buy_package_package_id = '{{$buy_package_package_id ?? '0'}}';
    var member_payment_buy_package_has_recurring_flag = '{{$has_reccuring ?? '0'}}';
    var member_settings_list_api_settings_url_data = '{{route('api-settings-data')}}';
    var member_settings_list_video_tutorial_data = '{{route('video-tutorial-data')}}';
    var member_settings_list_api_settings_url_update_data = '{{route('update-api-settings')}}';
    var member_settings_list_video_update_data = '{{route('update-video-settings')}}';
    var video_tutorial_update_data_action = '{{route('update-video-settings-action')}}';
    var member_settings_list_api_settings_url_save = '{{route('save-api-settings')}}';
    var member_settings_list_api_log_url_data = '{{route('list-payment-api-log-data')}}';

    var manual_payment_upload_file_route = '{{ route("Manual-payment-upload-file") }}';
    var manual_payment_submission_route = '{{ route("Manual-payment-submission") }}';
    var manual_payment_upload_file_delete_route = '{{ route("Manual-payment-uploaded-file-delete") }}';
    var manual_payment_handle_action_route = '{{ route("Manual-payment-handle-action") }}';

    var cancelcsvsubmission = '{{ __("Do you want to cancel this submission?") }}';
    var botIdNotFound = '{{ __("Please select a telegram bot") }}';

    var purchase_code_active = '{{ route("credential-check-action") }}';

    // Affiliate global lang
    <?php if(check_is_mobile_view()) echo 'var areWeUsingScroll = false;';
    else echo 'var areWeUsingScroll = true;';?>
</script>

