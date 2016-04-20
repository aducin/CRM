<?php

class Benutzer
{

	public $id;
	private $dbHandler;
	private $name;
	private $mail;
	private $passwort;
	private $isLogged;
	private $login_datum;
	private $lastSql;
	private $output;
	private $rolle = array();

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->isLogged = 0;
		$this->output = new OutputController($dbHandler);

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function checkBenutzer($mail, $passwort, $login = null) {
		if(isset($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$md5pass = md5($passwort);
		$array = $this->sqlCheckBenutzer($mail, $md5pass);
		$this->id = $array['id'];
		$this->setData();
		if (!isset($login)) {
			if ($array != false) {
				$this->saveBenutzerLog();
				$this->saveLastLogin();
				$data = array('success' => 'true', 'name' => $array['name'], 'benutzer_id' => $this->id);
				$this->isLogged = 1;
				$_SESSION['log'] = 1;
				$_SESSION['user'] = $this->id;
				//setcookie('crm_logged', '1', time()+3600);
			} else {
				$date = $this->checkLogin($ip);
				if ($date == 'true') {
					global $floodTime;
					$data = array('success' => 'false', 'name' => 'IP-Adresse wurde blockiert! Sie mussen noch '.$floodTime.' Sek. warten.');
				} else {
					$data = array('success' => 'false', 'name' => 'Fehlendes Passwort oder E-mail');
				}
				$this->saveLogin($ip);
			}
			echo json_encode($data);
		}
	}

	public function checkByToken($token, $password = null) {
		if (isset ($password)) {
			$sql = "SELECT * FROM Benutzer WHERE singlePassword = :password AND singleToken = :token AND active = 1";
		} else {
			$sql = 'SELECT * FROM Benutzer WHERE singleToken = :token AND active = 1';
		}
		$result=$this->dbHandler->prepare($sql);
		if (isset ($password)) {
			$result->bindValue(':password', $password);
		}
		$result->bindValue(':token', $token);
		$result->execute();
		$data = $result->fetch();
		if ( !isset( $password )) {
			return $data;
		} else {
			if ( $data == false ) {
				$error = 'Eingegebenes Passwort oder Login gelten nicht!';
				$data = array('success' => 'false', 'name' => $error);
				echo json_encode($data);
				exit();
			} else {
				return $data;
			}
		}
	}

	private function checkLogin($ip) {
		global $floodTime;
		$now = time() - $floodTime;
		$sql = "SELECT date_timestamp FROM Login WHERE ip = :ip AND date_timestamp > :now";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':ip', $ip);
		$result->bindValue(':now', $now);
		$result->execute();
		$date=$result->fetch();
		if ($date['date_timestamp'] == NULL){
			$error = 'false';
		} else {
			$error = 'true';
		}
		return $error;
	}

	public function createBenutzer( $name, $rolle, $mail, $passwort ) {
		$md5pass = md5($passwort);
		$sql="INSERT INTO Benutzer (name, rolle_id, mail, passwort, create_date) VALUES (:name, :rolle, :mail, :passwort, NOW())";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $name);
		$result->bindValue(':rolle', $rolle);
		$result->bindValue(':mail', $mail);
		$result->bindValue(':passwort', $md5pass);
		$result->execute();
		$this->checkBenutzer($mail, $passwort, 'true');
	}

	public function deleteBenutzer( $id ) {
		$sql="DELETE FROM Benutzer WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		unset ($this->id);
		unset ($this->name);
		unset ($this->rolle);
	}
	
	public function getBenutzerByMail($value) {
		$sql = 'SELECT name from Benutzer WHERE mail = :mail';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':mail', $value[0]);
		$result->execute();
		$ifFound = $result->fetch();
		if ($ifFound == false ) {
			$data = array('success' => 'false', 'message' => 'Inkorrekte E-mail Adresse');
		} else {
			$name = $ifFound['name'];
			$date = date('Y-m-d H:i:s');
			$token = md5($date);
			$this->updateSinglePassword($value, $token);
			$result = array('success' => 'true', 'token' => $token, 'name' => $ifFound['name']);
			return $result;
		}
	}
	
	public function getLastSearch() {
	    $sql = 'SELECT beginDate, end, projectName, clientSearch, eventNumber, clientOrderNumber, mandant, status FROM Benutzer WHERE id = :id';
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':id', $_SESSION['user']);
	    if ($result->execute()) {
	        $values = $result->fetch();
	        $conditions = array(
				'begin' => $values['beginDate'], 
				'endDate' => $values['end'], 
				'projectName' => $values['projectName'], 
				'clientName' => $values['clientSearch'], 
				'eventNumber' => $values['eventNumber'], 
				'clientOrderNumber' => $values['clientOrderNumber'],
				'mandant' => $values['mandant'],
				'status' => $values['status'],
				'ifPrevious' => true
	        );
	    return $conditions;
	    } else {
	    	$this->output->displayPhpError();
	    }
	}
	
	public function getLastSql() {
		$sql = $this->lastSql;
		if ($sql !='') {
			$result = $this->dbHandler->prepare($sql);
			$result->execute();
			foreach ($result as $singleResult) {
				$searchResult[] = array( 'id' => $singleResult['id'], 'liefertermin' => $singleResult['liefdate'], 'name' => $singleResult['proname'], 'number' => $singleResult['aufnumb']);
			}
			if (!isset($searchResult)) {
				$searchResult = null;
			}
		} else {
			$searchResult = 'empty';
		}
		return $searchResult;
	}

	private function saveBenutzerLog() {
		$sql = "INSERT INTO Benutzer_log (benutzer_id, log_date) VALUES (:id, NOW())";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	private function saveLastLogin() {
		$sql = "UPDATE Benutzer SET last_login = NOW() WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}
	
	public function saveLastSearch($array) {
		$begin = explode('/', $array['begin']);
		if ($begin[0] != '') {
			$begin = $begin[2].'-'.$begin[1].'-'.$begin[0];
		} else {
			$begin = null;
		}
		$end = explode('/', $array['endDate']);
		if ($end[0] != '') {
		$end = $end[2].'-'.$end[1].'-'.$end[0];
		} else {
			$end = null;
		}
		
		$sql = 'UPDATE Benutzer SET beginDate = :beginDate, end = :end, projectName = :projectName, clientSearch = :clientName, eventNumber = :eventNumber, clientOrderNumber = :clientOrderNumber,
		 	mandant = :mandant, status = :status WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':beginDate', $begin);
		$result->bindValue(':end', $end); 	
		$result->bindValue(':projectName', $array['projectName']); 
		$result->bindValue(':clientName', $array['clientName']); 
		$result->bindValue(':eventNumber', $array['eventNumber']); 
		$result->bindValue(':clientOrderNumber', $array['clientOrderNumber']); 
		$result->bindValue(':mandant', $array['mandant']); 
		$result->bindValue(':status', $array['status']); 
		$result->bindValue(':id', $_SESSION['user']); 
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	public function saveLastSql($searchSql) {
		$sql = 'UPDATE Benutzer SET lastSql = :searchSql WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->bindValue(':searchSql', $searchSql);
		if ($result->execute()) {
			$this->setLastSql($searchSql);
		} else {
			$this->output->displayPhpError();
		}
	}

	public function saveNewPassword($id, $password) {
		$sql = 'UPDATE Benutzer SET passwort = :password, singlePassword = NULL, singleToken = NULL WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->bindValue(':password', $password);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	private function saveLogin($ip) {
		$now = time();
		$sql = "INSERT INTO Login (ip, reg_date, date_timestamp) VALUES (:ip, NOW(), :now)";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':ip', $ip);
		$result->bindValue(':now', $now);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}
	
	private function selectData() {
		$sql = "SELECT name, rolle_id, mail, passwort, last_login, lastSql FROM Benutzer WHERE id = :id";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$array = $result->fetch();
			return $array;
		} else {
			$this->output->displayPhpError();
		}
	}
	
	public function setData() {
		$data = $this->selectData();
		$this->name = $data['name'];
		$this->mail = $data['mail'];
		$this->passwort = $data['passwort'];
		$this->setRolle($data['rolle_id']);
		$this->login_datum = $data['last_login'];
		$this->lastSql = $data['lastSql'];
	}

	public function setIsLogged() {
		$this->isLogged = 1;
	}

	private function setLastSql($sql) {
		$this->lastSql = $sql;
	}

	public function getMail() {
		return $this->mail;
	}

	public function getName() {
		return $this->name;
	}

	public function setRolle($id) {
		$sql="SELECT id, name FROM Rolle WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$id=$result->fetch();
			$return = array("id"=>$id['id'], 'name'=>$id['name']);
			$this->rolle = $return;
		} else {
			$this->output->displayPhpError();
		}
	}
	
	private function sqlCheckBenutzer($mail, $password) {
	  	$sql="SELECT id, name FROM Benutzer WHERE mail = :mail AND passwort = :passwort AND active = 1";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':mail', $mail);
		$result->bindValue(':passwort', $password);
		if ($result->execute()) {
			$array=$result->fetch();
			return $array;
		} else {
			$this->output->displayPhpError();
		}
	}
	
	public function getRolle() {
		return $this->rolle;
	}
	
	public function updateSinglePassword($value, $token) {
		$sql = 'UPDATE Benutzer SET singlePassword = :password, singleToken = :token WHERE mail = :mail';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':password', $value[1]);
		$result->bindValue(':token', $token);
		$result->bindValue(':mail', $value[0]);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}
}