<?php

class Lieferant
{
	public $id;
	private $name;

	function __construct($dbHandler, $id) {

		$this->id = $id;
		$this->setName($id, $dbHandler);

	}

	public function getLieferant() {
		return $this->lieferant;
	}

	private function setName($id, $dbHandler) {
		$sql = "SELECT name FROM Lieferant WHERE id = :id";
		$result = $dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$name = $result->fetch();
		$this->name = $name['name'];
	}
}