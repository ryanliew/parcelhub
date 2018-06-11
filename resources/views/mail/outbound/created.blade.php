@component('mail::message')
# Hi, {{ $outbound->user->name }}.#

This email is to inform you that we have received your outbound order #{{ $outbound->id }}

Our administrators will look into it as soon as possible.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
