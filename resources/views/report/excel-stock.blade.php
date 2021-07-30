<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
</head>
<body>
    <div class="pull-left width-half">
        <h1>Stock Level Preview</h1>
        <span style="margin-top: -15px;">Customer: <u>{{ auth()->user()->name }}</u></span>
        <p style="margin-top: 12px">Branch: <u>{{ $branch }}</u></p>
    </div>
    <div class="pull-right width-half pt-1">
        <p>From date : <u>{{ $from }}</u></p>
        <p>To date : <u>{{ $to }}</u></p>
    </div>

    <div class="clear-both"></div>

    <table class="item-table">
         <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Item</th>
                <th class="text-center">Opening Qty</th>
                <th>Closing Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->opening ?: 0 }}</td>
                    <td>{{ $product->closing ?: 0 }}</td>
                </tr>
                @if($details)             
                    @if($product->details->filter(function($detail) use ($type){ return $type == 'all' || ($type == 'in' && $detail['in'] > 0) || ($type == 'out' && $detail['out'] > 0); })->count() > 0)
                    <tr></tr>
                    <tr>
                        <td>
                            <table>
                                <thead>
                                    <tr>
                                        <th><i>Date</i></th>
                                        <th><i>Description</i></th>
                                        @if($type == 'all' || $type == 'in' ) <th><i>In</i></th> @endif
                                        @if($type == 'all' || $type == 'out' ) <th><i>Out</i></th> @endif
                                        @if($type == 'all') <th><i>Balance</i></th> @endif
                                    </tr>
                                </thead>

                                @foreach($product->details as $detail)
                                    @if($type == 'all' || ($type == 'in' && $detail['in'] > 0) || ($type == 'out' && $detail['out'] > 0))
                                    <tr>
                                        <td>{{ $detail['date']->toDateString() }}</td>
                                        <td>{{ $detail['description'] }}</td>
                                        @if($type == 'all' || $type == 'in' ) <td>{{ $detail['in'] }}</td> @endif
                                        @if($type == 'all' || $type == 'out' ) <td>{{ $detail['out'] }}</td> @endif
                                        @if($type == 'all') <td>{{ $detail['balance'] }}</td> @endif
                                    </tr>
                                    @endif
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td></td>
                        <td>
                            <i style="font-size: 10px">No transaction in the selected period</i>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>
</body>