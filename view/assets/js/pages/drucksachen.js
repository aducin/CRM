$( document ).ready(function() {
  
    function checkCheckbox(variable) {
        var ifChecked = variable.is(":checked");
        var value = variable.attr('id');
        var rowId = variable.parent().parent().attr('id');
        if (ifChecked == true) {
            var checkbox = 1;
        } else {
            var checkbox = 0;
        }
        var date = value + '<>' + checkbox;
        changeDate(date, rowId); 
    }

	function changeDate(curDate, drucksache) {
        var values = 'Drucksache<>' + drucksache + '<>' + curDate;
        if (window.location.href == urlPath) {
            console.log('No project at this time');
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
        $('.drucksacheToUpdate').hide();
        $('.drucksacheToChange').show();
        var value = variable.attr('id');
        var rowId = variable.parent().attr('id');
        var next = variable.next();
        variable.hide();
        next.show();
        next.find('input:first').focus();
        next.find('input:first').select();
    }

    function checkMachineName(name, previous) {
        var projectId = $( '#hiddenProjectId').val();
        if (window.location.href == urlPath) {
            console.log('No project at this time');
        } else {
            var path = "../Api/Select/";
            $.ajax({url: path + 'Maschine-' + name,
                type: "get",
                success: function(result)
                {
                    if(result != 'false') {
                        name = result;
                        previous.text( name );
                    } else {
                        $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                    }
                }
            }); 
        }
    }

    function columnChange(variable) {
      var name = variable.children().val();
      var value = variable.attr('id');
      var previous = variable.prev();
      if (value == 'amount') {
        name = name.replace(",", ".");
        function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
        var check = isNumber(name);
        if (check == false) {
          variable.children().val(previous.text());
          console.log('Inkorrektes format!');
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
      } else if( value == 'machine' ) {
        var name = variable.find(":selected").attr("id");
        if (name == "none") {
          return false;
        }
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
            if (value == 'amount') {
              previous.html( name );
              var projectId = $( '#hiddenProjectId').val();
              getAmount(projectId);
            } else if (value == 'machine') {
              checkMachineName(name, previous);
            }else {
              previous.html( name );
            }
            variable.hide();
            variable.prop('disabled', false);
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
      $.ajax({url: path + 'Drucksache-' + projectId,
	type: "get",
	success: function(result)
	{
	  if(result != 'false') {
	    $( '#totalDrucksache' ).text(result + ' EURO');
	  } else {
	    $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
	  }
	}
    });
  }

  function getRowId(variable) {
   $("tr.rowsDrucksachen").css('background-color', "#f9f9f9");
   var idVal = variable.attr('id');
   variable.css('background-color', "#e9e9e9");
   $('.deleteButtonDrucksachen').attr('id', idVal);
   $('.deleteButtonDrucksachen').prop('disabled', false);
   $('.cloneDruckButton').attr('id', idVal);
   $('.cloneDruckButton').prop('disabled', false);
 }

 $( "#newDrucksache" ).click(function() {
  $( '#hiddenTrDrucksachen' ).fadeIn( 'slow' );
  $( '#newDrucksache' ).hide();
  $( '.deleteButtonDrucksachen' ).hide();
  $( '.deleteButtonDrucksachen' ).prop('disabled', true);
  $( '.deleteButtonDrucksachen' ).attr('id', '');
  $( '#saveButtonDrucksache' ).show();
  $( '#hideButtonDrucksache' ).show();
  $("tr.rowsDrucksachen").css('background-color', "#f9f9f9");
  $('.cloneDruckButton').prop('disabled', true);
  $('.cloneDruckButton').attr('id', '');
});

  $("tr.rowsDrucksachen").click(function(){
    getRowId($(this));
  });
    
  $( ".cloneDruckButton" ).click(function() {
    $("tr.rowsDrucksachen").css('background-color', "#f9f9f9");
    var idValue = $('.cloneDruckButton').attr('id');
    var clone = $(".rowsDrucksachen[name=" + idValue + "]").clone(true, true);
    if (window.location.href == urlPath) {
      path = "Api/Clone/";
    } else {
      path = "../Api/Clone/";
      var concrete = true;
    }
    $.ajax({url: path + idValue,
      type: "get",
      success: function(result)
      {
        if(result != 'false') {
          $( ".cloneDruckButton" ).attr('id','');
          $( ".deleteButtonDrucksachen" ).attr('id','');
          clone.attr('id', result);
          clone.attr("name", result);
          clone.children(3).attr('id', result);
                    //clone.appendTo("#tableBody");
                    var rows = document.getElementById("drucksacheTable").rows.length;
                    var number = rows -2;
                    $( '#drucksacheTable > tbody > tr:nth-child(' + number + ')' ).after(clone);
		    var projectId = $( '#hiddenProjectId' ).val();
                    getAmount(projectId);
                  } else {
                    $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                  }
              }
          }); 
  });

  $( ".deleteButtonDrucksachen" ).click(function() {
        var idValue = $('.deleteButtonDrucksachen').attr('id');
        var values = 'delete-Drucksache-' + idValue;
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
                        var toDelete = $('.rowsDrucksachen[name=' + idValue + ']');
                        toDelete.remove();
                        $('.deleteButtonDrucksachen').prop('disabled', true);
                  			$('.cloneDruckButton').prop('disabled', true);
                  			$('.cloneDruckButton').attr('id', '');
                        $('.deleteButtonDrucksachen').attr('id', '');
                        var projectId = $( '#hiddenProjectId' ).val();
                        getAmount(projectId);
                    } else {
                      $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                    }
                }
            }); 
        }
    });

    $( "#hideButtonDrucksache" ).click(function() {
        $( '#hiddenTrDrucksachen' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#saveButtonDrucksache' ).hide();
        $( '#newDrucksache' ).show();
        $( '.deleteButtonDrucksachen' ).show();
    });

    $( "#saveButtonDrucksache" ).click(function() {
        $( '.druckClassDiv' ).removeClass('form-group has-error').addClass('form-group');
        var print = $('input[name=hiddenDrucksachenPrint]').val();
        var machine = $('select[name=hiddenDrucksachenMachine]').find('option:selected').attr('id');
        var machineName = $( "select[name=hiddenDrucksachenMachine] option:selected" ).text();
        var type = $('input[name=hiddenDrucksachenType]').val();
        var edition = $('input[name=hiddenDrucksachenEdition]').val();
        var format = $('input[name=hiddenDrucksachenFormat]').val();
        var size = $('input[name=hiddenDrucksachenSize]').val();
        var color = $('input[name=hiddenDrucksachenColor]').val();
        var paper = $('input[name=hiddenDrucksachenPaper]').val();
        var finished = $('input[name=hiddenDrucksachenFinished]').is(":checked");
        if (finished == true) {
            finished = 1;
        } else {
            finished = 0;
        }
        if (machine == 'none') {
	    var error = true;
	    $( '#drucksacheMachineDiv' ).removeClass('form-group').addClass('form-group has-error');
	}
        var remodelling = $('input[name=hiddenDrucksachenRemodelling]').val();
        var amount = $('input[name=hiddenDrucksachenAmount]').val();
        amount = amount.replace(',' , '.');
        amount = parseFloat(Math.round(amount * 100) / 100).toFixed(2);
        var firstCheck = isNumber(edition);
        var secondCheck = isNumber(amount);
        if (firstCheck == false) {
            if (edition.length != 0) {
                var error = true;
                $( '#drucksacheFirstAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (secondCheck == false) {
            if (amount.length != 0) {
                var error = true;
                $( '#drucksacheSecondAmountDiv' ).removeClass('form-group').addClass('form-group has-error');
            }
        }
        if (error == true) {
          return false;
        }
        var projectId = $( '#hiddenProjectId' ).val();
        var values = 'insert-Drucksache-' + projectId + '<>' + print + '<>' + machine + '<>' + type + '<>' + edition + '<>' + format + '<>' + size + '<>' + color + '<>' + paper + '<>' + remodelling + '<>' + finished + '<>' + amount;
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
                    $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
                } else {
            		  var clonedTable2 = $( '#hiddenDrucksachenMachine').clone();
            		  clonedTable2.attr("id", "drucksachenClonedTable");
            		  clonedTable2.attr("name", "drucksachenClonedTable");
                  clonedTable2.children('option:first').remove();
                  clonedTable2.find('option[value="' + machine + '"]').remove();
                  clonedTable2.prepend('<option value="' + machine + '" id="' + machine + '">' + machineName + '</option>');
                  var tableRow = '<tr class="clickable-row rowsDrucksachen" name="' + result + '" id="' + result + '">';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="print">';
                  if (print.length == 0) {
                    tableRow += '<i>keine Daten</i>';
                  } else {
                    tableRow +=  print; 
                  }
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="print" style="display: none;"><input type="text" class="form-control datepicker" size="9" placeholder="Bitte wählen" name="hiddenPrint" value="' + print + '" /></div></td>';
                  tableRow += '<td id="' + result + '" style="text-align: center;">';
                  tableRow += '<div class="drucksacheToChange" id="machine">';
                  if (machine == 1) {
                      tableRow += 'SpeedMaster';
                  } else if (machine == 2) {
                      tableRow +=  'GTO';
                  } else {
                      tableRow += '<i>keine Daten</i>';
                  }
                  tableRow += '</div>';
            		  tableRow += '<div class="drucksacheToUpdate toBeClonedDruck'+ result +'" id="machine" style="display: none;">';
            		  tableRow += '</div></td>';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="type">';
                  if (type.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  type; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="type" style="display: none;"><input type="text" class="form-control" name="hiddenType" value="' + type + '" /></div>';
                  tableRow += '</td>';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="edition">';
                  if (edition.length == 0) {
                      tableRow += '0';
                  } else {
                      tableRow +=  edition; 
                  }
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="edition" style="display: none;"><input type="text" class="form-control" name="hiddenEdition" value="' + edition + '" /></div>';
                  tableRow += '</td><td id="' + result + '"><div class="drucksacheToChange" id="format">';
                  if (format.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  format; 
                  }
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="format" style="display: none;"><input type="text" class="form-control" name="hiddenFormat" value="' + format + '" /></div>';
                  tableRow += '</td>';

                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="size">';
                  if (size.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  size; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="size" style="display: none;"><input type="text" class="form-control" name="hiddenSize" value="' + size + '" /></div>';
                  tableRow += '</td>';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="color">';
                  if (color.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  color; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="color" style="display: none;"><input type="text" class="form-control" name="hiddenColor" value="' + color + '" /></div>';
                  tableRow += '</td>';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="paper">';
                  if (paper.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  paper; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="paper" style="display: none;"><input type="text" class="form-control" name="hiddenPaper" value="' + paper + '" /></div>';
                  tableRow += '</td>';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="remodelling">';
                  if (remodelling.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  remodelling; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="remodelling" style="display: none;"><input type="text" class="form-control" name="hiddenRemodelling" value="' + remodelling + '" /></div>';
                  tableRow += '</td>';
                  tableRow += '<td class="v-center" id="' + result + '"><div class="checkbox">';
                  tableRow += '<input name="drucksachenFinished" id="finished" type="checkbox"';
                  if (finished == 1) {
                    tableRow += 'checked ="checked"';
                  }
                  tableRow += '><label></label></div></td>';
                  tableRow += '<td id="' + result + '"><div class="drucksacheToChange" id="finished">';
                  if (amount.length == 0) {
                      tableRow += '<i>keine Daten</i>';
                  } else {
                      tableRow +=  amount + ' EURO'; 
                  } 
                  tableRow += '</div>';
                  tableRow += '<div class="drucksacheToUpdate" id="amount" style="display: none;"><input type="text" class="form-control" name="hiddenAmount" value="' + amount + '" /></div>';
                  tableRow += '</td>';
                  tableRow += '</tr>'; 
                  $( '#hideButtonDrucksache' ).hide();   
                  $( '#saveButtonDrucksache' ).hide(); 
                  $( "#newDrucksache" ).show();
                  $( '.deleteButtonDrucksachen' ).show();
                  var rows = document.getElementById("drucksacheTable").rows.length;
                  if (rows == 2) {
                      $( '#drucksacheTable' ).prepend(tableRow);
                  } else {
                      var number = rows -2;
                      $( '#drucksacheTable > tbody > tr:nth-child(' + number + ')' ).after(tableRow);
                  }
                  $( '#hiddenTrDrucksachen' ).hide(); 
            		  var toBeCloned = $( '.toBeClonedDruck' );
            		  $( '.toBeClonedDruck' + result ).append(clonedTable2);

                  $("#drucksacheTable").on("click", "tr", function(){
                      getRowId($(this));
                  });
                  $( ".drucksacheToChange" ).dblclick(function() {
                      changeRow($(this));
                  });
                  $('.drucksacheToUpdate').change(function() {
                      columnChange($(this));
                  });
                  $('input[name=drucksachenFinished]').change(function() {
                      checkCheckbox($(this));
                  });
                  getAmount(projectId);
                  $('input[name=hiddenDrucksachenPrint]').val('');
                    $('select[name=hiddenDrucksachenMachine]').find('option:selected').attr('id');
                    $('input[name=hiddenDrucksachenType]').val('');
                    $('input[name=hiddenDrucksachenEdition]').val('');
                    $('input[name=hiddenDrucksachenFormat]').val('');
                    $('input[name=hiddenDrucksachenSize]').val('');
                    $('input[name=hiddenDrucksachenColor]').val('');
                    $('input[name=hiddenDrucksachenPaper]').val('');
                    $('input[name=hiddenDrucksachenFinished]').attr('checked', false);
                    $('input[name=hiddenDrucksachenRemodelling]').val('');
                    $('input[name=hiddenDrucksachenAmount]').val('');
                }
            }
        });
    }
});
    
    $( ".drucksacheToChange" ).dblclick(function() {
	$( this ).next().prop('disabled', false);
        $( this ).next().children().prop('disabled', false);
        changeRow($(this));
    });
    
    $('input[name=drucksachenFinished]').change(function() {
    	checkCheckbox($(this));
    });
    
    $('.drucksacheToUpdate').change(function() {
	    $( this ).children().css('border-color', '');
    	var name = $( this ).children().val();
    	var value = $( this ).attr('id');
    	var previous = $( this ).prev();
    	function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
    	if( value == 'machine') {
    		var name = $(this).find(":selected").attr("id");
    		if (name == "none") {
    			return false;
    		}
    	} else if (value == 'amount') {
        if (name != '') {
      		name = name.replace(",", ".");
      		var check = isNumber(name);
      		var name2 = name.split(".");
      		if (name2[1] == null) {
      			name = name + '.00';
      		} else if (name2[1].length == 1) {
      			name = name + '0';
      		} else if (name2[1].length == 0) {
      			name = name + '00';
      		}
        }
    	} 
    	if (value == 'edition' || value == 'amount') {
        if (name != '') {
      		var check = isNumber(name);
      		if (check == false) {
      			$( this ).children().css('border-color', '#a94442');
      			$( this ).children().focus();
      			$( this ).children().select();
      			return false;
      		}
        }
    	}
    	var rowId = $( this ).parent().attr('id');
    	var date = value + '<>' + name;
	if( value == 'machine') {
	    $( this ).prop('disabled', true);
	} else {
	    $( this ).children().prop('disabled', true);
	}
    	changeDate(date, rowId);
	    var timerId = setInterval(function() {
	     if(finalResult !== null) {
		    if(finalResult == 'done') {
          if (name == '') {
            name = '<i>keine Daten</i>';
          }
    	    if( value == 'amount') {
      		  if (name != '<i>keine Daten</i>') {
              previous.text( name + ' EURO' );
            } else {
              previous.text( '0.00 EURO' );
            }
    		    var projectId = $( '#hiddenProjectId').val();
    		    if (window.location.href == urlPath) {
    			   console.log('No project at this time');
    		} else {
    			var path = "../Api/Amount/";
    			$.ajax({url: path + 'Drucksache-' + projectId,
    				type: "get",
    				success: function(result)
    				{
    					if(result != 'false') {
    						$( '#totalDrucksache' ).text(result + ' EURO');
    					} else {
    						$('#ajaxError').fadeIn('slow').delay(5000).hide(1);
    					}
    				}
    			}); 
    		}
    	} else if( value == 'machine' ) {
    		checkMachineName(name, previous);
    	} else {
        if (name == '<i>keine Daten</i>') {
          if (value == 'edition') {
            name = 0;
          }
        }
    		previous.html( name );
    	}
    	$('.drucksacheToUpdate').hide();
	$( this ).children().prop('disabled', false);
    	previous.show();
	    }
	     clearInterval(timerId);
	    } else {
		    $('#ajaxError').fadeIn('slow').delay(5000).hide(1);
	    }
	 }, 1500);
    });
});