$( document ).ready(function() {
    $('#invidivuell_1').change(function() {
	if ($("#invidivuell_1").is(":checked")) {
	  $('#paymentOpt').prop('disabled', false);
	  $('#paymentOpt').siblings('button').removeClass('disabled');
	} else {
	  $('#paymentOpt').prop('disabled', 'disabled');
	  $('#paymentOpt').siblings('button').addClass('disabled');
	}
    });
    $('#invidivuell_2').change(function() {
	if ($("#invidivuell_2").is(":checked")) {
	  $('#skonto').prop('disabled', false);
	} else {
	  $('#skonto').prop('disabled', 'disabled');
	} 
    });
    $( "#auftraggeberId" ).keyup(function() {
	var clientName = $( "#auftraggeberId" ).val();
	if (window.location.href == "http://ad9bis.vot.pl/CRM/Erfassung") {
	  var path = "Api/Project/";
	} else {
	  var path = "../Api/Project/";
	}
	$.ajax({url: path + clientName, 
               type: "get",
               success: function(result)
                    {
                        var jsonData = JSON.parse(result);
                        switch(jsonData){
                            case 'error':
                            $( '#auftraggeberDiv' ).removeClass('form-group').addClass('form-group has-error');
                            $('#ajaxMessage').html('Nichts gefunden');
                            break;

                            default:
                            var arr = jsonData.map(function(object){ return object.name });

                            $( "#auftraggeberId" ).autocomplete({
                                source: arr
                            });
                        }
                 }
        }); 
    });
});
