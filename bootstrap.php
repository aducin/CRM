<?php

$baseUrl = $_SERVER['SERVER_NAME'];

include ('connect.php');

$controllerPrefix = 'controllers/';
$modelPrefix = 'model/';

$controllerClasses = array('ajax', 'controller' , 'loginController', 'printController');
$modelClasses = array('dbconnect', 'interface', 'ansprechpartner', 'auftraggeber', 'bemerkung', 'benutzer', 'creator', 
    'formBasics', 'document', 'drucksache', 'fremdsache', 'helpers', 'lieferant', 'project_calculation', 'projekt', 'rechnungsadresse', 'vorstufe', 'zahlungsziel');

if ( is_file($controllerPrefix.'outputController.php') ){
	include ($controllerPrefix.'outputController.php');
} else {
	$_SESSION['error'] = 'Es ist ein Fehler aufgetreten. Versuchen Sie bitte später.';
	$output = new OutputController();
	unset($_SESSION['error']);
}

foreach ($controllerClasses as $singleClass) {
	if ( is_file($controllerPrefix.$singleClass.'.php') ){
		include ($controllerPrefix.$singleClass.'.php');
	} else {
		$_SESSION['error'] = 'Es ist ein Fehler aufgetreten. Versuchen Sie bitte später.';
		$output = new OutputController();
		unset($_SESSION['error']);
	}
}

foreach ($modelClasses as $singleClass) {
	if ( is_file($modelPrefix.$singleClass.'.php') ){
		include ($modelPrefix.$singleClass.'.php');
	} else {
		$_SESSION['error'] = 'Es ist ein Fehler aufgetreten. Versuchen Sie bitte später.';
		$output = new OutputController();
		unset($_SESSION['error']);
	}
}

