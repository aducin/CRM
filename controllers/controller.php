<?php

class Controller
{
	private $dbHandler;
	private $ajax;
	private $benutzer;
	private $creator;
	private $output;
	private $project;
	public $params = array();
	private $currentId;
	
	public function __construct($dbHandler, $action = null, $variable = null) {
		$path = 'http://'.$_SERVER["HTTP_HOST"];
		$this->dbHandler = $dbHandler;
		$this->creator = new TvsatzCreator($dbHandler);
		$this->output = new OutputController($dbHandler);
		if (isset($_GET['token'])) {
		    $this->getLoginPage();
		} elseif (!isset($_SESSION['log']) OR !isset($_COOKIE['crm_logged'])) {
		    if ($action == 'renderLogin') {
			$this->getLoginPage();
		    } elseif ($action != 'ajax' && $action != 'login') {
			header('Location: '.$this->path.'/CRM/Login');
		    } else {
			  setcookie('crm_logged', '1', time()+3600);
			  $this->$action();
		    }
		}
		 elseif (!isset($_SESSION['log']) && !isset($_POST['action']) && !isset($_COOKIE['crm_logged'])) {
		    $this->getLoginPage();
		} else {
		    if (isset ($_GET['single'])) {
			$this->currentId = $_GET['single'];
		    }   
		    setcookie('crm_logged', '1', time()+3600);
		    $this->$action();
		}
	}

    private function ajax() {
        $this->creator->createProduct('Ajax', $_POST);
    }

    private function cloneProject() {
        $id = $_SESSION['projectId'];
        $this->project = $this->creator->createProduct('projekt', $id);
        $this->project->setDates();
        $success = $this->project->cloneProject();
        if ($success != true) {
            $this->output->displayError($success);
        }
    }

    private function config() {
        $helper = $this->creator->createProduct('helpers', $_POST);
        $paymentOptions = $helper->getZahlungsziel();
        $role = $helper->setRolle('true');
        $users = $helper->getCompleteBenutzerList();
        $this->output->renderConfigSite($paymentOptions, $role, $users);
    }

    private function erfassung() {
        $this->setBenutzer('erfassung', $_SESSION['user']);
    }

    private function getContent($id, $target) {
        $this->project = $this->creator->createProduct('projekt', $id);
        $this->project->setDates();
        $_SESSION['projectId'] = $this->project->getId();
        $this->output->$target($this->benutzer, $this->project);
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
    
    private function newProject() {
	$this->project = $this->creator->createProduct('projekt');
	$success = $this->project->insertNewProject();
	var_dump($success); exit();
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
                'begin' => $_POST["beginDate"],
                'endDate' => $_POST["endDate"],
                'projectName' => $_POST["projectName"],
                'clientName' => $_POST["clientName"],
                'eventNumber' => $_POST["eventNumber"],
                'clientOrderNumber' => $_POST["clientOrderNumber"],
                'mandant' => $_POST["mandant"],
                'status' => $_POST["status"],
            );
        $this->params = $params[0];
        $project = $this->creator->createProduct('projekt');
        $result = $project->searchByDates($params);
        $this->setBenutzer('liste', $_SESSION['user']);
    }

    private function setBenutzer( $name, $number ) {
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
            $helper = $this->creator->createProduct('helpers');
            if (isset($this->params['status'])) {
                $this->params['status'] = $helper->getSingleStatus($this->params['status']);
            }
            $this->output->renderListe($this->benutzer, $result, $this->params);
            break;
            case 'erfassung':
            if ( isset( $this->currentId ) ) {
                $this->getContent($this->currentId, 'renderErfassung');
            }
            $this->output->renderErfassung($this->benutzer, null);
            break;
        }
    }

}