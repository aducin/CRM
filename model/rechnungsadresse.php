<?php

class Rechnungsadresse implements TvsatzInterface
{
	public $id;
	private $name;
	private $abteilung;
	private $anschrift;
	private $anschrift2;
	private $plz;
	private $ort;
	private $output;
	private $reg_date;
	private $firma_id;
	private $dbHandler;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->output = new OutputController($dbHandler);

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function clientInsert($values) {
		$name = $values[1];
		$departement = $values[2];
		$address = $values[3];
		$address2 = $values[4];
		$code = $values[5];
		$place = $values[6];
		$clientId = $values[7];
		$sql = 'INSERT INTO Rechnungsadressen (name, abteilung, anschrift, anschrift2, plz, ort, firma_id, active) VALUES (:name, :departement, :address, :address2, :code, :place, :clientId, 1)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $name);
		$result->bindValue(':departement', $departement);
		$result->bindValue(':address', $address);
		$result->bindValue(':address2', $address2);
		$result->bindValue(':code', $code);
		$result->bindValue(':place', $place);
		$result->bindValue(':clientId', $clientId);
		if ($result->execute()) {
			$id = $this->getLastId($values);
			return $id;
		} else {
			return 'false';
		}
	}

	public function deleteCurrentDates() {
		$sql = "UPDATE Rechnungsadressen SET active = 0 WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			unset($this->id);
			unset($this->name);
			unset($this->abteilung);
			unset($this->anschrift);
			unset($this->anschrift2);
			unset($this->plz);
			unset($this->ort);
			unset($this->reg_date);
			unset($this->firma_id);
		} else {
			$this->output->displayPhpError();
		}
	}

	public function deleteSql( $data ) {
		$sql = "UPDATE Rechnungsadressen SET active = 0 WHERE id = :id";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $data);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}
	
	public function getAllRechnungsadressen($id) {
		$sql = "SELECT * FROM Rechnungsadressen WHERE firma_id = :id AND active = 1";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
			      $list[] = array(
			      'id' => $singleResult['id'], 
			      'name' => $singleResult['name'],
			      'abteilung' => $singleResult['abteilung'],
			      'anschrift' => $singleResult['anschrift'],
			      'anschrift2' => $singleResult['anschrift2'],
			      'plz' => $singleResult['plz'],
			      'ort' => $singleResult['ort']
			      );
			}
			return $list;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getDates() {
		$result = array(  'id' => $this->id, 'name' => $this->name, 'abteilung' => $this->abteilung, 'anschrift' => $this->anschrift, 'anschrift2' => $this->anschrift2, 'plz' => $this->plz,
			'ort' => $this->ort, 'firma_id' => $this->firma_id, 'reg_date' => $this->reg_date, 'firma_id' => $this->firma_id );
		return $result;
	}

	public function getId() {
		$sql = 'SELECT id, reg_date FROM Rechnungsadressen WHERE name = :name AND abteilung = :abteilung AND anschrift = :anschrift AND firma_id = :firma_id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':abteilung', $this->abteilung);
		$result->bindValue(':anschrift', $this->anschrift);
		$result->bindValue(':firma_id', $this->firma_id);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->id = $array['id'];
			$this->reg_date = $array['reg_date'];
		} else {
			$this->output->displayPhpError();
		}
	}

	private function getLastId($values) {
		$sql = 'SELECT id, reg_date FROM Rechnungsadressen WHERE name = :name AND abteilung = :abteilung AND anschrift = :anschrift AND firma_id = :firma_id ORDER BY id DESC LIMIT 1';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $values[1]);
		$result->bindValue(':abteilung', $values[2]);
		$result->bindValue(':anschrift', $values[3]);
		$result->bindValue(':firma_id', $values[7]);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->id = $array['id'];
			return $this->id;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getObjectId() {
		return $this->id;
	}
	
	public function searchByChosenName($value) {
	    $values = explode(': ', $value);
	    $name = $values[0];
	    $department = $values[1];
	    $sql = "SELECT id FROM Rechnungsadressen WHERE name = :name AND abteilung = :department";
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':name', $name);
	    $result->bindValue(':department', $department);
	    if ($result->execute()) {
	    	$id = $result->fetch();
	    	return $id['id'];
	    } else {
	    	$this->output->displayPhpError();
	    }
	}

	public function searchByName($value) {
		$isArray = explode(',', $value);
		if (isset($isArray[1])){
			$value1 = $isArray[0];
			$value1 = str_replace(': ', ' ', $value1);
			$value2 = $isArray[1];
			$name = '%'.$value2.'%';
			$searchedName = '%'.$value1.'%';
			$sql = "SELECT Rechnungsadressen.id, Rechnungsadressen.name as name, Rechnungsadressen.abteilung as abteilung FROM Rechnungsadressen INNER JOIN Auftraggeber ON Rechnungsadressen.firma_id = Auftraggeber.id
			WHERE Auftraggeber.name like ? AND CONCAT_WS(' ', Rechnungsadressen.name, Rechnungsadressen.abteilung) like ?";
			$result=$this->dbHandler->prepare($sql);
			$result->execute(array($name, $searchedName));
		} else {
			$name = '%'.$value.'%';
			$sql = "SELECT id, name, abteilung FROM Rechnungsadressen WHERE CONCAT_WS(' ', name, abteilung) like ?";
			$result=$this->dbHandler->prepare($sql);
			$result->execute(array($name));
		}
		foreach ($result as $singleResult) {
			$finalResult[] = array('name' => $singleResult["name"].': '.$singleResult["abteilung"], 'id' => $singleResult["id"]);
		}
		if (!isset($finalResult)) {
			$finalResult = null;
		}
		return $finalResult;
	}

	public function selectDates() {
		$sql = 'SELECT id, name, abteilung, anschrift, anschrift2, plz, ort, firma_id, reg_date, firma_id FROM Rechnungsadressen
		WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$data = $result->fetch();
			return $data;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setDates() {
		$data = $this->selectDates();
		$this->name = $data['name'];
		$this->abteilung = $data['abteilung'];
		$this->anschrift = $data['anschrift'];
		$this->anschrift2 = $data['anschrift2'];
		$this->plz = $data['plz'];
		$this->ort = $data['ort'];
		$this->firma_id = $data['firma_id'];
		$this->reg_date = $data['reg_date'];
		$this->firma_id = $data['firma_id'];
	}

	public function setCustomDates( $array ) {
		$this->name = $array[0];
		$this->abteilung = $array[1];
		$this->anschrift = $array[2];
		$this->anschrift2 = $array[3];
		$this->plz = $array[4];
		$this->ort = $array[5];
		$this->firma_id = $array[6];
		$this->saveCustomDates();
	}

	public function saveCustomDates() {
		$sql = 'INSERT INTO Rechnungsadressen (name, abteilung, anschrift, anschrift2, plz, ort, reg_date, firma_id) 
			VALUES ( :name, :abteilung, :anschrift, :anschrift2, :plz, :ort, NOW(), :firma_id )';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':abteilung', $this->abteilung);
		$result->bindValue(':anschrift', $this->anschrift);
		$result->bindValue(':anschrift2', $this->anschrift2);
		$result->bindValue(':plz', $this->plz);
		$result->bindValue(':ort', $this->ort);
		$result->bindValue(':firma_id', $this->firma_id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}
	
	public function updateRow($column, $rowId, $value) {
		$sql = 'UPDATE Rechnungsadressen SET '.$column.' = :value WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $rowId);
		$result->bindValue(':value', $value);
		if ($result->execute()) {
		    return 'success';
		} else {
		    return 'false';
		}
	}
}
