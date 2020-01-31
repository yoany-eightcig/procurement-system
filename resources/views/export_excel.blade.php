@php
	date_default_timezone_set('America/Los_Angeles');
	$date = date("Y-m-d");
	$time = date("hisa");

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=".$filename."-".$date."-".$time.".csv");
	// header("Pragma: no-cache"); 
	// header("Expires: 0");	

	echo "Sku,Name,Current Inventory,Unissued PO,On Order,Ave.,Max.,Dec.,Jan.,Feb.,Mar.,Apr.,May.,Jun.,Jul.,Aug.,Sept.,Oct.,Nov,Dec.".PHP_EOL;
@endphp
@foreach ($parts as $element)
	@include('sessions.fields2Export', [$element])
	@php
		echo PHP_EOL;
	@endphp
@endforeach
