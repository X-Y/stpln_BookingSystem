@extends('base')

@section('content')
	#This is the first laravel page I created.
	@foreach($users as $user)
		<p>{{ $user->username }}</p>
	@endforeach

@stop