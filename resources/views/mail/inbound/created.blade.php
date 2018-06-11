@component('mail::message')
# Hi, {{ $inbound->user->name }}.#

This email is to inform you that we have received your inbound order #{{ $inbound->id }}

Our administrators will look into it as soon as possible.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
