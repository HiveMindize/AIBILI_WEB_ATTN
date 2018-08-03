$(function() {
  moment.locale('pt');
  $('input[name="datas"]').daterangepicker({
    timePicker: true,
    timePicker24Hour: true,
    locale: {
      format: 'YYYY-MM-DD H:mm'
    },

    isInvalidDate: function(date) {
  		return (date.day() == 0 || date.day() == 6); //fins de semana
	  }
  });
});

$(document).ready(function () {

    $('input[type="radio"]').click(function () {
        
        if ($(this).attr("value") === "ausencia") {
            $("#uploaddiv").show();
            $("#upload").attr("required", "");
        }

        if ($(this).attr("value") === "ferias") {
            $("#uploaddiv").hide();
            $("#upload").removeAttr("required");
        }
    });
});