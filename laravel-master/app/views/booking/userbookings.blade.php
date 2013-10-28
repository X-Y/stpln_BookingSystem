<div class="span4 user-bookings">
	<div class="inner">
		<h2>My Bookings</h2>
		<div class="filters">
			<a href="?scope=active" class="active">active</a>
			|
			<a href="?scope=all" class="all">all</a>
		</div>
		<ul class="no-list" id="bookings">
		@foreach($userbookings as $one)
		<li class="booking">
			<h3 class="title {{$one->displayStatus()}}"><a data-toggle="collapse" data-parent="#bookings" href="#booking-item{{$one->id}}">{{$one->from->format("Y-m-d")}} - {{$one->title}}</a></h3>
			<div class="details collapse" id="booking-item{{$one->id}}" >
				<div class="date-time">
					<div class="tr">
						<span class="td caption">from:</span><span class="td">{{$one->from->format("H:i")}}</span>
					</div>
					<div class="tr">
						<span class="td caption">to:</span><span class="td">{{$one->to->format("H:i")}}</span>
					</div>
				</div>
				<div class="note">{{$one->note}}</div>
				@if($one->status==1)
				<div class="controls">
					<a href="{{URL::action('BookingController@getEdit',$one->id)}}" class="edit">edit</a>
					|
					<a href="{{URL::action('BookingController@getDelete',$one->id)}}" class="cancel">cancel</a>
					@if(Helper::queryPermission(Auth::user()->id,"moderator"))
					|
					<a href="{{URL::action('BookingController@getCheckin',$one->id)}}" class="check-in">check in</a>
					|
					<a href="{{URL::action('BookingController@getExpire',$one->id)}}" class="expire">expire</a>
					@endif

				</div>
				@endif
			</div>
		</li>
		@endforeach
		</ul>
	</div>
</div>