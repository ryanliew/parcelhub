@component('mail::message')
# User {{ $inbound->user->name }} has created an inbound order #{{ $inbound->id }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
