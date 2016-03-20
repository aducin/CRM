$( document ).ready(function() {
  
    function changeDate(curDate, drucksache) {
	var values = 'Drucksache-' + drucksache + '-' + curDate;
	if (window.location.href == "http://kluby.local/CRM/Erfassung") {
           alert('No project at this time');
        } else {
           var path = "../Api/Row/";
	   $.ajax({url: path,
               type: "post",
	       data: { 'action' : 'ajax', 'concrete' : 'drucksache', 'value' : values },
               success: function(result)
                 {
		    alert(result);
                 }
	    }); 
        }
    }
  
    $( "#newDrucksache" ).click(function() {
        $( '#hiddenTrDrucksachen' ).fadeIn( 'slow' );
        $( '#newDrucksache' ).hide();
        $( '#hideButtonDrucksache' ).show();
    });

    $("tr.rowsDrucksachen").click(function(){
       $("tr.rowsDrucksachen").css('background-color', "#f9f9f9");
       var idVal = $(this).attr('id');
       $(this).css('background-color', "rgb(238, 193, 213)");
       $('.deleteButtonDrucksachen').attr('id', idVal);
       $('.deleteButtonDrucksachen').prop('disabled', false);
    });

    $( ".deleteButtonDrucksachen" ).click(function() {
        var idValue = $('.deleteButtonDrucksachen').attr('id');
        alert(idValue);
    });

    $( "#hideButtonDrucksache" ).click(function() {
        $( '#hiddenTrDrucksachen' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#newDrucksache' ).show();
    });
    
    $( ".drucksacheToChange" ).dblclick(function() {
	var value = $( this ).attr('id');
	var rowId = $( this ).parent().attr('id');
	var next = $( this ).next();
	$( this ).hide();
	next.show();
    });
    
    $('input[name=drucksachenFinished]').change(function() {
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
    
    $('.drucksacheToUpdate').change(function() {
	var name = $( this ).children().val();
	var value = $( this ).attr('id');
	if (value == 'machine') {
	    var name = $(this).find(":selected").attr("id");
	}
	var rowId = $( this ).parent().attr('id');
	var date = value + '-' + name;
	var previous = $( this ).prev();
	alert(value + name + rowId);
	changeDate(date, rowId);
	if( value == 'amount') {
	    previous.text( name + ' EURO' );
	} else if( value == 'machine' ) {
	    if( name == 1 ){
	      name = 'SpeedMaster';
	    } else {
	      name = 'GTO';
	    }
	    previous.text( name );
	} else {
	    previous.text( name );
	}
    	$( this ).hide();
	previous.show();
    });
});