$( document ).ready(function() {

    $('#termin_korrektur').datepicker( {
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'DD MM yy'
    }); 
    
    $.datepicker.regional['de'] = {
        closeText: "Schließen",
        prevText: "&#x3C;Zurück",
        nextText: "Vor&#x3E;",
        currentText: "Heute",
        monthNames: [ "Januar","Februar","März","April","Mai","Juni",
        "Juli","August","September","Oktober","November","Dezember" ],
        monthNamesShort: [ "Jan","Feb","Mär","Apr","Mai","Jun",
        "Jul","Aug","Sep","Okt","Nov","Dez" ],
        dayNames: [ "Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag" ],
        dayNamesShort: [ "So","Mo","Di","Mi","Do","Fr","Sa" ],
        dayNamesMin: [ "So","Mo","Di","Mi","Do","Fr","Sa" ],
        weekHeader: "KW",
        dateFormat: "dd.mm.yy",
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: "" 
    };

    $.datepicker.setDefaults($.datepicker.regional['de']);

    $('input[name=amendmentTime]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=dateTime]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=proofTime]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=printTime]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=termin_fremdarbeiten]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=termin_fremdarbeitenDaten]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('#hiddenDateFremdarbeiten').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=lieferterminInput]').datepicker("option", "dateFormat", "dd/mm/yy");

    $('#invidivuell_1').change(function() {
        //var zielButton = $('[data-id="paymentOpt"]').prop('title');
    	if ($("#invidivuell_1").is(":checked")) {
    	  $('select[name=individual_payment]').prop('disabled', false);
    	  $('select[name=individual_payment]').siblings('button').removeClass('disabled');
          $('select[name=individual_payment]').siblings('button').addClass('paymentTarget');
    	} else {
    	  $('select[name=individual_payment]').prop('disabled', 'disabled');
          $('select[name=individual_payment]').siblings('button').removeClass('paymentTarget');
    	  $('select[name=individual_payment]').siblings('button').addClass('disabled');
    	}
    });

    $('#invidivuell_2').change(function() {
    	if ($("#invidivuell_2").is(":checked")) {
    	  $('input[name=individual_skonto]').prop('disabled', false);
    	} else {
    	  $('input[name=individual_skonto]').prop('disabled', 'disabled');
    	} 
    });

    $('input[name=auftraggeber]').keyup(function() {
        $( '#auftraggeberDiv' ).removeClass('form-group has-error').addClass('form-group');
        $( '#auftraggeberLabel' ).html('Auftraggeber');
	    var clientName = $('input[name=auftraggeber]').val();
	    if (window.location.href == "http://ad9bis.vot.pl/CRM/Erfassung") {
	        path = "Api/Project/";
	    } else {
	        path = "../Api/Project/";
	       var concrete = true;
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

                            $('input[name=auftraggeber]').autocomplete({
                                source: arr,
                                select: function (event, ui) {
				    var name = ui.item.value;
				    var newPath = path + "Name/";
				    $.ajax({url: newPath + name, 
				      type: "get",
				      success: function(result)
					    {
						$('input[name=ansprechpartner]').prop('disabled', false);
						$('input[name=rechnungsadresse]').prop('disabled', false);
						var currentVal = $('#hiddenCustomerName').val();
						if (currentVal != result) {
						    $('#hiddenCustomerName').val(result);
						    if (concrete == true) {
							var projectId = 'auftraggeber-' + $( '#hiddenProjectId' ).val();
							changeDate(result, projectId);
						    }
						    $('input[name=ansprechpartner]').val('');
						    $('input[name=rechnungsadresse]').val('');
						    if (concrete == true) {
							var projectId = 'ansprechpartner-' + $( '#hiddenProjectId' ).val();
							changeDate(null, projectId);
						    }
						    if (concrete == true) {
							var projectId = 'rechnungsadresse-' + $( '#hiddenProjectId' ).val();
							changeDate(null, projectId);
						    }
						} else {
						    alert('the same');
						}
					}
				    }); 
                                }
                            });
                        }
                 }
        }); 
    });

	$('input[name=ansprechpartner]').keyup(function() {
		$( '#ansprechpartnerDiv' ).removeClass('form-group has-error').addClass('form-group');
		$( '#ansprechpartnerLabel' ).html('Ansprechpartner');
		var clientName = $('input[name=auftraggeber]').val();
		var searchedName = $('input[name=ansprechpartner]').val();
		var dates = [ searchedName, clientName ];
		if (window.location.href == "http://ad9bis.vot.pl/CRM/Erfassung") {
		  var path = "Api/Employee/";
		} else {
		  var path = "../Api/Employee/";
		  var concrete = true;
		}
		$.ajax({url: path + dates, 
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
				      var arr = jsonData.map(function(object){ return object.name + '->' + object.id });

				    $('input[name=ansprechpartner]').autocomplete({
					source: arr,
							select: function (event, ui) {
								idNumber = ui.item.value.split("->")[1];
								ui.item.value = ui.item.value.split("->")[0];
								if (concrete == true) {
								    var project = $(this).attr('id');
								    changeDate(idNumber, project);
								}
							}
				    });
				}
			}
		}); 
	});

	$('input[name=rechnungsadresse]').keyup(function() {
		$( '#rechnungsadresseDiv' ).removeClass('form-group has-error').addClass('form-group');
		$( '#rechnungsadresseLabel' ).html('Rechnungsadresse');
		var clientName = $('input[name=auftraggeber]').val();
		var searchedName = $('input[name=rechnungsadresse]').val();
		var dates = [ searchedName, clientName ];
		if (window.location.href == "http://ad9bis.vot.pl/CRM/Erfassung") {
			var path = "Api/Address/";
		} else {
			var path = "../Api/Address/";
			var concrete = true;
		}
		$.ajax({url: path + dates, 
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
					$('input[name=rechnungsadresse]').autocomplete({
						source: arr,
						select: function (event, ui) {
							var name = ui.item.value;
							var newPath = path + "Name/";
							$.ajax({url: newPath + name, 
								type: "get",
								success: function(result)
								{
									if (concrete == true) {
										var projectId = 'rechnungsadresse-' + $( '#hiddenProjectId' ).val();
										changeDate(result, projectId);
									}
								}
							}); 
						}
					});
				}
			}
		}); 
	});

    $('input[name=individual_skonto]').keyup(function() {
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
        }
    });

    $('input[name=auftraggeber]').blur(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
	    $('input[name=ansprechpartner]').val('');
            $('input[name=ansprechpartner]').prop('disabled', 'disabled');
	    $('input[name=rechnungsadresse]').val('');
            $('input[name=rechnungsadresse]').prop('disabled', 'disabled');
	    var projectId = 'ansprechpartner-' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
	    var projectId = 'rechnungsadresse-' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
	    var projectId = 'auftraggeber-' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
        }
    });
    
    $('input[name=ansprechpartner]').blur(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
	    $('input[name=ansprechpartner]').val('');
	    var projectId = 'ansprechpartner-' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
        }
    });
    
    $('input[name=rechnungsadresse]').blur(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
	    $('input[name=rechnungsadresse]').val('');
	    var projectId = 'rechnungsadresse-' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
        }
    });
    
    $('input[name=intPriceShow1]').click(function() {
        var ifChecked = $(this).is(":checked");
	if (ifChecked == true) {
	    var checkbox = 1;
	    $('input[name=intPriceShow2]').prop('checked', true);
	} else {
	    var checkbox = 0;
	    $('input[name=intPriceShow2]').prop('checked', false);
	}
        var projectId = $(this).attr('id');
	changeDate(checkbox, projectId);
    });
    
    $('input[name=intPriceShow2]').click(function() {
        var ifChecked = $(this).is(":checked");
	if (ifChecked == true) {
	    var checkbox = 1;
	    $('input[name=intPriceShow1]').prop('checked', true);
	} else {
	    var checkbox = 0;
	    $('input[name=intPriceShow1]').prop('checked', false);
	}
        var projectId = $(this).attr('id');
	changeDate(checkbox, projectId);
    });
    
    function changeDate(curDate, project) {
	var values = project + '-' + curDate;
	if (window.location.href == "http://kluby.local/CRM/Erfassung") {
           alert('No project at this time');
        } else {
           var path = "../Api/Dates/";
	   $.ajax({url: path,
               type: "post",
	       data: { 'action' : 'ajax', 'concrete' : 'dates', 'value' : values },
               success: function(result)
                 {
		    alert(result);
                 }
	    }); 
        }
    }
    
    $( ".mandant" ).change(function() {
        var mandant = $( this ).val();
	var project = $( this ).attr('id');
	changeDate(mandant, project);    
    });
    
    $( '.projectStatus' ).change(function() {
        var status = $( this ).val();
	var project = $( this ).attr('id');
	changeDate(status, project); 
    });
    
    $('input[name=projektname]').blur(function(){
        var name = $( this ).val();
	var project = $( this ).attr('id');
	changeDate(name, project); 
    });
    
    $('input[name=lieferterminInput]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('input[name=amendmentTime]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
	$('input[name=termin_fremdarbeiten]').val(curDate);
    });
    
    $('input[name=dateTime]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
	$('input[name=termin_fremdarbeitenDaten]').val(curDate);
    });
    
    $('input[name=proofTime]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('input[name=printTime]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('input[name=termin_fremdarbeiten]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	var newproject = project.replace('termin_fremdarbeiten', '');
	finalProject = 'amendmentTime' + newproject;
	changeDate(curDate, finalProject);
	$('input[name=amendmentTime]').val(curDate);
    });
    
    $('input[name=termin_fremdarbeitenDaten]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	var newproject = project.replace('termin_fremdarbeitenDaten', '');
	finalProject = 'dateTime' + newproject;
	changeDate(curDate, finalProject);
	$('input[name=dateTime]').val(curDate);
    });
    
    $('input[name=pattern]').blur(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('input[name=pattern_to]').blur(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('select[name=individual_payment]').change(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('input[name=individual_skonto]').blur(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    });
    
    $('input[name=lieferant_id]').click(function() {
	var curDate = $(this).val();
	var project = $(this).attr('id');
	changeDate(curDate, project);
    }); 
});
