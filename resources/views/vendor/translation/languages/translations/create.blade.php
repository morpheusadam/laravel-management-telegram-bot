@extends('translation::layout')

@section('body')
    <div class="panel w-1/3 mx-auto create_form shadow-lg">

        <div class="panel-header">

            {{ __('translation::translation.add_translation') }}

        </div>

        <form action="{{ route('languages.translations.store', $language) }}" method="POST">

            <fieldset>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="panel-body p-4">
                    <div class="input-group d-block">
                        <label>{{ __("Group (Optional)") }} <span><a class="float-end text-decoration-none" lang-dir="{{ Request::segment(2) ?? 'en'; }}" href="#" id="create_contact_group"><i class="fas fa-plus-circle"></i> @lang('Create Group') </a></span></label>
                        @php $group_list[''] = __('Select File'); @endphp
                        {{
                            Form::select("group",$group_list,'',['class'=>'form-select d-block w-100','id'=>'group'])
                        }}
                    </div>
                    
                    @include('translation::forms.text', ['field' => 'key', 'label' => __('translation::translation.key_label'), 'placeholder' => __('translation::translation.key_placeholder')])

                    @include('translation::forms.text', ['field' => 'value', 'label' => __('translation::translation.value_label'), 'placeholder' => __('translation::translation.value_placeholder')])
                    
                </div>
            </fieldset>

            <div class="panel-footer d-flex">

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('translation::translation.save') }}
                </button>

                <a class="btn btn-light ms-auto" href="{{ route('languages.translations.index',Request::segment(2)) }}">
                    <i class="fas fa-times"></i> {{ __('Cancel') }}
                </a>

            </div>

        </form>

    </div>

    <script>
        var create_lang_group = "{{ route('languages.translations.create-new-group') }}";
    </script>

@endsection