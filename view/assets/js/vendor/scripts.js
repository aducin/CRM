$(document).ready(function() {
    $('#list-sortable').DataTable({
        "paging":   false,
        "info":     false,
        "bFilter": false
    });
    $( ".datepicker" ).datepicker({
      "dateFormat": 'mm/dd/yy',
      buttonImageOnly: true
    });
    $( ".datepicker-opener" ).on('click', function(){
      $(this).parent().children('.datepicker').datepicker("show");
    });    
} );