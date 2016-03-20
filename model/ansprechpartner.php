<?php

class Ansprechpartner implements TvsatzInterface
{
	public $id;
	private $dbHandler;
	private $name;
	private $vorname;
	private $telefon;
	private $telefon2;
	private $fax;
	private $mail;
	private $reg_date;
	private $firma_id;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Ansprechpartner WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		unset($this->id);
		unset($this->name);
		unset($this->vorname);
		unset($this->telefon);
		unset($this->telefon2);
		unset($this->fax);
		unset($this->mail);
		unset($this->reg_date);
		unset($this->firma_id);
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
		$result->execute();
		$array = $result->fetch();
		$this->id = $array['id'];
		$this->reg_date = $array['reg_date'];
	}

	public function searchById($id) {
		$sql = 'SELECT CONCAT_WS(" ", name, vorname) as name FROM Ansprechpartner WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$data = $result->fetch();
		return $data['name'];
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
		$result->execute();
		$data=$result->fetch();
		return $data;
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
		$result->execute();
	}
}