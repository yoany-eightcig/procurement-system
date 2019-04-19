@include('sessions.table_header', [])

@php
	$option = ($action == "clearance3Search") ? "3" : "4" ;
@endphp

@include('sessions.datatable_js', ["option" => $option ])
