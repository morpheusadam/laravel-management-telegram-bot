@extends('translation::layout')

@section('body')

    @if(count($userLanguages)>0)
        <div class="panel">

            <div class="panel-header border-bottom-0">

                {{ __('translation::translation.languages') }}

                @if(config('app.is_demo')!='1')
                    <div class="flex flex-grow justify-end items-center">

                        <a href="{{ route('languages.create') }}" class="btn btn-primary">
                            {{ __('translation::translation.add') }}
                        </a>

                    </div>
                @endif

            </div>

            <div class="panel-body">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th class="text-center;">{{ __('translation::translation.language_name') }}</th>
                            <th class="text-center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($userLanguages as $language => $name)
                            <tr>
                                <td>{{ $name }}</td>
                                <td>
                                    @php $uri = explode('-',$language) @endphp
                                    <a class="" href="{{ route('languages.translations.index', $uri[0]) }}?group=custom-landing" data-bs-toggle="tooltip" title="{{ __('Translate Texts') }}"><i class="fas fa-eye"></i></a>&nbsp;
                                    @if($language!='en')
                                        <a href="#" class="delete_lang" locale_name="{{ $language }}" data-bs-toggle="tooltip" title="{{ __('Delete Locale') }}"><i class="fas fa-trash-alt"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>

    @else

        <div id="error">
            <div class="container text-center pt-32">
                <h4>{{ __("Sorry, we could not find any language to show.") }}</h4>
                <a href="{{ route('languages.create') }}" class="btn btn-primary">{{ __("Add Language") }}</a>
            </div>
        </div>

    @endif

    <script>
        "use strict";
        var action_url = "{{ route('languages.delete') }}";
    </script>

@endsection
