@component('mail::message')

# Hi, {{ $outbound->user->name }} # 

Your outbound order {{ $outbound->id }} has been updated to

# status : {{ $outbound->process_status }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
