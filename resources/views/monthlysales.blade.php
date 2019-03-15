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
                    <div class="card-header">Parts</div>
                    @include('sessions.monthly', ['parts' => $parts])
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
