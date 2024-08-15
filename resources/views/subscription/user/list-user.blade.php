<?php
$lang_display = __('User Manager');
$title_display = $lang_display;
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{$title_display}}
                    <a href="{{route('create-user')}}" class="btn btn-outline-primary"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                </h3>
                <p class="text-subtitle text-muted">{{__('List of subscribed users')}}</p>
            </div>
        </div>
    </div>

    @if (session('save_user_limit_error')=='1')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Limit Exceeded')}}</h4>
            <p> {{ __('User creation limit exceeded. You cannot create more user.') }}</p>
        </div>
    @endif

    @if (session('save_team_limit_error')=='1')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Limit Exceeded')}}</h4>
            <p> {{ __('Team member creation limit exceeded. You cannot create more team member.') }}</p>
        </div>
    @endif

    @if (session('save_user_status')=='1')
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Successful')}}</h4>
            <p> {{ __('User has been saved successfully.') }}</p>
        </div>
    @elseif (session('save_user_status')=='0')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Failed')}}</h4>
            <p>
                {{ __('Something went wrong. Failed to save user.') }}&nbsp;{{session('save_user_status_error')}}
            </p>
        </div>
    @endif
    <section class="section">
        <div class="card">
            <div class="card-body data-card">
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3" id="searchbox">
                            <div class="input-group-prepend">
                                <?php echo Form::select('search_package_id',$packages,'',['class' => 'form-control select2','id'=>'search_package_id','autocomplete'=>'off']);?>
                            </div>
                            <?php $four_block=false;?>
                            <div class="input-group-prepend">
                                <input type="text" class="form-control no-radius" autofocus id="search_value" name="search_value" placeholder="{{__("Search...")}}">
                            </div>
                            <div class="input-group-prepend">
                                <button type="button" class="mt-2 btn btn-outline-light text-dark dropdown-toggle dropdown-toggle-split float-end"  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  data-reference="parent">
                                    <span>{{__('Options')}}</span>
                                </button>
                                <div class="dropdown-menu">
                                    @if(!$is_trial)
                                        <a class="dropdown-item" href="#" id="download_data"><i class="fas fa-cloud-download-alt"></i> <?php echo __("Download as CSV"); ?></a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <a class="dropdown-item" href="#" id="send_email_ui"><i class="fas fa-paper-plane"></i> <?php echo __("Send Email"); ?></a>
                                </div>
                            </div>
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
                            <th>{{__("Name") }}</th>
                            <th>{{__("Email") }}</th>
                            <th>{{__("Package/Role") }}</th>
                            <th>{{__("Status") }}</th>
                            <th>{{__("Role") }}</th>
                            <th>{{__("Actions") }}</th>
                            <th>{{__("Expiry date") }}</th>
                            <th>{{__("Created") }}</th>
                            <th>{{__("Last login") }}</th>
                            <th>{{__("Last IP") }}</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</div>
<div class="modal fade" id="modal_send_sms_email" tabindex="-1" aria-labelledby="modal_send_sms_email_label"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{{ __("Send Email") }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div id="show_message" class="text-center"></div>

                <div class="form-group">
                    <label for="subject">{{ __("Subject") }} *</label><br/>
                    <input type="text" id="subject" class="form-control"/>
                    <div class="invalid-feedback">{{ __("Subject is required") }}</div>
                </div>

                <div class="form-group">
                    <label for="message">{{ __("Message") }} *</label><br/>
                    <textarea name="message" class="h-min-200px form-control" id="message"></textarea>
                    <div class="invalid-feedback">{{ __("Message is required") }}</div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="send_sms_email" class="btn btn-primary" > <i class="fas fa-paper-plane"></i>  {{ __("Send") }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

<?php $width_normal = $four_block ? '25' : '33';?>
@push('styles-header')
    @include('subscription.user.list-user-css')
@endpush


@push('scripts-footer')
<script src="{{ asset('assets/js/pages/subscription/user.list-user.js') }}"></script>
@endpush


