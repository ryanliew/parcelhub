@extends('layout.admin.dashboard')
@section('body')

    <h1>Payment Section</h1>

<h2>Purchase Lots</h2>

    <form action="{{ route('payment.purchase') }}" method="post" enctype="multipart/form-data">

        {{ csrf_field() }}

        <table class="table">
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Category</th>
                <th>Volume</th>
            </tr>
            <tr>
            @foreach($lots as $index => $lot)
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" id="select_{{ $index }}" name="lots_purchase[{{ $index }}][id]" value="{{ $lot->id }}">
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" id="name_{{ $index }}" name="name" value="{{ $lot->name }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" id="category_{{ $index }}" name="category" value="{{ $lot->category->name }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" id="volume_{{ $index }}" name="volume" value="{{ $lot->volume }}" disabled>
                        </label>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="form-group">
            <label for="payment_slip">Bank transfer slip</label>
            <input type="file" name="payment_slip">
        </div>
        <input type="submit" name="purchase" id="purchase" value="Purchase">
    </form>

@endsection