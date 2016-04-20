<?php

class Vorstufe extends formBasics
{
	private $type; //Art
	private $performanceTime; //erledigt
	private $employee = array(); //Mitarbeiter
	private $description; //Beschreibung
	private $timeProposal; //Zeit Angebot
	private $timeReal; //Zeit tatsachlich
	private $timeSettlement; //Zeit verrechenbar
	private $amount; //Betrag
	private $settlement; //verrechenbar

	public function delete() {
		$this->deleteSqlVorstufe();
		unset($this->id);
		unset($this->projectId);
		unset($this->type);
		unset($this->performanceTime);
		unset($this->employee);
		unset($this->description);
		unset($this->timeProposal);
		unset($this->timeReal);
		unset($this->timeSettlement);
		unset($this->amount);
		unset($this->settlement);
		unset($this->reg_date);
	}

	public function getByProjectId($id) {
		$sql = "SELECT * FROM Vorstufe WHERE projectId = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$this->creator = new TvsatzCreator($this->dbHandler);
		$helper = $this->creator->createProduct('helpers');
		foreach ($result as $singleResult) {
			$performanceTime = explode("-", $singleResult['performanceTime']);
			$name = $helper->getSingleBenutzer($singleResult['employee']);
			$typeName = $helper->getSingleArt($singleResult['type']);
			$pritningData[] = array( 
				'id' => $singleResult['id'], 
				'typeId' => $singleResult['type'],
				'typeName' => $typeName,
				'performanceTime' => $performanceTime[2].'.'.$performanceTime[1].'.'.$performanceTime[0], 
				'employeeId' => $singleResult['employee'], 
				'employeeName' => $name,
				'description' => $singleResult['description'], 
				'timeProposal' => $singleResult['timeProposal'],
				'timeReal' => $singleResult['timeReal'],
				'timeSettlement' => $singleResult['timeSettlement'],
				'amount' => $singleResult['amount'],
				'settlement' => $singleResult['settlement'],
				'reg_date' => $singleResult['reg_date']
				);
		}
		if ( isset( $pritningData )) {
			return $pritningData;
		} else {
			return null;
		}
	}

	public function insert($self, $projectId) {
		$time = explode('.',$self["performanceTime"]);
		$self["performanceTime"] = $time[2].'-'.$time[1].'-'.$time[0];
		$sql = 'INSERT INTO Vorstufe (projectId, type, performanceTime, employee, description, timeProposal, timeReal, timeSettlement, 
			amount, settlement, reg_date) VALUES (:projectId, :type, :performanceTime, :employee, :description, :timeProposal, :timeReal, :timeSettlement, :amount, :settlement, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':type', $self["typeId"]);
		$result->bindValue(':performanceTime', $self["performanceTime"]);
		$result->bindValue(':employee', $self["employeeId"]);
		$result->bindValue(':description', $self["description"]);
		$result->bindValue(':timeProposal', $self["timeProposal"]);
		$result->bindValue(':timeReal', $self["timeReal"]);
		$result->bindValue(':timeSettlement', $self["timeSettlement"]);
		$result->bindValue(':amount', $self["amount"]);
		$result->bindValue(':settlement', $self["settlement"]);
		if ( $result->execute()) {
			return true;
		} else {
			return false;
		}
	}
	
	public function insertSql($value) {
	    $data = explode('<>', $value);
	    if ($data[1] == 'none') {
		    $data[1] = null;
	    }
	    if ($data[3] == 'none') {
		    $data[3] = null;
	    }
	    $projectId = $_SESSION['projectId'];
	    $performanceTime = str_replace('/', '-', $data[2]);
	    $settlement = intval($data[9]);
	    $sql = 'INSERT INTO Vorstufe (projectId, type, performanceTime, employee, description, timeProposal, timeReal, timeSettlement, 
			amount, settlement, reg_date) VALUES (:projectId, :type, :performanceTime, :employee, :description, :timeProposal, :timeReal, :timeSettlement, :amount, :settlement, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':type', $data[1]);
		$result->bindValue(':performanceTime', $performanceTime);
		$result->bindValue(':employee', $data[3]);
		$result->bindValue(':description', $data[4]);
		$result->bindValue(':timeProposal', $data[5]);
		$result->bindValue(':timeReal', $data[6]);
		$result->bindValue(':timeSettlement', $data[7]);
		$result->bindValue(':amount', $data[8]);
		$result->bindValue(':settlement', $settlement);
		if ( $result->execute()) {
			$wholeDate = $this->getByProjectId($data[0]);
			foreach ($wholeDate as $singleDate){
				$id = $singleDate['id'];
			}
			return $id;
		} else {
			return 'false';
		}
	}

	protected function save($data) {
		$sql = 'INSERT INTO Vorstufe (projectId, type, performanceTime, employee, description, timeProposal, timeReal, timeSettlement, 
			amount, settlement, reg_date) VALUES (:projectId, :type, :performanceTime, :employee, :description, :timeProposal, :timeReal, :timeSettlement, :amount, :settlement, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $data['projectId']);
		$result->bindValue(':type', $data['type']);
		$result->bindValue(':performanceTime', $data['performanceTime']);
		$result->bindValue(':employee', $data['employee']);
		$result->bindValue(':description', $data['description']);
		$result->bindValue(':timeProposal', $data['timeProposal']);
		$result->bindValue(':timeReal', $data['timeReal']);
		$result->bindValue(':timeSettlement', $data['timeSettlement']);
		$result->bindValue(':amount', $data['amount']);
		$result->bindValue(':settlement', $data['settlement']);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	protected function setConcreteClass($data) {
		$this->projectId = $data['projectId'];
		$this->type = $data['type'];
		$this->performanceTime = $data['performanceTime'];
		$this->employee = $data['employee'];
		$this->description = $data['description'];
		$this->timeProposal = $data['timeProposal'];
		$this->timeReal = $data['timeReal'];
		$this->timeSettlement = $data['timeSettlement'];
		$this->amount = $data['amount'];
		$this->settlement = $data['settlement'];
		if (isset($data['reg_date'])) {
			$this->reg_date = $data['reg_date'];
		}
	}
}