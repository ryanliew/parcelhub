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
            height: 25px;
            margin-bottom: 10px;
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

        .item-table th {
            text-align: center;
            font-size: 12px;
        }

        .item-table td {
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

        .checkbox {
            width: 10px;
            height: 10px;
            display:inline-block;
            border: 1px solid black;
        }

        ul {
            margin: 0;
        }


        .total {
            margin-top: 25px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .footer-table {
            border: 2px solid black;
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
        }

        .footer-table td{
            vertical-align: top;
        }

        .footer-table td:nth-child(1) {
            width: 15%;
        }

        .footer-table td:nth-child(3) {
            width: 30%;
        }

        .td-checkbox {
            width: 10px;
            max-width: 10px;
            display: table-cell;
        }

        .checkable td:nth-child(1) {
            width: 10px;
        }

        .fill-in-space {
            width: 50px;
            display: inline-block;
        }

        .box {
            border: 1px solid black;
            font-weight: bold;
            width: 17px;
            height: 17px;
            text-align:center;
            display: inline-block;
            margin-top: 3px;
            margin-bottom: 5px;
        }

        .end-bottom {
            border-top: 3px solid black;
            margin-top: 15px;
            font-style: italic;
            font-size: 10px;
            display: block;
        }

        .extra-rows td {
            height: 23px;
        }

        .text-center {
            text-align: center;
        }

        .barcode > div {
            margin: 0 auto;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="pull-left">
        <h1 style="margin-bottom: 10px;">Packing List</h1>
        @if($outbound->insurance > 0) &#9745; @endif <span><b style="padding-left: 5px;">Insurance MYR: </b> <u>{{ $outbound->insurance }}</u></span>
    </div>
    <div class="pull-right width-half">
        <div class="barcode text-center">
            {!! DNS1D::getBarcodeHTML( $outbound->PREFIX() . $outbound->id , "C128",2, 44,"black", true) !!}
            <span>{{ $outbound->PREFIX() . sprintf("%05d", $outbound->id) }}</span>
        </div>
        <p>Date : <u>{{ $outbound->created_at->toDateString() }}</u></p>
    </div>

    <div class="clear-both"></div>

    <div class="pull-left width-half">
        <p>Sender <i>(Customer Code)</i></p>
        <div class="s-text-box width-full">
            {{ $outbound->user->name }} 
        </div>
        <div class="s-text-box width-full" style="height: 40px;">
            <i style="13px">Courier & Tracking:</i><br>
            {{ $outbound->courier->name }}
        </div>
    </div>

    <div class="pull-right width-half">
        <p>Receiver :</p>
        <div class="md-text-box">
            {{ $outbound->recipient_name }} <br>
            {{ $outbound->recipient_address }}, <br>
            @if( !empty( $outbound->recipient_address_2) )
                {{ $outbound->recipient_address_2 }}, <br>
            @endif
            {{ $outbound->recipient_postcode }}, {{ $outbound->recipient_state }}, {{ $outbound->recipient_country }} <br>
            <b>Tel:</b> {{ $outbound->recipient_phone }}
        </div>
        <div class="checkbox"></div> <span style="font-size: 13px;">If <b>Single Item Multiple receiver</b>, refer attachment</span>
    </div>

    <div class="clear-both"></div>

    <table class="item-table pt-2">
        <thead>
        <tr>
            <th>No</th>
            <th>Item / Description</th>
            <th>Qty<br>(pcs / ctn / dozen)</th>
            <th>Total<br>Carton</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($outbound->products->unique('id') as $key => $product)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $product->selector_name }}</td>
                <td class="text-center">{{ $outbound->getTotalProductQuantityAttribute($product->id) }}</td>
                <td></td>
                <td>{{ $product->pivot->remark }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="4">
                    <ul>
                        @foreach ($outbound->getProductLocationInfoAttribute($product->id) as $info)
                            <li>{{ $info->name }} - Qty #{{ $info->quantity }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach
        @for ($i = 0; $i <= 5 - $outbound->products->count(); $i++)
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
    @if($outbound->products->count() > 6 && $outbound->products->count() < 9)
        <div class="page-break"></div>
    @endif
    <table class="footer-table">
        <tbody>
            <tr>
                <td rowspan="2">
                    Picked by:
                </td>
                <td colspan="4">
                    <b>Packing Material</b>
                </td>
                <td>
                    <b>Parcel Information</b>
                </td>
            </tr>
            <tr class="checkable">
                <td style="width: 5%;" class="td-checkbox"></td>
                <td style="width: 30%">Flyers [<span class="fill-in-space"></span>]</td>
                <td style="width: 5%;" class="td-checkbox"></td>
                <td style="width: 30%">Others [<span class="fill-in-space"></span>]</td>
                <td rowspan="4">Actual Weight: [<span class="fill-in-space"></span>]kg<br>Chargeable:<span style="padding-left:25px;
                ">[</span><span class="fill-in-space"></span>]kg</td>
            </tr>
            <tr class="checkable">
                <td rowspan="3">
                    Packed by:
                </td>
                <td class="td-checkbox"></td>
                <td>BB Wrap [ S / M / L ]</td>
                <td rowspan="3" colspan="2"></td>
            </tr>
            <tr class="checkable">
                <td class="td-checkbox"></td>
                <td>S.Film [ S / M / L ]</td>
            </tr>
            <tr class="checkable">
                <td class="td-checkbox"></td>
                <td>C.Box <span class="box">1</span> <span class="box">2</span> <span class="box">3</span> <span class="box">4</span> <span class="box">5</span> <span class="box">6</span>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="clear-both"></div>

    <div class="end-bottom">
        For Account Department use only 
    </div>
</body>