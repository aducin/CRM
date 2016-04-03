$( document ).ready(function() {
  
    var finalResult;
    var urlPath = "http://ad9bis.vot.pl/CRM/Erfassung";
  
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

    function changeClientOption(dates, value) {
	if ( window.location.href == urlPath ) {
           console.log('No project at this time');
        } else {
           var path = "../Api/ClientOption/";
	   $.ajax({url: path,
               type: "post",
	       data: { 'action' : 'ajax', 'concrete' : 'clientOption', 'value' : dates, 'singleAction' :  value},
               success: function(result)
                 {   
        		    console.log(result);
        		    finalResult = result;
                 }
	    }); 
        }
    }
    
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
                console.log(result);
                finalResult = result;
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
	var previous = variable.prev();
	var table = variable.children().attr("name");
	var rowId = variable.parent().attr('id');
	var date = table + '<>' + value + '<>' + rowId;
	changeClientOption(date, name);
	var timerId = setInterval(function() {
		if(finalResult !== null) {
		    if(finalResult == 'success') {
		      
			previous.text( name );
			variable.hide();
			previous.show();
		    }
		clearInterval(timerId);
		} else {
		    console.log(finalResult);
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
                console.log('No change available at the moment');
              }
            }
          }); 
    }

    function rowClick(object) {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
        var idVal = object.attr('id');
        object.css('background-color', "rgb(238, 193, 213)");
        $('.deleteBearbeitenButtonAddress').attr('id', idVal);
        $('.deleteBearbeitenButtonAddress').prop('disabled', false);
    }

    function rowPersonClick(object) {
        $("tr.rowsBearbeitenPerson").removeAttr( 'style' );
        var idVal = object.attr('id');
        object.css('background-color', "rgb(238, 193, 213)");
        $('.deleteBearbeitenButtonPerson').attr('id', idVal);
        $('.deleteBearbeitenButtonPerson').prop('disabled', false);
    }

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
        $( '#auftraggeberDiv' ).removeClass('form-group has-error').addClass('form-group');
        $( '#auftraggeberLabel' ).html('Auftraggeber');
	    var clientName = $('input[name=auftraggeber]').val();
	    if (window.location.href == "http://kluby.local/CRM/Erfassung") {
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
											$('input[name=ansprechpartnerBasic]').prop('disabled', false);
											$('input[name=rechnungsadresse]').prop('disabled', false);
											var currentVal = $('#hiddenCustomerName').val();
											if (currentVal == name) {
												console.log('The same');
												return false;
											} else {
											    $('#hiddenCustomerName').val(name);
											    if (concrete == true) {
												var projectId = 'auftraggeber<>' + $( '#hiddenProjectId' ).val();
												changeDate(result, projectId);
											    }
											    $('input[name=ansprechpartnerBasic]').val('');
											    $('input[name=rechnungsadresse]').val('');
											    if (concrete == true) {
												var projectId = 'ansprechpartner<>' + $( '#hiddenProjectId' ).val();
												changeDate('null', projectId);
											    }
											    if (concrete == true) {
												var projectId = 'rechnungsadresse<>' + $( '#hiddenProjectId' ).val();
												changeDate('null', projectId);
												var timerId = setInterval(function() {
												  if(finalResult !== null) {
												      if(finalResult == 'success') {
													location.reload();
												      }
												    clearInterval(timerId);
												    } else {
													console.log(finalResult);
												    }
												}, 1500);
											    }
											} 
										}
								    }); 
                                }
                            });
                        }
                 }
        }); 
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
	    $('input[name=ansprechpartnerBasic]').val('');
        $('input[name=ansprechpartnerBasic]').prop('disabled', 'disabled');
	    $('input[name=rechnungsadresseBasic]').val('');
        $('input[name=rechnungsadresseBasic]').prop('disabled', 'disabled');
	    var projectId = 'ansprechpartner<' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
	    var projectId = 'rechnungsadresse<>' + $( '#hiddenProjectId' ).val();
	    changeDate(null, projectId);
	    var projectId = 'auftraggeber<' + $( '#hiddenProjectId' ).val();
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
            $( '#patternDiv' ).removeClass('form-group has-error').addClass('form-group');
            var check = isInteger(curDate);
            if (check == false) {
                $( '#patternDiv' ).removeClass('form-group').addClass('form-group has-error');
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
            $( '#lieferadresse_abweichend' ).prop('disabled', true);
        } else if ($(this).attr('id') == 'abweichend') {
            $( '#lieferadresse_abweichend' ).prop('disabled', false);
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
		      $( this ).css('border-color', 'red');
		      return false;
		}
	    }
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
			  oldTotal = parseFloat(oldTotal);
			  value = parseFloat(value);
			  newValue = parseFloat(newValue);
			  var newTotal = oldTotal - value + newValue;
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
		    console.log(finalResult);
		}
	    }, 1500);
	});
    });
    
    $( "#topNewButton" ).mouseover(function() {
        $(this).css('cursor', 'not-allowed');
    });
    
    $( "#topNewButton" ).click(function() {
        event.preventDefault();
    });
    
});
