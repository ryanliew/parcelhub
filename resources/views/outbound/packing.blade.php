<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body, html {
            width: 100%;
            height: 100%;
        }
        .width-half {
            width: 50%;
        }
        .clear-both {
            clear: both;
        }
        .s-text-box, .md-text-box {
            padding: 10px;
            width: 300px;
            border: 1px black solid;
        }
        .s-text-box {
            height: 80px;
        }
        .md-text-box {
            height: 100px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .pull-left {
            float: left;
        }
        .pull-right {
            float: right;
        }
        .pt-1 {
            padding-top: 1em;
        }
        .pt-2 {
            padding-top: 2em;
        }
        .d-inline-block {
            display: inline-block;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .item-table th, td {
            border: 1px black solid;
        }
        .item-table td {
            padding: 5px;
        }
        .item-table th:nth-child(1) {
            width: 10%;
        }
        .item-table th:nth-child(3) {
            width: 15%;
        }
        .item-table th:nth-child(4) {
            width: 40%;
        }
    </style>
</head>
<body>
    <div class="pull-left">
        <h2>Parcel HUB</h2>
        <h4>Packing List</h4>
    </div>
    <div class="pull-right">
        <p>Ref No : <u>PHO-{{ $outbound->id }}</u></p>
        <p>Date : <u>{{ $outbound->created_at->toDateString() }}</u></p>
    </div>

    <div class="clear-both"></div>

    <div class="pull-left width-half">
        <p>Sender</p>
        <div class="md-text-box width-full">
            {{ $outbound->user->name }} <br>
            {{ $outbound->user->address }}, <br>
            @if( !empty( $outbound->user->address_2 ) )
                {{ $outbound->user->address_2 }}, <br>
            @endif
            {{ $outbound->user->postcode }}, {{ $outbound->user->state }}, {{ $outbound->user->country }} <br>
            <b>Tel:</b> {{ $outbound->user->phone }}
        </div>
    </div>

    <div class="clear-both"></div>

    <table class="item-table pt-2">
        <thead>
        <tr>
            <th>No</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($outbound->products->unique('id') as $key => $product)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $product->sku }} - {{ $product->name }}</td>
                <td class="text-center">{{ $outbound->getTotalProductQuantityAttribute($product->id) }}</td>
                <td>{{ $outbound->products[0]->pivot->remark }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3">
                    <ul>
                        @foreach ($outbound->getProductLocationInfoAttribute($product->id) as $info)
                            <li>{{ $info->name }} - Qty #{{ $info->quantity }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>