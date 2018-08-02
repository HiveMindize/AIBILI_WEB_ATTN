<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>Mapa de férias</title>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" />
		<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/pt.js"></script>
		<script type="text/javascript" src="../js/calendar.js"></script>

		<?php
    		require_once 'lib.php';  
            require_once 'setup.php';

            include_once 'header.php';

            require_once 'vendor/autoload.php';

            $db->query("START TRANSACTION;");

    		$ferias = json_encode(mapaFerias($db));
            $ausencias = json_encode(mapaAusencias($db));

            $holidays = Yasumi\Yasumi::create('Portugal', date("Y")); //recolhe feriados para o ano corrente

            $dates = json_encode($holidays->getHolidays());

    		$db->query("COMMIT;");
    	?>

		<script>
    		var mapa_ferias = <?php echo($ferias) ?>;
            var mapa_ausencias = <?php echo($ausencias) ?>;
    		var holidays = <?php echo($dates) ?>;

    		showCalendar(mapa_ferias, mapa_ausencias, holidays);
    	</script>
    </head>

    <body>
    	<div id='calendar'></div>
    </body>