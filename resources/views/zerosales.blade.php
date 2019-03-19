@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
            @endif
        @endforeach
    </div>    
    <div class="row">
        <div class="col-sm-2">
            @include('sidemenu', [])
        </div>
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-monthlysales" role="tabpanel" aria-labelledby="v-pills-monthlysales-tab">
                    <div class="d-flex justify-content-between">
                        <div class="card-header">Parts</div>
                        @php
                            $exportOptions = false;
                            if (count($_GET)) {
                                if (array_key_exists('search', $_GET) && array_key_exists('filter_sku', $_GET) && array_key_exists('filter_name', $_GET)) {
                                    $exportOptions = "search:{$_GET['search']};filter_sku:{$_GET['filter_sku']};filter_name:{$_GET['filter_name']}";    
                                }
                            }
                        @endphp
                        <a href="{{ route('exportToExcel', ['zerosales', "exportOptions" => $exportOptions]) }}">[ Export To Excel]</a>
                    </div>
                    
                    @include('sessions.zerosales', ['parts' => $parts])
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
