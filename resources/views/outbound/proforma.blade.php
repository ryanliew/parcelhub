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
            width: 30%;
        }
        .item-table th:nth-child(2) {
            width: 5%;
        }
        .item-table th:nth-child(3) {
            width: 10%;
        }
        .item-table th:nth-child(4) {
            width: 10%;
        }
        .item-table th:nth-child(5) {
            width: 10%;
        }
        .item-table th:nth-child(6) {
            width: 20%;
        }
        .no-bottom-border {
            border-bottom: 0px !important;
        }
        .no-top-border {
            border-top: 0px !important;
        }
        .no-left-border {
            border-left: 0px !important;
        }
        .no-right-border {
            border-right: 0px !important;
        }
        .vertical-align-top {
            vertical-align: top;
        }
        .text-align-left {
            text-align: left;
        }
        .text-align-right {
            text-align: right;
        }
        .text-align-center {
            text-align: center;
        }
        .empty-row{
            height:20px;
        }
        .term-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .term-table td, th{
            border:none;
        }
        .business-table {
            width: 55%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .business-table td, th{
            border:none;
        }
        .business-table td:nth-child(1) {
            width: 4%;
        }
        .business-table td:nth-child(2) {
            width: 5%;
        }
        .business-table td:nth-child(3) {
            width: 5%;
        }
        .business-table td:nth-child(4) {
            width: 1%;
        }
        .business-table td:nth-child(5) {
            width: 5%;
        }
        #square {
            width:5px;
            height:5px;
            border: 1px solid #000;
        }
        .pull-left {
            float: left;
        }
        .barcode {
            padding-top: 25px;
            padding-left: 120px;
        }
        .width-quarter {
            width: 25%;
        }
    </style>
</head>
<body>
    <table class="term-table">
        <tr>
            <td>
            </td>
            <td>
                <div class="barcode">
                    {!! DNS1D::getBarcodeHTML( $outbound->PREFIX() . $outbound->id , "C128",2, 44,"black", true) !!}
                </div>
            </td>
        </tr>
    </table>
    <table class="item-table pt-2">
        <thead>
        <tr>
            <th class="no-right-border text-align-left"><h1>Proforma Invoice</h1></th>
            <th class="no-right-border no-left-border"> </th>
            <th class="no-right-border no-left-border""> </th>
            <th colspan="3" class="no-left-border vertical-align-top text-align-left">Air Way Bill Number:</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="vertical-align-top no-bottom-border">Sender: {{$auth->name}}</td>
                <td colspan="3">Date: {{$outbound->created_at->toDateString()}}</td>
            </tr>
            <tr>
                <td colspan="3" rowspan="3" class="no-top-border vertical-align-top">Address: {{$auth->address}}, @if($auth->address_2 !== null) <br> {{$auth->address_2}} @endif</td>
                <td colspan="3">Invoice Number: {{ $outbound->display_no }}</td>
            </tr>
            <tr>
                <td colspan="3">Shipment Ref: {{$courier->name}}</td>
            </tr>
            <tr>
                <td colspan="3" class="vertical-align-top no-bottom-border">Comment:</td>
            </tr>
            <tr>
                <td colspan="3"><span style="padding-right:85px">Phone: {{$auth->phone}}</span><span>Fax:</span></td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3" class="empty-row"></td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3" class="vertical-align-top no-bottom-border">Receiver: {{$outbound->recipient_name}}</td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3" class="no-top-border vertical-align-top">Address: {{$outbound->recipient_address}}</td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3" class="empty-row"></td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3" class="empty-row"></td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3" class="empty-row"></td>
                <td colspan="3" class="empty-row no-top-border no-bottom-border"></td>
            </tr>
            <tr>
                <td colspan="3"><span style="padding-right:85px">Phone: {{$outbound->recipient_phone}}</span><span>Fax:</span></td>
                <td colspan="3" class="empty-row no-top-border"></td>
            </tr>
        </tbody>
        <thead>
            <tr class="text-align-center">
                <th>Full Description of Goods</th>
                <th>Qty</th>
                <th>Unit Value</th>
                <th>Total Value</th>
                <th>Weight (KG)</th>
                <th>Country of Manufacturer</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outbound->products as $index => $product)
            <tr>
                <td>{{$index+1}}) {{$product->name}}</td>
                <td>{{$product->pivot->quantity}}</td>
                <td>{{$product->pivot->unit_value}}</td>
                <td>{{$product->pivot->total_value}}</td>
                <td>{{$product->pivot->weight}}</td>
                <td>{{$product->pivot->manufacture_country}}</td>
            </tr>
            @endforeach
            <tr>
                <td class="no-left-border no-bottom-border text-align-right">Total</td>
                <td>{{$totalQuantity}}</td>
                <td class="no-bottom-border"></td>
                <td class="text-align-center">Total Price</td>
                <td>{{$totalPrice}}</td>
                <td class="no-right-border no-bottom-border"></td>
            </tr>
        </tbody>
    </table>

    <table class="term-table" style="margin-top: 30px;">
        <tbody>
            <tr>
                <td>Payer of GST/VAT: {{$outbound->payer_gst_vat}}</td>
                <td>Currency: <b>USD</b></td>
            </tr>
            <tr>
                <td>HARM Comm Code: {{$outbound->harm_comm_code}}</td>
                <td>Term of Trade: {{$outbound->trade_term}}</td>
            </tr>
            <tr>
                <td>Term of Payment: {{$outbound->payment_term}}</td>
            </tr>
            <tr>
                <td>Reason For Export: {{$outbound->export_reason}}</td>
            </tr>
            <tr>
                @if($outbound->is_business === true)
                    <td>Business</td>
                @else
                    <td>Non-business</td>
                @endif
            </tr>
            {{-- <tr>
                <table class="business-table">
                    <tbody>
                        <tr>
                            <td>( Business</td>
                            <td><div id="square"></div></td>
                            <td>Non-business</td>
                            <td><div id="square"></div></td>
                            <td> )</td>
                        </tr>
                    </tbody>
                </table>
            </tr> --}}
        </tbody>
    </table>

    <div style="height:20px"></div>
    <table class="term-table">
        <tbody>
            <tr>
                <td>
                    <b>I/We</b> hereby certify that the information of this invoice is true and correct and
                    that the content of the shipment are as stated above.
                </td>
            </tr>
        </tbody>
    </table>

    <div style="height:35px"></div>

    <div>
        Sender's Signature: .................................
    </div>
</body>