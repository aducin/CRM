<?php

class Drucksache extends formBasics
{
	private $print; //Drucksache
	private $machine; //Maschine
	private $type; //Art
	private $edition; //Auflage
	private $format;
	private $size; //Umfang
	private $color; //Farbe
	private $paper; //Papier
	private $remodelling; //verarbeitung
	private $finished; //erledigt
	private $amount; //Betrag

	public function delete() {
		$this->deleteSqlDrucksache();
		unset($this->id);
		unset($this->projectId);
		unset($this->print);
		unset($this->machine);
		unset($this->type);
		unset($this->edition);
		unset($this->format);
		unset($this->size);
		unset($this->color);
		unset($this->paper);
		unset($this->remodelling);
		unset($this->finished);
		unset($this->amount);
		unset($this->reg_date);
	}

	public function getByProjectId($id) {
		$sql = "SELECT * FROM Drucksache WHERE projectId = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		foreach ($result as $singleResult) {
			$pritningData[] = array( 
				'id' => $singleResult['id'], 
				'print' => $singleResult['print'], 
				'machine' => $singleResult['machine'], 
				'type' => $singleResult['type'], 
				'edition' => $singleResult['edition'], 
				'format' => $singleResult['format'],
				'size' => $singleResult['size'],
				'color' => $singleResult['color'],
				'paper' => $singleResult['paper'],
				'remodelling' => $singleResult['remodelling'],
				'finished' => $singleResult['finished'],
				'amount' => $singleResult['amount'],
				'reg_date' => $singleResult['reg_date']
				);
		}
		return $pritningData;
	}

	protected function save($data) {
		$sql = 'INSERT INTO Drucksache (projectId, print, machine, type, edition, format, size, color, paper, remodelling, finished, amount, reg_date) 
		VALUES (:projectId, :print, :machine, :type, :edition, :format, :size, :color, :paper, :remodelling, :finished, :amount, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $data['projectId']);
		$result->bindValue(':print', $data['print']);
		$result->bindValue(':machine', $data['machine']);
		$result->bindValue(':type', $data['type']);
		$result->bindValue(':edition', $data['edition']);
		$result->bindValue(':format', $data['format']);
		$result->bindValue(':size', $data['size']);
		$result->bindValue(':color', $data['color']);
		$result->bindValue(':paper', $data['paper']);
		$result->bindValue(':remodelling', $data['remodelling']);
		$result->bindValue(':finished', $data['finished']);
		$result->bindValue(':amount', $data['amount']);
		$result->execute();
	}

	protected function setConcreteClass($data) {
		$this->projectId = $data['projectId'];
		$this->print = $data['print'];
		$this->machine = $data['machine'];
		$this->type = $data['type'];
		$this->edition = $data['edition'];
		$this->format = $data['format'];
		$this->size = $data['size'];
		$this->color = $data['color'];
		$this->paper = $data['paper'];
		$this->remodelling = $data['remodelling'];
		$this->finished = $data['finished'];
		$this->amount = $data['amount'];
		if (isset($data['reg_date'])) {
			$this->reg_date = $data['reg_date'];
		}
	}
}
