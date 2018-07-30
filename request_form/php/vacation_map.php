<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>Mapa de férias</title>
        <meta charset="UTF-8">

        <link rel="stylesheet" href="../include/fullcalendar-3.9.0/fullcalendar.min.css" />
		<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="../include/fullcalendar-3.9.0/fullcalendar.min.js"></script>
		<script type="text/javascript" src="../include/fullcalendar-3.9.0/locale/pt.js"></script>

		<?php
    		require_once 'lib.php';  
            require_once 'setup.php';

            include_once 'header.php';

    		$ferias = json_encode(mapaFeriasOrganizacao($db));
    	?>

		<script>
    		var ferias = <?php echo $ferias ?>;
    		var _events = [];
    		var _title, _start, _end;
    		
    		for (var i = 0; i < ferias.length; i++) {

    			_title = ferias[i].colaborador  + " férias";
    			_start = ferias[i].inicio;
    			_end = ferias[i].fim;

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
    	</script>
    </head>

    <body>
    	<div id='calendar'></div>
    </body>