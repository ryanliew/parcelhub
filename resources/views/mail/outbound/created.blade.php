@component('mail::message')
# User {{ $outbound->user->name }} has created an outbound order #{{ $outbound->id }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
