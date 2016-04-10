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
  
    var finalResult;
    var urlPath = "http://ad9bis.vot.pl/CRM/Erfassung";

    function changeClientOption(dates, value) {
	if (window.location.href == urlPath) {
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
        if (window.location.href == urlPath) {
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
        object.css('background-color', "#e9e9e9");
        $('.deleteBearbeitenButtonAddress').attr('id', idVal);
        $('.deleteBearbeitenButtonAddress').prop('disabled', false);
    }

    function rowNewClick(object) {
        $("tr.rowsNewPerson2").removeAttr( 'style' );
    	var idVal = object.attr('id');
    	object.css('background-color', "#e9e9e9");
    	$('.deleteButtonContact').attr('id', idVal);
    	$('.deleteButtonContact').prop('disabled', false);
    }

    function rowNewAddressClick(object) {
        $("tr.rowsNewAddress2").removeAttr( 'style' );
        var idVal = object.attr('id');
        object.css('background-color', "#e9e9e9");
        $('.deleteButtonAddress').attr('id', idVal);
        $('.deleteButtonAddress').prop('disabled', false);
    }

    function rowPersonClick(object) {
        $("tr.rowsBearbeitenPerson").removeAttr( 'style' );
        var idVal = object.attr('id');
        object.css('background-color', "#e9e9e9");
        $('.deleteBearbeitenButtonPerson').attr('id', idVal);
        $('.deleteBearbeitenButtonPerson').prop('disabled', false);
    }

    $( "#newBearbeitenButtonAddress" ).click(function() {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
        $('.deleteBearbeitenButtonAddress').prop('disabled', true);
    	$( '#hiddenBearbeitenTrAddress' ).fadeIn( 'slow' );
    	$( '#hideBearbeitenButtonAddress' ).show();
        $( this ).prop('disabled', true);
    });
    
    $( "#newBearbeitenButtonPerson" ).click(function() {
        $("tr.rowsBearbeitenPerson").removeAttr( 'style' );
        $('.deleteBearbeitenButtonPerson').prop('disabled', true);
    	$( '#hiddenBearbeitenTrPerson' ).fadeIn( 'slow' );
    	$( '#hideBearbeitenButtonPerson' ).show();
        $( this ).prop('disabled', true);
    });
    
    $( "#newButtonAddress" ).click(function() {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
    	$( '#hiddenNewTrAddress' ).fadeIn( 'slow' );
    	$( '#hideButtonAddress' ).show();
        $( this ).prop('disabled', true);
    });

    $( "#newButtonContact" ).click(function() {
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
        $( '#hiddenNewTrContact' ).fadeIn( 'slow' );
        $( '#hideButtonContact' ).show();
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
    
    $( "#hideBearbeitenButtonAddress" ).click(function() {
    	$( '#hiddenBearbeitenTrAddress' ).fadeOut( 'slow' );
        $('#newBearbeitenButtonAddress').prop('disabled', false);
    	$( this ).hide();
    });
    
    $( "#hideBearbeitenButtonPerson" ).click(function() {
    	$( '#hiddenBearbeitenTrPerson' ).fadeOut( 'slow' );
        $('#newBearbeitenButtonPerson').prop('disabled', false);
    	$( this ).hide();
    });
    
    $( "#hideButtonAddress" ).click(function() {
        $( '#newButtonAddress' ).prop('disabled', false);
    	$( '#hiddenNewTrAddress' ).fadeOut( 'slow' );
    	$( this ).hide();
    });

    $( "#hideButtonContact" ).click(function() {
        $( '#newButtonContact' ).prop('disabled', false);
        $( '#hiddenNewTrContact' ).fadeOut( 'slow' );
        $( this ).hide();
    });
    
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
        $( '.hiddenBearbeitenTr' ).removeClass('form-group has-error').addClass('form-group');
        var name = $('input[name=hiddenBearbeitungAddressName]').val();
        var abteilung = $('input[name=hiddenBearbeitungAddressAbteilung]').val();
        var anschrift = $('input[name=hiddenBearbeitungAddressAnschrift]').val();
        var anschrift2 = $('input[name=hiddenBearbeitungAddressAnschrift2]').val();
        var plz = $('input[name=hiddenBearbeitungAddressPlz]').val();
        var ort = $('input[name=hiddenBearbeitungAddressOrt]').val();
        var clientId = $( this ).parent().attr('id');
        var error;
        if (name == '') {
            error = true;
            $( '#hiddenAddressNameDiv' ).addClass('form-group has-error').addClass('form-group');
        }
        if (abteilung == '') {
            error = true;
            $( '#hiddenAddressAbteilungDiv' ).addClass('form-group has-error').addClass('form-group');
        }
        if (anschrift == '') {
            error = true;
            $( '#hiddenAddressAnschriftDiv' ).addClass('form-group has-error').addClass('form-group');
        }
        if (plz == '') {
            error = true;
            $( '#hiddenAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
        } else {
            name = plz.replace('-', '');
            var check = isInteger(name);
            if (check == false) {
                error = true;
                $( '#hiddenAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
            } else if (name.length < 5 || name.length > 5) {
                error = true;
                $( '#hiddenAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
            }        
        }
        if (ort == '') {
            error = true;
            $( '#hiddenAddressOrtDiv' ).addClass('form-group has-error').addClass('form-group');
        } else if (ort.length < 3) {
            error = true;
            $( '#hiddenAddressOrtDiv' ).addClass('form-group has-error').addClass('form-group');
        }
        if (error == true) {
            return false;
        } else {
            var singleAction = 'clientInsert';
            var project = 'Rechnungsadressen<>' + name + '<>' +abteilung + '<>' + anschrift + '<>' + anschrift2 + '<>' + plz + '<>' + ort + '<>' + clientId;
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
                        var tableRow = '<tr class="clickable-row rowsBearbeitenAddress"  name="' + rowId + '" id="' + rowId + '">';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenAddressToChange" id="name">';
                        if (name.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += name; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenAddressToUpdate" id="name" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + name + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenAddressToChange" id="abteilung">';
                        if (abteilung.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += abteilung; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenAddressToUpdate" id="abteilung" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + abteilung + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenAddressToChange" id="anschrift">';
                        if (anschrift.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += anschrift;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenAddressToUpdate" id="anschrift" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + anschrift + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenAddressToChange" id="anschrift2">';
                        if (anschrift2.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += anschrift2;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenAddressToUpdate" id="anschrift2" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + anschrift2 + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenAddressToChange" id="plz">';
                        if (plz.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += plz;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenAddressToUpdate" id="plz" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + plz + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenAddressToChange" id="ort">';
                        if (ort.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += ort;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenAddressToUpdate" id="ort" style="display: none;"><input type="text" class="form-control" name="rechnungsadresse" value="' + ort + '" /></div></td>';
                        tableRow += '</tr>';
                        $( '#hideBearbeitenButtonAddress' ).hide(); 
                        $('input[name=hiddenBearbeitungAddressName]').val('');
                        $('input[name=hiddenBearbeitungAddressName]').val('');
                        $('input[name=hiddenBearbeitungAddressAbteilung]').val('');
                        $('input[name=hiddenBearbeitungAddressAnschrift]').val('');
                        $('input[name=hiddenBearbeitungAddressAnschrift2]').val('');
                        $('input[name=hiddenBearbeitungAddressPlz]').val('');
                        $('input[name=hiddenBearbeitungAddressOrt]').val('');
                        $( '#newBearbeitenButtonAddress' ).prop('disabled', false);
                        var rows = $('.bearbeitenAddressTable >tbody >tr').length;
                        var number = rows - 1; 
                        $( '#hiddenBearbeitenTrAddress' ).hide(); 
                        $( '.bearbeitenAddressTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
                        $(".bearbeitenAddressTable").on("click", "tr", function(){
                            rowClick($(this));
                        });
                        $( ".bearbeitenAddressToChange" ).dblclick(function() {
                            changeRow($(this), 'Address');
                        });
                        $('.bearbeitenAddressToUpdate').change(function() {
                           columnChange($(this));
                        });
                    } else {
                        console.log(finalResult);
                    }
                }
            });
        }
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
            name = plz.replace('-', '');
            var check = isInteger(name);
            if (check == false) {
                error = true;
                $( '#newAddressPlzDiv' ).addClass('form-group has-error').addClass('form-group');
            } else if (name.length < 5 || name.length > 5) {
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
                        if (name.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += name; 
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
                        $( '#hideButtonAddress' ).hide();
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
			  alert('here');
                           changeRow($(this), 'Person');
                        });
                        $('.newAddressToUpdate').change(function() {
                            columnChange($(this));
                        });
                        $("tr.rowsNewAddress2").click(function(){
                            rowNewAddressClick( $(this) );
                        });
                    } else {
                        console.log(finalResult);
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
                        console.log(finalResult);
                    }
                }
            });

        }
    });

    $('.hiddenBearbeitenPerson').change(function() {

        $( '.hiddenBearbeitenPersonTr' ).removeClass('form-group has-error').addClass('form-group');
        var personName = $('input[name=hiddenPersonName]').val();
        var vorname = $('input[name=hiddenPersonVorname]').val();
        var telefon = $('input[name=hiddenPersonTelefon]').val();
        var telefon2 = $('input[name=hiddenPersonTelefon2]').val();
        var fax = $('input[name=hiddenPersonFax]').val();
        var mail = $('input[name=hiddenPersonMail]').val();
        var clientId = $( this ).parent().attr('id');
        var error;
        var emailCheck = validateEmail(mail);
        if (personName == '') {
            error = true;
            $( '#hiddenPersonNameDiv' ).addClass('form-group has-error').addClass('form-group');
        } 
        if (vorname == '') {
            error = true;
            $( '#hiddenPersonVornameDiv' ).addClass('form-group has-error').addClass('form-group');
        }  
        if (telefon == '') {
            error = true;
            $( '#hiddenPersonTelefonDiv' ).addClass('form-group has-error').addClass('form-group');
        } else {
            var phoneNum = telefon.replace(/[^\d]/g, '');
            if(phoneNum.length < 8 || phoneNum.length > 11) { 
                error = true;
                $( '#hiddenPersonTelefonDiv' ).addClass('form-group has-error').addClass('form-group');
            }
        }
        if (mail == '') {
            error = true;
            $( '#hiddenPersonMailDiv' ).addClass('form-group has-error').addClass('form-group');
        } else if (emailCheck == false) {
            error = true;
            $( '#hiddenPersonMailDiv' ).addClass('form-group has-error').addClass('form-group');
        }
        if (error == true) {
            return false;
        } else {
            var singleAction = 'clientInsert';
            var project = 'Ansprechpartner<>' + personName + '<>' + vorname + '<>' + telefon + '<>' + telefon2 + '<>' + fax + '<>' + mail + '<>' + clientId;
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
                        var tableRow = '<tr class="clickable-row rowsBearbeitenPerson"  name="' + rowId + '" id="' + rowId + '">';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenPersonToChange" id="name">';
                        if (personName.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += personName; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenPersonToUpdate" id="name" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + personName + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenPersonToChange" id="vorname">';
                        if (vorname.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += vorname; 
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenPersonToUpdate" id="vorname" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + vorname + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenPersonToChange" id="telefon">';
                        if (telefon.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += telefon;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenPersonToUpdate" id="telefon" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + telefon + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenPersonToChange" id="telefon2">';
                        if (telefon2.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += telefon2;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenPersonToUpdate" id="telefon2" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + telefon2 + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenPersonToChange" id="fax">';
                        if (fax.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += fax;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenPersonToUpdate" id="fax" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + fax + '" /></div></td>';
                        tableRow += '<td id="' + rowId + '"><div class="bearbeitenPersonToChange" id="mail">';
                        if (mail.length == 0) {
                            tableRow += '<i>keine Daten</i>';
                        } else {
                            tableRow += mail;
                        }
                        tableRow += '</div>';
                        tableRow += '<div class="bearbeitenPersonToUpdate" id="mail" style="display: none;"><input type="text" class="form-control" name="ansprechpartner" value="' + mail + '" /></div></td>';
                        tableRow += '</tr>';
                        $( '#hideBearbeitenButtonPerson' ).hide(); 
                        var name = $('input[name=hiddenPersonName]').val('');
                        $('input[name=hiddenPersonVorname]').val('');
                        $('input[name=hiddenPersonTelefon]').val('');
                        $('input[name=hiddenPersonTelefon2]').val('');
                        $('input[name=hiddenPersonFax]').val('');
                        $('input[name=hiddenPersonMail]').val('');
                        $( '#newBearbeitenButtonPerson' ).prop('disabled', false);
                        var rows = $('.bearbeitenPersonTable >tbody >tr').length;
                        var number = rows - 1; 
                        $( '#hiddenBearbeitenTrPerson' ).hide(); 
                        $( '.bearbeitenPersonTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
                        $(".bearbeitenPersonTable").on("click", "tr", function(){
                            rowPersonClick($(this));
                        });
                        $( ".bearbeitenPersonToChange" ).dblclick(function() {
                           changeRow($(this), 'Person');
                        });
                        $('.bearbeitenPersonToUpdate').change(function() {
                            columnChange($(this));
                        });
                    } else {
                        console.log(finalResult);
                    }
                }
            });

        }
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
                }
            }
        }); 

    });
    
    $( ".deleteButtonContact" ).click(function() {
        var idValue = $(this).attr('id');
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
                    var toDelete = $('.rowsNewPerson2[name=' + idValue + ']');
                    toDelete.remove();
                    $('.deleteButtonContact').prop('disabled', true);
                    $('.deleteButtonContact').attr('id', '');
                }
            }
        });
    });

    $( ".deleteBearbeitenButtonPerson" ).click(function() {
        var idValue = $(this).attr('id');
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
                    var toDelete = $('.rowsBearbeitenPerson[name=' + idValue + ']');
                    toDelete.remove();
                    $('.deleteBearbeitenButtonPerson').prop('disabled', true);
                    $('.deleteBearbeitenButtonPerson').attr('id', '');
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
		    }
		clearInterval(timerId);
		} else {
		    console.log(finalResult);
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
    	    name = code.replace('-', '');
    	    var check = isInteger(name);
            if (check == false) {
        		 error = true;
        		 $( '#newPopupCode' ).css('border-color', '#a94442');
        		 $( '#newCodeErrorSpan' ).text('Postleitzahl nicht gültig');
            } else if (name.length < 5 || name.length > 5) {
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
	    $( '#newMailErrorSpan' ).text('Email-Adresse nicht gültig');
        } else {
    	    $( '#newPopupMail' ).css('border-color', '');
    	    $( '#newMailErrorSpan' ).text('');
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
		    $( '#newMailErrorSpan' ).text('Email-Adresse nicht gültig');
		} else {
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
                        }
                        clearInterval(timerId);
                    } else {
                        console.log(finalResult);
                    }
                }, 1500);
                }
            });
        }
	});

    $( "#popupClean" ).click(function() {
        $('#newPopupName').val('');
        $( '#newPopupDepartement' ).val('');
        $( '#newPopupAddress' ).val('');
        $( '#newPopupAddress2' ).val('');
        $( '#newPopupPlace' ).val('');
        $( '#newPopupCode' ).val('');
        $( '#newPopupPhone' ).val('');
        $( '#newPopupFax' ).val('');
        $( '#newPopupMail' ).val('');
        $( '#newPopupSkonto' ).val('');
	$( '#kundennummer' ).val('');
        $( '.rowsNewAddress2' ).remove();
        $( '.rowsNewPerson2' ).remove();
        $( this ).hide();
        $( '#newPopupSave' ).show();

    });
    
    $( "#druckButton" ).click(function() {
        var curDocument = $('select[name=formToPrint] option:selected').val();
        var field = $( '#descToPrint' ).attr('id');
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
                curDocument = '';
                $( "#printId" ).attr('action', finalPath);
                $( "#formToPrint" ).remove();
                $( '#descToPrint' ).remove();
                $( '#druckButton' ).text('in Vorbereitung');
                $( '#printId' ).submit();
                clearInterval(timerId);
                $( "#exampleModal" ).dialog('close');
            }
            clearInterval(timerId);
            } else {
                console.log(finalResult);
            }
        }, 1500);
    return false;
    });

    /*$( "#druckButton" ).click(function() {
	var document = $('select[name=formToPrint] option:selected').val();
	var field = $( '#descToPrint' ).attr('id');
	var column = field + document;
	var projId = $( '#hiddenProj' ).val();
	var text = $( '#descToPrint' ).val();
	var array = [column, projId];
	changeDate(text, array);
	var timerId = setInterval(function() {
		if(finalResult !== null) {
		    if(finalResult == 'success') {
			$("input[name='descToPrint']").remove();
			$( '#druckButton' ).text('in Vorbereitung');
			return false;
			$( '#printId').submit();
		    }
    		clearInterval(timerId);
    		} else {
    		    console.log(finalResult);
    		}
    	}, 1500);
	return false;
    });*/
});