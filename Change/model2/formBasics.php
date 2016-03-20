<?php

abstract class formBasics
{
	function __construct($dbHandler, $name) {

		$this->dbHandler = $dbHandler;
		$this->selfName = $name;
	}

	protected $dbHandler;
	protected $id;
	protected $projectId;
	protected $selfName;
	protected $reg_date;
	protected $creator;

	abstract public function delete();

	abstract protected function save($data);

	protected function deleteSql() {
		$sql = 'DELETE FROM '.$this->selfName.' WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
	}

	protected function getById() {
		$sql = "SELECT * FROM ".$this->selfName." WHERE id = :id";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$data = $result->fetch();
		return $data;
	}

	abstract function getByProjectId($id);

	public function setById($id) {
		$this->id = $id;
		$data = $this->getById();
		$this->setConcreteClass($data);
	}

	protected function setId() {
		$sql = 'SELECT id, reg_date FROM '.$this->selfName.' ORDER BY id DESC LIMIT 1';
		$result = $this->dbHandler->prepare($sql);
		$result->execute();
		$data = $result->fetch();
		$this->id = $data['id'];
		$this->reg_date = $data['reg_date'];
	}

	public function setNew($data) {
		$this->save($data);
		$this->setConcreteClass($data);
		$this->setId();
	}
}