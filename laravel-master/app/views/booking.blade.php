<div class="booking-form section">
<h2>Book a time</h2>
<form action="" method="post">
	{{Form::token()}}
	<div class="form-field {{isset($errors)&&$errors->first('title')?'error':''}}">
		<label><span>Title:</span><input type="text" name="title" value="{{isset($booking)?$booking['title']:''}}"/></label>
		@if(isset($errors))
			<span class="form-error">{{$errors->first("title")}}</span>
		@endif
	</div>
	<div class="form-field"><label class="form-field"><span>Date:</span><input type="text" name="date" class="date-picker" id="bookingForm-date" value="{{isset($booking)?$booking['date']:(new Datetime())->format('Y-m-d');}}"/></label></div>
	<div class="form-field"><label class="form-field"><span>From:</span><input type="text" name="from" class="time-picker" value="{{isset($booking)?$booking['from']:'00:00';}}"/></label></div>
	<div class="form-field"><label class="form-field"><span>To:</span><input type="text" name="to" class="time-picker" value="{{isset($booking)?$booking['to']:'00:00';}}"/></label></div>
	<div class="form-field"><label class="form-field"><span>Note:</span><textarea name="note">{{{isset($booking)?$booking['note']:''}}}</textarea></label></div>
	<div class="form-field"><input type="submit" value="book" class="btn-primary btn"/></div>
</form>
</div>
<div class="time-available section">
<h2>Time Available</h2>
<div id="time-available">
Choose a date...
</div>
</div>