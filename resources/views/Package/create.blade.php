@extends('app')

@section('styles')
<link href="{{ asset('/css/views/Package/create.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container-fluid text-center">
	<h1>Create New RIP Package</h1>
	<div class="row">
		@if (!Request::input('eprid'))
		<div class="col-md-4 col-md-offset-4">
			<p class="lead {{Request::input('eprid') ? 'text-muted' : ''}}">First, type in the Portfolio (EPR) ID.</p>
			<form action="{{url(Route::getCurrentRoute()->getPath())}}" method="GET">
				<div class="input-group">
					<input type="text" name="eprid" value="{{Request::input('eprid')}}" class="form-control" placeholder="EPR ID" autocomplete="Off">
					<span class="input-group-btn">
						<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</form>
		</div>
		@else
		<div class="col-md-8 col-md-offset-2">
			<p class="lead {{Request::input('eprid') ? '' : ''}}">Here's the data we've retrieved for EPR ID: <strong>{{Request::input('eprid')}}</strong>.</p>
			<table class="table table-striped table-bordered appInfo">
				<tr>
					<tr><td>Application Name</td><td>{{$appInfo->hp_ci_nm}}</td></tr>
					<tr><td>CI Logical Name</td><td>{{$appInfo->hp_leg_ci_lgcl_nm}}</td></tr>
					<tr><td>Description</td><td>{{$appInfo->description}}</td></tr>
					<tr><td>Status</td><td>{{$appInfo->hp_ci_stat_nm}}</td></tr>
					<tr><td>Criticality</td><td>{{$appInfo->hp_ci_crtclty_nm}}</td></tr>
					<tr><td>Data Classification</td><td>{{$appInfo->hp_info_confidentiality_nm}}</td></tr>
				</tr>
			</table>
			<br>
			<p class="lead {{Request::input('eprid') ? '' : ''}}">Select the instance(s) in scope for this package.</p>
			<form action="{{url(explode("/", Route::getCurrentRoute()->getPath())[0])}}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<?php $envs = ["dev", "itg", "test", "uat", "pro"]; ?>
				<div class="panel panel-default">
					<div class="panel-heading">

					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<label>Instances</label>
							</div>
							<div class="col-md-6">
								<label>Click here to create the package.</label>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								@foreach ($appInfo->appInstanceBeans as $i => $instance)
								<label class="checkbox-inline">
									<input type="checkbox" id="{{$instance->hp_app_instnc_prtfl_id}}" value="{{$instance->hp_leg_ci_lgcl_nm}}"> {{$instance->hp_leg_ci_lgcl_nm}}
								</label>
								@endforeach
							</div>
							<div class="col-md-6">
								<button class="btn btn-primary" type="submit">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	@endif
</div>
</div>

@endsection
