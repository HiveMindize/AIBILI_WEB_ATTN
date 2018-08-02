$(function() {
  moment.locale('pt');
  $('input[name="datas"]').daterangepicker({
    timePicker: true,
    timePicker24Hour: true,
    locale: {
      format: 'YYYY-MM-DD H:mm'
    },

    isInvalidDate: function(date) {
  		return (date.day() == 0 || date.day() == 6);
	  }
  });
});

$(document).ready(function () {

    $('input[type="radio"]').click(function () {
        
        if ($(this).attr("value") === "ausencia") {
            $("#upload").show();
            $("#upload").attr("required") = true;
        }

        if ($(this).attr("value") === "ferias") {
            $("#upload").hide();
            $("#upload").attr("required") = true;
        }
    });
});