<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			Laravel 4 - Bootstrap App
			@show
		</title>
		<meta name="keywords" content="your, awesome, keywords, here" />
		<meta name="author" content="Jon Doe" />
		<meta name="description" content="Lorem ipsum dolor sit amet, nihil fabulas et sea, nam posse menandri scripserit no, mei." />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- CSS
		================================================== -->
		<link href="{{{ asset('assets/css/bootstrap.css') }}}" rel="stylesheet">
		<link href="{{{ asset('assets/css/fabrica.css') }}}" rel="stylesheet">
		<link href="{{{ asset('assets/css/jquery-ui-1.10.3.custom.min.css') }}}" rel="stylesheet">

		<!-- OPENFONT -->		
		<link rel="stylesheet" media="screen" href="{{{ asset('assets/face/earthbound.css') }}}" rel="stylesheet" type="text/css"/>

		<style>
		@section('styles')
		@show
		</style>

		<link href="{{{ asset('assets/css/bootstrap-responsive.css') }}}" rel="stylesheet">
		
		<link href="{{{ asset('assets/css/style.css') }}}" rel="stylesheet">

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Favicons
		================================================== -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">
		<link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">
	</head>

	<body>
		<!-- Navbar -->
		@include('topBar')
		<!-- ./ navbar -->
		<header class="hero-unit">
			<h1>Machine booking</h1>
			<p>
				Book a time on {{Config::get('app.machineName')}}, {{Config::get('app.organization')}}. It's currently online.
			</p>
		</header>
		<!-- Container -->
		<div class="container main">
			<!-- Notifications -->
			@include('notifications')
			<!-- ./ notifications -->

			<!-- Content -->
			@yield('content')
			<!-- ./ content -->
		</div>
		<!-- ./ container -->

		<!-- Javascripts
		================================================== -->
		<script src="{{{ asset('assets/js/jquery.v1.8.3.min.js') }}}"></script>
		<script src="{{{ asset('assets/js/bootstrap/bootstrap.min.js') }}}"></script>
		<script src="{{{ asset('assets/js/bootstrap/transition.js') }}}"></script>
		<script src="{{{ asset('assets/js/bootstrap/collapse.js') }}}"></script>
		
		<script src="{{{ asset('assets/js/jquery-ui-1.10.3.custom.min.js') }}}"></script>
		<script src="{{{ asset('assets/js/jquery-ui-timepicker-addon.js') }}}"></script>
		
		@section("js")
		<script type="text/javascript" src="{{{ asset('assets/js/script.js') }}}"></script>
		@show
	</body>
</html>
