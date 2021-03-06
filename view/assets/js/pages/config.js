$( document ).ready(function() {
  
    var finalResult;
  
    function changeDate(singleAction, data, value) {
	  var path = "Api/Config/";
	  $.ajax({url: path,
	    type: "post",
	    data: { 'action' : 'ajax', 'concrete' : 'config', 'singleAction': singleAction, 'object' : data, 'value' : value },
	    success: function(result) {
			if (result == 'false') {
                $('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
                return false;
            }  else {
                console.log(result);
                finalResult = result;
            } 
	    }
	  }); 
    }
    
    function changeRow(object) {
    	var sth = $('.configToUpdate').val();
    	$('.configToUpdate').parent().text(sth);
    	$('.configToUpdate').hide();
    	$('.configToChange').show();
    	var value = object.attr('id');
    	var rowId = object.parent().attr('id');
    	var next = object.next();
    	var column = object.attr('id');
    	var value = object.text();
    	object.text('');
    	object.append('<input type="text" name="sth" class="form-control configToUpdate" id="' + column + '" value="' + value +'" />');
    	$( '.configUserToChange' ).focus();
    	var value = next.find('input:first').attr('id');
    	if (value == 'name') {
    		next.find('input:first').focus();
    	}
    	$('.configToUpdate').change(function() {
    		$( this ).removeAttr( 'style' );
    		var value = $(this).val();
    		if (value == '') {
    			$(this).css('border-color', '#a94442');
	 			$(this).focus();
	 			return false;
    		}
    		var column = $(this).attr('id');
    		var previous = $(this).prev();
    		var rowId = $(this).parent().parent().attr('id');
    		var data = 'Zahlungsziel<>' + column + '<>' + rowId;
    		var single = 'configUpdate';
    		$(this).prop('disabled', true);
    		changeDate(single, data, value);
    		var timerId = setInterval(function() {
    			if(finalResult !== null) {
    				if(finalResult == 'success') {
    					if (value == 0) {
    						value = '--';
    					}
    					$( '.configToUpdate' ).hide();
    					$(this).prop('disabled', false);
    					object.text(value);
    				}
    				clearInterval(timerId);
    			} else {
    				$('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
    				return false;
    			}
    		}, 1500);
    	});
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
    	}
    }
    
    function columnChange(object) {
     object.children().removeAttr( 'style' );
	 var value = object.children().val();
	 if (value == '') {
	 	object.children().css('border-color', '#a94442');
	 	object.children().focus();
	 	return false;
	 }
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
	 object.children().prop('disabled', true);
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
		  object.children().prop('disabled', false);
		  previous.show();
		}
	    	clearInterval(timerId);
	    } else {
			$('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
			return false;
	    }
	 }, 1500);
    }
    
    function configEmailToCheck(mail) {
	    var column = 'benutzer';
            var path = "Api/Mail/";
	    $.ajax({url: path + column + '<>' + mail, 
		  type: "get",
		  success: function(emailCheck)
                  { 
		    alert(emailCheck);
                        if (emailCheck == 'false') {
                            $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                            return false;
                        }  else {
			    if (emailCheck != 'no match') {
				$( '#userMailDiv' ).removeClass('form-group').addClass('form-group has-error');
				$( '#hiddenPersonMailDiv' ).children().children().css('color', 'red');
				return false;
			     } else {
			        $( '#userMailDiv' ).removeClass('form-group has-error').addClass('form-group');
				$( '#hiddenPersonMailDiv' ).children().children().removeAttr( 'style' );
				return 'free';
			     }
			}
		   }
	    });
    }
    
    function getRowId(object) {
	$("tr.rowsConfigPaymentOpt").css('background-color', "#f9f9f9");
	var idVal = object.attr('id');
	object.css('background-color', "#e9e9e9");
	$('.deletePaymentOpt').attr('id', idVal);
	$('.deletePaymentOpt').prop('disabled', false);
    }
    
    function getUserRowId(object) {
	$("tr.rowsConfigUser").css('background-color', "#f9f9f9");
	var idVal = object.attr('id');
	object.css('background-color', "#e9e9e9");
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
    
    $('.configUserToUpdate').change(function() {
	 columnChange($( this ));
    });
    
    $( "#saveUser" ).click(function() {
	  $( '#userNameDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#userMailDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#userPassDiv' ).removeClass('form-group has-error').addClass('form-group');
	  function validateEmail(email) 
		{
		    var re = /\S+@\S+\.\S+/;
		    return re.test(email);
		}
	  var name = $( '#hiddenConfigUserName' ).val();
	  var password = $( '#hiddenConfigUserPassword' ).val();
	  if (name == '') {
	      var error = true;
              $( '#userNameDiv' ).removeClass('form-group').addClass('form-group has-error');
	  } 
	  if (password == '') {
	      var error = true;
              $( '#userPassDiv' ).removeClass('form-group').addClass('form-group has-error');
	  }
	  var mail = $( '#hiddenConfigUserMail' ).val();
	  var mailCheck = validateEmail(mail) ;
	  if (mailCheck == false) {
		var error = true;
                $( '#userMailDiv' ).removeClass('form-group').addClass('form-group has-error');
	  } else {
	    var column = 'benutzer';
            var path = "Api/Mail/";
	    $.ajax({url: path + column + '<>' + mail, 
		  type: "get",
		  success: function(emailCheck)
                  { 
                        if (emailCheck == 'false') {
                            $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                            return false;
                        }  else {
			    if (emailCheck != 'no match') {
				$( '#userMailDiv' ).removeClass('form-group').addClass('form-group has-error');
				$('#ajaxErrorConfig3').parent().append('<div class="has-error" id="ajaxErrorConfig4" style="display: none; float: left; width: 30%; margin-left: 17%;" ><input type="text" class="form-control" id="emailError" value="E-Mail-Adresse schon gespeichert." disabled="disabled" style="margin-top: 1.1%; text-align: center;"/></div>');
				$('#ajaxErrorConfig4').fadeIn('slow').delay(5000).hide(1);
				return false;
			     } else {
			        $( '#userMailDiv' ).removeClass('form-group has-error').addClass('form-group');
				$( '#hiddenConfigUserMail' ).css('color', '');
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
		    $('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
		    return false;
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
		$('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
		return false;
	     }
	 }, 1500);
	  }
			     }
			}
		   }
	    });

	  } 
    });
    
    $( "#savePaymentOpt" ).click(function() {
	  $( '#paymentNameDiv' ).removeClass('form-group has-error').addClass('form-group');
	  $( '#paymentDescDiv' ).removeClass('form-group has-error').addClass('form-group');
	  var name = $( '#hiddenConfigName' ).val();
	  var description = $( '#hiddenConfigDescription' ).val();
	  if (name == '') {
	      var error = true;
              $( '#paymentNameDiv' ).removeClass('form-group').addClass('form-group has-error');
	  }
	  if (description == '') {
	      var error = true;
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
		    $('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
		    return false;
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
		$('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
		return false;
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
		  $('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
		  return false;
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
		  $('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
		  return false;
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
		if (result == 'false') {
            $('#ajaxErrorConfig3').fadeIn('slow').delay(5000).hide(1);
            return false;
        }  else {
            console.log(result);
        } 
	    }
	  }); 
    });  
    
});