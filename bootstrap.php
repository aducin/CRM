<?php

include ('connect.php');

$controllerPrefix = 'controllers/';
$modelPrefix = 'model/';

$controllerClasses = array('ajax', 'controller' , 'loginController', 'outputController');
$modelClasses = array('dbconnect', 'interface', 'ansprechpartner', 'auftraggeber', 'bemerkung', 'benutzer', 'creator', 
    'formBasics', 'drucksache', 'fremdsache', 'helpers', 'lieferant', 'project_calculation', 'projekt', 'rechnungsadresse', 'vorstufe', 'zahlungsziel');

foreach ($controllerClasses as $singleClass) {
	include ($controllerPrefix.$singleClass.'.php');
}

foreach ($modelClasses as $singleClass) {
	include ($modelPrefix.$singleClass.'.php');
}

