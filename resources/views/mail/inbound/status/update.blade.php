@component('mail::message')

# Hi, {{ $inbound->user->name }}.#

Your inbound order (#{{ $inbound->id }}) has been updated to

# status : {{ $inbound->process_status }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
