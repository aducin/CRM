<?php 

if (!isset($_SESSION)) {
	session_start();
}

if (isset($_GET['action']) && $_GET['action'] == 'projectSearch' && $_POST == null) {
	$action = 'liste';
} elseif (isset($_POST['action'])) {
	$action = $_POST['action'];
} elseif (isset($_GET['action'])) {
	$action = $_GET['action'];
}

include ('bootstrap.php');

$baseHttp = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$db = new dbConnect($host, $login, $password); 
$dbHandler = $db->getDb();

//Controller::initInstance($dbHandler);
if (isset($action)) {
	$controller = new Controller($dbHandler, $action);
} else {
	$controller = new Controller($dbHandler);
}


//$creator = new TvsatzCreator($dbHandler);
/*
$helper = $creator->createProduct('helpers');
$helper->setKalkulationsfelder();
$helper->setZahlungsziel();
$helper->setBenutzerList();
$single = $helper->getSingleBenutzer(5);
$helper->setRolle();
$helper->setLieferant();
$helper->setArt();
$helper->setMachine();
$list = $helper->getArt();
$single = $helper->getSingleArt(5);

$fremdsache = $creator->createProduct('Fremdsache');
$data = array('projectId' => 7, 'textDate' => '2016-04-10', 'deliverer' => 'Deliverer text 2', 'description' => 'Some description 2', 'purchasePrice' => 50.55, 'sellPrice' => 55.80, 'singlePriceRemoved' => 1);
$fremdsache->setNew($data);
//$fremdsache->setById(9);
//$fremdsache->delete();

$drucksache = $creator->createProduct('Drucksache');
//$data = array( 'projectId' => 2, 'print' => 'Some text', 'machine' => 1, 'type' => 'Some type', 'edition' => 500, 'format' => 'Some format', 
//	'size' => 'Some size', 'color' => 'Some color', 'paper' => 'Some paper', 'remodelling' => 'Some remodelling', 'finished' => 1, 'amount' => 40.55);
//$drucksache->setNew($data);
//$drucksache->setById(7);
//$drucksache->delete();

//$vorstufe = $creator->createProduct('Vorstufe');
//$data = array('projectId' => 1, 'type' => 1, 'performanceTime' => '2015-11-30', 'employee' => 2, 'description' => 'Some description', 'timeProposal' => '20',
//	'timeReal' => 10, 'timeSettlement' => 25, 'amount' => 35.10, 'settlement' => 1);
//$vorstufe->setNew($data);
//$vorstufe->setById(12);
//$vorstufe->delete();
//var_dump($vorstufe);
var_dump($fremdsache);
//var_dump($drucksache); 
exit();
/*
$angebot = $creator->createProduct('angebot');
$angebot->setOfferDate(20);
$angebot->createAuftragszettel();
//$angebot->deleteOffer(17);

$dates = array(
	'projekt_id' => 3,
	'benutzer_id' => 5,
	'clientOfferNumber' => 'Ihre Anfrage vom 01.02.2016',
	'format' => 'A4 hoch',
	'size' => '25 Blatt je Block',
	'color' => '4/0-farbig Euroskala',
	'paper' => '80 g/qm Offset weiß + 300 g/qm Graukarton als Abschlussdeckel',
	'remodelling' => 'schneiden, am Kopf mit Graupappe verleimen, optional bohren (2-fach oder 4-fach)',
	'printing' => '1000 Stück',
	'costs' => 895.00,
	'type' => 1,
	'vorstufeDesc' => 'Some description from Vorstufe',
	'drucksachenDesc' => 'Some description from Drucksachen',
	'fremdkostenDesc' => 'Some description from Fremdkosten',
	'zusammenfassungDesc' => 'Some description from Zusammenfassung'
	);
$angebot->createAngebot($dates);


$zahlungsziel = $creator->createProduct('zahlungsziel', 1);
//$zahlungsziel->createZahlungsziel('Ziel 4', "Beschreibung des Zieles 4");
//$zahlungsziel->deleteZahlungsziel();

$benutzer = $creator->createProduct('benutzer');
//$benutzer->setData();
//$benutzer->createBenutzer('AD9BIS', 2, 'ad9bis@gmail.com', 'NrEQ757542');
//$benutzer->deleteBenutzer(9);
//$benutzer->checkBenutzer('ad9bis@gmail.com', 'NrEQ757542');

$ansprechpartner = $creator->createProduct('ansprechpartner', 1);
$ansprechpartner->setDates();
//$mix = array('someName', 'someVorname', 'someTelefon', 'someTelefon2', "4000000000", "some.mail@gmail.com", 1);
//$ansprechpartner->setCustomDates( $mix );
//$ansprechpartner->getId();
//$ansprechpartner->deleteCurrentDates();

$rechnungsadresse = $creator->createProduct('rechnungsadresse', 1);
$rechnungsadresse->setDates();
//$mix = array('someName', 'somebteilung', "someAnschrift", "someAnschrift2", "20000", "Berlin", 1);
//$rechnungsadresse->setCustomDates( $mix );
//$rechnungsadresse->getId();
//$rechnungsadresse->deleteCurrentDates();

$auftraggeber = $creator->createProduct('auftraggeber', 1);
$auftraggeber->setDates();
//$mix = array('someName', 'somebteilung', "someAnschrift", "someAnschrift2", "20000", "Berlin", "404404404", "4000000000", "some.mail@gmail.com", 2, 1);
//$auftraggeber->setCustomDates( $mix );
//$auftraggeber->getId();
//$auftraggeber->deleteCurrentDates();
$auftraggeber->setAnsprechpartner();
$auftraggeber->setRechnungsadresse();

$projekt = $creator->createProduct('projekt');
//$projekt->setDates();
//$projekt->setLieferant(5, 'Etwas wichtig zu bemerken');

$mix = array( 'Projekt Numer 4', '112/2016', 2, 3, 2, 4, 3, 'Noch etwas zu bemerken', 'Sonst.', '0005', '005/16', '2016-04-10');
$projekt->setCustomDates( $mix );
//$projekt->deleteCurrentDates();
/*
$change = $projekt->changeAnsprechpartner(1);
if ($change != 'success') {
	echo $change.' - program has been stopped at line 57 - index.php!'; exit();
}
*/
//$projekt->changeAuftraggeber(1);
/*
$change = $projekt->changeRechnungsadresse(1);
if ($change != 'success') {
	echo $change.' - program has been stopped at line 55 - index.php!'; exit();
}

$projekt->changeBenutzer(5);
var_dump($projekt); exit();
*/
ob_end_flush();

?>