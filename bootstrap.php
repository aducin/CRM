<?php

include ('connect.php');

$controllerPrefix = 'controllers/';
$modelPrefix = 'model/';

$controllerClasses = array('controller' , 'loginController', 'outputController');
$modelClasses = array('dbconnect', 'interface', 'crm', 'ajax', 'ansprechpartner', 'auftraggeber', 'benutzer', 'creator', 
    'formBasics', 'drucksache', 'fremdsache', 'helpers', 'lieferant', 'projekt', 'rechnungsadresse', 'vorstufe', 'zahlungsziel');

foreach ($controllerClasses as $singleClass) {
	include ($controllerPrefix.$singleClass.'.php');
}

foreach ($modelClasses as $singleClass) {
	include ($modelPrefix.$singleClass.'.php');
}

