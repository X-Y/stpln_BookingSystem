@extends('base')

@section("title")
	Machine Booking System
@stop

@section('content')
	<div class="row-fluid">
		@include('booking/booking')
		@include('booking/userbookings')
		
	</div>
@stop