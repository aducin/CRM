<?php

class Ansprechpartner implements TvsatzInterface
{
	public $id;
	private $dbHandler;
	private $firma_id;
	private $name;
	private $fax;
	private $mail;
	private $output;
	private $reg_date;
	private $telefon;
	private $telefon2;
	private $vorname;


	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->output = new OutputController($dbHandler);

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function clientInsert($values) {
		$name = $values[1];
		$vorname = $values[2];
		$telefon = $values[3];
		$telefon2 = $values[4];
		$fax = $values[5];
		$mail = $values[6];
		$clientId = $values[7];
		$sql = 'INSERT INTO Ansprechpartner (name, vorname, telefon, telefon2, fax, mail, firma_id) VALUES (:name, :vorname, :telefon, :telefon2, :fax, :mail, :clientId)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $name);
		$result->bindValue(':vorname', $vorname);
		$result->bindValue(':telefon', $telefon);
		$result->bindValue(':telefon2', $telefon2);
		$result->bindValue(':fax', $fax);
		$result->bindValue(':mail', $mail);
		$result->bindValue(':clientId', $clientId);
		if ($result->execute()) {
			$id = $this->getLastId($values);
			return $id;
		} else {
			return 'false';
		}
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Ansprechpartner WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			unset($this->id);
			unset($this->name);
			unset($this->vorname);
			unset($this->telefon);
			unset($this->telefon2);
			unset($this->fax);
			unset($this->mail);
			unset($this->reg_date);
			unset($this->firma_id);
		} else {
			$this->output->displayPhpError();
		}
	}

	public function deleteSql( $data ) {
		$sql = "DELETE FROM Ansprechpartner WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $data);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}
	
	public function getAllAnsprechpartner($id) {
		$sql = "SELECT * FROM Ansprechpartner WHERE firma_id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
			    $list[] = array(
			      'id' => $singleResult['id'], 
			      'name' => $singleResult['name'],
			      'vorname' => $singleResult['vorname'],
			      'telefon' => $singleResult['telefon'],
			      'telefon2' => $singleResult['telefon2'],
			      'fax' => $singleResult['fax'],
			      'mail' => $singleResult['mail']
			    );
			}
		return $list;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getDates() {
		$result = array(  'id' => $this->id, 'name' => $this->name, 'vorname' => $this->vorname, 'telefon' => $this->telefon, 'telefon2' => $this->telefon2, 'fax' => $this->fax,
			'mail' => $this->mail, 'reg_date' => $this->reg_date, 'firma_id' => $this->firma_id );
		return $result;
	}

	public function getId() {
		$sql = 'SELECT id, reg_date FROM Ansprechpartner WHERE name = :name AND vorname = :vorname AND mail = :mail';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':vorname', $this->vorname);
		$result->bindValue(':mail', $this->mail);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->id = $array['id'];
			$this->reg_date = $array['reg_date'];
		} else {
			$this->output->displayPhpError();
		}
	}

	private function getLastId($values) {
		$sql = 'SELECT id, reg_date FROM Ansprechpartner WHERE name = :name AND vorname = :vorname AND telefon = :telefon AND firma_id = :firma_id ORDER BY id DESC LIMIT 1';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $values[1]);
		$result->bindValue(':vorname', $values[2]);
		$result->bindValue(':telefon', $values[3]);
		$result->bindValue(':firma_id', $values[7]);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->id = $array['id'];
			return $this->id;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getName() {
		$name = $this->name.' '.$this->vorname;
		return $name;
	}

	public function getObjectId() {
		return $this->id;
	}

	public function searchById($id) {
		$sql = 'SELECT CONCAT_WS(" ", name, vorname) as name FROM Ansprechpartner WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$data = $result->fetch();
			return $data['name'];
		} else {
			$this->output->displayPhpError();
		}
	}

	public function searchByName($value) {
		$isArray = explode(',', $value);
		if (isset($isArray[1])){
			$value1 = $isArray[0];
			$value2 = $isArray[1];
			$name = '%'.$value2.'%';
			$searchedName = '%'.$value1.'%';
			$sql = "SELECT Ansprechpartner.id, CONCAT_WS(' ', Ansprechpartner.name, Ansprechpartner.vorname) AS name FROM Ansprechpartner INNER JOIN Auftraggeber ON Ansprechpartner.firma_id = Auftraggeber.id 
				WHERE Auftraggeber.name like ? AND CONCAT_WS(' ', Ansprechpartner.name, Ansprechpartner.vorname) like ?";
			$result=$this->dbHandler->prepare($sql);
			$result->execute(array($name, $searchedName));
		} else {
			$name = '%'.$value.'%';
			$sql = "SELECT id, CONCAT_WS(' ', name, vorname) AS name FROM Ansprechpartner WHERE CONCAT_WS(' ', name, vorname) like ?";
			$result=$this->dbHandler->prepare($sql);
			$result->execute(array($name));
		}
		foreach ($result as $singleResult) {
				$finalResult[] = array('name' => $singleResult['name'], 'id' => $singleResult['id']);
		}
		if (!isset($finalResult)) {
			$finalResult = null;
		}
		return $finalResult;
	}

	private function selectDates() {
		$sql='SELECT name, vorname, telefon, telefon2, fax, mail, reg_date, firma_id FROM Ansprechpartner 
		WHERE id= :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$data=$result->fetch();
			return $data;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setDates() {
		$data = $this->selectDates();
		$this->name = $data['name'];
		$this->vorname = $data['vorname'];
		$this->telefon = $data['telefon'];
		$this->telefon2 = $data['telefon2'];
		$this->fax = $data['fax'];
		$this->mail = $data['mail'];
		$this->reg_date = $data['reg_date'];
		$this->firma_id = $data['firma_id'];
	}

	public function setCustomDates( $array ) {
		$this->name = $array[0];
		$this->vorname = $array[1];
		$this->telefon = $array[2];
		$this->telefon2 = $array[3];
		$this->fax = $array[4];
		$this->mail = $array[5];
		$this->firma_id = $array[6];
		$this->saveCustomDates();
	}

	public function saveCustomDates() {
		$sql = 'INSERT INTO Ansprechpartner (name, vorname, telefon, telefon2, fax, mail, reg_date, firma_id) 
			VALUES ( :name, :vorname, :telefon, :telefon2, :fax, :mail, NOW(), :firma_id )';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':vorname', $this->vorname);
		$result->bindValue(':telefon', $this->telefon);
		$result->bindValue(':telefon2', $this->telefon2);
		$result->bindValue(':fax', $this->fax);
		$result->bindValue(':mail', $this->mail);
		$result->bindValue(':firma_id', $this->firma_id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	public function updateRow($column, $rowId, $value) {
		$sql = 'UPDATE Ansprechpartner SET '.$column.' = :value WHERE id = :id';
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