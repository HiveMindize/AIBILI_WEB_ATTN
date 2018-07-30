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