<p3>{{$date->format("Y-m-d")}}</p3>
@foreach($periods as $period)
	<div>{{$period["from"]->format("H:i:s")}} to {{$period["to"]->format("H:i:s")}}</div>

@endforeach