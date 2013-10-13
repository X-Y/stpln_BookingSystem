<div class="span8 booking-form">
	<div class="inner">
		<h2>
		@if(!isset($booking))
			Book a time
		@else
			Edit booking
		@endif	
		</h2>
		
		<p>
			<strong>Note:</strong> After you pick the desired date, you can see which time of the day is available. Make your bookings within the available periods! 
		</p>
		<p>
			You can't make changes to bookings that are about to happen. No new bookings, or edit/delete for bookings happening within 3 hours.
		</p>
		
		<hr>
		
		<form action="" method="post">
			{{Form::token()}}
			<div class="form-field {{isset($errors)&&$errors->first('title')?'error':''}}">
				<label><span>Title:</span><input type="text" name="title" value="{{isset($booking)?$booking['title']:''}}"/></label>
				@if(isset($errors))
					<span class="form-error">{{$errors->first("title")}}</span>
				@endif
			</div>
			<div class="form-field"><label class="form-field"><span>Date:</span><input type="text" name="date" class="date-picker" id="bookingForm-date" value="{{isset($booking)?$booking['date']:(new Datetime())->format('Y-m-d');}}"/></label></div>
			<div class="time-available alert alert-info">
				<h4>Time Available</h4>
				<div id="time-available">
					Choose a date...
				</div>
			</div>

			<div class="form-field"><label class="form-field"><span>From:</span><input type="text" name="from" class="time-picker" value="{{isset($booking)?$booking['from']:'00:00';}}"/></label></div>
			<div class="form-field"><label class="form-field"><span>To:</span><input type="text" name="to" class="time-picker" value="{{isset($booking)?$booking['to']:'00:00';}}"/></label></div>
			<div class="form-field"><label class="form-field"><span>Note:</span><textarea name="note">{{{isset($booking)?$booking['note']:''}}}</textarea></label></div>
			<div class="form-field"><input type="submit" value="book" class="btn-primary btn"/></div>
		</form>
	</div>
</div>

<!--

-->