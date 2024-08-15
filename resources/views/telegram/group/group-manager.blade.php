@extends('layouts.auth')
@section('title',__('Bot Manager'))
@section('content')
    <div class="main-content container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ __('Telegram Group Manager') }}</h3>
                    <p class="text-subtitle text-muted">{{ __('Manage your Telegram group') }}</p>
                </div>
            </div>
        </div>
        <?php $w_100 = "width:100% !important";?>
        @if (!empty($bot_list_not_admin))
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('The following bots do not have administrator privileges in the specified groups:')}}</h4>
                <p>
                    @foreach ($bot_list_not_admin as $key)
                        {{ $key['group_name'] }}{{'{'}}
                                   @php
                                       $bot_names = [];
                                       foreach ($key['bot_list'] as $bot_name) {
                                           $bot_names[] = $bot_name['username'];
                                       }
                                       $bot_string = implode(',', $bot_names);
                                       echo $bot_string;
                                   @endphp
                        {{'}'}}
                    @endforeach
                </p>
            </div>
        @endif

        @if ($no_group_found == 1)
            <div class="alert alert-light-warning alert-dismissible fade show p-4 border-warning border-dashed"  role="alert">
                <h5 class="alert-heading text-dark">
                    <i class="fas fa-eye-slash fs-1 float-start mt-1 me-3"></i>
                    {{__('No Group Found')}} <small></small>
                </h5>
                <p class="">{{ __('Please add bot in your group') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @php
           $delete_all_forward_message = $filter_message_data->delete_all_forward_message ?? '0';  
           $delete_forward_links = $filter_message_data->delete_forward_links ?? '0';
           $delete_forword_image = $filter_message_data->delete_forword_image ?? '0';
           $delete_links = $filter_message_data->delete_links ?? '0';
           $delete_diceroll = $filter_message_data->delete_diceroll ?? '0';
           $delete_sticker = $filter_message_data->delete_sticker ?? '0';
           $delete_document = $filter_message_data->delete_document ?? '0';
           $remove_admin_message = $filter_message_data->remove_admin_message ?? '0';
           $delete_voice = $filter_message_data->delete_voice ?? '0';
           $delete_image = $filter_message_data->delete_image ?? '0';
           $delete_command = $filter_message_data->delete_command ?? '0';
           $user_joined_group = $filter_message_data->user_joined_group ?? '0';
           $user_left_group = $filter_message_data->user_left_group ?? '0';
           $same_message_delete_count = $filter_message_data->same_message_delete_count ?? '';
           $same_message_restrict_count = $filter_message_data->same_message_restrict_count ?? '';
           $allowed_message_per_time_count = $filter_message_data->allowed_message_per_time_count ?? '';
           $minute_allowed_message_per_time = $filter_message_data->minute_allowed_message_per_time ?? '';
           $restrict_member_time = $filter_message_data->restrict_member_time ?? '';

           if($restrict_member_time != ''){
             list($restrict_member_time_unit, $restrict_time_type_unit) = explode("-", $restrict_member_time);
           }
           else{
            $restrict_member_time_unit='';
            $restrict_time_type_unit = '';
           }

           $new_join_restrict_time = $filter_message_data->new_join_restrict_time ?? '';
           if($new_join_restrict_time !=''){
            list($new_join_restrict_time_unit, $new_join_restrict_time_type_unit) = explode("-", $new_join_restrict_time);
           }
           else{
            $new_join_restrict_time_unit='';
            $new_join_restrict_time_type_unit = '';
           }

           $same_message_restrict_time = $filter_message_data->same_message_restrict_time ?? '';
           if($same_message_restrict_time !=''){
            list($same_message_restrict_time_unit, $same_message_restrict_time_type_unit) = explode("-", $same_message_restrict_time);
           }
           else{
            $same_message_restrict_time_unit='';
            $same_message_restrict_time_type_unit = '';
           }

           $message_content = $send_message_data->message_content ?? '';  
           $pin_this_announcement = $send_message_data->pin_announcement ?? '0';
           $preview_the_url = $send_message_data->preview_url ?? '0';
           $protected_messages_no_copying = $send_message_data->message_protection ?? '0';
           $sound_alerts_for_messages = $send_message_data->message_sound_alerts ?? '0';
        @endphp

        <?php
            $email_quick_reply_id = $system_postbacks['email-quick-reply'] ?? 0;
            $phone_quick_reply_id = $system_postbacks['phone-quick-reply'] ?? 0;
            $location_quick_reply_id = $system_postbacks['location-quick-reply'] ?? 0;
            $birthday_quick_reply_id = $system_postbacks['birthday-quick-reply'] ?? 0;
            $chat_with_human_id = $system_postbacks['chat-with-human'] ?? 0;
            $chat_with_bot_id = $system_postbacks['chat-with-bot'] ?? 0;
            $unsubscribe_id = $system_postbacks['unsubscribe'] ?? 0;
            $resubscribe_id = $system_postbacks['resubscribe'] ?? 0;
            $get_started_id = $system_bot_settings['get-started'] ?? 0;
            $no_match_id = $system_bot_settings['no match'] ?? 0;
            $subscriber_status = [
                'active'=>'Active Member',
                'ban'=>'Ban Member',
                'unban'=>'Unban Member',
                'left'=>'Left Member',
                'removed'=>'Remove Member'
            ];
        ?>

        @if (session('save_campaign_message')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Successful')}}</h4>
                <p> {{ __('Campaign Message have been saved successfully.') }}</p>
            </div>
        @endif
        @if (session('save_filtering_message')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Successful')}}</h4>
                <p> {{ __('Filtering Message Settings have been saved successfully.') }}</p>
            </div>
        @endif
         @if (session('save_campaign_message')=='0')
            <div class="alert alert-danger">
                <h4 class="alert-heading">{{__('Failed to send message')}}</h4>
                <p> {{ session('save_campaign_message_content') }}</p>
            </div>
        @endif

        <section id="basic-horizontal-layouts">

            <div class="row multi_layout">

                <div class="col-12 col-md-5 col-lg-2 collef">
                    <div class="card main_card">
                        <div class="card-header p-3 d-flex justify-content-between">
                            <h6 class="me-3 pt-2">{{__('Groups')}}</h6>
                            <input type="text" class="form-control" id="search_bot_list" onkeyup="search_in_ul(this,'bot_list_ul')" autofocus="" placeholder="Search...">
                        </div>
                        @if ($no_group_found != 1)
                            <div class="card-body p-0">
                               <ul class="list-group" id="bot_list_ul">
                                    <?php $i=0;
                                    $active_group_name = '';
                                    foreach($group_info as $value) {
                                        $group_username = DB::table('telegram_groups')->select('telegram_bots.username')->leftJoin('telegram_bots', 'telegram_bots.id', '=', 'telegram_groups.telegram_bot_id')->where(['group_id'=>$value->group_id,'is_bot_admin'=>'1'])->get();
                                        if($i==0) $active_group_name = $value->group_name;
                                        ?>
                                        <li class="list-group-item <?php if($i==0) echo 'active'; ?> group_list_item" telegram_group_id="<?php echo $value->id; ?>">
                                            <div class="row">
                                               <div class="col-12">
                                                    <h6 class="page_name">{{$value->group_name}}</h6>
                                                    @foreach ($group_username as $username)
                                                        <small class="text-muted fst-italic" >&bull; {{$username->username}}</small><br>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    <?php $i++;
                                    } ?>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-7 col-lg-3 colmid" id="middle_column">

                    <div class="text-center waiting d-none">
                        <i class="fas fa-spinner fa-spin blue text-center"></i>
                    </div>

                    @if (!empty($group_info) && $no_group_found ==0)
                        <div id="middle_column_content">
                            <div class="card main_card">
                                <div class="card-header p-3"><h6 class="me-3 pt-2">{{$active_group_name}}</h6></div>
                                <div class="card-body px-3 py-3 nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                                    <a class="text-xs text-primary fw-bolder nav-link mb-2" id="v-pills-group-activity-tab" data-bs-toggle="pill" href="#v-pills-group-activity" role="tab" aria-controls="v-pills-group-activity" aria-selected="true">
                                        <div class="card mb-1 rounded">
                                            <div class="card-content">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-3 text-center pe-0">
                                                        <i class="fas fa-tasks text-primary list-icon"></i>
                                                    </div>
                                                    <div class="col-9 ps-0">
                                                        <div class="card-body px-2 py-3">
                                                            <h6>{{__('Group Activity')}}</h6>
                                                            <span class="text-xs"><i class="fas fa-circle text-success text-xs"></i> &nbsp; {{__('Change Settings')}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="text-xs text-primary fw-bolder nav-link mb-2" id="v-pills-group-subscriber-tab" data-bs-toggle="pill" href="#v-pills-group-subscriber" role="tab" aria-controls="v-pills-group-subscriber" aria-selected="true">
                                        <div class="card mb-1 rounded">
                                            <div class="card-content">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-3 text-center pe-0">
                                                        <i class="fas fa-robot text-primary list-icon"></i>
                                                    </div>
                                                    <div class="col-9 ps-0">
                                                        <div class="card-body px-2 py-3">
                                                            <h6>{{__('Group Members')}}</h6>
                                                            <span class="text-xs"><i class="fas fa-circle text-success text-xs"></i> &nbsp; {{__('Change Settings')}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="text-xs text-primary fw-bolder nav-link mb-2" id="v-pills-filtering-message-tab" data-bs-toggle="pill" href="#v-pills-filtering-message" role="tab" aria-controls="v-pills-filtering-message" aria-selected="true">
                                        <div class="card mb-1 rounded">
                                            <div class="card-content">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-3 text-center pe-0">
                                                        <i class="fas fa-filter text-primary list-icon"></i>
                                                    </div>
                                                    <div class="col-9 ps-0">
                                                        <div class="card-body px-2 py-3">
                                                            <h6>{{__('Filtering Message')}}</h6>
                                                            <span class="text-xs"><i class="fas fa-circle text-success text-xs"></i> &nbsp; {{__('Change Settings')}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="text-xs text-primary fw-bolder nav-link mb-2" id="v-pills-send-message-tab" data-bs-toggle="pill" href="#v-pills-send-message" role="tab" aria-controls="v-pills-send-message" aria-selected="true">
                                        <div class="card mb-1 rounded">
                                            <div class="card-content">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-3 text-center pe-0">
                                                        <i class="far fa-comment-alt text-primary list-icon"></i>
                                                    </div>
                                                    <div class="col-9 ps-0">
                                                        <div class="card-body px-2 py-3">
                                                            <h6>{{__('Send Message')}}</h6>
                                                            <span class="text-xs"><i class="fas fa-circle text-success text-xs"></i> &nbsp; {{__('Change Settings')}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-12 col-md-12 col-lg-7 colrig" id="right_column">
                    <div class="text-center waiting d-none">
                        <i class="fas fa-spinner fa-spin blue text-center"></i>
                    </div>

                    <div class="card no-shadow">
                        <div class="card-header p-3 d-flex justify-content-between">
                            <h6 class="me-3 pt-2" id="put_action_title"></h6>
                        </div>

                        <div class="card-body px-3 pt-1 pb-2">
                            <div class="tab-content" id="v-pills-tabContent">

                                <div class="tab-pane fade" id="v-pills-group-subscriber" role="tabpanel" aria-labelledby="v-pills-group-subscriber-tab">
                                    <div class="card p-0 no-shadow">
                                         <div class="card-body data-card">
                                            <div class="row">
                                                <div class="col-12 col-lg-10">
                                                   <input type="hidden" value="" id="auto_selected_flow_id">
                                                   <div class="input-group mb-3" id="searchbox">
                                                     <div class="input-group-prepend">
                                                       <select name="subscriber_status" class="form-control select2 cw-100" id="subscriber_status" autocomplete="off" style  = "{{$w_100}}">
                                                           @foreach($subscriber_status as $key => $value)
                                                               <option value="{{ $key }}" @if(session('telegram_subscriber_status') == $key) selected @endif>{{ $value }}</option>
                                                           @endforeach
                                                       </select>
                                                     </div>

                                                     <div id="put_label_dropdown">

                                                     </div>
                                                     <div class="input-group-prepend">
                                                       <input type="text" class="form-control no-radius" autofocus id="search_value" name="search_value" placeholder="{{__("Search...")}}">
                                                     </div>
                                                   </div>
                                               </div>

                                                <div class="col-12 col-lg-2">
                                                    <button type="button" class="mt-2 btn btn-outline-light text-dark dropdown-toggle dropdown-toggle-split float-end"  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  data-reference="parent">
                                                        <span>{{__('Options')}}</span>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" id="download_data"><i class="fas fa-cloud-download-alt"></i> <?php echo __("Download as CSV"); ?></a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" id="bulk_delete_contact" href=""><i class="fas fa-trash"></i> <?php echo __("Delete Subscriber"); ?></a>
                                                    </div>
                                                </div>
                                            </div>

                                        <div class="table-responsive">
                                            <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                                                <thead>
                                                <tr class="table-light">
                                                    <th>#</th>
                                                    <th>
                                                        <div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                                    </th>
                                                    <th>{{__("Avatar") }}</th>
                                                    <th>{{__("Chat ID") }}</th>
                                                    <th>{{__("First Name") }}</th>
                                                    <th>{{__("Last Name") }}</th>
                                                    <th>{{__("Username") }}</th>
                                                    <th>{{__("Is Ban") }}</th>
                                                    <th>{{__("Is Left") }}</th>
                                                    <th>{{__("Updated at") }}</th>
                                                    <th>{{__("Actions") }}</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>

                                    </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-filtering-message" role="tabpanel" aria-labelledby="v-pills-filtering-message-tab">
                                    <form  class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{route('group-message-filter')}}"> 
                                        @csrf
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("Filter Members Messages")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_messages_containing_bot_commands" type="checkbox" id="Delete_messages_containing_bot_commands" value="1" <?php echo old('Delete_messages_containing_bot_commands',$delete_command)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_messages_containing_bot_commands">{{__("Remove Messages that Include Bot Commands")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_messages_containing_bot_commands'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_messages_containing_bot_commands') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_image_message" type="checkbox" id="Delete_image_message" value="1" <?php echo old('Delete_image_message',$delete_image)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_image_message">{{__("Remove Messages Containing Images")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_image_message'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_image_message') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_voice_messages" type="checkbox" id="Delete_voice_messages" value="1" <?php echo old('Delete_voice_messages',$delete_voice)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_voice_messages">{{__("Remove Messages Containing Voice Recordings")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_voice_messages'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_voice_messages') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_documents" type="checkbox" id="Delete_documents" value="1" <?php echo old('Delete_documents',$delete_document)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_documents">{{__("Remove Messages Containing Attached Documents")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_documents'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_documents') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_stickers" type="checkbox" id="Delete_stickers" value="1" <?php echo old('Delete_stickers',$delete_sticker)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_stickers">{{__("Remove stickers and GIFs")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_stickers'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_stickers') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_dice" type="checkbox" id="Delete_dice" value="1" <?php echo old('Delete_dice',$delete_diceroll)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_dice">{{__("Remove member dice rolls")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_dice'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_dice') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
        
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_messages_contain_links" type="checkbox" id="Delete_messages_contain_links" value="1" <?php echo old('Delete_messages_contain_links',$delete_links)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_messages_contain_links">{{__("Remove  messages contain links")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_messages_contain_links'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_messages_contain_links') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("Filter Forwarded Messages")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Deleted_forwarded_messages_image" type="checkbox" id="Deleted_forwarded_messages_image" value="1" <?php echo old('Deleted_forwarded_messages_image',$delete_forword_image)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Deleted_forwarded_messages_image">{{__("Remove forwarded messages with media")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Deleted_forwarded_messages_image'))
                                                                    <span class="text-danger"> {{ $errors->first('Deleted_forwarded_messages_image') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Deleted_forwarded_messages_contain_links" type="checkbox" id="Deleted_forwarded_messages_contain_links" value="1" <?php echo old('Deleted_forwarded_messages_contain_links',$delete_forward_links)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Deleted_forwarded_messages_contain_links">{{__("Remove forwarded messages contain links")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Deleted_forwarded_messages_contain_links'))
                                                                    <span class="text-danger"> {{ $errors->first('Deleted_forwarded_messages_contain_links') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Deleted_all_forwarded_messages" type="checkbox" id="Deleted_all_forwarded_messages" value="1" <?php echo old('Deleted_all_forwarded_messages',$delete_all_forward_message)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Deleted_all_forwarded_messages">{{__("Remove all forwarded messages")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Deleted_all_forwarded_messages'))
                                                                    <span class="text-danger"> {{ $errors->first('Deleted_all_forwarded_messages') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                            @php
                                                                $restrict_time_options = [
                                                                    "" => __("Duration"),
                                                                    "minutes" => __("Minute"),
                                                                    "hours" => __("Hour"),
                                                                    "days" => __("Day"),
                                                                    "months" => __("Month")
                                                                ];
                                                            @endphp
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">{{__("When a member breaks the rules, user will be restricted for")}}</span>
                                                            <input type="number" name="restrict_member_time" id ="mute_time" class="form-control" value="{{ $restrict_member_time_unit ?? ''}}">
                                                            {{Form::select('restrict_time_type',$restrict_time_options,$restrict_time_type_unit,['class'=>'form-control bg-white','id'=>'restrict_time_type'] )}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php


                                            $restricted_keywords = isset($filter_message_data->delete_containing_words) ? json_decode($filter_message_data->delete_containing_words) : [];
      
                                            $associativeArray = [];
                                            foreach ($restricted_keywords as $value) {
                                                $associativeArray[$value] = $value;
                                            }
                                            $censor_words_value = empty($associativeArray) ? '0' : '1';
                                            $keyword_info = array_keys($associativeArray);                                              
                                        @endphp
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("Keyword Surveillance")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="censor_words" type="checkbox" id="censor_words" onchange="toggleKeywordAnalysis()" <?php echo old('$censor_words',$censor_words_value)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="censor_words">{{__("Remove messages contains following censor words")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('censor_words'))
                                                                    <span class="text-danger"> {{ $errors->first('censor_words') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div <?php echo $censor_words_value=='0' ? 'class="d-none"' : ''; ?> id="keyword_analysis">

                                                    <div> {{Form::select('keyword_list[]',$associativeArray,$keyword_info,['class'=>'form-control select2Tag','id'=>'keyword_list','multiple'=>'multiple','style'=>$w_100] )}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("Admin Filter Message")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="remove_admin_message" type="checkbox" id="remove_admin_message" value="1"  <?php echo old('$remove_admin_message',$remove_admin_message)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="remove_admin_message">{{__("Filter Message Apply for Admin")}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('remove_admin_message'))
                                                                    <span class="text-danger"> {{ $errors->first('remove_admin_message') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("Service Message")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_user_joined_the_group_message" type="checkbox" id="Delete_user_joined_the_group_message" value="1" <?php echo old('Delete_user_joined_the_group_message',$user_joined_group)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_user_joined_the_group_message">{{__('Delete `user joined the group` message')}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_user_joined_the_group_message'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_user_joined_the_group_message') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pt-2 w-100 bg-white">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" name="Delete_user_left_the_group_message" type="checkbox" id="Delete_user_left_the_group_message" value="1" <?php echo old('Delete_user_left_the_group_message',$user_left_group)=='1' ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label text-wrap" for="Delete_user_left_the_group_message">{{__('Delete `user left the group` message')}}</label>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                                @if ($errors->has('Delete_user_left_the_group_message'))
                                                                    <span class="text-danger"> {{ $errors->first('Delete_user_left_the_group_message') }} </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("New Members Restriction")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        @php
                                                            $new_member_restrict_time_options = [
                                                                "" => __("Duration"),
                                                                "minutes" => __("Minute"),
                                                                "hours" => __("Hour"),
                                                                "days" => __("Day")
                                                            ];
                                                        @endphp
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">{{__("When a member join a group, user will be restricted for")}}</span>
                                                            <input type="number" name="new_member_restrict_time" id ="new_member_restrict_time" class="form-control" value="{{ $new_join_restrict_time_unit ?? ''}}">                                                               
                                                            {{Form::select('new_members_restrict_time_type',$new_member_restrict_time_options,$new_join_restrict_time_type_unit,['class'=>'form-control bg-white','id'=>'new_members_restrict_time_type'] )}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header border-0">
                                                <b>{{__("Member message limitation")}}</b>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">{{__("The same message will be automatically deleted after")}}</span>
                                                            <input onkeyup="document.getElementById('same_message_restrict_count').value = this.value"  type="number" min="2" max="20" placeholder="{{ __('Number of message') }}" name="same_message_delete_count" id ="same_message_delete_count" class="form-control" value="{{ $same_message_delete_count ?? null}}">
                                                            <span class="input-group-text">{{__("Times")}}</span>                                                       
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-12">
                                                            @php
                                                                $same_message_restrict_time_type = [
                                                                    "" => __("Duration"),
                                                                    "minutes" => __("Minute"),
                                                                    "hours" => __("Hour"),
                                                                    "days" => __("Day")
                                                                ];
                                                            @endphp
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">{{__("If the user send same Messages")}}</span>
                                                                <input type="number" min="2" max="20" readonly placeholder="{{ __('Message Number') }}" name="same_message_restrict_count" id ="same_message_restrict_count" class="form-control" value="{{ $same_message_restrict_count ?? null}}">                                                       
                                                            <span class="input-group-text">{{__("Times , user will be Muted for")}}</span>
                                                                <input type="number"name="same_message_restrict_time_unit" id ="same_message_restrict_time_unit" class="form-control" value="{{ $same_message_restrict_time_unit ?? ''}}">                                                               
                                                                {{Form::select('same_message_restrict_time_type',$same_message_restrict_time_type,$same_message_restrict_time_type_unit,['class'=>'form-control bg-white','id'=>'same_message_restrict_time_type'] )}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">{{__("A user can send Maximum")}}</span>
                                                                <input type="number" min="2" max="20" placeholder="{{ __('Message Number') }}" name="allowed_message_per_time_count" id ="allowed_message_per_time_count" class="form-control" value="{{ $allowed_message_per_time_count ?? null}}">      
                                                            <span class="input-group-text">{{__("Messages per")}}</span>
                                                                <input type="number" placeholder="{{ __('Minutes') }}" name="minute_allowed_message_per_time" id ="minute_allowed_message_per_time" class="form-control" value="{{ $minute_allowed_message_per_time ?? null}}">      
                                                            <span class="input-group-text">{{__("Minutes.")}}</span>                                                              
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                        <div class="card mt-4">
                                            <div class="card-body">
                                                <button type="submit" id="filter_message_submit" class="btn btn-primary me-1"><i class="fas fa-save"></i> {{__('Save')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="tab-pane fade" id="v-pills-group-activity" role="tabpanel" aria-labelledby="v-pills-group-activity-tab">
                                    <div class="card px-2">
                                        <div class="card-body px-2">
                                            <div class="row mb-4">
                                                <div class="col-8">
                                                </div>
                                                <form id="time_selection_form" action="{{route('telegram-group-manager')}}">
                                                    @csrf
                                                    @php
                                                        $time_options = [
                                                            "1 day" => __("1 Day"),
                                                            "7 days" => __("7 Days"),
                                                            "1 month" => __("1 Month"),
                                                            "3 months" => __("3 Months"),
                                                            "6 months" => __("6 Months"),
                                                            "1 year" => __("1 Year")
                                                        ];
                                                        $default_time_info = '1 month';
                                                    @endphp
                                                    <div class="col-4">
                                                        <div class="input-group">
                                                            {{Form::select('time_selection',$time_options,request('time_selection', $default_time_info),['class'=>'form-control select2','id'=>'time_selection','style'=>$w_100] )}}
                                                        </div>
                                                    </div>
                                                </form>


                                            </div>
                                            <h6 class="mb-3">{{__('Members Activity')}}</h6>
                                            <div class="row mb-3">
                                                <div class="col-4">
                                                  <div class="card shadow-none card-statistic-1">
                                                    <div class="card-content">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col-3 text-end pe-0">
                                                               <i class="fas fa-users text-primary list-icon"></i>
                                                            </div>
                                                            <div class="col-9 ps-0">
                                                                <div class="card-body">
                                                                    <h4 class="card-title text-muted">{{ __("Group Members") }}</h4>
                                                                    <p class="card-text text-ellipsis fw-bold">{{ $total_members }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-4">
                                                  <div class="card shadow-none card-statistic-1">
                                                    <div class="card-content">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col-3 text-end pe-0">
                                                               <i class="fas fa-user-plus text-primary list-icon"></i>
                                                            </div>
                                                            <div class="col-9 ps-0">
                                                                <div class="card-body">
                                                                    <h4 class="card-title text-muted">{{ __("Joined Members") }}</h4>
                                                                    <p class="card-text text-ellipsis fw-bold">{{ $joined_members }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div></div>

                                                <div class="col-4">
                                                <div class="card shadow-none card-statistic-1">
                                                    <div class="card-content">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col-3 text-end pe-0">
                                                            <i class="fas fa-user-minus text-primary list-icon"></i>
                                                            </div>
                                                            <div class="col-9 ps-0">
                                                                <div class="card-body">
                                                                    <h4 class="card-title text-muted">{{ __("Left Members") }}</h4>
                                                                    <p class="card-text text-ellipsis fw-bold">{{ $left_members }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <div class="card shadow-none card-statistic-1">
                                                        <div class="card-content">
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col-3 text-end pe-0">
                                                                <i class="fas fa-user-times text-primary list-icon"></i>
                                                                </div>
                                                                <div class="col-9 ps-0">
                                                                    <div class="card-body">
                                                                        <h4 class="card-title text-muted">{{ __("Banned Members") }}</h4>
                                                                        <p class="card-text text-ellipsis fw-bold">{{ $banned_member }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="card shadow-none card-statistic-1">
                                                        <div class="card-content">
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col-3 text-end pe-0">
                                                                    <i class="fas fa-user-alt-slash text-primary list-icon"></i>
                                                                </div>
                                                                <div class="col-9 ps-0">
                                                                    <div class="card-body">
                                                                        <h4 class="card-title text-muted">{{ __("Muted members") }}</h4>
                                                                        <p class="card-text text-ellipsis fw-bold">{{ $muted_members }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-2">
                                        <div class="card-body p-0">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card no-shadow">
                                                        <div class="card-header">
                                                          <h5><i class="fas fa-users"></i> {{ __("Members (Last 30 Days)") }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                          <canvas id="myChart" height="142"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="v-pills-send-message" role="tabpanel" aria-labelledby="v-pills-send-message-tab">
                                  <section class="section">
                                      <div class="card">
                                          <div class="card-body data-card">
                                              <div class="row">
                                                 <div class="col-9">
                                                     <div class="input-group mb-3" id="searchbox">
                                                       <div class="input-group-prepend">
                                                         <select class="form-control select2" id="search_status" style  = "{{$w_100}}">
                                                           <option value="">{{__("Select Status")}}</option>
                                                           <option value="2">{{__("Completed")}}</option>
                                                           <option value="1">{{__("Processing")}}</option>
                                                           <option value="0">{{__("Pending")}}</option>
                                                         </select>
                                                       </div>
                                                       <div class="input-group-prepend">
                                                         <input type="text" class="form-control no-radius" autofocus 
                                                         id="search_value_send_message" name="search_value_send_message" 
                                                         placeholder="{{__("Search...")}}">
                                                       </div>
                                                     </div>
                                                 </div>
                                                  <div class="col-3">
                                                      <div class="ml-auto">
                                                          <a href="#"  id="create_group_campaign" class="btn btn-outline-primary float-end"><i class="fas fa-plus-circle"></i> {{ __('Create') }}</a>
                                                      </div>
                                                  </div>
                                                  
                                              </div>

                                              <div class="table-responsive">
                                                  <input type="hidden" id="hidden_campaign_id">
                                                  <table class='table table-hover table-bordered table-sm w-100' id="mytable2" >
                                                      <thead>
                                                      <tr class="table-light">
                                                          <th>#</th>
                                                          <th>{{__("Campaign Name") }}</th>
                                                          <th>{{__("Status") }}</th>
                                                          <th>{{__("Actions") }}</th>
                                                          <th>{{__("Scheduled at") }}</th>
                                                      </tr>
                                                      </thead>
                                                      <tbody></tbody>
                                                  </table>
                                              </div>

                                          </div>
                                      </div>

                                  </section>
                                </div>

                            </div>
                          
                        </div>

                     </div>

                </div>
                <input type="hidden" name="hidden_media_type" id="hidden_media_type" value="fb">
            </div>

        </section>
    </div>

    <div class="modal" id="total_msg_modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{__("Total Message List")}}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="card no-shadow">
                    <div class="card-body data-card pt-0">
                        <div class="table-responsive">
                            <table class='table table-hover table-bordered table-sm w-100' id="mytable3" >
                                <thead>
                                <tr class="table-light">
                                    <th>#</th>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__("Message") }}</th>
                                    <th>{{__("Sent at") }}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="mute_member_modal"  data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">{{ __("Mute Chat Member") }}&nbsp;&nbsp;&nbsp;&nbsp; <span class="text-danger" id="muted_date_time"></span></h5>  
                    <button type="button" class="btn btn-warning" id="unmute_member"  aria-label="Close">{{ __('Unmute') }}</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="">
                   <div class="row">
                       <div class="col-12 col-md-2">
                           <select class="form-control" id="mute_duration">
                              <option selected value = "">{{ __('Duration') }}</option>
                              <option value="minutes">{{ __('Minute') }}</option>
                              <option value="hours">{{ __('Hour') }}</option>
                              <option value="days">{{ __("Day") }}</option>
                              <option value="months">{{ __("Month") }}</option>
                            </select>
                       </div>
                       <div class="col-12 col-md-4">
                          <input type="number" id ="mute_time_duration" class="form-control">
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button id="mute_member_submit" class="btn btn-primary" > <i class="fas fa-check-circle"></i>  {{ __("Mute") }}</button>
                    <button type="button"  class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="group_message_send_modal" aria-labelledby="group_message_send_modal_label"  data-backdrop="static" data-keyboard="false" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">{{ __("Create Campaign") }}&nbsp;&nbsp;&nbsp;&nbsp; <span class="text-danger"></span></h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="">
        <form  class="form form-vertical" id="campaign_data_form"> 
            @csrf
            <input type="hidden" id="campaign_id" name="campaign_id" value="" >
            <div class="card">
                <div class="card-header border-0">
                {{__("Campaign Name")}} *
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" id="campaign_name" name="campaign_name" class="form-control" >
                        </div>
                        @if ($errors->has('campaign_name'))
                            <span class="text-danger"> {{ $errors->first('campaign_name') }} </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <label for="text_message" class="form-label">{{__('Message content')}}  *</label>
                    <textarea class="form-control" id="text_message" name="text_message" rows="3" > </textarea>
                </div>
                @if ($errors->has('text_message'))
                   <span class="text-danger"> {{ $errors->first('text_message') }} </span>
                @endif
            </div>
            <div class="card">
                <div class="card-header border-0">
                {{__("Options for sending messages")}}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text pt-2 w-100 bg-white">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="pin_this_announcement" type="checkbox" id="pin_this_announcement" value="1" <?php echo old('pin_this_announcement',$pin_this_announcement)=='1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-wrap" for="pin_this_announcement">{{__("Pin this announcement")}}</label>
                                            </div>
                                        </span>
                                    </div>
                                    @if ($errors->has('pin_this_announcement'))
                                        <span class="text-danger"> {{ $errors->first('pin_this_announcement') }} </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text pt-2 w-100 bg-white">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="preview_the_URL_in_the_text_message" type="checkbox" id="preview_the_URL_in_the_text_message" value="1" <?php echo old('preview_the_URL_in_the_text_message',$preview_the_url)=='1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-wrap" for="preview_the_URL_in_the_text_message">{{__("Preview the URL in the text message")}}</label>
                                            </div>
                                        </span>
                                    </div>
                                    @if ($errors->has('preview_the_URL_in_the_text_message'))
                                        <span class="text-danger"> {{ $errors->first('preview_the_URL_in_the_text_message') }} </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text pt-2 w-100 bg-white">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="protected_messages_no_copying_or_forwarding" type="checkbox" id="protected_messages_no_copying_or_forwarding" value="1" <?php echo old('protected_messages_no_copying_or_forwarding',$protected_messages_no_copying)=='1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-wrap" for="protected_messages_no_copying_or_forwarding">{{__("Protected messages, no copying or forwarding")}}</label>
                                            </div>
                                        </span>
                                    </div>
                                    @if ($errors->has('protected_messages_no_copying_or_forwarding'))
                                        <span class="text-danger"> {{ $errors->first('protected_messages_no_copying_or_forwarding') }} </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text pt-2 w-100 bg-white">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="sound_alerts_for_messages" type="checkbox" id="sound_alerts_for_messages" value="1" <?php echo old('sound_alerts_for_messages',$sound_alerts_for_messages)=='1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-wrap" for="sound_alerts_for_messages">{{__("Sound alerts for messages received by the other party")}}</label>
                                            </div>
                                        </span>
                                    </div>
                                    @if ($errors->has('sound_alerts_for_messages'))
                                        <span class="text-danger"> {{ $errors->first('sound_alerts_for_messages') }} </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-4">
                                    <label class="form-check-label" for="">{{__("Delayed automatic deletion:")}}</label>
                                </div>
                                <div class="col-3">
                                    <select class="form-select" name="delayed_automatic_deletion" aria-label="Default select example" id="delayed_automatic_deletion">
                                        <option selected value = "0">{{ __('OFF') }}</option>
                                        <option value="1-Minute">{{ __('1 Minute') }}</option>
                                        <option value="2-Minute">{{ __('2 Minutes') }}</option>
                                        <option value="5-Minute">{{ __("5 Minutes") }}</option>
                                        <option value="10-Minute">{{ __("10 Minutes") }}</option>
                                        <option value="15-Minute">{{ __('15 Minutes') }}</option>
                                        <option value="30-Minute">{{ __('Half an hour') }}</option>
                                        <option value="1-Hour">{{ __("1 Hour") }}</option>
                                        <option value="2-Hour">{{ __("2 Hours") }}</option>
                                        <option value="5-Hour">{{ __('5 Hours') }}</option>
                                        <option value="12-Hour">{{ __('Half Day') }}</option>
                                        <option value="1-day">{{ __("1 Day") }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header border-0">
                {{__("Scheduling sending messages")}}
                </div>
                <div class="card-body">
                    @php
                        $sending_option = 'later';
                    @endphp
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text pt-2 w-100 bg-white">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="sending_option" type="radio"  value="now" onchange="scheduleOption()" @if($sending_option=='now') {{ 'checked' }} @else {{ '' }} @endif>
                                                <span class="custom-switch-indicator"></span>
                                              <span class="custom-switch-description"><?php echo __('Send Now'); ?></span>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text pt-2 w-100 bg-white">
                                            <div class="form-check form-switch">
                                                <input type="radio" name="sending_option" value="later" id="send_later" onchange="scheduleOption()" class="form-check-input" @if($sending_option=='later') {{ 'checked' }} @else {{ '' }} @endif>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"><?php echo __('Send Later'); ?>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row <?php echo $sending_option=='later' ? 'class="d-none"' : ''; ?>" id="schedule_option">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">{{ __("Schedule time") }} </label>
                                <div class="input-group">
                                    <input type="text" id="schedule_time" name="schedule_time" class="form-control datetimepicker">
                                </div>
                                @if ($errors->has('schedule_time'))
                                    <span class="text-danger"> {{ $errors->first('schedule_time') }} </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">{{ __("Timezone") }} </label>
                                <div class="input-group" id="timezone">
                                    @php
                                        $selected = '';
                                        $timezone_list = get_timezone_list();
                                        echo Form::select('timezone',$timezone_list,request('timezone', $selected),array('class'=>'form-control select2'));
                                    @endphp
                                </div>
                                @if ($errors->has('timezone'))
                                    <span class="text-danger"> {{ $errors->first('timezone') }} </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <div class="modal-footer">
               <button type="submit" {!! config('app.is_demo')=='1' && $is_admin ? 'onclick="alert(\'This feature has been disabled in this demo version. We recommend to sign up as user and check.\');return false;"' : ''!!} class="btn btn-primary" id="save_campaign_data"> <i class="fas fa-check-circle"></i>  {{ __("Save") }}</button>
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
           </div>
        </form>
        </div>
        </div>
    </div>

@php
    $member_chart_labels = array();
    $member_chart_values = array();
    $member_values = array();

    $from_date = strtotime(date('Y-m-d H:i:s', strtotime('- 30 days')));
    $to_date = strtotime(date('Y-m-d H:i:s'));
    do
    {
        $temp = date("Y-m-d",$from_date);
        $temp2 = date("j M",$from_date);;
        $member_chart_values[$temp] = 0;
        $member_chart_labels[] = $temp2;
        $from_date = strtotime('+1 day',$from_date);
    }
    while ($from_date <= $to_date);
    
    foreach ($total_member_data as $key => $value)
    {
        $updated_at_formatted = date("Y-m-d",strtotime($value->updated_at));
        if (isset($member_chart_values[$updated_at_formatted])) {
            $member_chart_values[$updated_at_formatted]++;
        } 
    }
    $max = (!empty($member_chart_values)) ? max($member_chart_values) : 0;
    $steps = $max/5;
    if($steps==0) $steps = 1;
        
@endphp
  
    
    

@endsection

@push('styles-header')
    <link rel="stylesheet" href="{{ asset('assets/vendors/emoji/dist/emojionearea.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/telegram/bot.bot-manager.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pages/modal-right.css') }}">
@endpush

@push('scripts-header')
    <script src="{{ asset('assets/vendors/emoji/dist/emojionearea.min.js') }}"></script>
@endpush

@push('scripts-footer')
    <script>
        "use strict";
        var member_chart_steps = '{{ $steps ?? "0"; }}';
        var member_chart_labels = '{!! isset($member_chart_labels) ? json_encode($member_chart_labels): "" !!}';
        var member_chart_values = '{!! isset($member_chart_values) ? json_encode(array_values($member_chart_values)): "" !!}';
        var ajax_set_active_tag_id = '{{route('general-settings-set-session-active-tab')}}';
        var group_message_send = '{{route('group-message-send')}}';
    </script>
@endpush

@push('styles-footer')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/telegram/subscriber.list-subscriber.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/dropzone/dist/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/telegram/group.group-manager.css') }}" />
@endpush

@push('scripts-footer')
    <script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/telegram/subscriber.group-list-subscriber.js') }}"></script>
    <script src="{{ asset('assets/js/pages/telegram/subscriber.list-subscriber-common.js') }}"></script>
    <script src="{{ asset('assets/vendors/dropzone/dist/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/whatsapp/video-modal.js') }}"></script>

@endpush