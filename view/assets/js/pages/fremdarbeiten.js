$( document ).ready(function() {

  $('input[name=hiddenTextDate]').datepicker( {
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

  function changeDate(curDate, fremdsache) {
    var values = 'Fremdsache<>' + fremdsache + '<>' + curDate;
    if (window.location.href == urlPath) {
      alert('No project at this time');
    } else {
      var path = "../Api/Row/";
      $.ajax({url: path,
        type: "post",
        data: { 'action' : 'ajax', 'concrete' : 'tableUpdate', 'value' : values },
        success: function(result)
        {
          if (result == 'false') {
            $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
            return false;
          }  else {
            finalResult = result;
          }
        }
      }); 
    }
  }

  function changeRow(variable) {
    $('.fremdarbeitenToUpdate').hide();
    $('.fremdarbeitenToChange').show();
    var value = variable.attr('id');
    if (value == 'textDate') {
        $( this ).next().addClass('hiddenDateFremdarbeiten');
    }
    var rowId = variable.parent().attr('id');
    var next = variable.next();
    variable.hide();
    next.show();
    next.find('input:first').focus();
    next.find('input:first').select();
  }

  function columnChange(variable) {
    variable.children().css('border-color', '');
    var name = variable.children().val();
    var value = variable.attr('id');
    var previous = variable.prev();
    if (value == 'purchasePrice' || value == 'sellPrice') {
      name = name.replace(",", ".");
      function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
      if (name != '') {
        var check = isNumber(name);
        if (check == false) {
        	variable.children().css('border-color', '#a94442');
        	variable.children().focus();
        	variable.children().select();
          return false;
        }
      }
      var name2 = name.split(".");
      if (name2[1] == null) {
        name = name + '.00';
      } else if (name2[1].length == 1) {
        name = name + '0';
      } else if (name2[1].length == 0) {
        name = name + '00';
      }
    } else if (value == 'textDate') {
     var exploded = name.split("/");
     name = exploded[2] + '/' + exploded[1] + '/' + exploded[0];
    }
    var rowId = variable.parent().attr('id');
    var date = value + '<>' + name;
    variable.children().prop('disabled', true);
    changeDate(date, rowId);
    var timerId = setInterval(function() {
	     if(finalResult !== null) {
		if(finalResult == 'done') {
      if (name == '') {
        name = '<i>keine Daten</i>';
      }
      if (value == 'purchasePrice' || value == 'sellPrice') {
        if (name == '.00') {
          name = '<i>keine Daten</>';
        } else {
	  name = name + ' EURO';
	}
		    previous.html( name );
		    var projectId = $( '#hiddenProjectId').val();
		    getAmount(projectId);
		  } else if (value == 'textDate') {
		    var exploded = name.split("/");
		    name = exploded[2] + '/' + exploded[1] + '/' + exploded[0];
		    previous.html( name );
		  }else {
		    previous.html( name );
		  }
		  variable.hide();
      variable.children().prop('disabled', false);
		  previous.show();
		  }
		  clearInterval(timerId);
	     } else {
		$('#ajaxError').fadeIn('slow').delay(5000).hide(1);
	     }
	 }, 1500);
  }


  function getAmount(projectId) {
    if (window.location.href == urlPath) {
      console.log('No project at this time');
    } else {
      var path = "../Api/Amount/";
    }      
    $.ajax({url: path + 'Fremdsache-' + projectId,
      type: "get",
      success: function(result)
      {
        if(result != 'false') {
          $( '#totalFremdarbeiten' ).text(result + ' EURO');
        } else {
          $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
        }
      }
    });
  }

  function getRowId(variable) {
    $("tr.rowsFremdarbeiten").removeAttr( 'style' );
    var idVal = variable.attr('id');
    variable.css('background-color', "#e9e9e9");
    $('.deleteButtonFremdarbeiten').attr('id', idVal);
    $('.deleteButtonFremdarbeiten').prop('disabled', false);
  }

  $( '#saveButtonFremdarbeiten' ).click(function() {
    $( '.fremdClassDiv' ).removeClass('form-group has-error').addClass('form-group');
    function isNumber(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    } 
    $( '#hiddenFirstAmountDiv' ).removeClass('form-group has-error').addClass('form-group');
    $( '#hiddenSecondAmountDiv' ).removeClass('form-group has-error').addClass('form-group');
    var textDate = $( '#hiddenDateFremdarbeiten' ).val();
    var deliverer = $( '#hiddenCarrier' ).val();
    var description = $( '#hiddenDesc' ).val();
    var purchasePrice = $( '#hiddenFirstAmount' ).val();
    purchasePrice = purchasePrice.replace(',', '.');
    purchasePrice = parseFloat(Math.round(purchasePrice * 100) / 100).toFixed(2);
    var sellPrice = $( '#hiddenSecondAmount' ).val();
    sellPrice = sellPrice.replace(',', '.');
    sellPrice = parseFloat(Math.round(sellPrice * 100) / 100).toFixed(2);
    var firstCheck = isNumber(purchasePrice);
    var secondCheck = isNumber(sellPrice);
    if (textDate.length == 0) {
      var error = true;
      $( '#hiddenDateErrorDiv' ).removeClass('form-group').addClass('form-group has-error');
    }
    if (firstCheck == false) {
      if (purchasePrice.length != 0) {
        var error = true;
        $( '#hiddenFirstAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
      }
    }
    if (secondCheck == false) {
      if (sellPrice.length != 0) {
        var error = true;
        $( '#hiddenSecondAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
      }
    }
    if (error == true) {
      return false;
    }
    var projectId = $( '#hiddenProjectId' ).val();
    var values = 'insert-Fremdsache-' + projectId + '<>' + textDate + '<>' + deliverer + '<>' + description + '<>' + purchasePrice + '<>' + sellPrice;
    if (window.location.href == urlPath) {
      var path = "/Api/Row/";
    } else {
      var path = "../Api/Row/";
      $.ajax({url: path,
        type: "post",
        data: { 'action' : 'ajax', 'concrete' : 'row', 'value' : values },
        success: function(result)
        {
          if (result == 'false') {
            $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
          } else {
            var tableRow = '<tr class="clickable-row rowsFremdarbeiten" name="' + result + '" id="' + result + '">';
            tableRow += '<td id="' + result + '"><div class="fremdarbeitenToChange" id="textDate">';
            if (textDate.length == 0) {
              tableRow += '<i>keine Daten</i>';
            } else {
              tableRow +=  textDate; 
            }
            tableRow += '</div>';
            tableRow += '<div class="fremdarbeitenToUpdate" id="textDate" style="display: none;"><input type="text" class="form-control datepicker" size="9" placeholder="Bitte wählen" name="hiddenTextDate" value="' + textDate + '" /></div></td>';
            tableRow += '<td id="' + result + '">';
            tableRow += '<div class="fremdarbeitenToChange" id="deliverer">';
            if (deliverer.length == 0) {
              tableRow += '<i>keine Daten</i>';
            } else {
              tableRow +=  deliverer; 
            } 
            tableRow += '</div>';
            tableRow += '<div class="fremdarbeitenToUpdate" id="deliverer" style="display: none;"><input type="text" class="form-control" name="hiddenDeliverer" value="' + deliverer + '" /></div>';
            tableRow += '</td><td id="' + result + '"><div class="fremdarbeitenToChange" id="description">';
            if (description.length == 0) {
              tableRow += '<i>keine Daten</i>';
            } else {
              tableRow +=  description; 
            } 
            tableRow += '</div>';
            tableRow += '<div class="fremdarbeitenToUpdate" id="description" style="display: none;"><input type="text" class="form-control" name="hiddenDescription" value="' + description + '" /></div>';
            tableRow += '</td><td id="' + result + '"><div class="fremdarbeitenToChange" id="purchasePrice">';
            if (purchasePrice.length == 0) {
              tableRow += '0';
            } else {
              tableRow +=  purchasePrice + ' EURO'; 
            }
            tableRow += '</div>';
            tableRow += '<div class="fremdarbeitenToUpdate" id="purchasePrice" style="display: none;"><input type="text" class="form-control" name="hiddenPurchasePrice" value="' + purchasePrice + '" /></div>';
            tableRow += '</td><td id="' + result + '"><div class="fremdarbeitenToChange" id="sellPrice">';
            if (sellPrice.length == 0) {
              tableRow += '0';
            } else {
              tableRow +=  sellPrice + ' EURO'; 
            }
            tableRow += '</div>';
            tableRow += '<div class="fremdarbeitenToUpdate" id="sellPrice" style="display: none;"><input type="text" class="form-control" name="hiddenSellPrice" value="' + sellPrice + '" /></div>';
            tableRow += '</td></tr>'; 
            $( '#hideButtonFremdarbeiten' ).hide();   
            $( '#saveButtonFremdarbeiten' ).hide(); 
            $( "#newButtonFremdarbeiten" ).show();
            var rows = document.getElementById("tableFrendarbeiten").rows.length;
            $( '#hiddenTrFremdarbeiten' ).hide(); 
            $( '#hiddenDateFremdarbeiten' ).val('');
            $( '#hiddenCarrier' ).val('');
            $( '#hiddenDesc' ).val('');
            $( '#hiddenFirstAmount' ).val('');
            $( '#hiddenSecondAmount' ).val('');    
            if (rows == 2) {
              $( '#tableFrendarbeiten' ).prepend(tableRow);
            } else {
              var number = rows -2;              
            $( '#tableFrendarbeiten > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
            }
            $('input[name=hiddenTextDate]').datepicker( "destroy" );
            $('input[name=hiddenTextDate]').datepicker({ dateFormat: "dd/mm/yy" });
            $("#tableFrendarbeiten").on("click", "tr", function(){
              getRowId($(this));
            });
            $( ".fremdarbeitenToChange" ).dblclick(function() {
              changeRow($(this));
            });
            $('.fremdarbeitenToUpdate').change(function() {
              columnChange($(this));
            });
            getAmount(projectId);
          }
        }
      }); 
    }
  });

  $( "#newButtonFremdarbeiten" ).click(function() {
    $("tr.rowsFremdarbeiten").css('background-color', "#f9f9f9");
    $( '#hiddenTrFremdarbeiten' ).fadeIn( 'slow' );
    $( '#newButtonFremdarbeiten' ).hide();
    $( '#saveButtonFremdarbeiten' ).show();
    $( '#hideButtonFremdarbeiten' ).show();
    $('.deleteButtonFremdarbeiten').prop('disabled', true);
    $('.deleteButtonFremdarbeiten').attr('id', '');
  });

  $("tr.rowsFremdarbeiten").click(function(){
    getRowId($(this));
  });

  $( ".deleteButtonFremdarbeiten" ).click(function() {
        var idValue = $('.deleteButtonFremdarbeiten').attr('id');
        var values = 'delete-Fremdsache-' + idValue;
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
                        var toDelete = $('.rowsFremdarbeiten[name=' + idValue + ']');
                        toDelete.remove();
                        $('.deleteButtonFremdarbeiten').prop('disabled', true);
                        $('.deleteButtonFremdarbeiten').attr('id', '');
                        var projectId = $( '#hiddenProjectId' ).val();
                        getAmount(projectId);
                    } else {
                      $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                    }
                }
            }); 
        }
    });

  $( "#hideButtonFremdarbeiten" ).click(function() {
    $( '#hiddenTrFremdarbeiten' ).fadeOut( 'slow' );
    $( '#saveButtonFremdarbeiten' ).fadeOut( 'slow' );
    $( this ).hide();
    $( '#newButtonFremdarbeiten' ).show();
  });

  $( ".fremdarbeitenToChange" ).dblclick(function() {
    changeRow($(this));
  });

  $('.fremdarbeitenToUpdate').change(function() {
      columnChange($(this));
  });
});