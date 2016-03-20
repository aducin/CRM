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

	public function getByProjectId($id) {
		$sql = "SELECT * FROM Fremdsache WHERE projectId = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		foreach ($result as $singleResult) {
			$pritningData[] = array( 
				'id' => $singleResult['id'], 
				'textDate' => $singleResult['textDate'], 
				'deliverer' => $singleResult['deliverer'], 
				'description' => $singleResult['description'], 
				'purchasePrice' => $singleResult['purchasePrice'], 
				'sellPrice' => $singleResult['sellPrice'],
				'reg_date' => $singleResult['reg_date']
				);
		}
		return $pritningData;
	}

	protected function save($data) {
		$sql = 'INSERT INTO Fremdsache (projectId, textDate, deliverer, description, purchasePrice, sellPrice,reg_date) 
		VALUES (:projectId, :textDate, :deliverer, :description, :purchasePrice, :sellPrice, NOW())';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $data['projectId']);
		$result->bindValue(':textDate', $data['textDate']);
		$result->bindValue(':deliverer', $data['deliverer']);
		$result->bindValue(':description', $data['description']);
		$result->bindValue(':purchasePrice', $data['purchasePrice']);
		$result->bindValue(':sellPrice', $data['sellPrice']);
		$result->execute();
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