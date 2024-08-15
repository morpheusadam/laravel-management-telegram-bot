@component('mail::message')
# {{$name}}

{!! $message !!}

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent