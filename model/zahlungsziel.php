<?php

class Zahlungsziel
{
	private $id;
	private $name;
	private $beschreibung;
	private $output;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->output = new OutputController($dbHandler);

		if ($id != null) {
			$this->id = $id;
		}

	}

	public function createZahlungsziel($name, $beschreibung) {
		$this->name = $name;
		$this->beschreibung = $beschreibung;
		$this->saveZahlungsziel();
		$this->getZahlungszielbyDates();
	}

	public function deleteZahlungsziel() {
		$sql = 'DELETE FROM Zahlungsziel WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			unset($this->id);
			unset($this->name);
			unset($this->beschreibung);
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getBeschreibung() {
		return $this->beschreibung;
	}

	public function getName() {
		return $this->name;
	}

	private function getZahlungszielbyDates() {
		$sql = 'SELECT id FROM Zahlungsziel WHERE name = :name AND beschreibung = :beschreibung';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':beschreibung', $this->beschreibung);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->id = $array['id'];
		} else {
			$this->output->displayPhpError();
		}
	}

	private function saveZahlungsziel() {
		$sql = 'INSERT INTO Zahlungsziel (name, beschreibung) VALUES (:name, :beschreibung)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':beschreibung', $this->beschreibung);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	private function setDates() {
		$sql = 'SELECT name, beschreibung FROM Zahlungsziel WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->name = $array['name'];
			$this->beschreibung = $array['beschreibung'];
		} else {
			$this->output->displayPhpError();
		}
	}
}