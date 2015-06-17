@extends('report')

@section('styles')
<link href="{{ asset('/css/views/Tree/tree.css') }}" rel="stylesheet">
<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css" rel="stylesheet" />


@endsection

@section('scripts')

<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.js"></script>
<script src="{{ asset('/js/views/Tree/tree.js') }}"></script>
@endsection



@section('content')
<div class="container-fluid">
  <div class="row title">
    <div class="col-xs-12">
      <div class="text-center"><h3><span id='APPtitle'></span> Status Reporting Tree</span></h3></div>
    </div>
  </div>
  <div class="row filter">
    <div class="col-md-4">
      <label>Application:&nbsp</label>
      <select type="text" id="selAPPID" name="selAPPID" class="form-control" style="width:100%">
        <option value="">-Select-</option>
      </select>
    </div>
  </div>
  <div class="row content">
    <div class="col-md-9">
      <div id="graph" class="svg-container clearfix"></div>
    </div>
    <div class="col-md-3">
      <div id='summary-box'>
      </div>
    </div>
  </div>
</div>
@endsection
