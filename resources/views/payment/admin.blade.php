@extends('layout.admin.dashboard')
@section('body')

    <h1>Admin Payment Management</h1>

    <form action="{{ route('payment.approve') }}" method="post">

        {{ csrf_field() }}

        <table class="table">
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Email</th>
                <th>Bank Payment Slip</th>
                <th>Upload Time</th>
            </tr>

            @foreach($payments as $payment)
                <tr>
                    <td><label><input type="checkbox" name="payments[]" value="{{ $payment->id }}"></label></td>
                    <td>{{ $payment->user->name }}</td>
                    <td>{{ $payment->user->email }}</td>
                    <td><img src="{{ Storage::url($payment->picture) }}" width="50px" height="50px"></td>
                    <td>{{ $payment->created_at }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <h5>Purchased items :</h5>
                        <ul>
                            {{-- Retrieve all the purchased lots by this payment --}}
                            @foreach($lots as $lot)
                                @if($lot->user->id == $payment->user->id)
                                    <li>{{ $lot->name }} , {{ $lot->category->name }} , {{ $lot->volume }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach

        </table>

        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary">Approve</button>
        </div>
    </form>

@endsection