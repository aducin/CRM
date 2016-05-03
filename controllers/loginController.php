<?php

class LoginController
{
    private $dbHandler;
    private $creator;
    private $output;

	public function __construct($dbHandler, $array) {
		$this->dbHandler = $dbHandler;
        $this->creator = new TvsatzCreator($this->dbHandler);
        $this->output = new OutputController($this->dbHandler);
		$this->$array['action']($array);
    }

    private function login($array) {
        $floodTime = Helpers::getSettings('floodTime');
    	$mail = trim($array['mail']);
        $password = trim($array['password']);
        $benutzer = $this->creator->createProduct('benutzer');
        $benutzer->checkBenutzer($mail, $password);
    }

    private function logout() {
    	unset($_SESSION['log']);
        unset($_SESSION['user']);
        unset($_SESSION['projectId']);
        setcookie("crm_logged", "", time()-3600);
        setcookie("replace_again", "", time()-3600);
        setcookie("user", "", time()-3600);
        session_destroy();
        $this->output->renderLoginPage();
    }

    private function postLogin($array) {
    	$mail = trim($array['mail']);
        $password = md5(trim($array['password']));
	$benutzer = $this->creator->createProduct('benutzer');
	$user = $benutzer->checkUserId($mail, $password);
        if ($user != 'false') {
	    if ($_POST['rememberPassword'] == 'true') {
		setcookie('user', $user, time()+302400);
		setcookie('crm_logged', '1', time()+302400);
		setcookie('replace_again', '1', time()+302400);
	    }
            $this->userId = $user;
        } else {
            $this->output->displayPhpError();
        }
	return $this->userId;
    }

    public function getUserId() {
    	return $this->userId;
    }
}