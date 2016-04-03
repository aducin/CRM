$( document ).ready(function() {
  
    var finalResult;
    var urlPath = "http://kluby.local/CRM/Erfassung";

    function changeClientOption(dates, value) {
	if (window.location.href == "http://kluby.local/CRM/Erfassung") {
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
        if (window.location.href == "http://kluby.local/CRM/Erfassung") {
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

    function rowNewClick(object) {
        $("tr.rowsNewPerson").removeAttr( 'style' );
	var idVal = object.attr('id');
	object.css('background-color', "rgb(238, 193, 213)");
	$('.deleteButtonContact').attr('id', idVal);
	$('.deleteButtonContact').prop('disabled', false);
    }

    function rowPersonClick(object) {
        $("tr.rowsBearbeitenPerson").removeAttr( 'style' );
        var idVal = object.attr('id');
        object.css('background-color', "rgb(238, 193, 213)");
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
        $("tr.rowsBearbeitenAddress").removeAttr( 'style' );
        $('.deleteBearbeitenButtonAddress').prop('disabled', true);
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
    	$(this).css('background-color', "rgb(238, 193, 213)");
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
        }
        if (ort == '') {
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
	var addressName = $('input[name=hiddenAddressName]').val();
        var abteilung = $('input[name=hiddenAddressAbteilung]').val();
        var anschrift = $('input[name=hiddenAddressAnschrift]').val();
        var anschrift2 = $('input[name=hiddenAddressAnschrift2]').val();
        var plz = $('input[name=hiddenAddressPlz]').val();
        var ort = $('input[name=hiddenAddressOrt]').val();
        var clientId = $( '#hiddenClientId' ).val();
	alert(addressName + abteilung + anschrift + anschrift2 + plz + ort); return false;
    });

    $('.hiddenNewContactTr').change(function() {
        function validateEmail(email) 
        {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }
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
                        var tableRow = '<tr class="clickable-row rowsNewPerson"  name="' + rowId + '" id="' + rowId + '">';
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
                        $("tr.rowsNewPerson").click(function(){
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

        function validateEmail(email) 
        {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }
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
                    var toDelete = $('.rowsNewPerson[name=' + idValue + ']');
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
	var value = $( this ).attr('id');
	if (value == 'zahlungsziel_id') {
	  var column = value;
	  var value = $('select[name=zahlungszielClient]').attr('id');
	  var name = $('select[name=zahlungszielClient] option:selected').val();
	  var selectText = $('select[name=zahlungszielClient] option:selected').text();
	} else {
	    var name = $( this ).children().val();
	    var column = $( this ).children().attr("name");
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
		function validateEmail(email) 
        {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }
        function isInteger(value)      
        {       
            num = value.trim();         
            return !(value.match(/\s/g)||num==""||isNaN(num)||(typeof(value)=='number'));        
        }
        $( '.newNameError' ).removeClass('form-group has-error').addClass('form-group');
		var name = $('input[name=newPopupName]').val();
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
		var paymentOpt = $('select[name=newPopupPaymentOpt] option:selected').val();
		if (name == '') {
            error = true;
            $( '#newNameDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (departement == '') {
        	error = true;
            $( '#newDepartementDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (address == '') {
        	error = true;
            $( '#newAddressDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (place == '') {
        	error = true;
            $( '#newPlaceDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (code == '') {
        	error = true;
            $( '#newCodeDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (phone == '') {
        	error = true;
            $( '#newPhoneDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (mail == '') {
        	error = true;
            $( '#newMailDiv' ).removeClass('form-group').addClass('form-group has-error');
        } else if (emailCheck == false) {
            error = true;
            $( '#newMailDiv' ).removeClass('form-group').addClass('form-group has-error');
        }
        if (skonto != '') {
            skonto = skonto.replace(',', '.');
            var skontoCheck = isInteger(skonto);
            if (skontoCheck == false) {
                error = true;
                $( '#newSkontoDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (error == true) {
            return false;
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
                            alert(finalResult);
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
});