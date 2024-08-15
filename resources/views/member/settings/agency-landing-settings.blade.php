@extends('layouts.auth')
@section('title',__('Landing Page Editor'))
<link rel="stylesheet" href="{{ asset('assets/css/inlinecss.css') }}">

@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first">
                <h3>{{ __('Landing Page Editor') }}
                    <a class="btn btn-primary" href="{{ route('agency-landing-editor-reset') }}">{{ __("Reset") }}</a>
                </h3>
                <p class="text-subtitle text-muted">{{ __('Customize Landing Page') }}</p>
            </div>
            <div class="col-12 col-md-6 order-last">
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Saved')}}</h4>
            <p> {{ session('status') }}</p>
        </div>
    @endif

    <section class="section">
        <form class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('agency-landing-editor-action') }}">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="input-group mb-2">
                            <span class="input-group-text pt-2 w-100 py-4">
                                <div class="form-check form-switch mt-3 ms-2">
                                    <input class="form-check-input" id="disable_landing_page" name="disable_landing_page" type="checkbox" value="1" <?php echo (old('disable_landing_page',$xdata->disable_landing_page??0)=='0') ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="disable_landing_page"><h5 class="m-0 text-danger">{{__("Disable Landing Page")}}</h5></label>
                                </div>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input-group mb-2">
                            <span class="input-group-text pt-2 w-100 py-4">
                                <div class="form-check form-switch mt-3 ms-2">
                                    <input class="form-check-input" id="disable_review_section" name="disable_review_section" type="checkbox" value="1" <?php echo (old('disable_review_section',$xdata->disable_review_section??0)=='0') ? '' : 'checked'; ?>>
                                    <label class="form-check-label" for="disable_review_section"><h5 class="m-0 text-primary">{{__("Disable Review Section")}}</h5></label>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-md-9">
                                <div class="tab-content mt-4" id="v-pills-tabContent">

                                    <div class="tab-pane fade" id="v-pills-detailedFeature" role="tabpanel" aria-labelledby="v-pills-detailedFeature-tab">
                                        <div class="row">
                                        	<?php $i=0;?>
                                            @foreach($settings_data['details_features'] as $key => $form)
                                            <?php $i++;?>
                                                
                                                @foreach($form as $value)

                                                    <?php  
                                                    $name = $value['name'] ?? '';
                                                    $placeholder = $value['placeholder'] ?? '';
                                                    $type = $value['type'] ?? '';
                                                    $label = $value['label'] ?? '';
                                                    $upload = $value['upload'] ?? false;
                                                    if($upload) $label = '<a href="#" class="badge bg-primary float-end no-radius upload-file">'.__('Upload').'</a>';
                                                    $form_value = isset($xdata->$name) ? $xdata->$name : $value['value'];
                                                    ?>

                                                    <div class="card">
                                                        <div class="card-header pb-0">
                                                            <h4 class="card-title">{{$value['label'] ?? ''}}
                                                        </h4>
                                                        </div>
                                                        <div class="card-body">

                                                            @switch($value['field'])
                                                                @case("textarea")
                                                                    <div class="col-12">
                                                                        <div class="form-group mb-3">
                                                                            <label class="text-capitalize w-100">{!! $label !!}</label>
                                                                            <textarea class="form-control" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}">{{ $form_value }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                @break

                                                                @case("input")
                                                                        <div class="col-12">
                                                                            <div class="form-group mb-3">
                                                                                <label class="text-capitalize w-100">{!! $label !!}</label>
                                                                                <input class="form-control" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $form_value }}">
                                                                            </div>
                                                                        </div>
                                                                @break
                                                            @endswitch
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade show active" id="v-pills-company" role="tabpanel" aria-labelledby="v-pills-company-tab">
                                        <div class="row">
                                            @php($i=0)
                                            @foreach($settings_data['company_elements'] as $key => $form)
                                                <?php  $name = $form['name'] ?? '';
                                                $placeholder = $form['placeholder'] ?? '';
                                                $type = $form['type'] ?? '';
                                                $label = $form['label'] ?? '';
                                                $upload = $form['upload'] ?? false;
                                                if($upload) $label = $label.'<a href="#" class="badge bg-primary float-end no-radius upload-file">'.__('Upload').'</a>';
                                                $form_value = isset($xdata->$name) ? $xdata->$name : $form['value'];
                                                ?>
                                                @switch($form['field'])
                                                    @case("textarea")
                                                    <div class="col-12">
                                                        <div class="form-group mb-3">
                                                            <label class="text-capitalize w-100">{!! $label !!}</label>
                                                            <textarea class="form-control" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}">{{ $form_value }}</textarea>
                                                        </div>
                                                    </div>
                                                    @break

                                                    @case("input")
                                                    <div class="col-12">
                                                        <div class="form-group mb-3">
                                                            <label class="text-capitalize w-100">{!! $label !!}</label>
                                                            <input class="form-control" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $form_value }}">
                                                        </div>
                                                    </div>
                                                    @break
                                                @endswitch
                                                @php($i++)
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade pt-4" id="v-pills-reviews" role="tabpanel" aria-labelledby="v-pills-reviews-tab">
                                        <?php $i=0;?>
                                        @foreach($settings_data['customer_reviews'] as $key => $form)
                                            <?php $i++;?>
                                            <div class="card card-rounded mb-4 p-lg-2">
                                                <div class="card-body card-rounded">
                                                    <h4 class="card-title card-title-dash mb-4">{{ __('Review') }} {{$i}}</h4>
                                                    <div class="row">
                                                        @foreach($form as $value)

                                                            <?php  $name = $value['name'] ?? '';
                                                                    $placeholder = $value['placeholder'] ?? '';
                                                                    $type = $value['type'] ?? '';
                                                                    $label = $value['label'] ?? '';
                                                                    $upload = $value['upload'] ?? false;
                                                                    if($upload) $label = $label.'<a href="#" class="badge bg-light float-end no-radius upload-file text-decoration-none">'.__('Upload').'</a>';
                                                                    $form_value = isset($xdata->$name) ? $xdata->$name : $value['value'];
                                                            ?>

                                                            @switch($value['field'])

                                                                @case("input")
                                                                <div class="col-12 col-md-4">
                                                                    <div class="form-group mb-3">
                                                                        <label class="fw-bold w-100">{!! $label !!}</label>
                                                                        <input class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $form_value }}">
                                                                    </div>
                                                                </div>
                                                                @break

                                                                @case("textarea")
                                                                <div class="col-12">
                                                                    <div class="form-group mb-3">
                                                                        <label class="fw-bold w-100">{!! $label !!}</label>
                                                                        <textarea class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}">{{ $form_value }}</textarea>
                                                                    </div>
                                                                </div>
                                                                @break

                                                            @endswitch
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <div class="col-6 col-md-3">
                                <div class="nav flex-column nav-pills me-3 mt-5" id="v-pills-tab" role="tablist" aria-orientation="vertical">                                    
                                    <button class="nav-link active" id="v-pills-company-tab" data-bs-toggle="pill" data-bs-target="#v-pills-company" type="button" role="tab" aria-controls="v-pills-company" aria-selected="false">{{ __("Company") }}</button>
                                    <button class="nav-link" id="v-pills-detailedFeature-tab" data-bs-toggle="pill" data-bs-target="#v-pills-detailedFeature" type="button" role="tab" aria-controls="v-pills-detailedFeature" aria-selected="true">{{ __("Media") }}</button>
                                    <button class="nav-link" id="v-pills-reviews-tab" data-bs-toggle="pill" data-bs-target="#v-pills-reviews" type="button" role="tab" aria-controls="v-pills-reviews" aria-selected="false">{{ __("Reviews") }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-save"></i> {{__('Save')}}</button>
                    </div>
                </div>
        </form>
    </section>
</div>

<div class="modal fade" id="upload_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{__('Upload')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="current-item-name">
                <div id="agency-dropzone" class="dropzone mb-1">
                    <div class="dz-default dz-message">
                        <input class="form-control" name="thumbnail" id="uploaded-file" type="hidden">
                        <span id="spn_id" ><i class="fas fa-cloud-upload-alt" id="loud-upl" ></i> {{ __("Upload") }}</span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles-header')
    <link rel="stylesheet" href="{{ asset('assets/vendors/dropzone/dist/dropzone.css') }}">
@endpush
@push('scripts-footer')
    <script>
        "use strict";
         var upload_url = '{{route('agency-landing-upload-media')}}';
    </script>
    <script src="{{ asset('assets/vendors/dropzone/dist/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/member/settings.agency-landing-settings.js') }}"></script>
@endprepend
