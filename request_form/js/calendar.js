var ferias = "<?php echo $ferias ?>";
var _events = [];
var _title, _start, _end;
    		
for (var i = 0; i < ferias.length; i++) {

	_title = ferias[i].colaborador  + " férias";
	_start = new Date(ferias[i].inicio).toISOString();
	_end = new Date(ferias[i].fim).toISOString();

	_events.push({
					title : _title, 
    				start : _start, 
    				end : _end
    			 });
}


$(function() {
	$('#calendar').fullCalendar({
		defaultView: 'month',
		weekends: false,

		events: _events
	})
});