<?php

class Lieferant
{
	public $id;
	private $name;
	private $output;

	function __construct($dbHandler, $id) {

		$this->id = $id;
		$this->setName($id, $dbHandler);
		$this->output = new OutputController($dbHandler);

	}

	public function getLieferant() {
		return $this->lieferant;
	}

	private function setName($id, $dbHandler) {
		$sql = "SELECT name FROM Lieferant WHERE id = :id";
		$result = $dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$name = $result->fetch();
			$this->name = $name['name'];
		} else {
			$this->output->displayPhpError();
		}
	}
}