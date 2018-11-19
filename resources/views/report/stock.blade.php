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
            width: 48%;
            padding: 0 1%;
        }
        .clear-both {
            clear: both;
        }

        .text-box {
            border: 1px solid black;
        }

        .fill-in-title {
            max-width: 50px;
            font-size: 10px;
            display: inline-block;
            line-height: 10px;
        }
        .fill-in-space {
            width: 70px;
            display: inline-block;
        }

        label {
            font-size: 10px;
            padding-bottom: 0px;
            display: inline-block;
            padding-left: 3px;
            padding-top: 3px;
        }

        .s-text-box, .md-text-box {
            padding: 10px 5%;
            width: 80%;
            border: 1px black solid;
        }
        .s-text-box {
            height: 18px;
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
        .checkbox {
            width: 5px;
            height: 5px;
            display:inline-block;
            border: 1px solid black;
        }
        .pt-1 {
            padding-top: 1em;
            display: block;
        }
        .pt-2 {
            padding-top: 2em;
            display: block;
        }
        .pt-3 {
            padding-top: 3em;
            display: block;
        }
        .pt-4 {
            padding-top: 4em;
            display: block;
        }
        .pt-5 {
            padding-top: 5em;
            display: block;
        }
        .d-inline-block {
            display: inline-block;
        }
        .item-table, .subitem-table {
            width: 100%;
            table-layout: fixed;
            border-radius: 15px;
        }

        .subitem-table th, .item-table th  {
            text-align: center;
            font-size: 14px;
        }
        .subitem-table th, td, .item-table th, {
            border: 1px black solid;
        }
        .subitem-table td, .table td, .item-table td, .table td  {
            padding: 5px;
        }
        .item-table th:nth-child(1) {
            width: 5%;
        }
        .item-table th:nth-child(3) {
            width: 9%;
        }
        .item-table th:nth-child(4) {
            width: 10%;
        }

        .subitem-table th:nth-child(1) {
            width: 15%;
        }
        .subitem-table th:nth-child(2) {
            width: 50%;
        }
        .subitem-table th:nth-child(3) {
            width: unset;
        }
        .subitem-table th:nth-child(4) {
            width: unset;
        }
        .subitem-table th:nth-child(5) {
            width: unset;
        }
        .subitem-table, .item-table, .table {
            border-collapse: collapse;
        }

        .table td:nth-child(1) {
            width: 50%;
        }

        .more-details {
            position: absolute;
            left: 0;
        }

        .details {
            border: 1px solid black;
            float: left;
            padding: 0;
            display:inline-block;
        }

        .details-header {
            border-bottom: 1px solid black;
            padding: 0 4%;
            font-size: x-large;
        }

        .details-content {
            padding: 0 4%;
            height: 140px;
        }

        .extra-rows td {
            height: 23px;
        }

        .total {
            margin-top: 25px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .remarks {
            width: 70%;
            border: 1px solid black;
            height: 170px;
            display: block;
            margin-bottom: 20px;
        }

        .signature {
            width: 28%;
            margin-left: 2%;
        }

        .end-bottom {
            border-top: 3px solid black;
            margin-top: 15px;
            font-style: italic;
            font-size: 10px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="pull-left width-half">
        <h1>Parcelhub stock report</h1>
        <h3 style="margin-top: -15px;">@if($type == 'all') Balance report @elseif($type == 'in') Inbounds only @else Outbounds only @endif </h3>
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
                <th>Item</th>
                <th class="text-center">Opening Qty</th>
                <th>Closing Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $product->sku }} - {{ $product->name }}</td>
                    <td>{{ $product->opening ?: 0 }}</td>
                    <td>{{ $product->closing ?: 0 }}</td>
                </tr>
                @if($details)
                    <tr>
                        <td colspan="4">
                            @if($product->details->filter(function($detail) use ($type){ return $type == 'all' || ($type == 'in' && $detail['in'] > 0) || ($type == 'out' && $detail['out'] > 0); })->count() > 0)
                                <table class="subitem-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            @if($type == 'all' || $type == 'in' ) <th>In</th> @endif
                                            @if($type == 'all' || $type == 'out' ) <th>Out</th> @endif
                                            @if($type == 'all') <th>Balance</th> @endif
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
                            @else
                                No transaction in the selected period
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>