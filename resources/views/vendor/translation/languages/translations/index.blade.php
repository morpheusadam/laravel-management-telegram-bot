@extends('translation::layout')

@section('body')
    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    @php $langName = explode('-',$language); @endphp

    <form action="{{ route('languages.translations.index', ['language' => $language]) }}" method="get">

        <div class="panel">

            <div class="panel-header">
                {{ __('translation::translation.translations') }}

                <div class="flex flex-grow justify-end items-center">
                    @if(config('app.is_demo')!='1')
                    <a href="{{route('languages.translations.compile',Request::segment(2))}}?group={{Request::get('group')}}" class="btn btn-warning">
                        <i class="fas fa-check-circle"></i>  {{ __('Apply Changes') }}
                    </a>
                    @endif
                    <a href="{{ route('languages.download',Request::segment(2)) }}" class="btn btn-success ml-2" targ>
                        <i class="fas fa-download"></i> {{ __('Download') }}
                    </a>
                </div>
            </div>

            <div class="panel-header">
                <div class="flex flex-grow justify-end items-center">

                    @include('translation::forms.search', ['name' => 'filter', 'value' => Request::get('filter')])

                    @include('translation::forms.select', ['name' => 'language', 'items' => $userLanguages, 'submit' => true, 'selected' => $language])

                    <div class="sm:hidden lg:flex items-center">

                        @include('translation::forms.select', ['name' => 'group', 'items' => $groups, 'submit' => true, 'selected' => Request::get('group'), 'optional' => false])

                        @if(config('app.is_demo')!='1')
                            @if($language=='en' && Auth::user()->user_type=='Admin')
                                <a href="{{ route('languages.translations.create', $language) }}" class="button">
                                    {{ __('translation::translation.add') }}
                                </a>&nbsp;

                                <a href="{{route('languages.translations.find-language',Request::segment(2))}}" class="button">
                                    {{ __('Scan') }}
                                </a>
                            @endif
                        @endif

                    </div>

                </div>
            </div>


            <div class="panel-body">

                @if(count($translations))

                    <table>

                        <thead>
                            <tr>
                                @if(Auth::user()->user_type=='Admin')<th class="w-1/5 font-thin">{{ __('translation::translation.key') }}</th>@endif
                                <th class="font-thin">{{ $defaultList['en'] }}</th>
                                <th class="font-thin">{{ $defaultList[$langName[0]] }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($translations as $type => $items)

                                @foreach($items as $group => $translations)

                                    @foreach($translations as $key => $value)

                                        @if(!is_array($value['en']))
                                            <tr>
                                                @if(Auth::user()->user_type=='Admin')<td>{{ $key }}</td>@endif
                                                <td>{{ $value['en'] }}</td>
                                                <td>
                                                    <translation-input
                                                        initial-translation="{{ $value[$language] }}"
                                                        language="{{ $language }}"
                                                        group="{{ $group }}"
                                                        translation-key="{{ $key }}"
                                                        route="{{ config('translation.ui_url') }}">
                                                    </translation-input>
                                                </td>
                                            </tr>
                                        @endif

                                    @endforeach

                                @endforeach

                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </form>

@endsection
