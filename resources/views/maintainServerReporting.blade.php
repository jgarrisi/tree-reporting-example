@extends('tool')

@section('scripts')
<script src="{{ asset('/js/views/ServerReporting/maintainServerReporting.js') }}"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="text-center page-header"><h1>Server Status Update Tool</h1></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="row" id="inputRow">
                <div class="col-xs-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="input-group">
                        <input type="text" id="textEPRID" name="textEPRID" class="form-control" placeholder="EPR ID">
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="input-group">
                        <select type="text" id="selEnv" name="selEnv" class="form-control" placeholder="Environment">
                            <option value="">Environment</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-1">
                    <div class="btn btn-default" id="loadBtn">Load Servers</div>
                </div>
            </div>
        </div>
        <div class="col-xs-5"></div>
        <div class="col-xs-1">
            <div class="btn btn-primary" id="saveButton">Save</div>
        </div>
    </div>
    <hr>
    <div class="row" id="outputTable">
        <div class="col-xs-12">
            <table class="table" id="serverTable">
                <thead>
                    <th>Server</th>
                    <th>RE-IP Planned Date</th>
                    <th>RE-IP Completion Date</th>
                    <th>RE-IP Design Complete Date</th>
                    <th>Status</th>
                </thead>
                <tbody> </tbody>
            </table>
        </div>
    </div>
    <div class="row" id="messagesDiv"> </div>
    <div class="row text-center page footer">
        <a href="maintainServerReporting/export">Download the entire dataset to CSV.</a>
        {{--<p>Unsaved rows = <span id="updateServerList"></span></p>--}}
    </div>
</div>
@endsection
