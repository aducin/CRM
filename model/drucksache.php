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

	public function getById($id) {
		$sql = "SELECT projectId, print,machine, type, edition, format, size, color, paper, remodelling, finished, amount  
		FROM Drucksache WHERE Drucksache.id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		foreach ($result as $singleResult) {
			$pritningData[] = array( 
				'projectId' => $singleResult['projectId'],
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
				);
		}
		return $pritningData;
	}

	public function getByProjectId($id) {
		$sql = "SELECT Drucksache.id as id, print, Drucksache.machine as machine, Maschine.name as name, type, edition, format, size, color, paper, remodelling, finished, amount, reg_date 
		FROM Drucksache INNER JOIN Maschine ON Drucksache.machine = Maschine.id WHERE projectId = :id ORDER BY Drucksache.id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$printingData = array();
		foreach ($result as $singleResult) {
			$printingData[] = array( 
				'id' => $singleResult['id'],
				'print' => $singleResult['print'], 
				'machine' => $singleResult['machine'], 
				'machineName' => $singleResult['name'],
				'type' => $singleResult['type'], 
				'edition' => $singleResult['edition'], 
				'format' => $singleResult['format'],
				'size' => $singleResult['size'],
				'color' => $singleResult['color'],
				'paper' => $singleResult['paper'],
				'remodelling' => $singleResult['remodelling'],
				'finished' => $singleResult['finished'],
				'amount' => number_format($singleResult['amount'], 2, '.', ''),
				'reg_date' => $singleResult['reg_date']
				);
		}
		if ( isset( $printingData)) {
			return $printingData;
		} else {
            return null;
		}
	}

	public function getLastId($data) {
		$sql = "SELECT id FROM Drucksache WHERE projectId = :projectId AND print = :print AND machine = :machine AND type = :type AND edition = :edition AND format = :format AND size = :size ORDER BY id DESC LIMIT 1";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $data['projectId']);
		$result->bindValue(':print', $data['print']);
		$result->bindValue(':machine', $data['machine']);
		$result->bindValue(':type', $data['type']);
		$result->bindValue(':edition', $data['edition']);
		$result->bindValue(':format', $data['format']);
		$result->bindValue(':size', $data['size']);
		if ($result->execute()) {
			$final = $result->fetch();
		return $final['id'];
		} else {
			$error = 'false';
			return $error;
		}
	}

	public function insert($self, $projectId) {
		$sql = 'INSERT INTO Drucksache (projectId, print, machine, type, edition, format, size, color, paper, remodelling, finished, amount, reg_date) 
		VALUES (:projectId, :print, :machine, :type, :edition, :format, :size, :color, :paper, :remodelling, :finished, :amount, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':print', $self["print"]);
		$result->bindValue(':machine', $self["machine"]);
		$result->bindValue(':type', $self["type"]);
		$result->bindValue(':edition', $self["edition"]);
		$result->bindValue(':format', $self["format"]);
		$result->bindValue(':size', $self["size"]);
		$result->bindValue(':color', $self["color"]);
		$result->bindValue(':paper', $self["paper"]);
		$result->bindValue(':remodelling', $self["remodelling"]);
		$result->bindValue(':finished', $self["finished"]);
		$result->bindValue(':amount', $self["amount"]);
		if ( $result->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function insertSql( $data ) {
		$data = explode('<>', $data);
		if ($data[2] == 'none') {
		    $data[2] = null;
		}
		$sql = 'INSERT INTO Drucksache (projectId, print, machine, type, edition, format, size, color, paper, remodelling, finished, amount, reg_date) 
		VALUES (:projectId, :print, :machine, :type, :edition, :format, :size, :color, :paper, :remodelling, :finished, :amount, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $data[0]);
		$result->bindValue(':print', $data[1]);
		$result->bindValue(':machine', $data[2]);
		$result->bindValue(':type', $data[3]);
		$result->bindValue(':edition', $data[4]);
		$result->bindValue(':format', $data[5]);
		$result->bindValue(':size', $data[6]);
		$result->bindValue(':color', $data[7]);
		$result->bindValue(':paper', $data[8]);
		$result->bindValue(':remodelling', $data[9]);
		$result->bindValue(':finished', $data[10]);
		$result->bindValue(':amount', $data[11]);
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
	
	public function save( $data ) {
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
