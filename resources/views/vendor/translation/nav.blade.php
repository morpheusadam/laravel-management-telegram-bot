<nav class="header">

    <h1 class="text-lg px-6"><a href="{{ url('') }}">{{ config('app.name') }}</a></h1>

    <ul class="flex-grow justify-end pr-2">
        <li>
            <a href="{{ route('languages.index') }}" class="{{ set_active('') }}{{ set_active('/create') }}">
                @include('translation::icons.globe')
                {{ __('translation::translation.languages') }}
            </a>
        </li>
    </ul>

</nav>