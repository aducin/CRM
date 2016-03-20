<?php

class Controller
{
	private $dbHandler;
	private $ajax;
	private $benutzer;
	private $creator;
	private $output;
	private $project;
	private $params;

	public function __construct($dbHandler, $action = null, $variable = null) {
		$this->dbHandler = $dbHandler;
		$this->creator = new TvsatzCreator($dbHandler);
		$this->output = new OutputController($dbHandler);

        if (!isset($_SESSION['log']) && !isset($_POST['action'])) {
            $this->getLoginPage();
        } else {
            if (!isset ($_GET['single'])) {
                $this->$action ();
            } else {
                $this->$action ($_GET{'single'});
            }
        }
    }

    private function ajax() {
        $this->creator->createProduct('Ajax', $_POST);
    }

    private function getBenutzer() {
        return $this->benutzer;
    }

    private function liste() {
        $this->setBenutzer('liste', $_SESSION['user']);
    }

    private function getLoginPage() {
        $this->output->renderLoginPage();
    }

    private function login() {
        $array = array('action' => $_POST['action'], 'mail' => $_POST['mail'], 'password' => $_POST['passwort']);
        $this->creator->createProduct('loginController', $array);
    }

    private function logout() {
        $array = array('action' => 'logout');
        $this->creator->createProduct('loginController', $array);
    }

    private function postLogin() { 
        $array = array(
            'action' => $_POST['action'], 
            'mail' => $_POST['mail'], 
            'password' => $_POST['passwort']
        );
        $userId = $this->creator->createProduct('loginController', $array);
        $number = $userId->getUserId();
        if ($number == $_POST['benutzer']) {
            $this->setBenutzer('liste', $number);
        } else {
            $error = 'Błąd logowania - numer użytkownik azostał podmieniony!';
            OutputController::displayError($error);
            exit();
        }
    }

    private function projectSearch() {
        $params[]=array(
                'begin'=>$_POST["beginDate"],
                'endDate'=>$_POST["endDate"],
                'projectName'=>$_POST["projectName"],
                'clientName'=>$_POST["clientName"],
                'eventNumber'=>$_POST["eventNumber"],
                'clientOrderNumber'=>$_POST["clientOrderNumber"],
                'mandant'=>$_POST["mandant"],
            );
        $this->params = $params[0];
        $project = $this->creator->createProduct('projekt');
        $result = $project->searchByDates($params);
        $this->setBenutzer('liste', $_SESSION['user']);
    }

    private function setBenutzer($name, $number, $id = null) {
        $benutzer = $this->creator->createProduct('benutzer', $number);
        $this->benutzer = $benutzer;
        $this->benutzer->setData();
        if ($_SESSION["log"] == 1) {
            $benutzer->setIsLogged();
        } else {
            $error = 'Błąd logowania - konieczna weryfikacja ustawień!';
            OutputController::displayError($error);
            exit();
        }
        switch ($name) {
            case 'liste':
                $result = $benutzer->getLastSql();
                $this->output->renderListe($this->benutzer, $result, $this->params);
                break;
            case 'erfassung':
                if ( $id !='' ) {
                    $this->project = $this->creator->createProduct('projekt', $id);
                    $this->project->setDates();
                    $this->output->renderZusammenfassung($this->benutzer, $this->project);
                }
                $this->output->renderZusammenfassung($this->benutzer, null);
                break;
            case 'vorstufe':
                if ( $id != '' ) {
                    $this->project = $this->creator->createProduct('projekt', $id);
                    $this->project->setDates();
                    $this->output->renderVorstufe($this->benutzer, $this->project);
                }
                $this->output->renderVorstufe($this->benutzer, null);
                break;
            }
    }

    private function vorstufe($id = null) {
       if (!isset ($id)) {
            $this->setBenutzer('vorstufe', $_SESSION['user']);
        } else {
            $this->setBenutzer('vorstufe', $_SESSION['user'], $id);
        }
    }

    private function zusammenfassung($id = null) {

        if (!isset ($id)) {
            $this->setBenutzer('erfassung', $_SESSION['user']);
        } else {
            $this->setBenutzer('erfassung', $_SESSION['user'], $id);
        }
    }

}