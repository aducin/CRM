<?php

class Fremdsache extends formBasics
{
	private $textDate; //Datum
	private $deliverer; //Lieferant
	private $description; //Beschreibung
	private $purchasePrice; //Einkaufspreis
	private $sellPrice; //Verkaufspreis

	public function delete() {
		$this->deleteSql();
		unset($this->id);
		unset($this->projectId);
		unset($this->textDate);
		unset($this->deliverer);
		unset($this->description);
		unset($this->purchasePrice);
		unset($this->sellPrice);
		unset($this->reg_date);
		unset($this->selfName);
	}

	public function insert($self, $projectId) {
		$sql = "INSERT INTO Fremdsache ( projectId, textDate, deliverer, description, purchasePrice, sellPrice, reg_date ) VALUES ( :projectId, :textDate, :deliverer, :description, :purchasePrice, :sellPrice, NOW() )";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':textDate', $self["textDate"]);
		$result->bindValue(':deliverer', $self["deliverer"]);
		$result->bindValue(':description', $self["description"]);
		$result->bindValue(':purchasePrice', $self["purchasePrice"]);
		$result->bindValue(':sellPrice', $self["sellPrice"]);
		if ( $result->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function insertSql($data) {
		$dates = explode('<>', $data);
		if ($dates[1] != '') {
			$parts = explode('/', $dates[1]);
			$dates[1] = $parts[2].'-'.$parts[1].'-'.$parts[0];
		}
		$sql = "INSERT INTO Fremdsache ( projectId, textDate, deliverer, description, purchasePrice, sellPrice, reg_date ) VALUES ( :projectId, :textDate, :deliverer, :description, :purchasePrice, :sellPrice, NOW() )";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $dates[0]);
		$result->bindValue(':textDate', $dates[1]);
		$result->bindValue(':deliverer', $dates[2]);
		$result->bindValue(':description', $dates[3]);
		$result->bindValue(':purchasePrice', $dates[4]);
		$result->bindValue(':sellPrice', $dates[5]);
		if ( $result->execute()) {
			$wholeDate = $this->getByProjectId($dates[0]);
			foreach ($wholeDate as $singleDate){
				$id = $singleDate['id'];
			}
			return $id;
		} else {
			return 'false';
		}
	}

	public function getByProjectId($id) {
		$sql = "SELECT * FROM Fremdsache WHERE projectId = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			foreach ($result as $singleResult) {
				$pritningData[] = array( 
					'id' => $singleResult['id'], 
					'textDate' => $singleResult['textDate'], 
					'deliverer' => $singleResult['deliverer'], 
					'description' => $singleResult['description'], 
					'purchasePrice' => number_format($singleResult['purchasePrice'], 2, '.', ''), 
					'sellPrice' => number_format($singleResult['sellPrice'], 2, '.', ''), 
					'reg_date' => $singleResult['reg_date']
					);
			}
			
			if ( isset( $pritningData)) {
				return $pritningData;
			} else {
	            return null;
			}
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setConcreteClass($data) {
		$this->projectId = $data['projectId'];
		$this->textDate = $data['textDate'];
		$this->deliverer = $data['deliverer'];
		$this->description = $data['description'];
		$this->purchasePrice = $data['purchasePrice'];
		$this->sellPrice = $data['sellPrice'];
		if (isset($data['reg_date'])) {
			$this->reg_date = $data['reg_date'];
		}
	}
}