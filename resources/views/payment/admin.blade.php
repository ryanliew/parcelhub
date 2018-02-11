@extends('layout.admin.dashboard')
@section('body')

    <h1>Admin Payment Management</h1>

    <form action="{{ action('PaymentController@approve') }}" method="post">

        {{ csrf_field() }}

        <table class="table">
            <tr>
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
            @endforeach

        </table>

        <div class="form-group text-center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#approvePayment">
                Approve
            </button>
        </div>

        @component('payment.approve')
            <strong>Whoops!</strong> Something went wrong!
        @endcomponent

    </form>

@endsection