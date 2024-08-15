@extends('translation::layout')

@section('body')
    <div class="panel w-1/3 mx-auto create_form shadow-lg">

        <div class="panel-header">

            {{ __('translation::translation.add_language') }}

        </div>

        <form action="{{ route('languages.store') }}" method="POST">
            @csrf
            <fieldset>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="submit_type" value="{{ isset($selectedLang) ? 'edit':''}}">
                <input type="hidden" name="prev_locale" value="{{ isset($selectedLang) ? $selectedLang:''}}">

                <div class="panel-body p-4">
                    <div class="form-group d-none">
                        @include('translation::forms.text', ['field' => 'name', 'label' => __('translation::translation.language_name'), 'value'=>$selectedLang ?? ''])
                    </div>
                    <div class="form-group">
                        <label class="mb-2">{{ __("Select Language") }} </label>
                        <select name="locale" id="locale" class="form-control w-100">
                            <option value="">{{ __("Select") }}</option>
                                @foreach($preDefinedList as $key => $lang) {
                                    <option value="{{ $key }}">{{ $lang }}</option>;
                                @endforeach
                        </select>
                    </div>

                </div>

            </fieldset>

            <div class="panel-footer d-flex">

                <button class="btn btn-primary">
                    {{ __('translation::translation.save') }}
                </button>

                <a class="btn btn-light ms-auto" href="{{ route('languages.index') }}">
                    {{ __('Cancel') }}
                </a>

            </div>

        </form>

    </div>

@endsection