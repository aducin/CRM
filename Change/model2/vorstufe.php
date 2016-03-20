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
		$employee = $this->creator->createProduct('ansprechpartner');
		$helper = $this->creator->createProduct('helpers');
		foreach ($result as $singleResult) {
			$performanceTime = explode("-", $singleResult['performanceTime']);
			$name = $employee->searchById($singleResult['employee']);
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
		return $pritningData;
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
		$result->execute();
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