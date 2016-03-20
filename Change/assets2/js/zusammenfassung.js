$( document ).ready(function() {
    $('#invidivuell_1').change(function() {
        //var zielButton = $('[data-id="paymentOpt"]').prop('title');
        //alert (zielButton);
    	if ($("#invidivuell_1").is(":checked")) {
    	  $('#paymentOpt').prop('disabled', false);
    	  $('#paymentOpt').siblings('button').removeClass('disabled');
          $('#paymentOpt').siblings('button').addClass('paymentTarget');
    	} else {
    	  $('#paymentOpt').prop('disabled', 'disabled');
          $('#paymentOpt').siblings('button').removeClass('paymentTarget');
    	  $('#paymentOpt').siblings('button').addClass('disabled');
    	}
    });
    $('#invidivuell_2').change(function() {
    	if ($("#invidivuell_2").is(":checked")) {
    	  $('#skontoId').prop('disabled', false);
    	} else {
    	  $('#skontoId').prop('disabled', 'disabled');
    	} 
    });

    $( "#auftraggeberId" ).keyup(function() {
        $( '#auftraggeberDiv' ).removeClass('form-group has-error').addClass('form-group');
        $( '#auftraggeberLabel' ).html('Auftraggeber');
	    var clientName = $( "#auftraggeberId" ).val();
	    if (window.location.href == "http://ad9bis.vot.pl/CRM/Zusammenfassung") {
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
                            $( '#auftraggeberLabel' ).html('Nichts gefunden');
                            break;

                            default:
                            var arr = jsonData.map(function(object){ return object.name });

                            $( "#auftraggeberId" ).autocomplete({
                                source: arr,
                                select: function (event, ui) {
                                    $( '#ansprechpartnerId' ).prop('disabled', false);
                                    $( '#rechnungsadresseId' ).prop('disabled', false);
                                }
                            });
                        }
                 }
        }); 
    });

    $( "#ansprechpartnerId" ).keyup(function() {
        $( '#ansprechpartnerDiv' ).removeClass('form-group has-error').addClass('form-group');
        $( '#ansprechpartnerLabel' ).html('Auftraggeber');
        var clientName = $( "#ansprechpartnerId" ).val();
        if (window.location.href == "http://ad9bis.vot.pl/CRM/Zusammenfassung") {
           var path = "Api/Employee/";
        } else {
           var path = "../Api/Employee/";
        }
        $.ajax({url: path + clientName, 
               type: "get",
               success: function(result)
                    {
                        var jsonData = JSON.parse(result);
                        switch(jsonData.success){
                            case 'false':
                            $( '#ansprechpartnerDiv' ).removeClass('form-group').addClass('form-group has-error');
                            $( '#ansprechpartnerLabel' ).html('Nichts gefunden');
                            break;

                            default:
                            var arr = jsonData.map(function(object){ return object.name });

                            $( "#ansprechpartnerId" ).autocomplete({
                                source: arr
                            });
                        }
                 }
        }); 
    });

    $( "#rechnungsadresseId" ).keyup(function() {
        $( '#rechnungsadresseDiv' ).removeClass('form-group has-error').addClass('form-group');
        $( '#rechnungsadresseLabel' ).html('Rechnungsadresse');
        var clientName = $( "#rechnungsadresseId" ).val();
        if (window.location.href == "http://ad9bis.vot.pl/CRM/Zusammenfassung") {
           var path = "Api/Address/";
        } else {
           var path = "../Api/Address/";
        }
        $.ajax({url: path + clientName, 
               type: "get",
               success: function(result)
                    {
                        var jsonData = JSON.parse(result);
                        switch(jsonData.success){
                            case 'false':
                            $( '#rechnungsadresseDiv' ).removeClass('form-group').addClass('form-group has-error');
                            $( '#rechnungsadresseLabel' ).html('Nichts gefunden');
                            break;

                            default:
                            var arr = jsonData.map(function(object){ return object.name });

                            $( "#rechnungsadresseId" ).autocomplete({
                                source: arr
                            });
                        }
                 }
        }); 
    });

    $('#paymentOpt').change(function() {
        var current = $(this).val();
        $( '#zahlungszielDisplay' ).html( "Ziel " + current );
        //var zielButton = $('[data-id="paymentOpt"]').prop('title');
    });

    $('#skontoId').keyup(function() {
        $( '#skontoLabel' ).html('Skonto');
        $( '#skontoDiv' ).removeClass('form-group has-error').addClass('form-group');
        var currentSkonto = $(this).val();
        if ($.isNumeric( currentSkonto )) {
            var error = false;
        } else {
            $( '#skontoDiv' ).removeClass('form-group').addClass('form-group has-error');
            $( '#skontoLabel' ).html('Das ist keine Anzahl');
            return false;
        }
        if (currentSkonto < 0) {
            var error = true;
        } 
        if (currentSkonto > 100) {
            var error = true;
        }
        if ( error == true ) {
            $( '#skontoDiv' ).removeClass('form-group').addClass('form-group has-error');
            $( '#skontoLabel' ).html('Anzahl 0-100');
        } else {
            $( '#skontoDisplay' ).html( currentSkonto );
        }
    });

    $( "#auftraggeberId" ).change(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
            $( '#ansprechpartnerId' ).prop('disabled', 'disabled');
            $( '#rechnungsadresseId' ).prop('disabled', 'disabled');
        }
    });
});
