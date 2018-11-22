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
        .item-table {
            width: 100%;
            table-layout: fixed;
            border-radius: 15px;
        }

        .item-table th {
            text-align: center;
            font-size: 14px;
        }
        .item-table th, td {
            border: 1px black solid;
        }
        .item-table td, .table td {
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
        .item-table, .table {
            border-collapse: collapse;
        }

        .table td:nth-child(1) {
            width: 50%;
        }

        .table td:nth-child(2) {
            width:;
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
        <h1>Goods Received</h1>
    </div>
    <div class="pull-right width-half">
        <p>GR No : <u>{{ $inbound->prefix() }}{{ sprintf("%05d", $inbound->id) }}</u></p>
        <p>Date : <u>{{ $inbound->arrival_date->toDateString() }}</u></p>
    </div>

    <div class="clear-both"></div>

    <div class="pull-left width-half">
        <span>Sender / Transporter / Courier</span>
        <div class="s-text-box"></div>
        <span class="pt-1">Customer / Customer Code</span>
        <div class="s-text-box">{{ $inbound->user->name }}</div>
        <p><h2>Received by :</h2></p>
    </div>

    <div class="width-half more-details">
        <div class="details width-half">
            <div class="details-header">
                <span class="fill-in-title">Total Ctn Received:</span>
                [<span class="fill-in-space"></span>]
            </div>
            <div class="details-content">
                <span><div class="checkbox"></div><label>Goods received in good condition.</label></span><br>
                <span><div class="checkbox"></div><label>if NOT, please remark:</label></span>
                ________________
                ________________
                ________________
                ________________<br>
                <span><div class="checkbox"></div><label>Photo</label></span>
                <span><div class="checkbox"></div><label>Informed Customer</label></span>
            </div>
        </div>
        <div class="details width-half">
            <div class="details-header">
                <span class="fill-in-title">Total CBM:</span>
                [<span class="fill-in-space"></span>]
            </div>
            <div class="details-content">
                <label style="padding-top: 10px;">Measurement Remarks:</label>
            </div>
        </div>
    </div>

    <div class="clear-both"></div>

    <table class="item-table">
         <thead>
            <tr>
                <th>No</th>
                <th>Item / Description</th>
                <th class="text-center">Qty</th>
                <th>Total Carton</th>
                <th>Total CBM</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inbound->products as $key => $product)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $product->selector_name }}</td>
                <td class="text-center">{{ $product->pivot->quantity }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach

            @for ($i = 0; $i <= 11 - $inbound->products->count(); $i++)
                <tr class="extra-rows">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="total pull-right">
        Total: _______________________________________________________
    </div>

    <div class="clear-both"></div>

    <div class="remarks pull-left">
        Remarks (if any):
    </div>

    <div class="signature pull-right">
        <h2>Checked by:</h2><br><br>
        <h2>Date:</h2>
    </div>

    <div class="clear-both"></div>

    <div class="end-bottom">
        For Account Department use only 
    </div>
</body>