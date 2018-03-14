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
        .pt-3 {
            padding-top: 3em;
        }
        .pt-4 {
            padding-top: 4em;
        }
        .pt-5 {
            padding-top: 5em;
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
            width: 18%;
        }
        .item-table th:nth-child(4) {
            width: 20%;
        }
    </style>
</head>
<body>
    <div class="pull-left">
        <h2>Parcel HUB</h2>
    </div>
    <div class="pull-right width-half">
        <p>Ref No : ___________________</p>
        <p>Date : <u>{{ $inbound->created_at->toDateString() }}</u></p>
        <p>Arrival date : <u>{{ $inbound->arrival_date->toDateString() }}</u></p>
    </div>

    <div class="clear-both"></div>

    <div class="pull-left width-half">
        <p>Sender : {{ $inbound->user->name }}</p>
        <div class="md-text-box">
            {{ $inbound->user->address }}, <br>
            @if( !empty( $inbound->user->address_2 ) ) 
                {{ $inbound->user->address_2 }}, <br>
            @endif
            {{ $inbound->user->postcode }} {{ $inbound->user->state }}, {{ $inbound->user->country }} <br>
            <b>Tel</b>: {{ $inbound->user->phone }}
        </div>
    </div>

    <div class="clear-both"></div>

    <table class="pt-5 item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th class="text-center">Qty<br>(pcs/ ctn / dozen)</th>
                <th>Value (MYR)</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inbound->products as $key => $product)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $product->sku }}</td>
                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>