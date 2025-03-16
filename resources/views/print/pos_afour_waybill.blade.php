<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Invoice #{{ $order->id }}</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 9pt;
            background-color: #fff;
        }

        #products {
            width: 90%;
        }
        #products th, #products td {
            padding-top:5px;
            padding-bottom:5px;
            border: 1px solid black;
        }
        #products tr td {
            font-size: 8pt;
        }

        #printbox {
            width: 98%;
            margin: 5pt;
            padding: 5px;
            margin: 0px auto;
            text-align: justify;
        }

        .inv_info tr td {
            padding-right: 10pt;
        }

        .product_row {
            margin: 15pt;
        }

        .stamp {
            margin: 5pt;
            padding: 3pt;
            border: 3pt solid #111;
            text-align: center;
            font-size: 20pt;
            color:#000;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $store = \App\Store::find(1);
@endphp
<div id="printbox">
    <table width="100%">
        <tr><td valign="top" width="50%">
                <h2 style="margin-top:0" class="text-center">{{ $store->name}}</h2>
                <p align="left" >
                    {{ $store->first_address }}
                    @if(!empty($store->second_address))
                        <br/>
                        {{ $store->second_address }}
                    @endif
                    @if(!empty($store->contact_number))
                        <br/>
                        {{ $store->contact_number }}
                    @endif
                </p>
            </td>
            <td valign="top" align="right" width="50%">
                <img style="max-height:100px;float: right;margin-top: -20px;" src="{{ public_path("img/". $store->logo) }}" alt='Logo'>
            </td>
        </tr>
    </table>
    <br/>
    <table width="100%" style="margin-top: -45px;">
        <tr>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <th align="left" width="50%">Invoice Number</th>
                        <td align="right" width="50%">{{ $order->invoice_no }}</td>
                    </tr>
                    <tr>
                        <th align="left" width="50%">Invoice Date</th>
                        <td align="right" width="50%">{{ convert_date($order->order_date) }}</td>
                    </tr>
                <!--
                    <tr>
                        <th align="left" width="50%">Time</th>
                        <td align="right">{{ date("h:i a",strtotime($order->created)) }}</td>
                    </tr>
                    -->
                    <tr>
                        <th align="left" width="50%">Customer</th>
                        <td align="right" width="50%">{{ $order->user->shop_name }}</td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td>
            <td width="40%"></td>
        </tr>
    </table>
    <br/>
    <table width="100%">
        <tr><td valign="top" width="50%">
                <h2 style="margin-top:0" class="text-center">Billing Address</h2>
                <p align="left" >
                    {{ $store->first_address }}
                    @if(!empty($store->second_address))
                        <br/>
                        {{ $store->second_address }}
                    @endif
                    @if(!empty($store->contact_number))
                        <br/>
                        {{ $store->contact_number }}
                    @endif
                </p>
            </td>
            <td valign="top" align="right" width="50%">
                <h2 style="margin-top:0" class="text-center">Delivery Address</h2>
                <p align="right" >
                    {{ $store->first_address }}
                    @if(!empty($store->second_address))
                        <br/>
                        {{ $store->second_address }}
                    @endif
                    @if(!empty($store->contact_number))
                        <br/>
                        {{ $store->contact_number }}
                    @endif
                </p>
            </td>
        </tr>
    </table>
    <br/>
    <table width="100%">
        <tr><td valign="top" width="50%">
                <h2 style="margin-top:0" class="text-center">Payment Method</h2>
                <p>&nbsp;</p>
                <p align="left">{{ $order->paymentMethod->name }}</p>
            </td>
            <td valign="top" align="right" width="50%">
                <h2 style="margin-top:0" class="text-center">Delivery Method</h2>
                <p>&nbsp;</p>
                <p><b>{{ $order->shippingMethod->name }}</b></p>
                <p>&nbsp;</p>
                <p align="right">
                    @php
                        $orders = $order->toArray();
                        $s = strtolower($orders['checkout_dataparse']['delivery_method']['delivery_method']);
                        if(isset($orders['checkout_dataparse']['delivery_method'][$s])){
                            $details = $orders['checkout_dataparse']['delivery_method'][$s];
                        }
                    @endphp
                    @if(isset($details['name']))
                        {{ $details['name'] }} - N{{ number_format($details['amount'],2) }}
                    @endif
                </p>
            </td>
        </tr>
    </table>
    <br/>
    @if($order->no_of_cartons != NULL)
        <h5 style="color: red">Note : Total Number of Cartons Packed : {{ $order->no_of_cartons }}</h5>
        <br/>
    @endif
    <h2 style="margin-top:0" class="text-center">Order WayBill</h2>
    <table id="products" style="width: 100%;">
        <tr class="product_row">
            <td  width="5%" align="center">#</td>
            <td align="left" width="50%"><b>Name</b></td>
            <td align="center"  width="5%"><b>Qty</b></td>
        </tr>
        <tbody id="appender">
        @php
            $num = 1;
        @endphp
        @foreach($order->orderProducts()->get() as $item)
            <tr>
                <td align="center">{{ $num }}</td>
                <td align="left" class="text-left">{{ $item->name }}</td>
                <td align="center" class="text-center">{{ $item->quantity }}</td>
            </tr>
            @php
                $num++;
            @endphp
        @endforeach
        </tbody>
    </table>
    <br/>
    <div align="center">
        <img src="data:image/png;base64,' . {{ DNS1D::getBarcodePNG($order->invoice_no, 'C39+',2,50) }} . '" alt="barcode"   />
    </div>
    <br/>
    <div class="text-center"> {!! softwareStampWithDate() !!}</div>
</div>
</body>
</html>

