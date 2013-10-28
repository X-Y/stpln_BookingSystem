var BookingSystem=(function($,me){

	var config={
		baseURL:"/laravel-master/public/bookings",
		availableURL:"/available/"
	};
	
	function init(config){
		$.extend(config,this.config);
		initTimeAvailable();
		initWidgets();
		initQuickEdit();
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
			url:config.baseURL+config.availableURL+date,
			
		}).done(function(data){
			$("#time-available").html(data);
		});
	};
	
	function initQuickEdit(){
		$(".quick-edit").on("focus",function(){
			var me=$(this);
			me.data("oldVal",me.val());
		});
		$(".quick-edit").on("blur",function(){
			var me=$(this);
			var myForm=me.parents("form");
			if(me.val()!=me.data("oldVal")){
				$.ajax({
					type:"POST",
					url:myForm.attr("action"),
					data:myForm.serialize(),
				});
			}
		});
	}
	return me;
}(jQuery, BookingSystem || {}));

