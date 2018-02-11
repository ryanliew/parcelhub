<h1>Inbound {{$inbound->id}}</h1>
<table>
	<tr>
		<th>Product</th>
		<th>Quantity</th>
	</tr>
	@foreach($inbound->products as $product)
	<tr>
		<td>{{$product->name}}</td>
		<td>{{$product->pivot->quantity}}</td>
	</tr>
	@endforeach
</table>
<div>Arrival Date: {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $inbound->arrival_date)->formatLocalized('%d %B %Y')}}</div>
<div>
Total Carton: {{$inbound->total_carton}}
</div>
