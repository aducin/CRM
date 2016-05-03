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
$sql = "UPDATE Projekt SET ".$column." = :value WHERE id = ".$id;
$db = new dbConnect($host, $login, $password); 
$dbHandler = $db->getDb();
$check = is_object($dbHandler);

if ($check == false) {
	if ($action == 'ajax' OR $action == 'login') {
		echo 'false';
		exit();
	}
	$_SESSION['error'] = 'Keine Verbindung mit der Datenbank erstellt. Versuchen Sie bitte später.';
	$output = new OutputController();
	//$output->displayError($error);
}

//Controller::initInstance($dbHandler);
if (isset($action)) {
	$controller = new Controller($dbHandler, $action);
} else {
	$controller = new Controller($dbHandler);
}


ob_end_flush();

?>