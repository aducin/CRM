<?php

class LoginController
{
    private $dbHandler;
    private $creator;
    private $output;

	public function __construct($dbHandler, $array) {
		$this->dbHandler = $dbHandler;
		$this->$array['action']($array);
    }

    private function login($array) {
        $floodTime = Helpers::getSettings('floodTime');
    	$mail = trim($array['mail']);
        $password = trim($array['password']);
        $this->creator = new TvsatzCreator($this->dbHandler);
        $benutzer = $this->creator->createProduct('benutzer');
        $benutzer->checkBenutzer($mail, $password);
    }

    private function logout() {
    	unset($_SESSION['log']);
        $this->output = new OutputController($this->dbHandler);
        $this->output->renderLoginPage();
    }

    private function postLogin($array) {
    	$mail = trim($array['mail']);
        $password = md5(trim($array['password']));
        $sql = 'SELECT id FROM Benutzer WHERE mail = :mail AND passwort = :password';
        $result = $this->dbHandler->prepare($sql);
		$result->bindValue(':mail', $mail);
		$result->bindValue(':password', $password);
		$result->execute();
		$number = $result->fetch();
		$this->userId = $number['id'];
		return $this->userId;
    }

    public function getUserId() {
    	return $this->userId;
    }
}