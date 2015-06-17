<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>{{ Config::get('app.name') }}</title>

        <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
        <!-- view-specific styles go in styles section of the view -->
        @yield('styles')

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    	<![endif]-->
    </head>

    <body>
        <!-- nav section -->
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{url('/')}}">
                        <img src="{{asset('/img/logo-white.png')}}" alt="logo">
                        <span>{{ Config::get('app.name') }}</span>
                    </a>
                </div>

                <?php $navs=[ '/'=> '<i class="fa fa-fw fa-home"></i> Home', 'package' => '<i class="fa fa-fw fa-list"></i> View Packages', 'package/create' => '<i class="fa fa-fw fa-plus-square"></i> Create Package', 'schedule' => '<i class="fa fa-clock-o"></i> View Schedule'
                ] ?>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        @foreach ($navs as $url => $name)
                        <li class="{{Request::is($url) ? 'active' : ''}}"><a href="{{ url($url) }}">{!! $name !!}</a></li>
                        @endforeach
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        @if (Auth::guest())
                        <li><a href="#">User Name <i class="fa fa-fw fa-user"></i></a></li>
                        @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <!-- errors section -->
        @if($errors)
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert"><i class="fa fa-warning"></i> {{$error}}</div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <!-- content section -->
        @yield('content')

        <!-- Scripts -->
        <script src="{{ asset('/js/jquery-2.1.3.min.js') }}"></script>
        <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
        @yield('scripts')
    </body>

</html>
