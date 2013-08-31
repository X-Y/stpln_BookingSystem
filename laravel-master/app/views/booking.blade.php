@extends("base")

@section("title")
	Machine Booking System
@stop

@section("content")
	<form action="" method="post">
		<input type="hidden" name="user" value="12345"/>
		<div><label>Title:<input type="text" name="title"/></label></div>
		<div><label>Date:<input type="text" name="date"/></label></div>
		<div><label>From:<input type="text" name="from"/></label></div>
		<div><label>To:<input type="text" name="to"/></label></div>
		<div><label>Note:<textarea name="note"></textarea></label></div>
		<div><input type="submit"/></div>
	</form>
@stop

	