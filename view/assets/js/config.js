$( document ).ready(function() {
  
    var finalResult;
  
    function changeDate(singleAction, data, value) {
	  var path = "Api/Config/";
	  $.ajax({url: path,
	    type: "post",
	    data: { 'action' : 'ajax', 'concrete' : 'config', 'singleAction': singleAction, 'object' : data, 'value' : value },
	    success: function(result) {
		finalResult = result;    
	    }
	  }); 
    }
    
    function changeRow(object) {
	$('.configToUpdate').hide();
        $('.configToChange').show();
        var value = object.attr('id');
        var rowId = object.parent().attr('id');
        var next = object.next();
        object.hide();
        next.show();
        next.find('input:first').focus();
        next.find('input:first').select();
    }
    
    function changeUserRow(object) {
	$('.configUserToUpdate').hide();
        $('.configUserToChange').show();
        var next = object.next();
        object.hide();
	next.show();
	var value = next.find('input:first').attr('id');
	if (value == 'name') {
	    next.find('input:first').focus();
	    next.find('input:first').select();
	}
    }
    
    function columnChange(object) {
	 var value = object.children().val();
	 var rowId = object.parent().attr('id');
	 var column = object.children().attr('id');
	 var previous = object.prev();
	 var table = object.attr('id');
	 if (table == 'user') {
	      var data = 'Benutzer<>' + column + '<>' + rowId;
	 } else if (table == 'rolle_id') {
	      value = object.find(":selected").attr("id");
	      var selectedName = object.find(":selected").text();
	      var data = 'Benutzer<>' + table + '<>' + rowId;
	 } else {
	      var data = 'Zahlungsziel<>' + column + '<>' + rowId;
	 }
	 var single = 'configUpdate';
	 changeDate(single, data, value);
	 var timerId = setInterval(function() {
	     if(finalResult !== null) {
		if(finalResult == 'success') {
		  if (table == 'rolle_id') {
		      previous.text( selectedName );
		      object.hide();
		  } else {
		      previous.text( value );
		  }
		  if (table == 'user') {
		      $( '.configUserToUpdate' ).hide();
		  } else {
		      $( '.configToUpdate' ).hide();
		  }
		  previous.show();
		}
	     clearInterval(timerId);
	     } else {
		console.log(finalResult);
	     }
	 }, 1500);
    }
    
    function getRowId(object) {
	$("tr.rowsConfigPaymentOpt").css('background-color', "#f9f9f9");
	var idVal = object.attr('id');
	object.css('background-color', "rgb(238, 193, 213)");
	$('.deletePaymentOpt').attr('id', idVal);
	$('.deletePaymentOpt').prop('disabled', false);
    }
    
    function getUserRowId(object) {
	$("tr.rowsConfigUser").css('background-color', "#f9f9f9");
	var idVal = object.attr('id');
	object.css('background-color', "rgb(238, 193, 213)");
	$('.deleteUser').attr('id', idVal);
	$('.deleteUser').prop('disabled', false);
    }
    
    $( "#newUser" ).click(function() {
	$('.deleteUser').prop('disabled', true);
	$("tr.rowsConfigUser").css('background-color', "#f9f9f9");
	$( '.hiddenConfigUser' ).fadeIn( 'slow' );
	$( '#newUser' ).hide();
	$( '.deleteUser' ).hide();
	$( '#saveUser' ).show();
	$( '#hideUser' ).show();
    });
  
    $( "#newPaymentOpt" ).click(function() {
	$('.deletePaymentOpt').prop('disabled', true);
	$("tr.rowsConfigPaymentOpt").css('background-color', "#f9f9f9");
	$( '#hiddenConfigPaymentOpt' ).fadeIn( 'slow' );
	$( '#newPaymentOpt' ).hide();
	$( '.deletePaymentOpt' ).hide();
	$( '#savePaymentOpt' ).show();
	$( '#hidePaymentOpt' ).show();
    });
    
    $("tr.rowsConfigUser").click(function(){
	getUserRowId($(this));
    });
  
    $("tr.rowsConfigPaymentOpt").click(function(){
	getRowId($(this));
    });
    
    $( "#hideUser" ).click(function() {
        $( '.hiddenConfigUser' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#newUser' ).show();
	$( '.deleteUser' ).show();
        $( '#saveUser' ).hide();
    });
    
    $( "#hidePaymentOpt" ).click(function() {
        $( '#hiddenConfigPaymentOpt' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#newPaymentOpt' ).show();
	$( '.deletePaymentOpt' ).show();
        $( '#savePaymentOpt' ).hide();
    });
    
    $( ".configToChange" ).dblclick(function() {
	changeRow($( this ));
    });
    
    $( ".configUserToChange" ).dblclick(function() {
	changeUserRow($( this ));
    });
    
    $('.configToUpdate').change(function() {
	 columnChange($( this ));
    });
    
    $('.configUserToUpdate').change(function() {
	 columnChange($( this ));
    });
    
    $( "#saveUser" ).click(function() {
	  $( '#userNameDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#userNameSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
	  $( '#userMailDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#userMailSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
	  $( '#userPassDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#userPassSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
	  function validateEmail(email) 
		{
		    var re = /\S+@\S+\.\S+/;
		    return re.test(email);
		}
	  var name = $( '#hiddenConfigUserName' ).val();
	  var mail = $( '#hiddenConfigUserMail' ).val();
	  var mailCheck = validateEmail(mail) ;
	  if (mailCheck == false) {
		var error = true;
		$( '#userMailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#userMailDiv' ).removeClass('form-group').addClass('form-group has-error');
	  }
	  var password = $( '#hiddenConfigUserPassword' ).val();
	  if (name == '') {
	      var error = true;
	      $( '#userNameSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
              $( '#userNameDiv' ).removeClass('form-group').addClass('form-group has-error');
	  } 
	  if (password == '') {
	      var error = true;
	      $( '#userPassSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
              $( '#userPassDiv' ).removeClass('form-group').addClass('form-group has-error');
	  }
	  if (error == true) {
	      return false;
	  }
	  var role = $('select[name=hiddenRole]').find('option:selected').attr('id');
	  var roleName = $( "select[name=hiddenRole] option:selected" ).text();
	  if (name == '' || mail == '' || password == '' || role == '') {
		console.log('All fields must be filled in');
		return false;
	  } else {
	      var previousroleName = $( this ).prev();
	      var single = 'configSave';
	      var data = 'Benutzer<>' + name + '<>' + mail + '<>' + role;
	      changeDate(single, data, password);
	      var timerId = setInterval(function() {
	      if(finalResult !== null) {
		if(finalResult == 'false') {
		    console.log('Data could not be saved');
		} else {
		    var newId = finalResult;
		    var tableRow = '<tr class="clickable-row rowsVorstufe rowsConfigUser" name="' + newId + '" id="' + newId + '">';
                    tableRow += '<td colspan="2" id="' + newId + '"><div class="configUserToChange" id="user">';
		    if (name.length == 0) {
			tableRow += '<i>keine Daten</i>';
		    } else {
			tableRow +=  name; 
		    }
		    tableRow += '</div>';
		    tableRow += '<div class="configUserToUpdate" id="user" style="display: none;"><input type="text" class="form-control" name="hiddenUserName" id="name" value="' + name + '" /></div>';
		    tableRow += '</td>';
		    tableRow += '<td colspan="2" id="' + newId + '"><div class="configUserToChange" id="description">';
		    tableRow += roleName;
		    tableRow += '</div>';
		    tableRow += '<div class="configUserToUpdate" id="user" style="display: none;">';
		    tableRow += '<select class="form-control vorstufeToUpdate" id="rolle_id">';
		    if (role == 1) {
			tableRow += '<option id=' + role + '>' + roleName + '</option>';
			tableRow += '<option id="2">Direktor</option>';
			tableRow += '<option id="3">Benutzer</option>';
		    } else if (role == 2) {
			tableRow += '<option id=' + role + '>' + roleName + '</option>';
			tableRow += '<option id="1">Admin</option>';
			tableRow += '<option id="3">Benutzer</option>';
		    } else if (role == 3) {
			tableRow += '<option id=' + role + '>' + roleName + '</option>';
			tableRow += '<option id="1">Admin</option>';
			tableRow += '<option id="2">Benutzer</option>';
		    }
		    tableRow += '</select>';
		    tableRow += '</div>';
		    tableRow += '</td>';
		    $( '#hiddenConfigUserName' ).val('');
		    $( '#hiddenConfigUserMail' ).val('');
		    $( '#hiddenConfigUserPassword' ).val('');
		    $( '#hideUser' ).hide();   
		    $( '#saveUser' ).hide(); 
		    $( "#newUser" ).show();
		    $( '.deleteUser' ).show();
		    var rows = document.getElementById("userTable").rows.length;
		    var number = rows -2;
		    $( '.hiddenConfigUser' ).hide(); 
		    $( '#userTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
		    $( ".configUserToChange" ).dblclick(function() {
			changeUserRow($( this ));
		    });
		    $("#userTable").on("click", "tr", function(){
			getUserRowId($(this));
		    });
		    $('.configUserToUpdate').change(function() {
			columnChange($(this));
		    });
		}
	     clearInterval(timerId);
	     } else {
		console.log(finalResult);
	     }
	 }, 1500);
	  }
    });
    
    $( "#savePaymentOpt" ).click(function() {
	  $( '#paymentNameDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#paymentNameSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
	  $( '#paymentDescDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#paymentDescSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
	  var name = $( '#hiddenConfigName' ).val();
	  var description = $( '#hiddenConfigDescription' ).val();
	  if (name == '') {
	      var error = true;
	      $( '#paymentNameSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
              $( '#paymentNameDiv' ).removeClass('form-group').addClass('form-group has-error');
	  }
	  if (description == '') {
	      var error = true;
	      $( '#paymentDescSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
              $( '#paymentDescDiv' ).removeClass('form-group').addClass('form-group has-error');
	  }
	  if (error == true) {
	      return false;
	  }
	  var previous = $( this ).prev();
	  var single = 'configSave';
	  var data = 'Zahlungsziel<>' + name;
	  changeDate(single, data, description);
	  var timerId = setInterval(function() {
	     if(finalResult !== null) {
		if(finalResult == 'false') {
		    console.log('Data could not be saved');
		} else {
		    var newId = finalResult;
		    var tableRow = '<tr class="clickable-row rowsVorstufe rowsConfigPaymentOpt" name="' + newId + '" id="' + newId + '">';
                    tableRow += '<td id="' + newId + '"><div class="configToChange" id="name">';
		    if (name.length == 0) {
			tableRow += '<i>keine Daten</i>';
		    } else {
			tableRow +=  name; 
		    }
		    tableRow += '</div>';
		    tableRow += '<div class="configToUpdate" id="name" style="display: none;"><input type="text" class="form-control" name="hiddenConfigName" id="name" value="' + name + '" /></div>';
		    tableRow += '</td>';
		    tableRow += '<td id="' + newId + '"><div class="configToChange" id="description">';
		    if (description.length == 0) {
			tableRow += '<i>keine Daten</i>';
		    } else {
			tableRow +=  description; 
		    }
		    tableRow += '</div>';
		    tableRow += '<div class="configToUpdate" id="beschreibung" style="display: none;"><input type="text" class="form-control" name="hiddenConfigDescription" id="beschreibung" value="' + description + '" /></div>';
		    tableRow += '</td>';
		    $( '#hiddenConfigName' ).val('');
		    $( '#hiddenConfigDescription' ).val('');
		    $( '#hidePaymentOpt' ).hide();   
		    $( '#savePaymentOpt' ).hide(); 
		    $( "#newPaymentOpt" ).show();
		    $( '.deletePaymentOpt' ).show();
		    var rows = document.getElementById("configPaymentOptTable").rows.length;
		    var number = rows -2;
		    $( '#hiddenConfigPaymentOpt' ).hide(); 
		    $( '#configPaymentOptTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
		    $( ".configToChange" ).dblclick(function() {
			changeRow($( this ));
		    });
		    $("#configPaymentOptTable").on("click", "tr", function(){
			getRowId($(this));
		    });
		    $('.configToUpdate').change(function() {
			columnChange($(this));
		    });
		}
	     clearInterval(timerId);
	     } else {
		console.log(finalResult);
	     }
	 }, 1500);
    });
    
    $( ".deletePaymentOpt" ).click(function() {
	var idValue = $('.deletePaymentOpt').attr('id');
	var single = 'configDelete';
	var data = 'Zahlungsziel';
	changeDate(single, data, idValue);
	var timerId = setInterval(function() {
	     if(finalResult == 'false') {
		  console.log('Object could not be deleted');
	     } else {
		  var toDelete = $('.rowsConfigPaymentOpt[name=' + idValue + ']');
		  toDelete.remove();
		  $('.deletePaymentOpt').prop('disabled', true);
                  $('.deletePaymentOpt').attr('id', '');
	     }
	     clearInterval(timerId);
	 }, 1500);
    });
    
    $( ".deleteUser" ).click(function() {
	var idValue = $( this ).attr('id');
	var single = 'configDelete';
	var data = 'Benutzer';
	changeDate(single, data, idValue);
	var timerId = setInterval(function() {
	     if(finalResult == 'false') {
		  console.log('Object could not be deleted');
	     } else {
		  var toDelete = $('.rowsConfigUser[name=' + idValue + ']');
		  toDelete.remove();
		  $('.deleteUser').prop('disabled', true);
                  $('.deleteUser').attr('id', '');
	     }
	     clearInterval(timerId);
	 }, 1500);
    });
    
    $( "#configStandardText" ).change(function() {
	  var text = $( this ).val();
	  var path = "Api/Config/";
	  $.ajax({url: path,
	    type: "post",
	    data: { 'action' : 'ajax', 'concrete' : 'config', 'singleAction': 'standardText', 'object' : text },
	    success: function(result) {
		console.log(result);
	    }
	  }); 
    });  
    
});