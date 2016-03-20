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
	
	function __construct($dbHandler) {

		$this->dbHandler = $dbHandler;
		self::$handler = $dbHandler;

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

	public function getArt() {
		return $this->art;
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

	public function getSingleArt($id) {
		$sql='SELECT name FROM Art WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$name = $result->fetch();
		$singleName = $name['name'];
		return $singleName;
	}

	public function setKalkulationsfelder() {
		
		$sql='SELECT * FROM Kalkulationsfelder';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$felder = array();
		$counter = 0;
		foreach ($result as $singleResult) {
			$felder[$counter] = $singleResult["name"];
			$counter++;
		}
		$this->kalkulationsfelder = $felder;
	}

	public function getKalkulationsfelder() {
		return $this->kalkulationsfelder;
	}

	public function getMandant() {
		return $this->mandant;
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

	public function getBenutzerList() {
		return $this->benutzerList;
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

	public function setRolle() {
		$sql='SELECT id, name FROM Rolle';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$list = array();
		foreach ($result as $singleResult) {
			$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
		}
		$this->rolle = $list;
	}

	public function getRolle() {
		return $this->rolle;
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

	public function getMachine() {
		return $this->machine;
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
}
