@extends('layout.admin.dashboard')
@section('body')
    <div class="container">
        <h1>Payment Section</h1>

        <form class="form-horizontal" action="{{ action('PaymentController@store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="bankPaymentSlip">Bank transfer slip</label>
                <div class="col-sm-8">
                    <input class="form-control-file" type="file" id="bankPaymentSlip" name="bankPaymentSlip">
                </div>
            </div>

            <div class="form-group row text-center">
                <input class="btn btn-primary" type="submit" name="upload" value="Upload">
            </div>

            @if(count($errors))
                <div class="form-group">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{session('success')}}</li>
                    </ul>
                </div>
            @endif

        </form>
    </div>
@endsection