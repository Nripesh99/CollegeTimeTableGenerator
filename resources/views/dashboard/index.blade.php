@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('content')
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
            <h1><span class="fa fa-dashboard"></span> Dashboard</h1>
        </div>
    </div>

    <div class="page-body menubar">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row cards-container">
                    <?php $count = 1; ?>
                    @foreach ($data['cards'] as $index => $card)
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <!-- Use AdminLTE 3 small-box component dynamically -->
                        <div class="small-box bg-{{ ['info', 'success', 'warning', 'danger'][$index % 4] }}">
                            <div class="inner">
                                <h3>{{ $card['value'] }}</h3>
                                <p>{{ $card['title'] }}</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-{{ $card['icon'] }}"></i>
                            </div>
                            <!-- Make the link dynamic -->
                            <a href="{{$card['route']}}" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 50px">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                <button class="btn-primary timetable-btn btn-block" id="resource-add-button"><i class="fa fa-calendar"></i> Generate New Timetables</button>
            </div>
        </div>
    </div>

    <div id="resource-container">
        @include('dashboard.timetables')
    </div>
</div>
@include('dashboard.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/dashboard/index.js')}}"></script>
@endsection