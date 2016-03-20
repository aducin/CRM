$(function() {
    $( "#configButton" ).click(function() {
        var userId = $( '#idValue' ).val();
        if ( userId == 1 ) {
            $( '#config' ).submit();
        } else {
            alert( 'Sie sind nicht erlaubt!');
        }
    });
});