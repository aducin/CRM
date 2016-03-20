$( document ).ready(function() {

    $( "#newButtonFremdarbeiten" ).click(function() {
        $( '#hiddenTrFremdarbeiten' ).fadeIn( 'slow' );
        $( '#newButtonFremdarbeiten' ).hide();
        $( '#hideButtonFremdarbeiten' ).show();
    });

    $("tr.rowsFremdarbeiten").click(function(){
       $("tr.rowsFremdarbeiten").css('background-color', "#f9f9f9");
       var idVal = $(this).attr('id');
       $(this).css('background-color', "rgb(238, 193, 213)");
       $('.deleteButtonFremdarbeiten').attr('id', idVal);
       $('.deleteButtonFremdarbeiten').prop('disabled', false);
    });

    $( ".deleteButtonFremdarbeiten" ).click(function() {
        var idValue = $('.deleteButtonFremdarbeiten').attr('id');
        alert(idValue);
    });

    $( "#hideButtonFremdarbeiten" ).click(function() {
        $( '#hiddenTrFremdarbeiten' ).fadeOut( 'slow' );
        $( this ).hide();
        $( '#newButtonFremdarbeiten' ).show();
    });
});