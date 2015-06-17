@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>Packages</h1>
        <ul>
            @foreach ($packages as $package)
            <li><a href="{{url('package/' . $package->PackageID)}}">{{$package->PackageID}}: {{$package->PackageName}}</a></li>
            @endforeach
        </ul>
        </div>
    </div>
</div>
@endsection
