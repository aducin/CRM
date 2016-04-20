<?php

abstract class formBasics
{
	function __construct($dbHandler, $name) {

		$this->dbHandler = $dbHandler;
		$this->selfName = $name;
		$this->output = new OutputController($dbHandler);
	}

	protected $dbHandler;
	protected $id;
	protected $projectId;
	protected $selfName;
	protected $reg_date;
	protected $creator;
	protected $output;

	abstract public function delete();

	//abstract protected function save($data);

	public function deleteSql($id) {
		$sql = 'DELETE FROM '.$this->selfName.' WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}

	protected function getById() {
		$sql = "SELECT * FROM ".$this->selfName." WHERE id = :id";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$data = $result->fetch();
			return $data;
		} else {
			$this->output->displayPhpError();
		}
	}

	abstract function getByProjectId($id);

	public function getTotalAmount($name) {
		$dates = explode('-', $name);
		$table = $dates[0];
		$projectId = $dates[1];
		if ($table == 'Fremdsache') {
			$column = 'sellPrice';
		} elseif ($table == 'Drucksache') {
			$column = 'amount';
		} elseif ($table == 'Vorstufe') {
			$column = 'amount';
		}
		$sql = "SELECT ".$column." FROM ".$table." WHERE projectId = ".$projectId;
		$result = $this->dbHandler->prepare($sql);
	    if ($result->execute()) {
	    	$amount = '';
	    	foreach ($result as $singleResult) {
	    		$amount += $singleResult[$column];
	    	}
	    	if ($amount == "") {
	    		$amount = 0;
	    	}
	    	$amount = number_format($amount, 2, '.', '');
	    } else {
		$amount = 'false';
	    }
	    return $amount;
	}

	public function row($origin, $id, $column, $value) {
	    $sql = 'UPDATE '.$origin.' SET '.$column.' = :value WHERE id = :id';
	    $result = $this->dbHandler->prepare($sql);
	    $result->bindValue(':id', $id);
	    $result->bindValue(':value', $value);
	    if ($result->execute()) {
		$success = 'done';
	    } else {
		$success = 'false';
	    }
	    return $success;
	}
	
	public function setById($id) {
		$this->id = $id;
		$data = $this->getById();
		$this->setConcreteClass($data);
	}

	protected function setId() {
		$sql = 'SELECT id, reg_date FROM '.$this->selfName.' ORDER BY id DESC LIMIT 1';
		$result = $this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$data = $result->fetch();
			$this->id = $data['id'];
			$this->reg_date = $data['reg_date'];
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setNew($data) {
		$this->save($data);
		$this->setConcreteClass($data);
		$this->setId();
	}
}