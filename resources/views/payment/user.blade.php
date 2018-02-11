@extends('layout.admin.dashboard')
@section('body')

    <h1>Payment Section</h1>

    <form class="form-horizontal" action="{{ action('PaymentController@store') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="paymentSlip">Bank transfer slip</label>
            <div class="col-sm-8">
                <input class="form-control-file" type="file" id="paymentSlip" name="paymentSlip">
            </div>
        </div>

        <div class="form-group row text-center">
            <input class="btn btn-primary" type="submit" name="upload" value="Upload">
        </div>

    </form>

@endsection