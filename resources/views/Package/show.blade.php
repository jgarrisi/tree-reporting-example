@extends('app')

<?php $appInfo = ["EPR ID", "Production CI", "Production CI Status", "Status", "...", "...", "..."] ?>
<?php $servers = ["Hostname", "IP Address", "Server Model", "OS", "...", "...", "..."] ?>
<?php $benches = ["Windows", "Linux", "HP-UX", "MS SQL", "Oracle", "Apache", "Tidal"] ?>
<?php $upstream = ["EPR - App Name", "EPR - App Name", "EPR - App Name"] ?>
<?php $downstream = ["EPR - App Name", "EPR - App Name", "EPR - App Name"] ?>

@section('scripts')
<script src="{{ asset('/js/views/Package/show.js') }}"></script>
@endsection

@section('content')
@if (isset($package))
<div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
			<div class="box">
				<h3>App Info</h3>
				<div class="box-body">
					<h4>{{$package->PackageName}}</h4>
					<ul class="panel-list appInfo">
						@foreach ($appInfo as $item)
						<li>{{ $item }}</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="box">
				<h3>Servers</h3>
				<div class="box-body">
					<select class="form-control" name="">
						<option>Server Name</option>
					</select>
					<ul class="panel-list servers">
						@foreach ($servers as $item)
						<li>{{ $item }}</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="box">
				<h3>Benches</h3>
				<div class="box-body">
					<ul class="panel-list appInfo">
						@foreach ($benches as $item)
						<li class="checkbox">
							<label>
								<input type="checkbox" value="{{ $item }}">
								{{ $item }}
							</label>
						</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="box">
				<h3>Dependencies</h3>
				<div class="box-body">
					<h5>Upstream</h5>
					<ul class="panel-list upstream">
						@foreach ($upstream as $item)
						<li>{{ $item }}</li>
						@endforeach
					</ul>
					<h5>Downstream</h5>
					<ul class="panel-list downstream">
						@foreach ($downstream as $item)
						<li>{{ $item }}</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="box">
				<h3>Schedule</h3>
				<div class="box-body">
					<label>Target Start:</label>
					<div class="input-group">
						<input type="text" name="eprid" value="{{Request::input('eprid')}}" class="form-control" placeholder="DD/MM/YY" autocomplete="Off">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-calendar"></i></button>
						</span>
					</div>
					<label>Target End:</label>
					<div class="input-group">
						<input type="text" name="eprid" value="{{Request::input('eprid')}}" class="form-control" placeholder="DD/MM/YY" autocomplete="Off">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-calendar"></i></button>
						</span>
					</div>
					<label>Actual Start:</label>
					<div class="input-group">
						<input type="text" name="eprid" value="{{Request::input('eprid')}}" class="form-control" placeholder="DD/MM/YY" autocomplete="Off">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-calendar"></i></button>
						</span>
					</div>
					<label>Actual End:</label>
					<div class="input-group">
						<input type="text" name="eprid" value="{{Request::input('eprid')}}" class="form-control" placeholder="DD/MM/YY" autocomplete="Off">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-calendar"></i></button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-4">
			<div class="box">
				<h3>Attachments</h3>
				<div class="box-body">
					<form action="http://c0007700.itcs.hp.com:8080/ITSMOReIPServices/services/file/upload" method="post">
						<div class="input-group">
							<input type="file" name="file" class="form-control">
							<div class="input-group-btn">
								<button id="uploadBtn" class="btn btn-primary"><i class="fa fa-upload"></i> Upload</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="box">
				<h3>Comments</h3>
				<div class="box-body">
					<textarea rows="8" class="form-control"></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="box">
				<h3>Issues</h3>
				<div class="box-body">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>
									ID
								</th>
								<th>
									Category
								</th>
								<th>
									Description
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									22
								</td>
								<td>
									IP Block
								</td>
								<td>
									Missing Address
								</td>
							</tr>
								<tr>
									<td>
										23
									</td>
									<td>
										Hardware
									</td>
									<td>
										Disk failure
									</td>
								</tr>
									<tr>
										<td>
											76
										</td>
										<td>
											Config
										</td>
										<td>
											GSLB settings
										</td>
									</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
@endsection
