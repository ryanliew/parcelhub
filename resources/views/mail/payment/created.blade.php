@component('mail::message')

# Thank You!

@component('mail::table')
| Name              | Category                      | Volume                | Rental Duration               | Price                         |
| ----------------- | ----------------------------: | --------------------: | ----------------------------: | ----------------------------: |
@foreach($payment->lots as $lot)
| {{ $lot->name }}  | {{ $lot->category->name }}    | {{ $lot->volume }}    | {{ $lot->rental_duration }}   | RM{{ $lot->price }}           |
@endforeach
|                   |                               |                       | Total:                        | RM{{ $payment->total_price }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
