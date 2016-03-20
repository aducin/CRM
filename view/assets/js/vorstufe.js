$( document ).ready(function() {

  $( "#newButton" ).click(function() {
      //var selfName = $(this).text();
      //if (selfName == 'Neu') {
	    //var clone = $("#typeSelect").clone();
	    //var employeesTable = $("#employeesSelect").clone();
	    //var tableRow = '<tr id="newTrRow"><td id="newTdType"></td><td><input type="text" name="newPerformanceTime" class="form-control datepicker" data-date-format="dd/mm/yyyy" id="newPerformanceTime"></td><td id="newEmployeer"></td><td><input type="text" name="newDescription" class="form-control" ></td><td><input type="text" name="newTimeProposal" class="form-control" ></td><td><input type="text" name="newTimeReal" class="form-control" ></td><td><input type="text" name="newTimeSettlement" class="form-control" ></td><td><input type="text" name="newAmount" class="form-control" placeholder="Preis in Euro" ></td><td><input name="newSettlement" type="checkbox"></td></tr>';   
	    //$("#tableRow").append(tableRow);
	    //$("#newTdType").html(clone);
      $( '#hiddenTr' ).fadeIn( 'slow' );
	    $( '#newButton' ).hide();
      $( '#hideButton' ).show();
    });
    
     $("tr.rows").click(function(){
       $("tr.rows").css('background-color', "#f9f9f9");
       var idVal = $(this).attr('id');
       $(this).css('background-color', "rgb(238, 193, 213)");
       $('.deleteButton').attr('id', idVal);
       $('.deleteButton').prop('disabled', false);
    });
     
    $( ".deleteButton" ).click(function() {
	    var idValue = $('.deleteButton').attr('id');
      alert(idValue);
    });
     
    $( "#hideButton" ).click(function() {
        $( '#hiddenTr' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#newButton' ).show();
         $( '#saveButton' ).fadeOut('slow');
    });
});