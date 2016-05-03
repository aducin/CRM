$( document ).ready(function() {
  
    function isInteger(value)      
        {       
            num = value.trim();         
            return !(value.match(/\s/g)||num==""||isNaN(num)||(typeof(value)=='number'));        
        }
        
    function validateEmail(email) 
        {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
	}
    
    function changeDate(curDate, project) {
        if (window.location.href == urlPath) {
            console.log('No project at this time');
        } else {
            var path = "../Api/Dates/";
            $.ajax({url: path,
                type: "post",
                data: { 'action' : 'ajax', 'concrete' : 'dates', 'value' : project, 'singleAction' :  curDate},
                success: function(result)
                {   
                if (result == 'false') {
                        $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                        $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
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
    /*
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
                    var clientEmployee = $('input[name=ansprechpartnerBasic]').attr('id');
                    if (table == 'ansprechpartner') {
                        if (clientEmployee == rowId) {
                            if (value == 'name' || value == 'vorname') {
                                if (value == 'name') {
                                    var brother = variable.parent().next().children().text();
                                    var complete = name + ' ' + brother;
                                    $('input[name=ansprechpartnerBasic]').val(complete);
                                } else if (value == 'vorname') {
                                    var brother = variable.parent().prev().children().text();
                                    var complete = brother + ' ' + name;
                                    $('input[name=ansprechpartnerBasic]').val(complete);
                                }
                            }
                        } 
                    } else {
                        var searchedName = variable.parent().parent().first().children().children().html();
                        var searchedDepartment = variable.parent().parent().children('td').eq(1).children().html();
                        var searchedAddress = variable.parent().parent().children('td').eq(2).children().html();
                        //var searchedAddress2 = variable.parent().parent().children('td').eq(3).children().html();
                        var searchedCode = variable.parent().parent().children('td').eq(4).children().html();
                        var searchedPlace = variable.parent().parent().children('td').eq(5).children().html();
                        var addressBeforeUpdate = searchedName + ': ' + searchedDepartment + ' - ' + searchedAddress + ', ' + searchedCode + ' ' + searchedPlace;
                    } 
                    previous.html( name );
                    if (table == 'rechnungsadresse') {
                        var curAddress = $( '#selectToBeDeleted' ).children().children().children().first().text();
                        if (curAddress == addressBeforeUpdate) {
                            var searchedName = variable.parent().parent().first().children().children().html();
                            var searchedDepartment = variable.parent().parent().children('td').eq(1).children().html();
                            var searchedAddress = variable.parent().parent().children('td').eq(2).children().html();
                            //var searchedAddress2 = variable.parent().parent().children('td').eq(3).children().html();
                            var searchedCode = variable.parent().parent().children('td').eq(4).children().html();
                            var searchedPlace = variable.parent().parent().children('td').eq(5).children().html();
                            var finalAdress = searchedName + ': ' + searchedDepartment + ' - ' + searchedAddress + ', ' + searchedCode + ' ' + searchedPlace;
                            var curAddress = $( '#selectToBeDeleted' ).children().children().first().prop('title', finalAdress);
                            var curAddress = $( '#selectToBeDeleted' ).children().children().eq(1).children().children().first().children().children().text(finalAdress);
                            var curAddress = $( '#selectToBeDeleted' ).children().children().children().first().text(finalAdress);
                        }
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
    */
    
    function description(path, column, value) {
        $.ajax({url: path + '' + column + '<><>' + value,
            type: "get",
            success: function(result)
            {
              if(result != 'false') {
                    console.log(result);
              } else {
                $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
              }
            }
          }); 
    }

    function emailToCheck(mail) {
    var column = 'auftraggeber';
        if (window.location.href == urlPath) {
               var path = "Api/Mail/";
            } else {
                var path = "../Api/Mail/";
        }
        $.ajax({url: path + column + '<>' + mail, 
          type: "get",
          success: function(result)
                  { 
                        if (result == 'false') {
                            $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                            return true;
                        }  else {
                if (result != 'no match') {
                $( '#newPopupMail' ).css('border-color', '#a94442');
                $( '#newMailErrorSpan' ).text('Email-Adresse schon gespeichert');
                return true;
                 } else {
                    $( '#newPopupMail' ).css('border-color', '');
		    $( '#newMailErrorSpan' ).text('');
		    return false;
                 }
            }
           }
        });
    }

    $( "#newBearbeitenButtonAddress" ).click(function() {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
        $('.deleteBearbeitenButtonAddress').prop('disabled', true);
    	$( '#hiddenBearbeitenTrAddress' ).fadeIn( 'slow' );
    	//$( '#hideBearbeitenButtonAddress' ).show();
        $( this ).prop('disabled', true);
    });
    
    $( "#newBearbeitenButtonPerson" ).click(function() {
        $("tr.rowsBearbeitenPerson").removeAttr( 'style' );
        $('.deleteBearbeitenButtonPerson').prop('disabled', true);
    	$( '#hiddenBearbeitenTrPerson' ).fadeIn( 'slow' );
    	//$( '#hideBearbeitenButtonPerson' ).show();
        $( this ).prop('disabled', true);
    });
    
    $( "#newButtonAddress" ).click(function() {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
    	$( '#hiddenNewTrAddress' ).fadeIn( 'slow' );
    	//$( '#hideButtonAddress' ).show();
        $( this ).prop('disabled', true);
    });

    $( "#newButtonContact" ).click(function() {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
        $( '#hiddenNewTrContact' ).fadeIn( 'slow' );
        //$( '#hideButtonContact' ).show();
        $( this ).prop('disabled', true);
    });

    $("tr.rowsBearbeitenAddress").click(function(){
        rowClick($(this));
    });
    
    $("tr.rowsBearbeitenPerson").click(function(){
    	rowPersonClick($(this));
    });
    
    $("tr.rowsNewAddress").click(function(){
    	$("tr.rowsNewAddress").removeAttr( 'style' );
    	var idVal = $(this).attr('id');
    	$(this).css('background-color', "#e9e9e9");
    	$('.deleteNewButtonAddress').attr('id', idVal);
    	$('.deleteNewButtonAddress').prop('disabled', false);
    });
    
    //$( "#hideBearbeitenButtonAddress" ).click(function() {
    //	$( '#hiddenBearbeitenTrAddress' ).fadeOut( 'slow' );
    //    $('#newBearbeitenButtonAddress').prop('disabled', false);
    //	$( this ).hide();
    //});
    
    //$( "#hideBearbeitenButtonPerson" ).click(function() {
    //	$( '#hiddenBearbeitenTrPerson' ).fadeOut( 'slow' );
    //    $('#newBearbeitenButtonPerson').prop('disabled', false);
    //	$( this ).hide();
    //});
    
    //$( "#hideButtonAddress" ).click(function() {
    //    $( '#newButtonAddress' ).prop('disabled', false);
    //	$( '#hiddenNewTrAddress' ).fadeOut( 'slow' );
    //	$( this ).hide();
    //});

    //$( "#hideButtonContact" ).click(function() {
    //    $( '#newButtonContact' ).prop('disabled', false);
    //    $( '#hiddenNewTrContact' ).fadeOut( 'slow' );
    //    $( this ).hide();
    //});
    
    $( ".bearbeitenAddressToChange" ).dblclick(function() {
	   changeRow($(this), 'Address');
    });
    
    $( ".bearbeitenPersonToChange" ).dblclick(function() {
	   changeRow($(this), 'Person');
    });
    
    $('.bearbeitenAddressToUpdate').change(function() {
	   columnChange($(this));
    });

    $('.bearbeitenPersonToUpdate').change(function() {
        columnChange($(this));
    });

    $('.hiddenBearbeitenAddress').change(function() {
        popupAddressAdd( $( this ));
    });
    
    $('.hiddenNewAddressTr').change(function() {
        var error = false;
        $( '.hiddenNewAddressTr' ).removeClass('form-group has-error').addClass('form-group');
        var addressName = $('input[name=hiddenAddressName]').val();
        var abteilung = $('input[name=hiddenAddressAbteilung]').val();
        var anschrift = $('input[name=hiddenAddressAnschrift]').val();
        var anschrift2 = $('input[name=hiddenAddressAnschrift2]').val();
        var plz = $('input[name=hiddenAddressPlz]').val();
        var ort = $('input[name=hiddenAddressOrt]').val();
        var clientId = $( '#hiddenClientId' ).val();
        if (addressName == '') {
            error = true;
            $( '#newAddressNameDiv' ).addClass('form-group has-error').addClass('form-group');
        } 
        if (abteilung == '') {
            error = true;
            $( '#newAddressAbteilungDiv' ).addClass('form-group has-error').addClass('form-group');
        } 
        if (anschrift == '') {
            error = true;
            $( '#newAddressAnschriftDiv' ).addClass('form-group has-error').addClass('form-group');
        } 
        if (plz == '') {
            error = true;
            $( '#newAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
        } else {
            plzName = plz.replace('-', '');
            var check = isInteger(plzName);
            if (check == false) {
                error = true;
                $( '#newAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
            } else if (plzName.length < 5 || plzName.length > 5) {
                error = true;
                $( '#newAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
            }        
        }
        if (ort == '') {
            error = true;
            $( '#newAddressOrtDiv' ).addClass('form-group has-error').addClass('form-group');
        } else if (ort.length < 3) {
            error = true;
            $( '#newAddressOrtDiv' ).addClass('form-group has-error').addClass('form-group');
        }

        if (error == true) {
            return false;
        } else {
            var singleAction = 'clientInsert';
            var project = 'Rechnungsadressen<>' + addressName + '<>' +abteilung + '<>' + anschrift + '<>' + anschrift2 + '<>' + plz + '<>' + ort + '<>' + clientId;
            if (window.location.href == urlPath) {
               var path = "Api/Dates/";
            } else {
                var path = "../Api/Dates/";
            }
            $.ajax({url: path,
                type: "post",
                data: { 'action' : 'ajax', 'concrete' : 'dates', 'value' : project, 'singleAction' :  singleAction},
                success: function(result)
                {   
                    if(result !== 'false') {
                        var rowId = result;
                        var tableRow = '<tr class="clickable-row rowsNewAddress2"  name="' + rowId + '" id="' + rowId + '">';
                        tableRow += '<td id="' + rowId + '"><div class="newAddressToChange" id="name">';
                        if (addressName.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += addressName; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newAddressToUpdate" id="name" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + addressName + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newAddressToChange" id="abteilung">';
                        if (abteilung.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += abteilung; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newAddressToUpdate" id="abteilung" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + abteilung + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newAddressToChange" id="anschrift">';
                        if (anschrift.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += anschrift;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newAddressToUpdate" id="anschrift" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + anschrift + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newAddressToChange" id="anschrift2">';
                        if (anschrift2.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += anschrift2;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newAddressToUpdate" id="anschrift2" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + anschrift2 + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newAddressToChange" id="plz">';
                        if (plz.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += plz;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newAddressToUpdate" id="plz" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + plz + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newAddressToChange" id="ort">';
                        if (ort.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += ort;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newAddressToUpdate" id="ort" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + ort + '" /></div></td>';
                        tableRow += '</tr>';
                        //$( '#hideButtonAddress' ).hide();
                        $('input[name=hiddenAddressName]').val('');
                        $('input[name=hiddenAddressAbteilung]').val('');
                        $('input[name=hiddenAddressAnschrift]').val('');
                        $('input[name=hiddenAddressAnschrift2]').val('');
                        $('input[name=hiddenAddressPlz]').val('');
                        $('input[name=hiddenAddressOrt]').val('');
                        $( '#newButtonAddress' ).prop('disabled', false);

                        var rows = $('.newAddressTable >tbody >tr').length;
                        if (rows == 1) {
                             $('.newAddressTable').prepend(tableRow);
                        } else {
                             var number = rows - 1;
                             $( '.newAddressTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
                        }
                        $( '#hiddenNewTrAddress' ).hide(); 
                        $(".newAddressTable").on("click", "tr", function(){
                            rowPersonClick($(this));
                        });
                        $( ".newAddressToChange" ).dblclick(function() {
                           changeRow($(this), 'Person');
                        });
                        $('.newAddressToUpdate').change(function() {
                            columnChange($(this));
                        });
                        $("tr.rowsNewAddress2").click(function(){
                            rowNewAddressClick( $(this) );
                        });
                    } else {
                        $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                        $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                    }
                }
            });
        }
    });

    $('.hiddenNewContactTr').change(function() {

        $( '.hiddenNewContactTr' ).removeClass('form-group has-error').addClass('form-group');
        var personName = $('input[name=hiddenContactName]').val();
        var vorname = $('input[name=hiddenContactVorname]').val();
        var telefon = $('input[name=hiddenContactTelefon]').val();
        var telefon2 = $('input[name=hiddenContactTelefon2]').val();
        var fax = $('input[name=hiddenContactFax]').val();
        var mail = $('input[name=hiddenContactMail]').val();
        var clientId = $( '#hiddenClientId' ).val();
        var error;
        var emailCheck = validateEmail(mail);
        if (personName == '') {
            error = true;
            $( '#newContactNameDiv' ).addClass('form-group has-error').addClass('form-group');
        } 
        if (vorname == '') {
            error = true;
            $( '#newContactVornameDiv' ).addClass('form-group has-error').addClass('form-group');
        } 
        if (telefon == '') {
            error = true;
            $( '#newContactTelefonDiv' ).addClass('form-group has-error').addClass('form-group');
        } else {
            var phoneNum = telefon.replace(/[^\d]/g, '');
            if(phoneNum.length < 8 || phoneNum.length > 11) { 
                error = true;
                $( '#newContactTelefonDiv' ).addClass('form-group has-error').addClass('form-group');
            }
        }
        if (telefon2 != '') {
            var phoneNum = telefon2.replace(/[^\d]/g, '');
            if(phoneNum.length < 8 || phoneNum.length > 11) { 
                error = true;
                $( '#newContactTelefon2Div' ).addClass('form-group has-error').addClass('form-group');
            }
        }
        if (mail == '') {
            error = true;
            $( '#newContactMailDiv' ).addClass('form-group has-error').addClass('form-group');
        } else if (emailCheck == false) {
            error = true;
            $( '#newContactMailDiv' ).addClass('form-group has-error').addClass('form-group');
        }
        if (error == true) {
            return false;
        } else {
            var singleAction = 'clientInsert';
            var project = ['Ansprechpartner', personName, vorname, telefon, telefon2, fax, mail, clientId];
            if (window.location.href == urlPath) {
               var path = "Api/Dates/";
            } else {
                var path = "../Api/Dates/";
            }
            $.ajax({url: path,
                type: "post",
                data: { 'action' : 'ajax', 'concrete' : 'dates', 'value' : project, 'singleAction' :  singleAction},
                success: function(result)
                {   
                    if(result !== 'false') {
                        var rowId = result;
                        var tableRow = '<tr class="clickable-row rowsNewPerson2"  name="' + rowId + '" id="' + rowId + '">';
                        tableRow += '<td id="' + rowId + '"><div class="newPersonToChange" id="name">';
                        if (personName.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += personName; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newPersonToUpdate" id="name" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + personName + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newPersonToChange" id="vorname">';
                        if (vorname.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += vorname; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newPersonToUpdate" id="vorname" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + vorname + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newPersonToChange" id="telefon">';
                        if (telefon.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += telefon;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newPersonToUpdate" id="telefon" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + telefon + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newPersonToChange" id="telefon2">';
                        if (telefon2.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += telefon2;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newPersonToUpdate" id="telefon2" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + telefon2 + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newPersonToChange" id="fax">';
                        if (fax.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += fax;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newPersonToUpdate" id="fax" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + fax + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="newPersonToChange" id="mail">';
                        if (mail.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += mail;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="newPersonToUpdate" id="mail" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + mail + '" /></div></td>';
                        tableRow += '</tr>';
                        $( '#hideButtonContact' ).hide(); 
                        $('input[name=hiddenContactName]').val('');
                        $('input[name=hiddenContactVorname]').val('');
                        $('input[name=hiddenContactTelefon]').val('');
                        $('input[name=hiddenContactTelefon2]').val('');
                        $('input[name=hiddenContactFax]').val('');
                        $('input[name=hiddenContactMail]').val('');
                        $( '#newButtonContact' ).prop('disabled', false);
                        var rows = $('.newPersonTable >tbody >tr').length;
                        if (rows == 1) {
			                 $('.newPersonTable').prepend(tableRow);
                        } else {
                             var number = rows - 1;
			                 $( '.newPersonTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
                        }
                        $( '#hiddenNewTrContact' ).hide(); 
                        
                        $(".newPersonTable").on("click", "tr", function(){
                            rowPersonClick($(this));
                        });
                        $( ".newPersonToChange" ).dblclick(function() {
                           changeRow($(this), 'Person');
                        });
                        $('.newPersonToUpdate').change(function() {
                            columnChange($(this));
                        });
                        $("tr.rowsNewPerson2").click(function(){
                            rowNewClick( $(this) );
                        });
                    } else {
                        $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                        $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                    }
                }
            });

        }
    });

    $('.hiddenBearbeitenPerson').change(function() {
        popupPersonAdd( $( this ));
    });

    $( ".deleteButtonAddress" ).click(function() {
        var idValue = $(this).attr('id');
        var values = 'delete-rechnungsadresse-' + idValue;
        if (window.location.href == urlPath) {
            var path = "Api/Dates/";
        } else {
            var path = "../Api/Dates/";
        }
        $.ajax({url: path,
            type: "post",
            data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
            success: function(result)
            {
                if (result == 'success') {
                    var toDelete = $('.rowsNewAddress2[name=' + idValue + ']');
                    toDelete.remove();
                    $('.deleteButtonAddress').prop('disabled', true);
                    $('.deleteButtonAddress').attr('id', '');
                } else {
                    $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                    $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                }
            }
        }); 

    });
    
    $( ".deleteButtonContact" ).click(function() {
        var split = $(this).attr('id');
        var value = split.split('<>');
        var idValue = value[0];
        var employeeName = value[1];
        var values = 'delete-ansprechpartner-' + idValue;
        if (window.location.href == urlPath) {
            var path = "Api/Dates/";
        } else {
            var path = "../Api/Dates/";
        }
        $.ajax({url: path,
            type: "post",
            data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
            success: function(result)
            {
                if (result == 'success') {
                    var clientEmployee = $('input[name=ansprechpartnerBasic]').val();
                    if ( employeeName == clientEmployee ) {
                        $('input[name=ansprechpartnerBasic]').val('');
                        $('input[name=ansprechpartnerBasic]').attr('id', '');
                    }
                    var toDelete = $('.rowsNewPerson2[name=' + idValue + ']');
                    toDelete.remove();
                    $('.deleteButtonContact').prop('disabled', true);
                    $('.deleteButtonContact').attr('id', '');
                } else {
                    $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                    $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                }
            }
        });
    });

    $( ".deleteBearbeitenButtonPerson" ).click(function() {
        var split = $(this).attr('id');
        var value = split.split('<>');
        var idValue = value[0];
        var employeeName = value[1];
        var values = 'delete-ansprechpartner-' + idValue;
        if (window.location.href == urlPath) {
            var path = "Api/Dates/";
        } else {
            var path = "../Api/Dates/";
        }
        $.ajax({url: path,
            type: "post",
            data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
            success: function(result)
            {
                if (result == 'success') {
                    var clientEmployee = $('input[name=ansprechpartnerBasic]').val();
                    if ( employeeName == clientEmployee ) {
                        $('input[name=ansprechpartnerBasic]').val('');
                        $('input[name=ansprechpartnerBasic]').attr('id', '');
                    }
                    var toDelete = $('.rowsBearbeitenPerson[name=' + idValue + ']');
                    toDelete.remove();
                    $('.deleteBearbeitenButtonPerson').prop('disabled', true);
                    $('.deleteBearbeitenButtonPerson').attr('id', '');
                } else {
                    $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                    $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                }
            }
        });
    });

    $( ".deleteBearbeitenButtonAddress" ).click(function() {
        var idValue = $(this).attr('id');
        var values = 'delete-rechnungsadresse-' + idValue;
        if (window.location.href == urlPath) {
            var path = "Api/Dates/";
        } else {
            var path = "../Api/Dates/";
        }
        $.ajax({url: path,
            type: "post",
            data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
            success: function(result)
            {
                if (result == 'success') {
                    var toDelete = $('.rowsBearbeitenAddress[name=' + idValue + ']');
                    toDelete.remove();
                    $('.deleteBearbeitenButtonAddress').prop('disabled', true);
                    $('.deleteBearbeitenButtonAddress').attr('id', '');
                    if (window.location.href == urlPath) {
                        path = "Api/ClientAddressData/";
                    } else {
                        path = "../Api/ClientAddressData/";
                    }
                    var clientNumber = $( '#bearbeitenKundennummer' ).val();
                    $.ajax({url: path + '' + clientNumber,
                        type: "get",
                        success: function(result)
                        {
                            if(result != 'false') {
                                var jsonResult = JSON.parse(result);
                                var jsonArray = jsonResult.map(function(object)
                                { return [object.id, object.name, object.abteilung, object.anschrift, object.anschrift2, object.plz, object.ort] });
                                    $( '#rechnungsadresseDiv' ).children().children().children().eq(1).children().children().remove();
                                    var text = $( "#rechnungsadresse option:selected" ).text();
                                    var textId = $( "#rechnungsadresse option:selected" ).val();
                                    $( '#rechnungsadresseDiv' ).children().children().children().eq(2).children().remove();
                                    $( '#rechnungsadresse' ).append('<option selected="true" style="display:none;" value="' + textId + '">' + text + '</option>');
                                    if ( text != 'Bitte wählen' ) {
                                        $( '#rechnungsadresse' ).prev().children().append('<li data-original-index="0" class="selectedToChange"><a tabindex="0" class="singleTarget" style data-tokens="null"><span class="text" id="' + textId + '">' + text + '</span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>');
                                        var indexCounter = 1;
                                    } else {
                                        var indexCounter = 0;
                                    }
                                    var counter = 0;
                                    var popUpNewRow = '';
                                    if (jsonResult == '' ) {
                                        $( '#rechnungsadresse' ).prev().children().append('<li data-original-index="0" class="selectedToChange" id="emptyAddressToDelete"><a tabindex="0" class="singleTarget" style data-tokens="null"><span class="text" id="0">Keine Adresse</span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>');
                                    } else {
                                            $.each( jsonArray, function() {
                                            if (jsonArray[counter][0] != textId) {
                                                $( '#rechnungsadresse' ).prev().children().append('<li data-original-index="' + indexCounter + '" class="selectedToChange"><a tabindex="0" class="singleTarget" style data-tokens="null"><span class="text" id="' + jsonArray[counter][0] + '">' + jsonArray[counter][1] + ': ' + jsonArray[counter][2] + ' - ' + jsonArray[counter][3] + ', ' + jsonArray[counter][6] + ' ' + jsonArray[counter][5] + '</span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>');
                                                $( '#rechnungsadresse' ).append('<option class="singleTarget" value="' + jsonArray[counter][0] + '">' + jsonArray[counter][1] + ': ' + jsonArray[counter][2] + ' - ' + jsonArray[counter][3] + ', ' + jsonArray[counter][6] + ' ' + jsonArray[counter][5] + '</option>');
                                            }
                                            indexCounter++;  
                                            counter++;
                                        });
                                    }
                                    $( '#selectToBeDeleted' ).show();
                                    $( "#addressUlToAppend>li>a" ).on('click',function(){
                                        $( '.addressDiv' ).removeClass('form-group has-error').addClass('form-group');
                                        $( '#rechnungsadresseSpan' ).text('');
                                        var val = $(this).children().html(); 
                                        var numbVal = $(this).children().attr('id');
                                        $( '.selectedToChange' ).removeClass('selected');
                                        $(this).parent().addClass('selected');
                                        $( '#selectToBeDeleted' ).children().children().first().prop('title', val);
                                        $( '#selectToBeDeleted' ).children().children().children().first().html(val);
                                        if (window.location.href == urlPath) {
                                            $( '#newProjectAddress').val(numbVal);
                                        } else {
                                            var split = projectId.split("<>");
                                            $( '#newProjectAddress').val(numbVal);
                                            changeDate(numbVal, split);
                                        }
                                    });
                            }
                        }
                    });
                } else {
                    $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                    $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                }
            }
        }); 
    });
    
    $('.bearbeitenDate').change(function() {

	var error = false;
	var value = $( this ).attr('id');
	var input = $( this ).children().attr('id');
	if (value == 'zahlungsziel_id') {
	  var column = value;
	  var value = $('select[name=zahlungszielClient]').attr('id');
	  var name = $('select[name=zahlungszielClient] option:selected').val();
	  var selectText = $('select[name=zahlungszielClient] option:selected').text();
	} else {
	    var name = $( this ).children().val();
	    var column = $( this ).children().attr("name");
	}
	if (input == 'name' || input == 'abteilung' || input == 'anschrift' || 
	    input == 'ort' || input == 'plz' || input == 'telefon' || input == 'mail') {
	    $( this ).children().css('border-color', '');
	    if (name.length == 0) {
		error = true;
	    }
	} 
	if (input == 'ort') {
	    if (name.length < 3) {
		error = true;
	    }
	}
	if (input == 'plz') {
	    $( this ).children().css('border-color', '');
	    name = name.replace('-', '');
	    var check = isInteger(name);
            if (check == false) {
		error = true;
            }
	    if (name.length < 5 || name.length > 5) {
	        error = true;  
	    }
	}
	if (input == 'telefon') {
	    $( this ).children().css('border-color', '');
	    var phoneNum = name.replace(/[^\d]/g, '');
	    if(phoneNum.length < 6 || phoneNum.length > 11) { 
		error = true;
	    }
	}
	if (input == 'skonto') {
	     $( this ).children().css('border-color', '');
	     skonto = name.replace(',', '.');
	     var check = isInteger(skonto);
	     if (check == false) {
		error = true;
	    }
	}
	if (input == 'mail') {
	    var check = validateEmail(name);
	    if (check == false) {
		error = true;
	    }
	}
	if (error == true) {
	      $( this ).children().css('border-color', '#a94442');
          $( this ).children().focus();
	      return false; 
	}
	var previous = $( this ).prev();
	var table = $( this ).parent().attr('id');
	var date = table + '<>' + column + '<>' + value;
	changeClientOption(date, name);
	var timerId = setInterval(function() {
        if(finalResult !== null) {
            if(finalResult == 'success') {
                if (column == 'skonto') {
                    $( '#skontoDisplay' ).text(name);
                } else if (column == 'zahlungsziel_id') {
                    $( '#zahlungszielDisplay' ).text(selectText);  
                    var lastOption = $('select[name=individual_payment] option:selected').text();
                    if (selectText == lastOption) {
                      $('#invidivuell_1').attr('checked', false);
                      $('select[name=individual_payment]').attr('disabled', 'disabled');
                    }
                }
                if (input == 'name') {
                    $('input[name=auftraggeber]').val(name);
                }
                if (window.location.href == urlPath) {
                    path = "Api/DeliveryAddress/";
                } else {
                    path = "../Api/DeliveryAddress/";
                }
                $.ajax({url: path + value, 
                    type: "get",
                    success: function(result)
                    {
                        if (result != 'false') {
                            $( '#lieferung_per' ).val(result);
                        } else {
                            $( '#lieferung_per' ).val('');
                        }
                    }
                });
            }
            clearInterval(timerId);
        } else {
            $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
            $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
        }
        }, 1500);
    });

	$('#newPopupSave').click(function() {
        var error = false;
        var name = $('#newPopupName').val();
        var departement = $( '#newPopupDepartement' ).val();
        var address = $( '#newPopupAddress' ).val();
        var address2 = $( '#newPopupAddress2' ).val();
        var place = $( '#newPopupPlace' ).val();
        var code = $( '#newPopupCode' ).val();
        var phone = $( '#newPopupPhone' ).val();
        var fax = $( '#newPopupFax' ).val();
        var mail = $( '#newPopupMail' ).val();
        var emailCheck = validateEmail(mail);
        var skonto = $( '#newPopupSkonto' ).val();
        var paymentOpt = $('select[name=zahlungsziel] option:selected').val();
        var paymentText = $('select[name=zahlungsziel] option:selected').text();
        if (name == '') {
            error = true;
            $( '#newPopupName' ).css('border-color', '#a94442');
           $( '#newNameErrorSpan' ).text('Geben Sie bitte die Name ein');
        } else {
            $( '#newPopupName' ).css('border-color', '');
            $( '#newNameErrorSpan' ).text('');
       }
        if (departement == '') {
            error = true;
           $( '#newPopupDepartement' ).css('border-color', '#a94442');
           $( '#newDepartementErrorSpan' ).text('Geben Sie bitte die Abteilung ein');
        } else {
            $( '#newPopupDepartement' ).css('border-color', '');
            $( '#newDepartementErrorSpan' ).text('');
       }
        if (address == '') {
            error = true;
            $( '#newPopupAddress' ).css('border-color', '#a94442');
        $( '#newAddressErrorSpan' ).text('Geben Sie bitte die Anschrift ein');
        } else {
            $( '#newPopupAddress' ).css('border-color', '');
            $( '#newAddressErrorSpan' ).text('');
       }
        if (place == '') {
            error = true;
            $( '#newPopupPlace' ).css('border-color', '#a94442');
            $( '#newPlaceErrorSpan' ).text('Geben Sie bitte den Ort');
        } else {
            var placeCheck = isInteger(place);
            if (place.length < 3) {
                error = true;
                $( '#newPopupPlace' ).css('border-color', '#a94442');
                $( '#newPlaceErrorSpan' ).text('Mindestens 3 Buchstaben');
            } else if (placeCheck == true) {
                error = true;
                $( '#newPopupPlace' ).css('border-color', '#a94442');
                $( '#newPlaceErrorSpan' ).text('Anzahl gilt nicht als Ort');
            } else {
                $( '#newPopupPlace' ).css('border-color', '');
                $( '#newPlaceErrorSpan' ).text('');
            }
        }
        if (code == '') {
            error = true;
            $( '#newPopupCode' ).css('border-color', '#a94442');
            $( '#newCodeErrorSpan' ).text('Geben Sie bitte die Postleitzahl ein');
        } else {
            codeName = code.replace('-', '');
            var check = isInteger(codeName);
            if (check == false) {
                 error = true;
                 $( '#newPopupCode' ).css('border-color', '#a94442');
                 $( '#newCodeErrorSpan' ).text('Postleitzahl nicht gültig');
            } else if (codeName.length < 5 || codeName.length > 5) {
                    error = true;
                $( '#newPopupCode' ).css('border-color', '#a94442');
                $( '#newCodeErrorSpan' ).text('Postleitzahl nicht gültig');
            } else {
                $( '#newPopupCode' ).css('border-color', '');
                $( '#newCodeErrorSpan' ).text('');
            }
        }
        if (phone == '') {
            error = true;
            $( '#newPopupPhone' ).css('border-color', '#a94442');
            $( '#newPhoneErrorSpan' ).text('Geben Sie bitte das Telefon');
        } else {
            var phoneNum = phone.replace(/[^\d]/g, '');
            if(phoneNum.length < 8 || phoneNum.length > 11) { 
                error = true;
                $( '#newPopupPhone' ).css('border-color', '#a94442');
                $( '#newPhoneErrorSpan' ).text('Telefonnummer nicht gültig');
            } else {
                $( '#newPopupPhone' ).css('border-color', '');
                $( '#newPhoneErrorSpan' ).text('');
            }
        }
        if (mail == '') {
            error = true;
            $( '#newPopupMail' ).css('border-color', '#a94442');
            $( '#newMailErrorSpan' ).text('Geben Sie bitte E-Mail-Adresse ein');
        } else if (emailCheck == false) {
            error = true;
            $( '#newPopupMail' ).css('border-color', '#a94442');
            $( '#newMailErrorSpan' ).html('Email-Adresse nicht gültig');
        } else {
            //var check = emailToCheck(mail);
            var column = 'auftraggeber';
            if (window.location.href == urlPath) {
               var path = "Api/Mail/";
            } else {
                var path = "../Api/Mail/";
            }
            $.ajax({url: path + column + '<>' + mail, 
                type: "get",
                success: function(result)
                    { 
                        if (result == 'false') {
                            $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                            error = true;
                        }  else {
                    if (result != 'no match') {
           $( '#newPopupMail' ).css('border-color', '#a94442');
           $( '#newMailErrorSpan' ).text('Email-Adresse schon gespeichert');
           error = true; 
                 } else {
                    $( '#newPopupMail' ).css('border-color', '');
            $( '#newMailErrorSpan' ).text('');
                 }
            }
           }
        });
        }
        if (skonto != '') {
            skonto = skonto.replace(',', '.');
            var skontoCheck = isInteger(skonto);
            if (skontoCheck == false) {
                error = true;
                $( '#newPopupSkonto' ).css('border-color', '#a94442');
                $( '#newSkontoErrorSpan' ).text('Anzahl im Zahlzeichen');
            } else {
                $( '#newPopupSkonto' ).css('border-color', '');
                $( '#newSkontoErrorSpan' ).text('');
            }
        }
        if (error == true) {
            $( "#newPopupName" ).keyup(function() {
                var name = $(this).val();
                if (name == '') {
                    $( '#newPopupName' ).css('border-color', '#a94442');
                    $( '#newNameErrorSpan' ).text('Geben Sie bitte die Name ein');
                }
                var check = isInteger(name);
                if (check == true) {
                    $( '#newPopupName' ).css('border-color', '#a94442');
                    $( '#newNameErrorSpan' ).text('Geben Sie nicht nur Anzahl ein');
                } else {
                    $( '#newPopupName' ).css('border-color', '');
                    $( '#newNameErrorSpan' ).text('');
                }
            });
      
            $( "#newPopupDepartement" ).keyup(function() {
               var department = $(this).val();
               if (department == '') {
                    $( '#newPopupDepartement' ).css('border-color', '#a94442');
                    $( '#newDepartementErrorSpan' ).text('Geben Sie bitte die Abteilung ein');
                } 
                var check = isInteger(department);
                if (check == true) {
                    $( '#newPopupDepartement' ).css('border-color', '#a94442');
                    $( '#newDepartementErrorSpan' ).text('Geben Sie nicht nur Anzahl ein');
                } else {
                    $( '#newPopupDepartement' ).css('border-color', '');
                    $( '#newDepartementErrorSpan' ).text('');
                }
            });

            $( '#newPopupPlace' ).keyup(function() {
                var place = $( this ).val();
                if (place == '') {
                    $( '#newPopupPlace' ).css('border-color', '#a94442');
                    $( '#newPlaceErrorSpan' ).text('Geben Sie bitte den Ort');
                    return false;
                }
                var placeCheck = isInteger(place);
                if (place.length < 3) {
                    $( '#newPopupPlace' ).css('border-color', '#a94442');
                    $( '#newPlaceErrorSpan' ).text('Mindestens 3 Buchstaben');
                    return false;
                } else if (placeCheck == true) {
                    $( '#newPopupPlace' ).css('border-color', '#a94442');
                    $( '#newPlaceErrorSpan' ).text('Anzahl gilt nicht als Ort');
                    return false;
                } else {
                    $( '#newPopupPlace' ).css('border-color', '');
                    $( '#newPlaceErrorSpan' ).text('');
                }
            });   
      
            $( "#newPopupAddress" ).keyup(function() {
                var address = $(this).val();
                if (address == '') {
                  $( '#newPopupAddress' ).css('border-color', '#a94442');
                  $( '#newAddressErrorSpan' ).text('Geben Sie bitte die Anschrift ein');
              } 
              var check = isInteger(address);
              if (check == true) {
                  $( '#newPopupAddress' ).css('border-color', '#a94442');
                  $( '#newAddressErrorSpan' ).text('Geben Sie nicht nur Anzahl ein');
              } else {
                  $( '#newPopupAddress' ).css('border-color', '');
                  $( '#newAddressErrorSpan' ).text('');
              }
            });
            $( "#newPopupSkonto" ).keyup(function() {
                var skonto = $(this).val();
                if (skonto == '') {
                    $( '#newPopupSkonto' ).css('border-color', '');
                    $( '#newSkontoErrorSpan' ).text('');
                } else {
                    skonto = skonto.replace(',', '.');
                    var skontoCheck = isInteger(skonto);
                    if (skontoCheck == false) {
                        $( '#newPopupSkonto' ).css('border-color', '#a94442');
                        $( '#newSkontoErrorSpan' ).text('Anzahl im Zahlzeichen');
                    } else {
                        $( '#newPopupSkonto' ).css('border-color', '');
                        $( '#newSkontoErrorSpan' ).text('');
                    }
                }
            });
      
            $( "#newPopupCode" ).keyup(function() {
                var name = $(this).val();
                name = name.replace('-', '');
                var check = isInteger(name);
                if (check == false) {
                    $( '#newPopupCode' ).css('border-color', '#a94442');
                    $( '#newCodeErrorSpan' ).text('Postleitzahl nicht gültig');
                } else if (name.length < 5 || name.length > 5) {
                    $( '#newPopupCode' ).css('border-color', '#a94442');
                    $( '#newCodeErrorSpan' ).text('Postleitzahl nicht gültig');
                } else {
                    $( '#newPopupCode' ).css('border-color', '');
                    $( '#newCodeErrorSpan' ).text('');
                }
            });
      
            $( "#newPopupPhone" ).keyup(function() {
                var phone = $(this).val();
                if (phone == '') {
                    $( '#newPopupPhone' ).css('border-color', '#a94442');
                    $( '#newPhoneErrorSpan' ).text('Geben sie bitte das Telefon ein');
                } else {
                    var phoneNum = phone.replace(/[^\d]/g, '');
                    if(phoneNum.length < 8 || phoneNum.length > 11) { 
                        $( '#newPopupPhone' ).css('border-color', '#a94442');
                        $( '#newPhoneErrorSpan' ).text('Telefonnummer nicht gültig');
                    } else {
                        $( '#newPopupPhone' ).css('border-color', '');
                        $( '#newPhoneErrorSpan' ).text('');
                    }
                }
            });
        
            $( "#newPopupMail" ).keyup(function() {
                var mail = $(this).val();
                var emailCheck = validateEmail(mail);
                if (mail == '') {
                    $( '#newPopupMail' ).css('border-color', '#a94442');
                    $( '#newMailErrorSpan' ).text('Geben sie bitte E-Mail-Adresse ein');
                } else if (emailCheck == false) {
                    $( '#newPopupMail' ).css('border-color', '#a94442');
                    $( '#newMailErrorSpan' ).html('Email-Adresse nicht gültig');
                } else {
                    var check = emailToCheck(mail);
                    if (check == true) {
                    } else {
                        $( '#newPopupMail' ).css('border-color', '#a94442');
                        $( '#newMailErrorSpan' ).text('Email-Adresse schon gespeichert');
                    }
                $( '#newPopupMail' ).css('border-color', '');
                $( '#newMailErrorSpan' ).text('');
                }
            });
        } else {
            if (window.location.href == urlPath) {
                var path = "Api/NewClient/";
            } else {
                var path = "../Api/NewClient/";
            }
            var dates = [name, departement, address, address2, place, code, phone, fax, mail, skonto, paymentOpt];
            $.ajax({url: path,
                type: "post",
                data: { 'action' : 'ajax', 'concrete' : 'newClient', 'value' : dates },
                success: function(finalResult)
                {
                    var timerId = setInterval(function() {
                    if(finalResult !== null) {
                        if(finalResult != 'false') {
                            console.log(finalResult);
                            $( '#newPopupSave' ).hide();
                            $( '#popupClean' ).show();
                            $( '#kundennummer' ).val(finalResult);
                            $( '#hiddenClientId' ).val(finalResult);
                            $( '#newButtonAddress' ).attr('disabled', false);
                            $( '#newButtonContact' ).attr('disabled', false);
                            $( '#newPopupName' ).attr('disabled', true);
                            $( '#newPopupDepartement' ).attr('disabled', true);
                            $( '#newPopupAddress' ).attr('disabled', true);
                            $( '#newPopupAddress2' ).attr('disabled', true);
                            $( '#newPopupPlace' ).attr('disabled', true);
                            $( '#newPopupCode' ).attr('disabled', true);
                            $( '#newPopupPhone' ).attr('disabled', true);
                            $( '#newPopupFax' ).attr('disabled', true);
                            $( '#newPopupMail' ).attr('disabled', true);
                            $( '#newPopupSkonto' ).attr('disabled', true);
                            $( 'select[name=zahlungsziel]' ).attr('disabled', true);
                            $( '#zahlungsziel' ).children().hide();
                            $( '#zahlungsziel' ).append('<div class="btn-group bootstrap-select form-control"><input type="text" class="form-control" id="replacedPayment" value="' + paymentText + '" disabled="disabled" /></div>');
                        } else {
                            $( '#newPopupMail' ).css('border-color', '#a94442');
                            $( '#newMailErrorSpan' ).text('Email-Adresse schon gespeichert');
                            $( "#newPopupMail" ).keyup(function() {
                                var mail = $(this).val();
                                var emailCheck = validateEmail(mail);
                                if (mail == '') {
                                    $( '#newPopupMail' ).css('border-color', '#a94442');
                                    $( '#newMailErrorSpan' ).text('Geben sie bitte E-Mail-Adresse ein');
                                } else if (emailCheck == false) {
                                    $( '#newPopupMail' ).css('border-color', '#a94442');
                                    $( '#newMailErrorSpan' ).html('Email-Adresse nicht gültig');
                                } else {
                                    var check = emailToCheck(mail);
                                    if (check == true) {
                                    } else {
                                        $( '#newPopupMail' ).css('border-color', '#a94442');
                                        $( '#newMailErrorSpan' ).text('Email-Adresse schon gespeichert');
                                    }
                                    $( '#newPopupMail' ).css('border-color', '');
                                    $( '#newMailErrorSpan' ).text('');
                                }
                            });
                        }
                        clearInterval(timerId);
                    } else {
                        $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                        $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
                    }
                }, 1500);
                }
            });
        }
    });

    $( "#popupClean" ).click(function() {
        cleanPopup();
    });
    
    $( '#formToPrint' ).change(function() {
    	var selected = $( this ).val();
    	if (selected != 0) {
    	    var projId = $( '#hiddenProj' ).val();
    	    var column = 'descToPrint' + selected;
    	    var array = [column, projId];
    	    changeDate('', array);
    	    var timerId = setInterval(function() {
    		if(finalResult !== null) {
    		    if(finalResult == 'success') {
    			$( '#hiddenHref' ).attr('href', '../Print/' + selected + '?projId=' + projId );
    			$( '.descToPrint' ).val('');
    			$( '.descToPrint' ).attr('id', selected);
    			$( '.descToPrint' ).attr('disabled', false);
    			$('#hiddenHref').removeClass('disabled');
    			}
                clearInterval(timerId);
              } else {
                    $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                    $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
              }
            }, 500);
    	} else {
    	    $( '.descToPrint' ).val('');
    	    $( '.descToPrint' ).attr('id', '');
    	    $( '.descToPrint' ).attr('disabled', true);
    	    $('#hiddenHref').addClass('disabled');
    	    $( '#hiddenHref' ).attr('href', '');
    	}
    });
    
    $( '.descToPrint' ).keyup(function() {
    	var curDocument = $( this ).attr('id');
    	var projId = $( '#hiddenProj' ).val();
    	var text = $( '.descToPrint' ).val();
    	var column = 'descToPrint' + curDocument;
    	var array = [column, projId];
    	changeDate(text, array);
    	var timerId = setInterval(function() {
        if(finalResult !== null) {
	        if(finalResult == 'success') {
	        }
            clearInterval(timerId);
        } else {
                $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
          }
        }, 1000);
    });
    
    $( "#druckButton" ).click(function() {
        $( '#druckButton' ).text('in Vorbereitung...');
        var href = $( '#hiddenHref' ).attr('href');
        var curDocument = $('select[name=formToPrint] option:selected').val();
        var field = $( '.descToPrint' ).attr('id');
        var column = field + curDocument;
        var projId = $( '#hiddenProj' ).val();
        var text = $( '#descToPrint' ).val();
        var array = [column, projId];
        changeDate(text, array);
        var timerId = setInterval(function() {
        if(finalResult !== null) {
            if(finalResult == 'success') {
                var formPath = $( '#printId' ).attr( 'action' );
                var finalPath = formPath + '/' + curDocument;
                var finalhref = href + '/' + curDocument + '?projId=' + projId;
                $( '#hiddenHref' ).attr('href', finalhref);
                curDocument = '';
                $( "#printId" ).attr('action', finalPath);
                $( "#formToPrint" ).remove();
                $( "#descLabel" ).remove();
                $( '#descToPrint' ).remove();
                clearInterval(timerId);
                window.open( finalhref );
                //location.reload();
            }
            clearInterval(timerId);
            } else {
                $('#ajaxPopupError').fadeIn('slow').delay(5000).hide(1);
                $('#ajaxPopupError2').fadeIn('slow').delay(5000).hide(1);
            }
        }, 1500);
    return false;
    });
    
});