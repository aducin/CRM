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
	private $reg_date;
	private $firma_id;
	private $dbHandler;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Rechnungsadressen WHERE id = :id";
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
		unset($this->reg_date);
		unset($this->firma_id);
	}

	public function selectDates() {
		$sql = 'SELECT name, abteilung, anschrift, anschrift2, plz, ort, firma_id, reg_date, firma_id FROM Rechnungsadressen
		WHERE id = :id';
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
		$result->execute();
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
		$result->execute();
		$array = $result->fetch();
		$this->id = $array['id'];
		$this->reg_date = $array['reg_date'];
	}
}
