@extends('base')

@section("content")
	<table>
		<tr>
			<th>state</th>
			<th>title</th>
			<th>user</th>
			<th>date</th>
			<th>from</th>
			<th>to</th>
			<th>note</th>
			<th>action</th>
		</tr>
	@foreach($bookings as $one)
		<tr>
			<td class="{{$one->displayStatus()}}">
				{{$one->displayStatus()}}
			</td>
			<td>
				{{$one->title}}
			</td>
			<td>
				{{$one->myUser["username"]}}

			</td>
			<td>
				{{$one->from->format("Y-m-d")}}
			</td>
			<td>
				{{$one->from->format("H:i")}}
			</td>
			<td>
				{{$one->to->format("H:i")}}
			</td>
			<td>
				{{$one->note}}
			</td>
			<td>
				<a href="{{URL::action('BookingController@getEdit',$one->id)}}" class="edit">edit</a>
				|
				<a href="{{URL::action('BookingController@getDelete',$one->id)}}" class="cancel">cancel</a>
				|
				<a href="{{URL::action('BookingController@getCheckin',$one->id)}}" class="check-in">check in</a>
				|
				<a href="{{URL::action('BookingController@getExpire',$one->id)}}" class="expire">expire</a>
			</td>
		</tr>
	@endforeach

	</table>
@stop