@extends('base')

@section("title")
	Users Management
@stop


@section('content')
	<table>
		<tr>
			<th>username</th>
			<th>email</th>
			<th>phone number</th>
			<th>credit</th>
			<th>permission</th>
		</tr>
	@foreach($users as $one)
		<tr>
			<td>{{$one->username}}</td>
			<td>{{$one->email}}</td>
			<td>{{$one["phone number"]}}</td>
			<td>
				<form action="{{URL::action('UserController@update',array('id'=>$one->id,'field'=>'credits'))}}" method="POST">
					{{Form::token()}}
					<input type="text" name="credits" class="quick-edit small-number" value="{{$one->credits}}"/>
				</form>
			</td>
			<td>
				<form action="{{URL::action('UserController@update',array('id'=>$one->id,'field'=>'role'))}}" method="POST">
					<input type="text" name="role" class="quick-edit small-number" value="{{$one->role}}"/>
				</form>
			</td>
		</tr>
	@endforeach
	</table>
@stop