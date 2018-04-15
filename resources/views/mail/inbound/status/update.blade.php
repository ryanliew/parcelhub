@component('mail::message')

# User {{ $inbound->user->name }} inbound order #{{ $inbound->id }}

# status : {{ $inbound->process_status }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
