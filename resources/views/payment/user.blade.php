@extends('layout.admin.dashboard')
@section('body')

<h2>Purchase Lots</h2>

    <form action="{{ route('payment.purchase') }}" method="post" enctype="multipart/form-data">

        {{ csrf_field() }}

        <table class="table">
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Category</th>
                <th>Volume</th>
                <th>Price</th>
                <th>Rental Duration (Min 90 days)</th>
            </tr>
            <tr>

            {{--Post Data Structure Requirement--}}
            {{--Data must be in array of object consist of lots_purchase[0][id], lots_purchase[0][rental_duration] and payment_slip--}}
            {{--Only selected item is required--}}
            @foreach($lots as $index => $lot)
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" id="select_{{ $index }}" name="lots_purchase[{{ $index }}][id]" value="{{ $lot->id }}">
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" value="{{ $lot->name }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" value="{{ $lot->category->name }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" value="{{ $lot->volume }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" value="{{ $lot->price }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" id="rental_duration_{{ $index }}" name="lots_purchase[{{ $index }}][rental_duration]"
                                   value="{{ $lot->rental_duration }}">
                        </label>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="form-group">
            <label for="payment_slip">Bank transfer slip</label>
            <input type="file" name="payment_slip" id="payment_slip">
        </div>
        <button name="purchase" id="purchase">Purchase</button>
    </form>
@endsection