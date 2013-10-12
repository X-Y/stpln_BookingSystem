var BookingSystem=(function($,me){
	var baseURL="http://localhost/public/bookings";
	
	var availableURL="/available/";
	
	function init(){
		initTimeAvailable();
		initWidgets();
	};
	me.init=init;
	
	function initWidgets(){
		$(".date-picker").datepicker({
			dateFormat:"yy-mm-dd",
		});
		$(".time-picker").timepicker({
			stepMinute: 15,
		});
	}
	function initTimeAvailable(){
		
		$("#bookingForm-date").on("change", function(){
			$("#time-available").html("Loading...");
			var date=$(this).val();
			requestTimeAvailable(date);
		});
		$("#bookingForm-date").trigger("change");
	};
	function requestTimeAvailable(date){
		$.ajax({
			url:baseURL+availableURL+date,
			
		}).done(function(data){
			$("#time-available").html(data);
		});
	};
	return me;
}(jQuery, BookingSystem || {}));

$(document).ready(function(){
	BookingSystem.init();
});