$( document ).ready(function() {

    //$( '#datumsbereich-von' ).css('visibility', 'visible');
    //$( '#datumsbereich-bis' ).css('visibility', 'visible');
    
    $('#datumsbereich-von').datepicker( {
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

    var begin = $( '#vonToChange' ).val();
    var finish = $( '#bisToChange' ).val();
    $('#datumsbereich-von').val(begin);
    $('#datumsbereich-bis').val(finish);
    $('#datumsbereich-von').datepicker("option", "dateFormat", "dd/mm/yy");
    $('#datumsbereich-bis').datepicker("option", "dateFormat", "dd/mm/yy");

    $( "#logoutButton" ).click(function() {
        $( '#logoutButton' ).addClass('active');
        $( '#listeButton' ).removeClass('active');
    });

    $( "#auftraggeber" ).keyup(function() {
        $( '#auftraggeberDiv' ).removeClass('form-group has-error').addClass('form-group');
        $('#ajaxMessage').html('Auftraggeber');
        var text = $(this).val();
        $.ajax({url: "index.php", 
            type: "post",
            data: {
            "action": 'ajax',
            "concrete": 'clientSearch',
            "value": text,
        },
        success: function(result)
        {
            if (result == 'false') {
                $('#ajaxErrorConfig4').fadeIn('slow').delay(5000).hide(1);
                return false;
            }
            var jsonData = JSON.parse(result);
            switch(jsonData){
                case 'error':
                    $( '#auftraggeberDiv' ).removeClass('form-group').addClass('form-group has-error');
                    $('#ajaxMessage').html('Nichts gefunden');
                    break;

                default:
                    var arr = jsonData.map(function(object){ return object.name });

                    $( "#auftraggeber" ).autocomplete({
                    source: arr
                    });
                }
            }
        });
    });

    $( ".tableRow" ).click(function() {
        var projectId = $(this).attr('id');
        $.ajax({url: "index.php", 
            type: "post",
            data: {
            "action": 'ajax',
            "concrete": 'getToTheProject',
            "value": projectId,
            },
        })
    });

    $( "#searchButton" ).click(function() {
        var beginDate = $('#datumsbereich-von').val();
        var endDate = $('#datumsbereich-bis').val();
        var projectName = $('#freitextsuche').val();
        var clientName = $('#auftraggeber').val();
        var eventNumber = $('#vorgangsnummer').val();
        var clientOrderNumber = $('#kundenauftragsnummer').val();
        $('#searchForm').submit();
    });
});