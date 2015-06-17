@extends('app')

@section('styles')
	<link rel="stylesheet" href="{{ asset('/css/fullcalendar.min.css') }}" />
@endsection

@section('scripts')
	<script src="{{ asset('/js/moment.min.js') }}"></script>
	<script src="{{ asset('/js/fullcalendar.min.js') }}"></script>
	<script src="{{ asset('/js/views/Schedule/schedule.js') }}"></script>
@endsection

<?php $benches = ['Windows', 'Linux', 'HP-UX', 'MS SQL', 'Oracle', 'Apache','Tidal']; ?>
<?php $shifts = ['Morning', 'Day', 'Night']; ?>
<?php $locations = ['Houston', 'Austin']; ?>

@section('content')
<div class="container-fluid">
	<div class="text-center"><h1>RIP Implementation Schedule</h1></div>
	<div class="row">
		<div class="3">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="text" id="workWeekTextBox" placeholder="Jump to Work Week" >

			<select id="shiftSelect" name="shiftSelect">
				<option>Shift</option>
				@foreach($shifts as $i => $shift)
				<option value="{{$shift}}">{{$shift}}</option>
				@endforeach
			</select>
			<select id="locationSelect" name="locationSelect">
				<option>Location</option>
				@foreach($locations as $i => $location)
				<option value="{{$location}}">{{$location}}</option>
				@endforeach
			</select>
			<select id="benchSelect" name="benchSelect">
				<option>Bench</option>
				@foreach($benches as $i => $bench)
				<option value="{{$bench}}">{{$bench}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-md-9"></div>
	</div>
 	<div class="row">
		<div class="col-md-12">
			<div id="calendar"></div>
    	</div>
	</div>
</div>
@endsection
