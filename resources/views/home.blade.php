@extends('tool')

@section('content')
<div class="container">
	<div class="row">
		<h1 class="page-header text-center">Day 1 IT SMO Re-Addressing Update Tool</h1>
		<?php $navs = [
            'server' => '<i class="fa fa-fw fa-database"></i> Update Servers',
            'epr-status' => '<i class="fa fa-fw fa-archive"></i> Update Apps',
        ] ?>
		@foreach ($navs as $url => $name)
		<div class="col-xs-6">
			<h3 class="text-center">
				<a href="{{ url($url) }}">{!! $name !!}</a>
			</h3>
		</div>
		@endforeach
	</div>
</div>
@endsection
