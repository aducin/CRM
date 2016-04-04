$( document ).ready(function() {
  
  var finalResult;
  var urlPath = "http://ad9bis.vot.pl/CRM/Erfassung";

  $('input[name=hiddenVorstufeDate]').datepicker( {
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
  
  $('input[name=hiddenVorstufeDate]').datepicker("option", "dateFormat", "dd/mm/yy");
  
  function checkCheckbox(variable) {
        var ifChecked = variable.is(":checked");
        var value = variable.attr('id');
        var rowId = variable.parent().parent().attr('id');
        if (ifChecked == true) {
            var checkbox = 1;
        } else {
            var checkbox = 0;
        }
        var date = value + '-' + checkbox;
        changeDate(date, rowId); 
    }

  function changeDate(curDate, vorstufe) {
    var values = 'Vorstufe-' + vorstufe + '-' + curDate;
    if (window.location.href == urlPath) {
      console.log('No project at this time');
    } else {
      var path = "../Api/Row/";
      $.ajax({url: path,
        type: "post",
        data: { 'action' : 'ajax', 'concrete' : 'tableUpdate', 'value' : values },
        success: function(result)
        {
          finalResult = result;    
        }
      }); 
    }
  }
  
  function changeRow(variable) {
        $('.vorstufeToUpdate').hide();
        $('.vorstufeToChange').show();
        var value = variable.attr('id');
        var rowId = variable.parent().attr('id');
        var next = variable.next();
        variable.hide();
        next.show();
        next.find('input:first').focus();
        next.find('input:first').select();
    }
  
  function checkTypeName(name, previous) {
        var projectId = $( '#hiddenProjectId').val();
        if (window.location.href == urlPath) {
            console.log('No project at this time');
        } else {
            var path = "../Api/Select/";
	    var url = path + 'Art-' + name;
          $.ajax({url: url,
            type: "get",
            success: function(result)
            {
              if(result != 'false') {
                name = result;
                previous.text( name );
              } else {
                console.log('No description currently available');
              }
            }
          }); 
        }
    }
  
  function columnChange(variable) {
    variable.children().css('border-color', '');
    var value = variable.attr('id');
    if (value == 'employee') {
	var select = variable.find(":selected");
	var name = select.attr("id");
	var employeeName = select.text();
    } else {
	var name = variable.children().val();
    }
    var previous = variable.prev();
    if (value == 'amount') {
      name = name.replace(",", ".");
      function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
      var check = isNumber(name);
      if (check == false) {
        //variable.children().val(previous.text());
        variable.children().css('border-color', 'red');
	variable.children().focus();
	variable.children().select();
        return false;
      }
      var name2 = name.split(".");
      if (name2[1] == null) {
        name = name + '.00';
      } else if (name2[1].length == 1) {
        name = name + '0';
      } else if (name2[1].length == 0) {
        name = name + '00';
      }
    } else if( value == 'type' ) {
        var name = variable.find(":selected").attr("id");
        if (name == "none") {
            return false;
        }
    } else if (value == 'performanceTime') {
         var exploded = name.split("/");
         name = exploded[2] + '/' + exploded[1] + '/' + exploded[0];
    }
    var rowId = variable.parent().attr('id');
    var date = value + '-' + name;
    changeDate(date, rowId);
    var timerId = setInterval(function() {
	     if(finalResult !== null) {
		if(finalResult == 'done') {
    if (value == 'amount') {
      previous.text( name );
      var projectId = $( '#hiddenProjectId').val();
      getAmount(projectId);
    } else if (value == 'type') {
      checkTypeName(name, previous);
    } else if (value == 'performanceTime') {
       var exploded = name.split("/");
       name = exploded[2] + '/' + exploded[1] + '/' + exploded[0];
       previous.text( name );
    } else if (value == 'employee') {
       previous.text( employeeName );
    } else {
      previous.text( name );
    }
    variable.hide();
    previous.show();
    }
	     clearInterval(timerId);
	     } else {
		console.log(finalResult);
	     }
	 }, 1500);
  }
  
  function getAmount(projectId) {
    if (window.location.href == urlPath) {
      console.log('No project at this time');
    } else {
      var path = "../Api/Amount/";
    }      
    $.ajax({url: path + 'Vorstufe-' + projectId,
      type: "get",
      success: function(result)
      {
        if(result != 'false') {
          $( '#totalVorstufe' ).text(result + ' EURO');
        } else {
          console.log('No description currently available');
        }
      }
    });
  }
  
  function getRowId(variable) {
       $("tr.rowsVorstufe").css('background-color', "#f9f9f9");
       var idVal = variable.attr('id');
       variable.css('background-color', "rgb(238, 193, 213)");
       $('.deleteButtonVorstufe').attr('id', idVal);
       $('.deleteButtonVorstufe').prop('disabled', false);
    }

  $( "#newVorstufeButton" ).click(function() {
      $( '#hiddenTrVorstufe' ).fadeIn( 'slow' );
      $( '#newVorstufeButton' ).hide();
      $( '#saveVorstufeButton' ).show();
      $( '#hideVorstufeButton' ).show();
      $("tr.rowsVorstufe").css('background-color', "#f9f9f9");
      $('.deleteButtonVorstufe').prop('disabled', true);
      $('.deleteButtonVorstufe').attr('id', '');
    });

    $( "#saveVorstufeButton" ).click(function() {
	$("tr.rowsVorstufe").css('background-color', "#f9f9f9");
        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }
        $( '.vorstufeClassDiv' ).removeClass('form-group has-error').addClass('form-group');
        var type = $('select[name=hiddenVorstufeType]').find('option:selected').attr('id');
        var performanceTime = $('input[name=hiddenVorstufeDate]').val();
	if (performanceTime.length == 0) {
	    var error = true;
            $( '#vorstufeZeroDiv' ).removeClass('form-group').addClass('form-group has-error');
	}
	var timeArray = performanceTime.split("/");
	var performanceTime2 = timeArray[2] + '/' + timeArray[1] + '/' + timeArray[0];
        var employee = $('select[name=hiddenVorstufeEmployee]').find('option:selected').attr('id');
	var employeeName = $( "select[name=hiddenVorstufeEmployee] option:selected" ).text();
        var description = $('input[name=hiddenVorstufeDescription]').val();
        var timeProposal = $('input[name=hiddenVorstufeTimeProposal]').val();
        var timeReal = $('input[name=hiddenVorstufeTimeReal]').val();
        var timeSettlement = $('input[name=hiddenVorstufeTimeSettlement]').val();
        var amount = $('input[name=hiddenVorstufeAmount]').val();
        var settlement = $('input[name=hiddenVorstufeSettlement]').is(":checked");
        if (settlement == true) {
            settlement = 1;
        } else {
            settlement = 0;
        }
        var firstCheck = isNumber(timeProposal);
        var secondCheck = isNumber(timeReal);
        var thirdCheck = isNumber(timeSettlement);
        var forthCheck = isNumber(amount);
        if (firstCheck == false) {
            if (timeProposal.length != 0) {
                var error = true;
                $( '#vorstufeFirstAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (secondCheck == false) {
            if (timeReal.length != 0) {
                var error = true;
                $( '#vorstufeSecondAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (thirdCheck == false) {
            if (timeSettlement.length != 0) {
                var error = true;
                $( '#vorstufeThirdAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (forthCheck == false) {
            if (amount.length != 0) {
                var error = true;
                $( '#vorstufeForthAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (error == true) {
          return false;
        }
        var projectId = $( '#hiddenProjectId' ).val();
	var employeeSelect = $( "#hiddenVorstufeEmployee" ).clone();
	var values = 'insert-Vorstufe-' + projectId + '<>' + type + '<>' + performanceTime2 + '<>' + employee + '<>' + description + '<>' + timeProposal + 		'<>' + timeReal + '<>' + timeSettlement + '<>' + amount + '<>' + settlement;
	var url = document.location.href;
        var lastChar = url.substr(-11);
        if (lastChar == '/Erfassung/') {
            return false;
        } else {
	  var path = "../Api/Row/";
            $.ajax({url: path,
                type: "post",
                data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
                success: function(result)
                {
                  if (result == 'false') {
                    console.log('Data could not be saved');
                } else {
		  var clonedTable = $( '#hiddenVorstufeEmployee').clone();
		  clonedTable.attr("id", "vorstufeClonedTable");
		  clonedTable.attr("name", "vorstufeClonedTable");
                    var tableRow = '<tr class="clickable-row rowsVorstufe" name="' + result + '" id="' + result + '">';
                    tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="type">';
                    if (type == 1) {
                      tableRow += 'Satz';
                  } else if (type == 2){
                      tableRow += 'AV';
                  } else if (type == 3){
                      tableRow += 'Proof/Andruck';
                  } else if (type == 4){
                      tableRow += 'Litho';
                  } else if (type == 5){
                      tableRow += 'Problem';
                  } else if (type == 6){
                      tableRow += 'sonstiges';
                  } else {
                      tableRow += '<i>Keine Daten</i>';
                  }
                  tableRow += '</div>';
                  tableRow += '<div class="vorstufeToUpdate" id="type" style="display: none;">';
		  tableRow += '<select class="form-control" id="type" name="hiddenVorstufeType">';
		  if (type == 1) {
                      tableRow += '<option id="1">Satz</option>';
                  } else if (type == 2){
                      tableRow += '<option id="2">AV</option>';
                  } else if (type == 3){
                      tableRow += '<option id="3">Proof/Andruck</option>';
                  } else if (type == 4){
                      tableRow += '<option id="4">Litho</option>';
                  } else if (type == 5){
                     tableRow += '<option id="5">Problem</option>';
                  } else if (type == 6){
                     tableRow += '<option id="6">sonstiges</option>';
                  } else {
                     tableRow += '<option id="none">Bitte wählen</option>';
                  }
                  if (type != 1) {
		      tableRow += '<option id="1">Satz</option>';
		  }
                  if (type != 2) {
		      tableRow += '<option id="2">AV</option>';
		  }
                  if (type != 3) {
		      tableRow += '<option id="3">Proof/Andruck</option>';
		  }
		  if (type != 4) {
		      tableRow += '<option id="4">Litho</option>';
		  }
		  if (type != 5) {
		      tableRow += '<option id="5">Problem</option>';
		  }
		  if (type != 6) {
		      tableRow += '<option id="6">sonstiges</option>';
		  }
		  tableRow += '</select></div></td>';
		  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="performanceTime">';
		  if (textDate.length == 0) {
		    tableRow += '<i>Keine Daten</i>';
		  } else {
		    tableRow +=  performanceTime; 
		  }
		  tableRow += '</div>';
		  tableRow += '<div class="vorstufeToUpdate" id="performanceTime" style="display: none;"><input type="text" class="form-control datepicker" size="9" placeholder="Bitte wählen" name="hiddenVorstufePerformanceTime" value="' + performanceTime + '" /></div></td>';
                  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="employee">';
		  if (employee.length == 0) {
                      tableRow += '<i>Keine Daten</i>';
                  } else {
                      tableRow +=  employeeName; 
                  } 
		  tableRow += '</div>';
		  tableRow += '<div class="vorstufeToUpdate toBeCloned'+ result +'" id="employee" style="display: none;">';
		  tableRow += '</div></td>';
		  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="description">';
                  if (description.length == 0) {
                      tableRow += '<i>Keine Daten</i>';
                  } else {
                      tableRow +=  description; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="vorstufeToUpdate" id="description" style="display: none;"><input type="text" class="form-control" name="descriptionHidden" value="' + description + '" /></div>';
                  tableRow += '</td>';
		  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="timeProposal">';
                  if (timeProposal.length == 0) {
                      tableRow += '<i>Keine Daten</i>';
                  } else {
                      tableRow +=  timeProposal; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="vorstufeToUpdate" id="timeProposal" style="display: none;"><input type="text" class="form-control" name="timeProposalHidden" value="' + timeProposal + '" /></div>';
                  tableRow += '</td>';
		  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="timeReal">';
                  if (timeReal.length == 0) {
                      tableRow += '<i>Keine Daten</i>';
                  } else {
                      tableRow +=  timeReal; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="vorstufeToUpdate" id="timeReal" style="display: none;"><input type="text" class="form-control" name="timeRealHidden" value="' + timeReal + '" /></div>';
                  tableRow += '</td>';
		  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="timeSettlement">';
                  if (timeSettlement.length == 0) {
                      tableRow += '<i>Keine Daten</i>';
                  } else {
                      tableRow +=  timeSettlement; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="vorstufeToUpdate" id="timeSettlement" style="display: none;"><input type="text" class="form-control" name="timeSettlement" value="' + timeSettlement + '" /></div>';
                  tableRow += '</td>';
		  tableRow += '<td id="' + result + '"><div class="vorstufeToChange" id="amount">';
                  if (amount.length == 0) {
                      tableRow += '<i>Keine Daten</i>';
                  } else {
                      tableRow +=  amount + ' EURO'; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="vorstufeToUpdate" id="amount" style="display: none;"><input type="text" class="form-control" name="amountHidden" value="' + amount + '" /></div>';
                  tableRow += '</td>';
		  tableRow += '<td class="v-center" id="' + result + '"><div class="vorstufeToChange" id="settlement">';
                  tableRow += '<input type="checkbox" id="settlement" name="hiddenSettlement"';
                  if (settlement == 1) {
                    tableRow += 'checked ="checked"';
                  }
                  tableRow += '></div>';
                  tableRow += '<div class="checkbox vorstufeToUpdate" id="settlement" style="display: none;"><input type="checkbox" name="hiddenVorstufeSettlement"';
                  if (settlement == 1) {
                    tableRow += 'checked ="checked"';
                  }
                  tableRow += '></div></td>';
                  tableRow += '</tr>'; 
                  $( '#hideVorstufeButton' ).hide();   
                  $( '#saveVorstufeButton' ).hide(); 
                  $( "#newVorstufeButton" ).show();
                  $( '.deleteButtonVorstufe' ).show();
                  var rows = document.getElementById("vorstufeMainTable").rows.length;
                  var number = rows -2;
                  $( '#hiddenTrVorstufe' ).hide();               
                  $( '#vorstufeMainTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
		  $('input[name=hiddenVorstufePerformanceTime]').datepicker( "destroy" );
		  $('input[name=hiddenVorstufePerformanceTime]').datepicker({ dateFormat: "dd/mm/yy" });
		  var toBeCloned = $( '.toBeCloned'+ result );
		  toBeCloned.append(clonedTable);
		  /*
		  $( '#vorstufeClonedTable' ).on('change', function (e) {
		      var employee = $('#vorstufeClonedTable').find('option:selected').attr('id');
		      var employeeName = $( "#vorstufeClonedTable option:selected" ).text();
		      var var2 = $(this).parent().parent().attr('id');
		      var var1 = 'employee-' + employee;
		      changeDate(var1, var2);

		      

		      var next = $( this ).closest("#employee");
		      $( this ).parent().hide();
		      next.text(employeeName);
		      next.show();
		      next.find('input:first').focus();
		      next.find('input:first').select();
		  });
	    */

                  $("#vorstufeTable").on("click", "tr", function(){
                      getRowId($(this));
                  });
                  $( ".vorstufeToChange" ).dblclick(function() {
                      changeRow($(this));
                  });
                  $('.vorstufeToUpdate').change(function() {
                      columnChange($(this));
                  });
                  $('input[name=hiddenSettlement]').change(function() {
                      checkCheckbox($(this));
                  });
                  getAmount(projectId);
		  $('select[name=hiddenVorstufeType]').prop("selected", false);
		  $('input[name=hiddenVorstufeDate]').val('');
		  $('select[name=hiddenVorstufeEmployee]').prop("selected", false);
		  $('input[name=hiddenVorstufeDescription]').val('');
		  $('input[name=hiddenVorstufeTimeProposal]').val('');
		  $('input[name=hiddenVorstufeTimeReal]').val('');
		  $('input[name=hiddenVorstufeTimeSettlement]').val('');
		  $('input[name=hiddenVorstufeAmount]').val('');
		  $('input[name=hiddenVorstufeSettlement]').attr('checked', false);
                }
            }
        });
    }
    });
    
     $("tr.rowsVorstufe").click(function(){
       $("tr.rowsVorstufe").css('background-color', "#f9f9f9");
       var idVal = $(this).attr('id');
       $(this).css('background-color', "rgb(238, 193, 213)");
       $('.deleteButtonVorstufe').attr('id', idVal);
       $('.deleteButtonVorstufe').prop('disabled', false);
    });

    $( ".deleteButtonVorstufe" ).click(function() {
        var idValue = $('.deleteButtonVorstufe').attr('id');
        var values = 'delete-Vorstufe-' + idValue;
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
                        var toDelete = $('.rowsVorstufe[name=' + idValue + ']');
                        toDelete.remove();
			$('.deleteButtonDrucksachen').prop('disabled', true);
                        $('.deleteButtonDrucksachen').attr('id', '');
                        var projectId = $( '#hiddenProjectId' ).val();
                        getAmount(projectId);
                    }
                }
            }); 
        }
    });
     
    $( "#hideVorstufeButton" ).click(function() {
        $( '#hiddenTrVorstufe' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#newVorstufeButton' ).show();
        $( '#saveVorstufeButton' ).fadeOut('slow');
    });

    $( ".vorstufeToChange" ).dblclick(function() {
      changeRow($(this));
    });

    $('input[name=vorstufeSettlement]').change(function() {
      var ifChecked = $(this).is(":checked");
      var value = $( this ).attr('id');
      var rowId = $( this ).parent().attr('id');
      if (ifChecked == true) {
        var checkbox = 1;
      } else {
        var checkbox = 0;
      }
      var date = value + '-' + checkbox;
      changeDate(date, rowId);    
    });
    
    $('.vorstufeToUpdate').change(function() {
      $( this ).children().css('border-color', '');
      var name = $( this ).children().val();
      var value = $( this ).attr('id');
      var previous = $( this ).prev();
      function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
      if( value == 'type' || value == 'employee') {
        var name = $(this).find(":selected").attr("id");
        if (name == "none") {
          return false;
        }
      } else if (value == 'amount') {
        name = name.replace(",", ".");
        var check = isNumber(name);
	if (check == false) {
          $( this ).children().css('border-color', 'red');
	  $( this ).children().focus();
	  $( this ).children().select();
          return false;
        }
        var name2 = name.split(".");
        if (name2[1] == null) {
          name = name + '.00';
        } else if (name2[1].length == 1) {
          name = name + '0';
        } else if (name2[1].length == 0) {
          name = name + '00';
        }
      } 
      if (value == 'timeProposal' || value == 'timeReal' || value == 'timeSettlement') {
        var check = isNumber(name);
        if (check == false) {
          //$( this ).children().val(previous.text());
          $( this ).children().css('border-color', 'red');
	  $( this ).children().focus();
	  $( this ).children().select();
          return false;
        }
      }
      if (value == 'performanceTime') {
         var exploded = name.split("/");
         name = exploded[2] + '/' + exploded[1] + '/' + exploded[0];
      }
      var rowId = $( this ).parent().attr('id');
      var date = value + '-' + name;
      changeDate(date, rowId);
      var timerId = setInterval(function() {
	if(finalResult !== null) {
		if(finalResult == 'done') {
      if( value == 'amount') {
        previous.text( name + ' EURO' );
        var projectId = $( '#hiddenProjectId').val();
        if (window.location.href == urlPath) {
          console.log('No project at this time');
        } else {
          var path = "../Api/Amount/";
          $.ajax({url: path + 'Vorstufe-' + projectId,
            type: "get",
            success: function(result)
            {
              if(result != 'false') {
                $( '#totalVorstufe' ).text(result + ' EURO');
              } else {
                console.log('No total amount available');
              }
            }
          }); 
        }
      } else if (value == 'performanceTime') {
        var exploded = name.split("/");
        name = exploded[2] + '/' + exploded[1] + '/' + exploded[0];
        previous.text( name );
      } else if( value == 'type' || value == 'employee' ) {
        var projectId = $( '#hiddenProjectId').val();
        if (window.location.href == urlPath) {
          console.log('No project at this time');
        } else {
          var path = "../Api/Select/";
          if( value == 'type') {
            var url = path + 'Art-' + name;
          } else {
              var url = path + 'Benutzer-' + name;
          }
          $.ajax({url: url,
            type: "get",
            success: function(result)
            {
              if(result != 'false') {
                name = result;
                previous.text( name );
              } else {
                console.log('No description currently available');
              }
            }
          }); 
        }
      } else {
        previous.text( name );
      }
      $('.vorstufeToUpdate').hide();
      previous.show();
      }
	     clearInterval(timerId);
	     } else {
		console.log(finalResult);
	     }
       }, 1500);
    });
     
});