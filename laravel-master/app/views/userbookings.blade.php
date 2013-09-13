<div class="user-bookings section">
	<h2>My Bookings</h2>
	<div class="filters">
		<a href="?scope=active" class="active">active</a>|
		<a href="?scope=all" class="all">all</a>
	</div>
	<ul class="bookings no-list">
	@foreach($userbookings as $one)
	<li class="booking">
		<h3 class="title">{{$one->title}}</h3>
		<div class="details" >
			<div class="date-time">
				{{$one->from->format("Y-m-d")}}<br/>
				from:{{$one->from->format("H:i")}}<br/>
				to:{{$one->to->format("H:i")}}
			</div>
			<div class="status">{{$one->displayStatus()}}</div>
			<div class="note">{{$one->note}}</div>
			@if($one->status==1)
			<div class="controls">
				<a href="/public/edit/{{$one->id}}" class="edit">edit</a>|
				<a href="/public/delete/{{$one->id}}" class="cancel">cancel</a>|
				<a href="/public/checkin/{{$one->id}}" class="check-in">check in</a>|
				<a href="/public/expire/{{$one->id}}" class="expire">expire</a>

			</div>
			@endif
		</div>
	</li>
	@endforeach
	</ul>
</div>