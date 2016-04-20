<?php

class Auftraggeber implements TvsatzInterface
{
	public $id;
	private $name;
	private $abteilung;
	private $anschrift;
	private $anschrift2;
	private $plz;
	private $ort;
	private $telefon;
	private $fax;
	private $mail;
	private $output;
	private $skonto;
	private $reg_date;
	private $zahlungsziel = array();
	private $dbHandler;

	public $ansprechpartner = array();
	public $rechnungsadresse = array();

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->output = new OutputController($dbHandler);

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Auftraggeber WHERE id = :id";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			unset($this->id);
			unset($this->name);
			unset($this->abteilung);
			unset($this->anschrift);
			unset($this->anschrift2);
			unset($this->plz);
			unset($this->ort);
			unset($this->telefon);
			unset($this->fax);
			unset($this->mail);
			unset($this->skonto);
			unset($this->reg_date);
			unset($this->zahlungsziel);
		} else {
			$this->output->displayPhpError();
		}
	}
	
	public function getAjaxDates($id) {
	    $sql = 'SELECT Auftraggeber.id as clientId, Auftraggeber.name as name, abteilung, anschrift, anschrift2, ort, plz, fax, telefon, mail, skonto, Zahlungsziel.id as payId, Zahlungsziel.name as payTarget FROM Auftraggeber INNER JOIN Zahlungsziel ON Auftraggeber.zahlungsziel_id = Zahlungsziel.id WHERE Auftraggeber.id = :id';
	    $result = $this->dbHandler->prepare($sql);
	    $result->bindValue(':id', $id);
	    if ($result->execute()) {
			$dates = $result->fetch();
			$final = array( 
			    'id' => $dates['clientId'],
			    'name' => $dates['name'], 
			    'department' => $dates['abteilung'],
			    'address' => $dates['anschrift'],
			    'address2' => $dates['abteilung'],
			    'place' => $dates['ort'],
			    'code' => $dates['plz'],
			    'fax' => $dates['fax'],
			    'phone' => $dates['telefon'],
			    'mail' => $dates['mail'],
			    'skonto' => $dates['skonto'],
			    'paymentId' => $dates['payId'],
			    'payment' => $dates['payTarget'],
			);
			echo json_encode($final);
	     } else {
			return 'false';
	     }
	}
	
	public function getClientDetails($id) {
		$sql = 'SELECT Auftraggeber.skonto, Zahlungsziel.name FROM Auftraggeber INNER JOIN Zahlungsziel ON Auftraggeber.zahlungsziel_id = Zahlungsziel.id WHERE Auftraggeber.id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$names = $result->fetch();
			$result = array( 'skonto' => $names['skonto'], 'paymentOpt' => $names['name']);
			echo json_encode($result);
		} else {
			return 'false';
		}
	}
	
	public function getCurrentId() {
		return $this->id;
	}

	public function getDates() {
		$result = array(  'id' => $this->id, 'name' => $this->name, 'abteilung' => $this->abteilung, 'anschrift' => $this->anschrift, 'anschrift2' => $this->anschrift2, 'plz' => $this->plz,
			'ort' => $this->ort, 'telefon' => $this->telefon, 'fax' => $this->fax, 'mail' => $this->mail, 'skonto' => $this->skonto, 
			'zahlungsziel' => $this->zahlungsziel, 'reg_date' => $this->reg_date);
		return $result;
	}
	
	public function getDeliveryAddress($id) {
		$sql = 'SELECT name, anschrift, anschrift2, ort, plz FROM Auftraggeber WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$array = $result->fetch();
			$final = array(
				'name' => $array['name'],
				'address' => $array['anschrift'],
				'address2' => $array['anschrift2'],
				'place' => $array['ort'],
				'code' => $array['plz']
			);
			return $final;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getId() {
		$sql = 'SELECT id, reg_date FROM Auftraggeber WHERE name = :name AND abteilung = :abteilung AND anschrift = :anschrift AND zahlungsziel_id = :zahlungsziel_id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':abteilung', $this->abteilung);
		$result->bindValue(':anschrift', $this->anschrift);
		$result->bindValue(':zahlungsziel_id', $this->zahlungsziel);
		if ($result->execute()) {
			$array = $result->fetch();
			$this->id = $array['id'];
			$this->reg_date = $array['reg_date'];
		} else {
			$this->output->displayPhpError();
		}
	}
	
	public function getName() {
		return $this->name;
	}

	private function getSql() {
		$sql='SELECT Auftraggeber.name as name, abteilung, anschrift, anschrift2, plz, ort, telefon, fax, mail, skonto, zahlungsziel_id, reg_date, Zahlungsziel.name as zahlName FROM Auftraggeber
		INNER JOIN Zahlungsziel ON Auftraggeber.zahlungsziel_id = Zahlungsziel.id 
		WHERE Auftraggeber.id= :id';
		return $sql;
	}
	
	public function getObjectId() {
		return $this->id;
	}

	public function getAnsprechpartner() {
		return $this->ansprechpartner;
	}

	public function getRechnungsadresse() {
		return $this->rechnungsadresse;
	}

	private function selectDates() {
		$sql = $this->getSql();
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$data = $result->fetch();
			return $data;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setDates() {
		$data = $this->selectDates();
		$this->name = $data['name'];
		$this->abteilung = $data['abteilung'];
		$this->anschrift = $data['anschrift'];
		$this->anschrift2 = $data['anschrift2'];
		$this->plz = $data['plz'];
		$this->ort = $data['ort'];
		$this->telefon = $data['telefon'];
		$this->fax = $data['fax'];
		$this->mail = $data['mail'];
		$this->skonto = $data['skonto'];
		$this->zahlungsziel = array('id' => $data['zahlungsziel_id'], 'name' => $data['zahlName']);
		$this->reg_date = $data['reg_date'];
	}

	public function setCustomDates( $array ) {
		$this->name = $array[0];
		$this->abteilung = $array[1];
		$this->anschrift = $array[2];
		$this->anschrift2 = $array[3];
		$this->plz = $array[4];
		$this->ort = $array[5];
		$this->telefon = $array[6];
		$this->fax = $array[7];
		$this->mail = $array[8];
		$this->skonto = $array[9];
		$this->zahlungsziel = $array[10];
		$success = $this->saveCustomDates();
		if ($success == 'success') {
			$this->getId();
			return $this->getObjectId();
		} else {
			return $success;
		}
	}

	public function saveCustomDates() {
		$sql = 'INSERT INTO Auftraggeber (name, abteilung, anschrift, anschrift2, plz, ort, telefon, fax, mail, skonto, zahlungsziel_id, reg_date) 
			VALUES ( :name, :abteilung, :anschrift, :anschrift2, :plz, :ort, :telefon, :fax, :mail, :skonto, :zahlungsziel, NOW() )';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':abteilung', $this->abteilung);
		$result->bindValue(':anschrift', $this->anschrift);
		$result->bindValue(':anschrift2', $this->anschrift2);
		$result->bindValue(':plz', $this->plz);
		$result->bindValue(':ort', $this->ort);
		$result->bindValue(':telefon', $this->telefon);
		$result->bindValue(':fax', $this->fax);
		$result->bindValue(':mail', $this->mail);
		$result->bindValue(':skonto', $this->skonto);
		$result->bindValue(':zahlungsziel', $this->zahlungsziel);
		if ( $result->execute() ){
			return 'success';
		} else {
			return 'false';
		}
	}

	public function searchByName($name) {
		$name = '%'.$name.'%';
		$sql = "SELECT id, name FROM Auftraggeber WHERE name LIKE ?";
		$result = $this->dbHandler->prepare($sql);
		if ($result->execute(array($name))) {
			$sql2 = "SELECT skonto, Zahlungsziel.name as paymentName FROM Auftraggeber INNER JOIN Zahlungsziel ON Auftraggeber.zahlungsziel_id = Zahlungsziel.id 		WHERE Auftraggeber.id = :id";
			foreach ($result as $singleResult) {
				$id = $singleResult['id'];
				$result = $this->dbHandler->prepare($sql2);
				$result->bindValue(':id', $id);
				$result->execute();
				$paymentName = $result->fetch();
				$names[] = array('name' => $singleResult['name'], 'id' => $singleResult['id'], 'payment' => $paymentName['paymentName'], 'skonto' => $paymentName['skonto']);
			}
			if (!isset($names)) {
				$names = 'error';
			}
			return $names;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setAnsprechpartner() {
		$sql = 'SELECT id FROM Ansprechpartner WHERE firma_id= :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$counter = 0;
			foreach ($result as $singleResult) {
				$singlePartner = new Ansprechpartner($this->dbHandler, $singleResult['id']);
				$singlePartner->setDates();
				$this->ansprechpartner[$counter] = $singlePartner;
				$counter++;
			}
		} else {
			$this->output->displayPhpError();
		}
	}

	public function setRechnungsadresse() {
		$sql = 'SELECT id FROM Rechnungsadressen WHERE firma_id= :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
			$counter = 0;
			foreach ($result as $singleResult) {
				$singlePartner = new Rechnungsadresse($this->dbHandler, $singleResult['id']);
				$singlePartner->setDates();
				$this->rechnungsadresse[$counter] = $singlePartner;
				$counter++;
			}
		} else {
			$this->output->displayPhpError();
		}
	}
	
	public function updateRow($column, $rowId, $value) {
		$sql = 'UPDATE Auftraggeber SET '.$column.' = :value WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':value', $value);
		$result->bindValue(':id', $rowId);
		if ($result->execute()) {
		    return 'success';
		} else {
		    return 'false';
		}
	}
}