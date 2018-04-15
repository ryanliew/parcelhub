@component('mail::message')

# User {{ $outbound->user->name }} outbound order #{{ $outbound->id }}

# status : {{ $outbound->process_status }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
