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

    $('#termin_korrektur').datepicker("option", "dateFormat", "dd/mm/yy");
    $('#termin_daten').datepicker("option", "dateFormat", "dd/mm/yy");
    $('#termin_proof_andruck').datepicker("option", "dateFormat", "dd/mm/yy");
    
    $( "#newRow" ).click(function() {
      var selfName = $(this).text();
      if (selfName == 'Neu') {
	    var clone = $("#typeSelect").clone();
	    var employeesTable = $("#employeesSelect").clone();
	    var tableRow = '<tr id="newTrRow"><td id="newTdType"></td><td><input type="text" name="newPerformanceTime" class="form-control datepicker" data-date-format="dd/mm/yyyy" id="newPerformanceTime"></td><td id="newEmployeer"></td><td><input type="text" name="newDescription" class="form-control" ></td><td><input type="text" name="newTimeProposal" class="form-control" ></td><td><input type="text" name="newTimeReal" class="form-control" ></td><td><input type="text" name="newTimeSettlement" class="form-control" ></td><td><input type="text" name="newAmount" class="form-control" placeholder="Preis in Euro" ></td><td><input name="newSettlement" type="checkbox"></td></tr>';   
	    $("#tableRow").append(tableRow);
	    $("#newTdType").html(clone);
	    $("#newEmployeer").html(employeesTable);
	    $('#newRow').hide();
	    $('#verstecken').fadeIn('slow');
	    $('#speichernButton').css('margin-left', '7px');
	    $('#speichernButton').fadeIn('slow');
	    $('#speichernButton').attr('id','newRowSave');
	    $('#newPerformanceTime').datepicker();
      } else {
	    alert('error');
      }
    });
    
     $("tr.rows").click(function(){
       $("tr.rows").css('background-color', "#f9f9f9");
       var idVal = $(this).attr('id');
       $(this).css('background-color', "rgb(238, 193, 213)");
       $('.loschenButton').attr('id', idVal);
       $('.loschenButton').prop('disabled', false);
    });
     
     $( ".loschenButton" ).click(function() {
	var idValue = $('.loschenButton').attr('id');
        alert(idValue);
     });
     
     $( "#verstecken" ).click(function() {
	$('#speichernButton').fadeOut('slow');
	$('#newRow').fadeOut('slow');
        alert('hidden');
     });
});