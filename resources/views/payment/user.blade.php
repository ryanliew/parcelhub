@extends('layout.admin.dashboard')
@section('body')

    <h1>Payment Section</h1>

<h2>Purchase Lots</h2>

    <form action="" method="post">

        {{ csrf_field() }}

        <table class="table">
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Volume</th>
            </tr>
            <tr>
                @foreach($lots as $index => $lot)
                <tr class="child">
                    <td>
                        <label>
                            <input type="hidden" name="lots[$index][id]" value="{{ $lot->id }}">
                            <input type="text" name="lots[$index][name]" value="{{ $lot->name }}">
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" name="lots[$index][category]" value="{{ $lot->category->name }}" disabled>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" name="lots[$index][volume]" value="{{ $lot->volume }}" disabled>
                        </label>
                    </td>
                </tr>
                @endforeach
            </tr>
            
        </table>
        <input type="submit" name="purchase" id="purchase" value="Purchase">
    </form>
    
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