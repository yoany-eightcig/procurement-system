@php
	date_default_timezone_set('America/Los_Angeles');
	
	$date = date("Y-m-d");
	$time = date("hisa");

	header("Content-Type: application/octet-stream");    
	header("Content-Disposition: attachment; filename=".$filename."-".$date."-".$time.".xls");
	header("Pragma: no-cache"); 
	header("Expires: 0");	
@endphp
Sku,Name,Current Inventory,Unissued PO,On Order,Ave.,Max.,Dec.,Jan.,Feb.,Mar.,Apr.,May.,Jun.,Jul.,Aug.,Sept.,Oct.,Nov.
@foreach ($parts as $element)
	@include('sessions.fields2Export', [$element])
	@php
		echo "\r";
	@endphp
@endforeach