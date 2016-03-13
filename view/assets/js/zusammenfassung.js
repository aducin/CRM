$( document ).ready(function() {
    $('#invidivuell_1').change(function() {
	debugger;
	if ($("#invidivuell_1").is(":checked")) {
	  $('#paymentOpt').prop('disabled', false);
	  // $('#paymentOpt').siblings('button').prop('disabled', false);
	  $('#paymentOpt').siblings('button').removeClass('disabled');
	} else {
	  $('#paymentOpt').prop('disabled', 'disabled');
	  // $('#paymentOpt').siblings('button').prop('disabled', 'disabled');
	  $('#paymentOpt').siblings('button').addClass('disabled');
	}
    });
});
