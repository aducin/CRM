<?php

class Auftraggeber implements TvsatzInterface
{
	public $id;
	private $name;
	private $abteilung;
	private $anschrift;
	private $anschrift2;
	private $plz;
	private $ort;
	private $telefon;
	private $fax;
	private $mail;
	private $skonto;
	private $reg_date;
	private $zahlungsziel = array();
	private $dbHandler;

	public $ansprechpartner = array();
	public $rechnungsadresse = array();

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Auftraggeber WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		unset($this->id);
		unset($this->name);
		unset($this->abteilung);
		unset($this->anschrift);
		unset($this->anschrift2);
		unset($this->plz);
		unset($this->ort);
		unset($this->telefon);
		unset($this->fax);
		unset($this->mail);
		unset($this->skonto);
		unset($this->reg_date);
		unset($this->zahlungsziel);
	}

	private function selectDates() {
		$sql = $this->getSql();
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$data = $result->fetch();
		return $data;
	}

	public function setDates() {
		$data = $this->selectDates();
		$this->name = $data['name'];
		$this->abteilung = $data['abteilung'];
		$this->anschrift = $data['anschrift'];
		$this->anschrift2 = $data['anschrift2'];
		$this->plz = $data['plz'];
		$this->ort = $data['ort'];
		$this->telefon = $data['telefon'];
		$this->fax = $data['fax'];
		$this->mail = $data['mail'];
		$this->skonto = $data['skonto'];
		$this->zahlungsziel = array('id' => $data['zahlungsziel_id'], 'name' => $data['zahlName']);
		$this->reg_date = $data['reg_date'];
	}

	public function setCustomDates( $array ) {
		$this->name = $array[0];
		$this->abteilung = $array[1];
		$this->anschrift = $array[2];
		$this->anschrift2 = $array[3];
		$this->plz = $array[4];
		$this->ort = $array[5];
		$this->telefon = $array[6];
		$this->fax = $array[7];
		$this->mail = $array[8];
		$this->skonto = $array[9];
		$this->zahlungsziel = $array[10];
		$this->saveCustomDates();
	}

	public function saveCustomDates() {
		$sql = 'INSERT INTO Auftraggeber (name, abteilung, anschrift, anschrift2, plz, ort, telefon, fax, mail, skonto, zahlungsziel_id, reg_date) 
			VALUES ( :name, :abteilung, :anschrift, :anschrift2, :plz, :ort, :telefon, :fax, :mail, :skonto, :zahlungsziel, NOW() )';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':abteilung', $this->abteilung);
		$result->bindValue(':anschrift', $this->anschrift);
		$result->bindValue(':anschrift2', $this->anschrift2);
		$result->bindValue(':plz', $this->plz);
		$result->bindValue(':ort', $this->ort);
		$result->bindValue(':telefon', $this->telefon);
		$result->bindValue(':fax', $this->fax);
		$result->bindValue(':mail', $this->mail);
		$result->bindValue(':skonto', $this->skonto);
		$result->bindValue(':zahlungsziel', $this->zahlungsziel);
		$result->execute();
	}

	public function searchByName($name) {
		$name = '%'.$name.'%';
		$sql="SELECT id, name FROM Auftraggeber WHERE name LIKE ?";
		$result=$this->dbHandler->prepare($sql);
		$result->execute(array($name));
		foreach ($result as $singleResult) {
			$names[] = array('name' => $singleResult['name'], 'id' => $singleResult['id']);
		}
		if (!isset($names)) {
			$names = 'error';
		}
		return $names;
	}

	public function setAnsprechpartner() {
		$sql='SELECT id FROM Ansprechpartner
		WHERE firma_id= :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$counter = 0;

		foreach ($result as $singleResult) {
			$singlePartner = new Ansprechpartner($this->dbHandler, $singleResult['id']);
			$singlePartner->setDates();
			$this->ansprechpartner[$counter] = $singlePartner;
			$counter++;
		}

	}

	public function setRechnungsadresse() {
		$sql='SELECT id FROM Rechnungsadressen
		WHERE firma_id= :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$counter = 0;

		foreach ($result as $singleResult) {
			$singlePartner = new Rechnungsadresse($this->dbHandler, $singleResult['id']);
			$singlePartner->setDates();
			$this->rechnungsadresse[$counter] = $singlePartner;
			$counter++;
		}

	}

	private function getSql() {
		$sql='SELECT Auftraggeber.name as name, abteilung, anschrift, anschrift2, plz, ort, telefon, fax, mail, skonto, zahlungsziel_id, reg_date, Zahlungsziel.name as zahlName FROM Auftraggeber
		INNER JOIN Zahlungsziel ON Auftraggeber.zahlungsziel_id = Zahlungsziel.id 
		WHERE Auftraggeber.id= :id';
		return $sql;
	}

	public function getDates() {
		$result = array(  'id' => $this->id, 'name' => $this->name, 'abteilung' => $this->abteilung, 'anschrift' => $this->anschrift, 'anschrift2' => $this->anschrift2, 'plz' => $this->plz,
			'ort' => $this->ort, 'telefon' => $this->telefon, 'fax' => $this->fax, 'mail' => $this->mail, 'skonto' => $this->skonto, 
			'zahlungsziel' => $this->zahlungsziel, 'reg_date' => $this->reg_date);
		return $result;
	}

	public function getId() {
		$sql = 'SELECT id, reg_date FROM Auftraggeber WHERE name = :name AND abteilung = :abteilung AND anschrift = :anschrift AND zahlungsziel_id = :zahlungsziel_id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':abteilung', $this->abteilung);
		$result->bindValue(':anschrift', $this->anschrift);
		$result->bindValue(':zahlungsziel_id', $this->zahlungsziel);
		$result->execute();
		$array = $result->fetch();
		$this->id = $array['id'];
		$this->reg_date = $array['reg_date'];
	}

	public function getAnsprechpartner() {
		return $this->ansprechpartner;
	}

	public function getRechnungsadresse() {
		return $this->rechnungsadresse;
	}
}