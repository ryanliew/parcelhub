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
        <p>Date : <u>{{ $outbound->created_at->toDateString() }}</u></p>
    </div>

    <div class="clear-both"></div>

    <div class="pull-left width-half">
        <p>Sender</p>
        <div class="md-text-box">
            {{ $outbound->user->name }}
        </div>
    </div>
    <div class="pull-right width-half">
        <p>Receiver</p>
        <div class="md-text-box">
            {{ $outbound->recipient_name }} <br>
            {{ $outbound->recipient_address }}, <br>
            {{ $outbound->recipient_address_2 }}, <br>
            {{ $outbound->recipient_postcode }}, {{ $outbound->recipient_state }}, {{ $outbound->recipient_country }} <br>
            {{ $outbound->recipient_phone }}
        </div>
    </div>

    <div class="clear-both"></div>

    <div class="pt-2 pull-left width-half">
        <div class="s-text-box">
            <p>Courier Service : <br>{{ $outbound->courier->name }}</p>
        </div>
    </div>
    <div class="pt-2 pull-right width-half">
        <div class="s-text-box">
            <span>Tracking Number : </span>
        </div>
        <div class="d-inline-block width-half pt-1">Actual Weight :</div>
        <div class="d-inline-block width-half pt-1">Chargeable Weight :</div>
    </div>

    <div class="clear-both"></div>

    <div class="pull-left" style="padding-left: 20%">
        <p>Insurances</p>
    </div>
    <div class="pull-right width-half">
        <p>
            <strong>MYR</strong> : {{ $outbound->amount_insured }}
        </p>
    </div>

    <div class="clear-both"></div>

    <table class="item-table">
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
            @foreach ($outbound->products as $key => $product)
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