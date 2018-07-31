function showCalendar(mapa) {

	var _events = [];
	var _title, _start, _end;
	    		
	for (var i = 0; i < mapa.length; i++) {

		_title = mapa[i].colaborador  + " férias";
		_start = mapa[i].inicio;
		_end = mapa[i].fim;

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
			locale: 'pt',
			timezone: false,

			events: _events
		})
	});
}