<?php

class Helpers 
{
	private static $handler;
	private $dbHandler;
	private $art = array();
	private $kalkulationsfelder = array();
	private $mandant = array ('TVS', 'Sonst.');
	private $zahlungsziel = array();
	private $benutzerList = array();
	private $rolle = array();
	private $lieferant = array();
	private $machine;
	private $ansprechpartner = array();
	private $status = array();
	
	function __construct($dbHandler) {

		$this->dbHandler = $dbHandler;
		self::$handler = $dbHandler;

	}

	public static function getSettings($name) {
		$sql = 'SELECT value FROM Settings WHERE name = :name';
		$result = self::$handler->prepare($sql);
		$result->bindValue(':name', $name);
		$result->execute();
		$value = $result->fetch();
		$singleValue = $value['value'];
		return $singleValue;
	}
	
	public function configDelete($table, $rowId) {
		$sql = 'DELETE FROM '.$table.' WHERE id = '.$rowId;
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
		    return 'success';
		} else {
		    return 'false';
		}
	}
	
	public function configSave($object, $data) {
		$values = explode('<>', $object);
		$table = $values[0];
		$name = $values[1];
		if ($table == 'Zahlungsziel') {
		    $sql = "INSERT INTO Zahlungsziel (name, beschreibung) VALUES (:name, :description)";
		    $result=$this->dbHandler->prepare($sql);
		    $result->bindValue(':name', $name);
		    $result->bindValue(':description', $data);
		} elseif ($table == 'Benutzer') {
		    $password = md5($data);
		    $mail = $values[2];
		    $role = $values[3];
		    $sql = "INSERT INTO Benutzer (name, mail, passwort, rolle_id) VALUES (:name, :mail, :password, :role)";
		    $result=$this->dbHandler->prepare($sql);
		    $result->bindValue(':name', $name);
		    $result->bindValue(':mail', $mail);
		    $result->bindValue(':password', $password);
		    $result->bindValue(':role', $role);
		}
		if ($result->execute()) {
		    $id = $this->getLastId($table);
		    return $id;
		} else {
		    $error = 'Unable to save data';
		    return $error;
		}
	}
	
	public function configUpdate($object, $data) {
		$values = explode('<>', $object);
		$table = $values[0];
		$column = $values[1];
		$rowId = $values[2];
		$sql = "UPDATE ".$table." SET ".$column."= :value WHERE id = :rowId";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':value', $data);
		$result->bindValue(':rowId', $rowId);
		if ($result->execute()) {
		    return 'success';
		} else {
		    $error = 'No config change possible';
		    return $error;
		}
	}
	
	public function getAnsprechpartner() {
		$sql = 'SELECT id, name, vorname FROM Ansprechpartner';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"].' '.$singleResult["vorname"]);
		}
		$this->ansprechpartner = $list;
		return $list;
	}
	
	public function getArt() {
		return $this->art;
	}
	
	public function getBenutzerList() {
		return $this->benutzerList;
	}
	
	public function getCompleteBenutzerList() {
		$sql = 'SELECT Benutzer.id as id, Benutzer.name as name, Rolle.name as rolle FROM Benutzer INNER JOIN Rolle ON Benutzer.rolle_id = Rolle.id';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		foreach ($result as $singleResult) {
			$list[] = array('id' => $singleResult['id'], 'name' => $singleResult["name"], 'rolle' => $singleResult["rolle"]);
		}
		return $list;
	}
	
	public function getKalkulationsfelder() {
		return $this->kalkulationsfelder;
	}
	
	private function getLastId($table) {
	    $sql = "SELECT id FROM ".$table." ORDER BY id DESC LIMIT 1";
	    $result=$this->dbHandler->prepare($sql);
	    if ($result->execute()) {
		$singleId = $result->fetch();
		return $singleId['id'];
	    } else {
		return 'false';
	    }
	}
	
	public function getMandant() {
		return $this->mandant;
	}
	
	public function getMachine() {
		return $this->machine;
	}
	
	public function getSingleArt($id) {
		$sql='SELECT name FROM Art WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$name = $result->fetch();
		$singleName = $name['name'];
		return $singleName;
	}
	
	public function getSingleBenutzer($id) {
		$sql='SELECT Benutzer.name as name, Rolle.id as rolleId, Rolle.name as rolleName FROM Benutzer INNER JOIN Rolle ON Benutzer.rolle_id = Rolle.id WHERE Benutzer.id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$date = $result->fetch();
		$dateArray = array ('name' => $date['name'], 'rolle_id' => $date['rolleId'], 'rolle_name' => $date['rolleName']);
		return $dateArray;
	}

	public function getSingleStatus($id) {
		$sql = "SELECT id, name FROM Status WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$date = $result->fetch();
		$result = array('id' => $date['id'], 'name' => $date['name']);
		return $result;
	}
	
	public function getSingleZahlungsziel($id) {
		$sql = "SELECT name FROM Zahlungsziel WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$date = $result->fetch();
		return $date['name'];
	}
	
	public function getRolle() {
		return $this->rolle;
	}

	public function getSingleSelect($name) {
		$dates = explode('-', $name);
		$table = $dates[0];
		$projectId = $dates[1];
		$sql = "SELECT name FROM ".$table." WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $projectId);
		if ($result->execute()) {
			$name = $result->fetch();
			return $name['name'];
		} else {
			return 'false';
		}
	}
	
	public function getStatusList() {
		$sql = "SELECT id, name FROM Status";
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
		}
		$this->status = $list;
		return $list;
	}
	
	public function getZahlungsziel() {
		
		$sql='SELECT id, name, beschreibung FROM Zahlungsziel';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$ziel = array();
		foreach ($result as $singleResult) {
			$ziel[] = array("id" => $singleResult['id'], "name" => $singleResult['name'], "beschreibung" => $singleResult["beschreibung"]);
		}
		$this->zahlungsziel = $ziel;
		return $ziel;
	}
	
	public function setArt() {
		$sql='SELECT id, name FROM Art';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$ziel = array();
		foreach ($result as $singleResult) {
			$artList[] = array("id" => $singleResult['id'], "name" => $singleResult['name']);
		}
		$this->art = $artList;
	}
	
	public function setBenutzerList() {
		$sql='SELECT id, name FROM Benutzer';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
		}
		$this->benutzerList = $list;
	}
	
	public function setKalkulationsfelder() {
		$sql='SELECT id, name FROM Kalkulationsfelder';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$fields = array();
		foreach ($result as $singleResult) {
			$fields[] = array( 'id' => $singleResult["id"], 'name' => $singleResult["name"] );
		}
		$this->kalkulationsfelder = $fields;
		return $fields;
	}
	
	public function setLieferant( $id = null ) {
		$sql='SELECT id, name FROM Lieferant';
		if (isset($id)) {
			$sql.= ' WHERE id = :id';
		}
		$result=$this->dbHandler->prepare($sql);
		if (isset($id)) {
			$result->bindValue(':id', $id);
		}
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
		}
		$this->lieferant = $list;
		return $list;
	}
	
	public function setMachine() {
		$sql='SELECT id, name FROM Maschine';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
		}
		$this->machine = $list;
	}
	
	public function setRolle($return = null) {
		$sql='SELECT id, name FROM Rolle';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id' => $singleResult['id'], 'name' => $singleResult["name"]);
		}
		$this->rolle = $list;
		if (isset($return) && $return = 'true') {
			return $list;
		}
	}
	
	public function standardText($text) {
	      $sql = 'UPDATE Settings SET value = :text WHERE name = "standardText"';
	      $result=$this->dbHandler->prepare($sql);
	      $result->bindValue(':text', $text);
	      if ($result->execute()) {
		    return 'success';
	      } else {
		    $error = 'No text change possible';
		    return $error;
	      }
	}
}
