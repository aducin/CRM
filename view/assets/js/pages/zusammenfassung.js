$( document ).ready(function() {

    $( '#deliveryTime' ).css('visibility', 'visible');

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
    $('input[name=hiddenPerformanceTime]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=hiddenTextDate]').datepicker("option", "dateFormat", "dd/mm/yy");
    $('input[name=lieferterminInput]').datepicker("option", "dateFormat", "dd/mm/yy");
    
    function changeDate(curDate, project) {
        if ( window.location.href == urlPath ) {
            console.log('No project at this time');
        } else {
            var path = "../Api/Dates/";
            $.ajax({url: path,
                type: "post",
                data: { 'action' : 'ajax', 'concrete' : 'dates', 'value' : project, 'singleAction' :  curDate},
                success: function(result)
                { 
                    if (result == 'false') {
                        $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                        return false;
                    }  else {
                        console.log(result);
                        finalResult = result;
                    }
                }
            }); 
        }
    }
    
    function changeRow(variable, origin) {
      if (origin == 'Address') {
	    $('.bearbeitenAddressToUpdate').hide();
	    $('.bearbeitenAddressToChange').show();
      } else {
	    $('.bearbeitenPersonToUpdate').hide();
	    $('.bearbeitenPersonToChange').show();
      }
      var value = variable.attr('id');
      var rowId = variable.parent().attr('id');
      var next = variable.next();
      variable.hide();
      next.show();
      next.find('input:first').focus();
      next.find('input:first').select();
    }

    function columnChange(variable) {
        var name = variable.children().val();
        var value = variable.attr('id');
        if (value == 'plz') {
            name = name.replace('-', '');
            var check = isInteger(name);
            if (check == false) {
                variable.children().css('border-color', '#a94442');
                return false;
            } else if (name.length < 5 || name.length > 5) {
                variable.children().css('border-color', '#a94442');
                return false;
            } else {
                variable.children().css('border-color', '');
            }
        } else if (value == 'telefon' || value == 'telefon2') {
            var phoneNum = name.replace(/[^\d]/g, '');
            if(phoneNum.length < 8 || phoneNum.length > 11) { 
                variable.children().css('border-color', '#a94442');
                return false;
            } else {
                variable.children().css('border-color', '');
            }
        } else if (value == 'mail') {
            var emailCheck = validateEmail(name);
            if (emailCheck == false) {
                variable.children().css('border-color', '#a94442');
                return false;
            } else {
                variable.children().css('border-color', '');
            }
        }
        var previous = variable.prev();
        var table = variable.children().attr("name");
        var rowId = variable.parent().attr('id');
        var date = table + '<>' + value + '<>' + rowId;
        variable.children().prop('disabled', true);
        changeClientOption(date, name);
        var timerId = setInterval(function() {
            if (finalResult !== null) {
                if (finalResult == 'success') {
                    if (name == '') {
                        name = '<i>keine Daten</i>';
                    }
                    previous.html( name );
                    variable.hide();
                    variable.children().prop('disabled', false);
                    previous.show();
                }
            clearInterval(timerId);
            } else {
                $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
            }
        }, 1500);
    }

    function description(path, column, value) {
        $.ajax({url: path + '' + column + '<><>' + value,
            type: "get",
            success: function(result)
            {
              if(result != 'false') {
                    console.log(result);
              } else {
                $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
              }
            }
          }); 
    }

    $( "#zusammenfassungHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#vorstufeTable' ).hide();
        $( '#drucksachenTable' ).hide();
        $( '#fremdarbeitenTable' ).hide();
        $( '#kalkulationTable' ).hide();
        $( '#lieferscheinTable' ).hide();
        $( '#akteTable' ).hide();
        $( '#zusammenfassungTable' ).show();
        $( '#zusammenfassungId' ).addClass( 'active' );
    });

    $( "#vorstufeHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#zusammenfassungTable' ).hide();
        $( '#drucksachenTable' ).hide();
        $( '#fremdarbeitenTable' ).hide();
        $( '#kalkulationTable' ).hide();
        $( '#lieferscheinTable' ).hide();
        $( '#akteTable' ).hide();
        $( '#vorstufeTable' ).show();
        $( '#vorstufeId' ).addClass( 'active' );
    });

    $( "#drucksachenHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#zusammenfassungTable' ).hide();
        $( '#vorstufeTable' ).hide();
        $( '#fremdarbeitenTable' ).hide();
        $( '#kalkulationTable' ).hide();
        $( '#lieferscheinTable' ).hide();
        $( '#akteTable' ).hide();
        $( '#drucksachenTable' ).show();
        $( '#drucksachenId' ).addClass( 'active' );
    });

    $( "#fremdarbeitenHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#zusammenfassungTable' ).hide();
        $( '#vorstufeTable' ).hide();
        $( '#drucksachenTable' ).hide();
        $( '#kalkulationTable' ).hide();
        $( '#lieferscheinTable' ).hide();
        $( '#akteTable' ).hide();
        $( '#fremdarbeitenTable' ).show();
        $( '#fremdarbeitenId' ).addClass( 'active' );
    });
    
    $( "#kalkulationHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#zusammenfassungTable' ).hide();
        $( '#vorstufeTable' ).hide();
        $( '#drucksachenTable' ).hide();
        $( '#fremdarbeitenTable' ).hide();
        $( '#lieferscheinTable' ).hide();
        $( '#akteTable' ).hide();
        $( '#kalkulationTable' ).show();
        $( '#kalkulationId' ).addClass( 'active' );
    });
    
    $( "#kalkulationNotAllowed" ).mouseover(function() {
        $(this).css('cursor', 'not-allowed');
    });
    
    $( "#kalkulationNotAllowed" ).click(function($e) {
        $e.preventDefault();
    });
    
    $( "#lieferscheinHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#zusammenfassungTable' ).hide();
        $( '#vorstufeTable' ).hide();
        $( '#drucksachenTable' ).hide();
        $( '#fremdarbeitenTable' ).hide();
        $( '#kalkulationTable' ).hide();
        $( '#akteTable' ).hide();
        $( '#lieferscheinTable' ).show();
        $( '#lieferscheinId' ).addClass( 'active' );
    });
    
    $( "#akteHref" ).click(function($e) {
    $e.preventDefault();
        $( '.liTable' ).removeClass( 'active' );
        $( '#zusammenfassungTable' ).hide();
        $( '#vorstufeTable' ).hide();
        $( '#drucksachenTable' ).hide();
        $( '#fremdarbeitenTable' ).hide();
        $( '#kalkulationTable' ).hide();
        $( '#lieferscheinTable' ).hide();
        $( '#akteTable' ).show();
        $( '#akteId' ).addClass( 'active' );
    });

$( '#pattern' ).keyup(function() {
    var value = $( this ).val();
    if (value == '') {
        $( '#pattern' ).removeAttr( 'style' );
        $( '#musterSpan' ).text('');
        return false;
    }
    var check = isInteger(value);
    if (check == false) {
        $( '#pattern' ).css('border-color', '#a94442');
        $( '#musterSpan' ).text('Bitte eine Anzahl einschreiben');
    } else {
        $( '#pattern' ).removeAttr( 'style' );
        $( '#musterSpan' ).text('');
    }
});

$( '#individual_skonto' ).keyup(function() {
    var value = $( this ).val();
    if (value == '') {
        $( '#individual_skonto' ).removeAttr( 'style' );
        $( '#skontoSpan' ).text('');
        return false;
    }
    var check = isInteger(value);
    if (check == false) {
        $( '#individual_skonto' ).css('border-color', '#a94442');
        $( '#skontoSpan' ).text('Nur Anzahl');
    } else {
        $( '#individual_skonto' ).removeAttr( 'style' );
        $( '#skontoSpan' ).text('');
    }
});
    
$( "#saveProjectButton" ).click(function() {
	$( '.errorNewProjectDiv' ).removeClass('form-group has-error').addClass('form-group');
	var name = $( 'input[name=projektname]' ).val();
	var clientId = $( 'input[name=auftraggeber]' ).attr('id');
	var personId = $( 'input[name=ansprechpartnerBasic]' ).attr('id');
	var addressId = $( 'input[name=rechnungsadresseBasic]' ).attr('id');
	var client = $( 'input[name=auftraggeber]' ).val();
	var person = $( 'input[name=ansprechpartnerBasic]' ).val();
	var address = $( 'input[name=rechnungsadresseBasic]' ).val();
	var curAddress = $( '#newProjectAddress' ).val();
	var kundenauftragsnummer = $( 'input[name=kundenauftragsnummer]' ).val();
    var pattern = $( '#pattern' ).val();
    var paymentOpt = $('select[name=individual_payment] option:selected').val();
    $( '#individual_paymentOpt' ).val(paymentOpt);
    var error = false;
    if (pattern != '') {
        var patternCheck = isInteger(pattern);
        if (patternCheck == false) {
            $( '#pattern' ).focus();
            $( '#pattern' ).select();
            return false;
        }
    }
    var skonto = $( '#individual_skonto' ).val();
    if (skonto != '') {
        var skontoCheck = isInteger(skonto);
        if (skontoCheck == false) {
            $( '#individual_skonto' ).focus();
            $( '#individual_skonto' ).select();
            return false;
        }
    }
	if (name == '') {
         $( '.projectDiv' ).removeClass('form-group').addClass('form-group has-error');
         $( '#projektnameSpan' ).text('Geben Sie bitte die Projektname ein');
         error = true;
    } else if (name.length < 4) {
        $( '.projectDiv' ).removeClass('form-group').addClass('form-group has-error');
        $( '#projektnameSpan' ).text('Projektname ist zu kurz');
         error = true;
    } else {
        $( '#projektnameSpan' ).text('');
    }
	if (client == '') {
	     $( '.clientDiv' ).removeClass('form-group').addClass('form-group has-error');
         $( '#auftraggeberSpan' ).text('Suchen Sie den Auftraggeber aus');
	     error = true;
	} else {
        $( '#auftraggeberSpan' ).text('');
    }
	if (person == '') {
	     $( '.personDiv' ).removeClass('form-group').addClass('form-group has-error');
         $( '#ansprechpartnerSpan' ).text('Suchen Sie den Ansprechpartner aus');
	     error = true;
	} else {
        $( '#ansprechpartnerSpan' ).text('');
    }
	if (curAddress == '') {
	     $( '.addressDiv' ).removeClass('form-group').addClass('form-group has-error');
             $( '#rechnungsadresseSpan' ).text('Wählen Sie die Rechnungsadresse aus');
	     error = true;
	} else {
        $( '#rechnungsadresseSpan' ).text('');
    }
	//if (kundenauftragsnummer == '') {
    //     $( '#numberDiv' ).removeClass('form-group').addClass('form-group has-error');
    //     $( '#kundenauftragsnummerSpan' ).text('Geben Sie bitte die Auftragsnummer ein');
    //     error = true;
    //}
    if(kundenauftragsnummer != '') {
        if (kundenauftragsnummer.length < 4) {
            $( '.numberDiv' ).removeClass('form-group').addClass('form-group has-error');
            $( '#kundenauftragsnummerSpan' ).text('Nummer ist zu kurz');
            error = true;
        }
    } else {
        $( '#kundenauftragsnummerSpan' ).text('');
    }
	if (error == true) {
        $( 'input[name=projektname]' ).keyup(function() {
            var value = $( this ).val();
            if (value.length > 3 ) {
                $( '.projectDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#projektnameSpan' ).text('');
            } else {
                $( '.projectDiv' ).removeClass('form-group').addClass('form-group has-error');
                $( '#projektnameSpan' ).text('Projektname ist zu kurz');
            }
        });
        $( 'input[name=kundenauftragsnummer]' ).keyup(function() {
            var value = $( this ).val();
            if (value.length > 2 || value.length == 0) {
                $( '#numberDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#kundenauftragsnummerSpan' ).text('');
            } else {
                $( '#numberDiv' ).removeClass('form-group').addClass('form-group has-error');
                $( '#kundenauftragsnummerSpan' ).text('Auftragsnummer ist zu kurz');
            }
        });
        $('input[name=auftraggeber]').keyup(function() {
            var value = $('input[name=auftraggeber]').val();
            if (value == '') {
                $( '.clientDiv' ).removeClass('form-group').addClass('form-group has-error');
                $( '#auftraggeberSpan' ).text('Suchen Sie den Auftraggeber aus');
            }
        });
        $('input[name=ansprechpartnerBasic]').keyup(function() {
            var value = $('input[name=ansprechpartnerBasic]').val();
            if (value == '') {
                $( '.personDiv' ).removeClass('form-group').addClass('form-group has-error');
                $( '#ansprechpartnerSpan' ).text('Suchen Sie den Ansprechpartner aus');
            }
        });
        $('input[name=rechnungsadresseBasic]').keyup(function() {
            var value = $('input[name=rechnungsadresseBasic]').val();
            if (value == '') {
                $( '.addressDiv' ).removeClass('form-group').addClass('form-group has-error');
                $( '#rechnungsadresseSpan' ).text('Suchen Sie die Rechnungsadresse aus');
            }
        });
	   return false;
	}
	$( '#newProjectClient' ).val(clientId);
	$( '#newProjectPerson' ).val(personId);
	//$( '#newProjectAddress' ).val(addressId);
	$( "#newProjectForm" ).submit();
    });

    $('#invidivuell_1').change(function() {
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
        updateClient();
    });

	$('input[name=ansprechpartnerBasic]').keyup(function() {
		$( '#ansprechpartnerDiv' ).removeClass('form-group has-error').addClass('form-group');
		$( '#ansprechpartnerLabel' ).html('Ansprechpartner');
		var clientName = $('input[name=auftraggeber]').val();
		var searchedName = $('input[name=ansprechpartnerBasic]').val();
		var dates = [ searchedName, clientName ];
		if (window.location.href == urlPath) {
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
				      var arr = jsonData.map(function(object){ return object.name });

				    $('input[name=ansprechpartnerBasic]').autocomplete({
					source: arr,
					select: function (event, ui) {
							var name = ui.item.value;
							var newPath = path + "Name/";
							$.ajax({url: newPath + name, 
								type: "get",
								success: function(result)
								{
                                    $( '#ansprechpartnerSpan' ).text('');
									$('input[name=ansprechpartnerBasic]').attr('id', result);
									if (concrete == true) {
										var projectId = 'ansprechpartner<>' + $( '#hiddenProjectId' ).val();
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

	$('input[name=rechnungsadresseBasic]').keyup(function() {
		$( '#rechnungsadresseDiv' ).removeClass('form-group has-error').addClass('form-group');
		$( '#rechnungsadresseLabel' ).html('Rechnungsadresse');
		var clientName = $('input[name=auftraggeber]').val();
		var searchedName = $('input[name=rechnungsadresseBasic]').val();
		var dates = [ searchedName, clientName ];
		if (window.location.href == urlPath) {
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
					$('input[name=rechnungsadresseBasic]').autocomplete({
						source: arr,
						select: function (event, ui) {
							var name = ui.item.value;
							var newPath = path + "Name/";
							$.ajax({url: newPath + name, 
								type: "get",
								success: function(result)
								{
                                    $( '#rechnungsadresseSpan' ).text('');
									$('input[name=rechnungsadresseBasic]').attr('id', result);
									if (concrete == true) {
										var projectId = 'rechnungsadresse<>' + $( '#hiddenProjectId' ).val();
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

    $( 'input[name=auftraggeber]' ).blur(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
            $( 'input[name=invidivuell_1]' ).prop('disabled', 'disabled');
            $( 'input[name=invidivuell_2]' ).prop('disabled', 'disabled');
            $( '#lieferung_per' ).val('');
            $( '#selectToBeDeleted' ).val('');
            $( '#selectToBeDeleted' ).hide();    
    	    $( 'input[name=ansprechpartnerBasic]' ).val('');
    	    $( 'input[name=ansprechpartnerBasic]' ).prop('disabled', 'disabled');
    	    $( 'input[name=rechnungsadresseBasic]' ).val('');
    	    $( 'input[name=rechnungsadresseBasic]' ).prop('disabled', 'disabled');
            $( '#bearbeitenButton' ).prop('disabled', 'disabled');
    	    var projectId = 'ansprechpartner<>' + $( '#hiddenProjectId' ).val();
    	    changeDate(null, projectId);
    	    var projectId = 'rechnungsadresse<>' + $( '#hiddenProjectId' ).val();
    	    changeDate(null, projectId);
    	    var projectId = 'auftraggeber<>' + $( '#hiddenProjectId' ).val();
	       changeDate(null, projectId);
        }
    });
    
    $('input[name=ansprechpartnerBasic]').blur(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
	    $('input[name=ansprechpartnerBasic]').val('');
	    var projectId = 'ansprechpartner<>' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
        }
    });
    
    $('input[name=rechnungsadresseBasic]').blur(function() {
        var currentClient = $(this).val();
        if (currentClient == '') {
	    $('input[name=rechnungsadresseBasic]').val('');
	    var projectId = 'rechnungsadresse<>' + $( '#hiddenProjectId' ).val();
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
    
    $( '.datePickerToUpdate' ).change(function() {
    	var curDate = $(this).val();
    	var project = $(this).attr('id');
    	var projectId = $( '#hiddenProjectId' ).val();
	if (project == 'termin_fremdarbeiten') {
	    var date = 'amendmentTime<>' + projectId;
	} else if (project == 'termin_fremdarbeitenDaten') {
	    var date = 'dateTime<>' + projectId;
	} else {
	    var date = project + '<>' + projectId;
	}
    	changeDate(curDate, date);
	if (project == 'amendmentTime') {
	    $('input[name=termin_fremdarbeiten]').val(curDate);
	} else if (project == 'dateTime') {
	    $('input[name=termin_fremdarbeitenDaten]').val(curDate);
	} else if (project == 'termin_fremdarbeiten') {
	    $('input[name=amendmentTime]').val(curDate);
	} else if (project == 'termin_fremdarbeitenDaten') {
	    $('input[name=dateTime]').val(curDate);
	}
    });
    
    $( '.dateToUpdate' ).change(function() {
    	var curDate = $(this).val();
    	var project = $(this).attr('id');
	if (project == 'adresse_auftragsgeber') {
	    project = 'abweichend';
	}
	var projectId = $( '#hiddenProjectId' ).val();
	var array = [project, projectId];
    	changeDate(curDate, array);
    });

    $( '.singleDateToChange' ).blur(function() {
        function isInteger(value)      
        {       
            num = value.trim();         
            return !(value.match(/\s/g)||num==""||isNaN(num)||(typeof(value)=='number'));        
        }
        var projectId = $( '#hiddenProjectId' ).val();
        var curDate = $( this ).val();
        var variable = $( this ).attr('id');
        if ( variable == 'pattern' ) {
            $( '#pattern' ).removeAttr( 'style' );
            var check = isInteger(curDate);
            if (curDate == '') {
                $( '#pattern' ).css('border-color', '');
                return false;
            }
            if (check == false) {
                $( '#pattern' ).css('border-color', '#a94442');
                return false;
            }
        } else if (variable == 'individual_skonto') {
            $( '#individual_skonto' ).removeAttr( 'style' );
            $( '#skontoLabel' ).html('Skonto');
            if (curDate == '') {
                return false;
            }
            var check = isInteger(curDate);
            if (check == false) {
                $( '#skontoLabel' ).html('Das ist keine Anzahl');
                $( '#individual_skonto' ).css('border-color', '#a94442');
                return false;
            }
        }
        var array = [variable, projectId];
        changeDate(curDate, array);
    });

    $('.descCheckbox').change(function() {
        var ifChecked = $(this).is(":checked");
        var column = $( this ).attr('id');
        if (ifChecked == true) {
            var checkbox = 1;
        } else {
            var checkbox = 0;
        }
        if (window.location.href == urlPath) {
          console.log('No project at this time');
        } else {
          var path = "../Api/Description/";
          description(path, column, checkbox);
        } 
    });

    $('.descTextarea').change(function() {
        var value = $(this).val();
        var column = $( this ).attr('id');
        if (window.location.href == urlPath) {
          consloe.log('No project at this time');
        } else {
          var path = "../Api/Description/";
          description(path, column, value);
        } 
    });

    $('.descIntern').change(function() {
        var value = $(this).val();
        var column = $( this ).attr('id');
        var fakeColumn = 'desc5';
        if (window.location.href == urlPath) {
          console.log('No project at this time');
        } else {
          var path = "../Api/Description/";
          description(path, fakeColumn, value);
          $('.descIntern').val('');
          $( '#desc5').val(value);
          $( '#desc6').val(value);
          $( '#desc7').val(value);
          $( '#desc8').val(value);
        } 
    });
    $( "#topNewButton" ).click(function() {
        $( '#productToReplace' )[0].click();
    });

    $( "#cloneButton" ).click(function() {
        $( '#productToClone' )[0].click();
    });

    $('input[type=radio][name=lieferung]').change(function() {
        if ($(this).attr('id') == 'adresse_auftragsgeber') {
            $( '#lieferadresse_ab' ).prop('disabled', true);
        } else if ($(this).attr('id') == 'abweichend') {
            $( '#lieferadresse_ab' ).prop('disabled', false);
        }
    });
    
    $( '.calculationCheckbox' ).change(function() {
    	var ifChecked = $(this).is(":checked");
        var column = $( this ).attr('id');
	    var projectId = $( '#hiddenProjectId' ).val();
        if (ifChecked == true) {
            var checkbox = 1;
        } else {
            var checkbox = 0;
        }
        var array = ['Project_Calculation', projectId, column, checkbox];
    	changeDate('update', array);
    });

    $( '.calcToChange' ).dblclick(function() {
       function isInteger(value)      
       {       
            num = value.trim();         
            return !(value.match(/\s/g)||num==""||isNaN(num)||(typeof(value)=='number'));        
        }
        var previous = $( '.newCalc' ).val();
        var previousId = $( '.newCalc' ).attr('id');
        if (previousId) {
            var split = previousId.split('_');
            var parent = $( '.newCalc' ).parent();
            $( '.newCalc' ).remove();
            if (previous == '') {
                parent.text('--');
            } else if (split[0] == 'preis') {
                parent.text(previous + ' EURO');
            } else {
                parent.text(previous);
            }
        }
        var column = $( this ).children().attr('id');
        var split = column.split('_');
        var origin = split[0];
        var total = split[1];
        var value = $( this ).children().text();
        var projectId = $( '#hiddenProjectId' ).val();
        value = value.replace(" EURO", "");
	    $( this ).children().text('');
	    if (value == '--') {
	       value = '';
	    }
	    $( this ).children().append('<input type="text" name="sth" class="form-control1 newCalc" id="' + column + '" value="' + value +'" />');
    	$( '.newCalc' ).focus();
    	$( '.newCalc' ).select();
    	if (value == '') {
    	    value = 0;
    	}
    	$( '.newCalc' ).change(function() {
    	    $( '.newCalc' ).css("border-color", "");
    	    var newValue = $( this ).val();
    	    newValue = newValue.replace(',' , '.');
    	    var valueCheck = isInteger(newValue);
    	    if (newValue  != '') {
                if (valueCheck == false) {
                    $( this ).css('border-color', '#a94442');
                    return false;
                }
            }
            var type = $( this ).attr('id');
            type = type.substr(0, 5);
            if (type == 'preis') {
                newValue = parseFloat(Math.round(newValue * 100) / 100).toFixed(2);
            }
            $( this ).css('background-color', '#eee');
	        $( this ).css('cursor', 'not-allowed');
	        $( this).blur();
    	    var array = ['Project_Calculation', projectId, column, newValue];
    	    changeDate('update', array);
    	    var timerId = setInterval(function() {
    		if(finalResult !== null) {
    		    if(finalResult == 'success') {
    		      $( '.newCalc' ).remove();
    		      if (newValue == '') {
    			  newValue = 0;
    		      }
    		      if (origin == 'preis') {
    			  var oldTotal = $( '#total' + total ).text();
    			  oldTotal = oldTotal.replace(' EURO', '');
    			  if (oldTotal != '') {
    			      oldTotal = parseFloat(oldTotal);
    			  }
    			  value = parseFloat(value);
    			  newSumValue = parseFloat(newValue);
    			  var newTotal = oldTotal - value + newSumValue;
                  newTotal = parseFloat(Math.round(newTotal * 100) / 100).toFixed(2);
    			  $( '#total' + total ).text(newTotal + ' EURO');
    			  if (newValue != 0) {
    			      newValue = newValue + ' EURO';
    			  } else {
    			      newValue = '--';
    			  }
    		      }
    		      $( '#' + column ).text(newValue);
    		    }
    		clearInterval(timerId);
    		} else {
    		    $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
    		}
    	    }, 1500);
    	});
    });
    
    $( ".notAvailable" ).mouseover(function() {
        $(this).css('cursor', 'not-allowed');
    });
    
    $( ".notAvailable" ).click(function() {
        event.preventDefault();
    });
    
    $( "#kalkulationNotAllowed" ).mouseover(function() {
        $(this).css('cursor', 'not-allowed');
    });
    
    $( "#kalkulationNotAllowed" ).click(function() {
        event.preventDefault();
    });
    
    $('input[name=mitarbeiter]').click(function() {
        var projectId = $( '#hiddenProjectId' ).val();
        var idNumber = $( this ).attr('id');
        var ifCheck = $('input[name=mitarbeiter]').is(":checked");
        if (ifCheck == true) {
            ifCheck = 1;
        } else {
            ifCheck = 0;
        }
        var array = ['user', projectId, idNumber];
        changeDate(ifCheck, array);
    });
    
    $('.documentList').click(function() {
    	$("tr.documentList").css('background-color', "#f9f9f9");
    	var id = $( this ).attr('id');
    	$( this ).css('background-color', "#e9e9e9");
    	$( '.documentDelete').attr('id', id);
    	$( '.documentDelete').prop('disabled', false);
        }); 
        
        $('.documentDelete').click(function() {
    	var idValue = $( this ).attr('id');
    	var values = 'delete-document-' + idValue;
            if (window.location.href == urlPath) {
                var path = "/Api/Row/";
            } else {
                var path = "../Api/Row/";
                $.ajax({url: path,
                    type: "post",
                    data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
                    success: function(result)
                    {
                        if (result == 'success') {
                            var toDelete = $('.documentList[name=' + idValue + ']');
                            toDelete.remove();
                            $('.documentDelete').prop('disabled', true);
                            $('.documentDelete').attr('id', '');
                        }
                    }
            }); 
        }
    }); 
    
    $( '#documentRestore' ).click(function() {
		var path = "../Api/Document/";
		$.ajax({url: path + projectId, 
			type: "get",
			success: function(result)
			{
				$( '.documentList' ).remove();
				var jsonResult = JSON.parse(result);
				var jsonArray = jsonResult.map(function(object)
				      { return [object.id, object.documentName, object.userName, object.date, object.description, object.file] });
				var counter = 0;
				var documentTableRow = '';
				$.each( jsonArray, function() {
				    documentTableRow += '<tr name="' + jsonArray[counter][0] + '" class="documentList" id="' + jsonArray[counter][0] + '">';
				    documentTableRow += '<td>' + jsonArray[counter][1] + '</td>';
				    documentTableRow += '<td>' + jsonArray[counter][3] + '</td>';
				    documentTableRow += '<td>' + jsonArray[counter][4] + '</td>';
				    documentTableRow += '<td>' + jsonArray[counter][2] + '</td>';
				    documentTableRow += '<td><a href="' + documentPath + '/' + jsonArray[counter][5] + '" target="_blank">anzeigen</a></td>'
				    documentTableRow += '</tr>';
				    counter++;
				});
				$( '#akteTbody' ).append(documentTableRow);
				$('.documentList').click(function() {
				      $("tr.documentList").css('background-color', "#f9f9f9");
				      var id = $( this ).attr('id');
				      $( this ).css('background-color', "#e9e9e9");
				      $( '.documentDelete').attr('id', id);
				      $( '.documentDelete').prop('disabled', false);
				});
			}
		}); 
    });
});
