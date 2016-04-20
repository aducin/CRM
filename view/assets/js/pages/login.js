 $( document ).ready(function() {

            function isValidEmailAddress(emailAddress) {
                var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
                return pattern.test(emailAddress);
            };

            function displayError(message) {
                $( '#errorMessage' ).html(message);
                $( '#errorMessage' ).css("text-align", "center");
                $( '#errorMessage' ).show();
            }

            function displayChangeError(message) {
                $( '#errorChangeMessage' ).html(message);
                $( '#errorChangeMessage' ).css("text-align", "center");
                $( '#errorChangeMessage' ).show();
            }

            function displayForgottenError(message) {
                $( '#forgottenErrorMessage' ).html(message);
                $( '#forgottenErrorMessage' ).css("text-align", "center");
                $( '#forgottenErrorMessage' ).show();
            }
            
            $( "#passwordChange" ).click(function() {
                var options = {};
                $( '#currentLoginForm' ).hide();
                $( '#forgottenPasswordMainDiv' ).show();
                return false;
            });

            $( "#toLogin" ).click(function() {
                var options = {};
                $( '#changePasswordMainDiv' ).hide();
                $( '#currentLoginForm' ).show();
            });
            
            $( "#zurück" ).click(function() {
                var options = {};
                $( '#forgottenPasswordMainDiv' ).hide();
                $( '#currentLoginForm' ).show();
            });

            $( "#passwordChangeSubmit" ).click(function($e) {
                $e.preventDefault();
                $( '#forgottenEmailDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#forgottenEmailSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#forgottenPasswordDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#forgottenPasswordSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#forgottenErrorMessage' ).addClass('alert-danger');
                $( '#forgottenErrorMessage' ).removeAttr("style");
                $( '#forgottenErrorMessage' ).hide();
                var forgottenMail = $( '#forgottenEmail' ).val();
                var forgottenPassword = $( '#forgottenPassword' ).val();
                if ( !forgottenMail && !forgottenPassword ) {
                    displayForgottenError('Füllen Sie E-mail und Passwort auf!');
                    $( '#forgottenEmailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#forgottenEmailDiv' ).removeClass('form-group').addClass('form-group has-error');
                    $( '#forgottenPasswordSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#forgottenPasswordDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else if (!forgottenMail) {
                    displayForgottenError('Füllen Sie E-mail auf!');
                    $( '#forgottenEmailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#forgottenEmailDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else if ( !isValidEmailAddress( forgottenMail )) {
                    displayForgottenError('Unrichtige E-Mail Format');
                    $( '#forgottenEmailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#forgottenEmailDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else if (!forgottenPassword) {
                    displayForgottenError('Füllen Sie ein einmaliges Passwort auf!');
                    $( '#forgottenPasswordSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#forgottenPasswordDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else {
                    var dates = [ forgottenMail, forgottenPassword ];
                    $.ajax({url: "index.php", 
                        type: "post",
                        data: {
                            "action": 'ajax',
                            "concrete": 'forgottenPassword',
                            "value": dates,
                            },
                            success: function(result){
                                if (result == 'false') {
                                    $( '#forgottenErrorMessage').html('Keine Verbindung mit der Datenbank erstellt.');
                                    $( '#forgottenErrorMessage').css("text-align", "center");
                                    $( '#forgottenErrorMessage').show();
                                    return false;
                                }
                                var jsonData = JSON.parse(result);
                                if (jsonData.success == 'true') {
                                    $( '#forgottenErrorMessage' ).removeClass('alert-danger').addClass('alert-success');
                                    $( '#forgottenErrorMessage' ).css("text-align", "center");
                                    $( '#forgottenErrorMessage' ).html('Hallo <b>' + jsonData.name + '</b>!<br>Eine eMail wird an die angegebene Adresse gesendet.');
                                    $( '#divToHide' ).hide();
                                    $( '#forgottenErrorMessage' ).fadeIn('slow');
                                } else {
                                    $( '#forgottenErrorMessage').html(jsonData.message);
                                    $( '#forgottenErrorMessage').css("text-align", "center");
                                    $( '#forgottenErrorMessage').show();
                                }
                            }
                    });       
                }
            });

            $( "#password3Submit" ).click(function($e) {
        $e.preventDefault();
                $( '#singlePasswordDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#newPassword1Div' ).removeClass('form-group has-error').addClass('form-group');
                $( '#newPassword2Div' ).removeClass('form-group has-error').addClass('form-group');
                $( '#singlePasswordSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#newPasswordSpan1' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#newPasswordSpan2' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#errorChangeMessage' ).addClass('alert-danger');
                $( '#errorChangeMessage' ).removeAttr("style");
                $( '#errorChangeMessage' ).hide();
                
                var singlePassword = $( '#singlePassword' ).val();
                var newPassword1 = $( '#newPassword1' ).val();
                var newPassword2 = $( '#newPassword2' ).val();
                var token = $( '#changeToken' ).val();
                if (!singlePassword || !newPassword1 || !newPassword2) {
                    displayChangeError('Füllen Sie alle Felder!');
                    if (!singlePassword) {
                        $( '#singlePasswordSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                        $( '#singlePasswordDiv' ).removeClass('form-group').addClass('form-group has-error');
                    }
                    if (!newPassword1) {
                        $( '#newPasswordSpan1' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                        $( '#newPassword1Div' ).removeClass('form-group').addClass('form-group has-error');
                    }
                    if (!newPassword2) {
                        $( '#newPasswordSpan2' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                        $( '#newPassword2Div' ).removeClass('form-group').addClass('form-group has-error');
                    }
                } else {
                    if (newPassword1 != newPassword2) {
                        displayChangeError('Bitte ein identisches Password zweimal eintippen!');
                    $( '#newPasswordSpan1' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#newPassword1Div' ).removeClass('form-group').addClass('form-group has-error');
                    $( '#newPasswordSpan2' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#newPassword2Div' ).removeClass('form-group').addClass('form-group has-error');
                    } else {
                        var dates = [ singlePassword, newPassword1, newPassword2, token ];
                        $.ajax({url: "index.php", 
                            type: "post",
                            data: {
                                "action": 'ajax',
                                "concrete": 'changePassword',
                                "value": dates,
                                },
                            success: function(result){
                                if (result == 'false') {
                                    $( '#errorChangeMessage').html('Keine Verbindung mit der Datenbank erstellt.');
                                    $( '#errorChangeMessage').css("text-align", "center");
                                    $( '#errorChangeMessage').show();
                                    return false;
                                }
                            var jsonData = JSON.parse(result);
                                if (jsonData.success == 'true') {
                                    $( '#errorChangeMessage' ).removeClass('alert-danger').addClass('alert-success');
                                    $( '#errorChangeMessage' ).css("text-align", "center");
                                    $( '#errorChangeMessage' ).html('Hallo <b>' + jsonData.name + '</b>!<br>Das Passwort wurde geändert. Sie können schon einloggen.');
                                    $( '#errorChangeMessage' ).fadeIn('slow');
                                    $( '#password3Submit').fadeOut('slow');
                                    $( '#toLogin').fadeIn('slow');
                                } else {
                                    displayChangeError(jsonData.name);
                                }
                            }
                        });
                    }
                }
            });

            $( "#loginSubmit" ).click(function($e) {
		$e.preventDefault();
                $( '#errorMessage' ).addClass('alert-danger');
                $( '#emailDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#emailSpan' ).removeClass('glyphicon glyphicon-remove form-control-feedback');
                $( '#passwordDiv' ).removeClass('form-group has-error').addClass('form-group');
                $( '#loginEmail' ).removeAttr("style");
                $( '#loginPasswort' ).removeAttr("style");
                $( '#errorMessage' ).removeAttr("style");
                $( '#errorMessage' ).hide();
                var mail = $( '#loginEmail' ).val();
                var passwort = $( '#loginPasswort' ).val();

                if (!mail && !passwort) {
                   displayError('Füllen Sie E-mail und Passwort auf!');
                    $( '#emailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#emailDiv' ).removeClass('form-group').addClass('form-group has-error');
                    $( '#emailPassword' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#passwordDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else if (!mail) {
                    displayError('Füllen Sie E-mail auf!');
                    $( '#emailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#emailDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else if (!passwort) {
                    displayError('Füllen Sie das Passwort auf!');
                    $( '#emailPassword' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#passwordDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else if( !isValidEmailAddress( mail ) ) { 
                    displayError('Unrichtige E-Mail Format');
                    $( '#emailSpan' ).addClass('glyphicon glyphicon-remove form-control-feedback');
                    $( '#emailDiv' ).removeClass('form-group').addClass('form-group has-error');
                } else {
                    $.ajax({url: "/CRM/index.php",
                        type: "post",
                        data: {
                            "action": 'login',
                            "mail": mail,
                            "passwort": passwort,
                            },
                        success: function(result){
                            if (result == 'false') {
                                    $( '#errorMessage').html('Keine Verbindung mit der Datenbank erstellt.');
                                    $( '#errorMessage').css("text-align", "center");
                                    $( '#errorMessage').show();
                                    return false;
                                }
                        var jsonData = JSON.parse(result);
                        if (jsonData.success == 'true') {
                            $( '#errorMessage' ).removeClass('alert-danger').addClass('alert-success');
                            $( '#errorMessage' ).css("text-align", "center");
                            $( '#errorMessage' ).html('Hallo <b>' + jsonData.name + '</b>!<br> Sie werden jetzt weitergeleitet...');
                            $( '#errorMessage' ).fadeIn('slow');
                            $( '#hiddenInput' ).val('postLogin');
                            $( '#hiddenInput2' ).val(jsonData.benutzer_id);
                            $(function() {
                                setTimeout(function() {
                                $('#loginForm').submit();
                                }, 1500);
                            });
                        } else {
                            $( '#errorMessage').html(jsonData.name);
                            $( '#errorMessage').css("text-align", "center");
                            $( '#errorMessage').show();
                        }
                    }});
                }
            });
        });