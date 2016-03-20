<?php

class Projekt
{
	public $id;
	private $name;
	private $kundenauftragsnummer;
	public $auftraggeber;
	public $rechnungsadresse;
	public $ansprechpartner;
	public $benutzer;
	private $lieferant = array();
	private $lieferant_bemerkung;
	private $reg_date;
	private $mandant_select;
	private $vorgangsnummer;
	private $auftragsnummer;
	private $drucksache = array();
	private $fremdsache;
	private $vorstufe;
	private $changeDate;
	private $pattern;
	private $patternTo;
	private $amendmentTime;
	private $dateTime;
	private $proofTime;
	private $printTime; //Termin Andruck - Drucksachen
	private $showIndPrice; // Einzelpreise nicht auf RE
	private $deliveryTime;
	private $status;
	private $individual_payment;
	private $individual_skonto;

	private $creator;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->creator = new TvsatzCreator($dbHandler);

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function changeAnsprechpartner($id) {
		$valid = $this->checkIfAvailable('Ansprechpartner', $id);
		if ($valid != false) {
			$this->setAnsprechpartner($id);
			$this->saveTwoBinds('ansprechpartner', $id, $this->id);
			$result = 'success';
		} else {
			$result = 'Dieser Ansprechpartner gilt nicht für die gewählte Firma';
		}
		return $result;
	}

	public function changeAuftraggeber($id) {
		$this->setAuftraggeber($id);
		$this->saveTwoBinds('auftraggeber', $id, $this->id);
		$this->ansprechpartner = null;
		$this->rechnungsadresse = null;
	}

	public function changeBenutzer($id) {
		$this->setBenutzer($id);
		$this->saveTwoBinds('mandant', $id, $this->id);
	}

	public function changeRechnungsadresse($id) {
		$valid = $this->checkIfAvailable('Rechnungsadressen', $id);
		if ($valid != false) {
			$this->setRechnungsadresse($id);
			$this->saveTwoBinds('rechnungsadresse', $id, $this->id);
			$result = 'success';
		} else {
			$result = 'Diese Rechnungsadresse gilt nicht für die gewählte Firma';
		}
		return $result;
	}

	private function checkIfAvailable ($table, $id) {
		switch($table) {
			case 'Ansprechpartner':
                $sql = "SELECT * FROM Ansprechpartner WHERE id = :id AND firma_id = :firma_id";
                break;
            case 'Rechnungsadressen':
                $sql = "SELECT * FROM Rechnungsadressen WHERE id = :id AND firma_id = :firma_id";
                break;
		}
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->bindValue(':firma_id', $this->auftraggeber->id);
		$result->execute();
		$success = $result->fetch();
		return $success;
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Projekt WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		unset($this->id);
		unset($this->name);
		unset($this->kundenauftragsnummer);
		unset($this->auftraggeber);
		unset($this->rechnungsadresse);
		unset($this->ansprechpartner);
		unset($this->benutzer);
		unset($this->reg_date);
		unset($this->mandant_select);
		unset($this->vorgangsnummer);
		unset($this->auftragsnummer);
		unset($this->changeDate);
		unset($this->pattern);
		unset($this->patternTo);
		unset($this->amendmentTime);
		unset($this->dateTime);
		unset($this->proofTime);
		unset($this->printTime);
		unset($this->showIndPrice);
		unset($this->status);
		unset($this->deliveryTime);
		unset($this->individual_payment);
		unset($this->individual_skonto);
	}

	public function getAnsprechpartner() {
		return $this->ansprechpartner;
	}

	public function getAuftraggeber() {
		return $this->auftraggeber;
	}

	public function getBenutzer() {
		return $this->benutzer;
	}

	public function getChangeDate() {
		return $this->changeDate;
	}

	public function getDeliveryTime() {
		return $this->deliveryTime;
	}

	public function getDrucksachen() {
		$result = array($this->drucksache, $this->printTime, $this->showIndPrice);
		return $result;
	}

	public function getFremdsache() {
		$result = array($this->fremdsache, $this->amendmentTime, $this->dateTime);
		return $result;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getIndividuals() {
		$array = array('payment' => $this->individual_payment, 'skonto' => $this->individual_skonto);
		return $array;
	}

	public function getKundenautragsnummer() {
		return $this->kundenauftragsnummer;
	}

	private function getLastIdValue() {
		$sql = 'SELECT id FROM Projekt ORDER BY id DESC LIMIT 1';
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$id = $result->fetch();
		$id = $id['id'];
		return $id;
	}

	public function getLieferant() {
		return $this->lieferant;
	}

	public function getMandantSelect() {
		return $this->mandant_select;
	}

	public function getName() {
		return $this->name;
	}

	public function getPatterns() {
		$patterns = array('pattern' => $this->pattern, 'patternTo' => $this->patternTo);
		return $patterns;
	}

	public function getRechnungsadresse() {
		return $this->rechnungsadresse;
	}

	public function getRegDate() {
		return $this->reg_date;
	}

	public function getShowIndPrice() {
		return $this->showIndPrice;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getVorgangsnummer() {
		return $this->vorgangsnummer;
	}

	public function getVorstufe() {
		$result = array($this->vorstufe, $this->amendmentTime, $this->dateTime, $this->proofTime);
		return $result;
	}

	private function saveCustomDates($auftraggeberId, $rechnungsadresseId, $ansprechpartnerId, $benutzerId, $mandant_select, $vorgangsnummer, $auftragsnummer, $changeDate, 
		$pattern, $patternTo, $amendmentTime, $dateTime, $proofTime, $printTime, $showIndPrice, $status, $deliveryTime) {
		$now = new DateTime();
		$changeDate = $now->format('Y-m-d');

		$sql = 'INSERT INTO Projekt (name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant, reg_date, mandant_select, vorgangsnummer, auftragsnummer, changeDate, pattern, pattern_to, amendmentTime, dateTime, proofTime, printTime, showIndPrice, status, deliveryTime, individual_payment, individual_skonto) 
			VALUES ( :name, :kundenauftragsnummer, :auftraggeber, :rechnungsadresse, :ansprechpartner, :benutzer, NOW(), :mandant_select, :vorgangsnummer, :auftragsnummer, :changeDate, :pattern, :patternTo, :amendmentTime, :dateTime, :proofTime, :printTime, :showIndPrice, :status, :deliveryTime, :individual_payment, individual_skonto )';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':kundenauftragsnummer', $this->kundenauftragsnummer);
		$result->bindValue(':auftraggeber', $auftraggeberId);
		$result->bindValue(':rechnungsadresse', $rechnungsadresseId);
		$result->bindValue(':ansprechpartner', $ansprechpartnerId);
		$result->bindValue(':benutzer', $benutzerId);
		$result->bindValue(':mandant_select', $mandant_select);
		$result->bindValue(':vorgangsnummer', $vorgangsnummer);
		$result->bindValue(':auftragsnummer', $auftragsnummer);
		$result->bindValue(':changeDate', $changeDate);
		$result->bindValue(':pattern', $pattern);
		$result->bindValue(':patternTo', $patternTo);
		$result->bindValue(':amendmentTime', $amendmentTime);
		$result->bindValue(':dateTime', $dateTime);
		$result->bindValue(':proofTime', $proofTime);
		$result->bindValue(':printTime', $printTime);
		$result->bindValue(':showIndPrice', $showIndPrice);
		$result->bindValue(':status', $status);
		$result->bindValue(':deliveryTime', $deliveryTime);
		$result->bindValue(':individual_payment', $individual_payment);
		$result->bindValue(':individual_skonto', $individual_skonto);
		$result->execute();
	}

	private function saveTwoBinds($column, $value) {
		$sql = "UPDATE Projekt SET $column = :value WHERE id = :projektId";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':value', $value);
		$result->bindValue(':projektId', $this->id);
		$result->execute();
	}

	public function searchByDates($params) {
		$begin = explode('/', $params[0]['begin']);
		if ($begin[0] != '') {
			$begin = array_reverse($begin);
			$begin = implode('-', $begin);
			$params[0]['begin'] = $begin.' 00:00:00';
		} else {
			$params[0]['begin'] = '';
		}
		$end = explode('/', $params[0]['endDate']);
		if ($end[0] != '') {
			$end = array_reverse($end);
			$end = implode('-', $end);
			$params[0]['endDate'] = $end.' 23:59:59';
		} else {
			$params[0]['endDate'] = '';
		}
		foreach ($params as $result){
			if ($result['begin'] == "") {
				unset($result['begin']);
			} else {
				$prequery[] = " Projekt.reg_date >= '".$result['begin']."'";
			}
			if ($result['endDate'] == "") {
				unset($result['endDate']);
			} else {
				$prequery[] = " Projekt.reg_date <= '".$result['endDate']."'";
			}
			if ($result['projectName'] == "") {
				unset($result['projectName']);
			} else {
				$prequery[] = " Projekt.name LIKE '"."%".$result['projectName']."%'";
			}
			if ($result['clientName'] == "") {
				unset($result['clientName']);
			} else {
				$prequery[] = " Auftraggeber.name LIKE '"."%".$result['clientName']."%'";
			}
			if ($result['eventNumber'] == "") {
				unset($result['eventNumber']);
			} else {
				$prequery[] = " Projekt.vorgangsnummer LIKE '"."%".$result['eventNumber']."%'";
			}
			if ($result['clientOrderNumber'] == "") {
				unset($result['clientOrderNumber']);
			} else {
				$prequery[] = " Projekt.auftragsnummer LIKE '"."%".$result['clientOrderNumber']."%'";
			}
			if ($result['mandant'] == "") {
				unset($result['mandant']);
			} else {
				$prequery[] = " Projekt.mandant_select ='".$result['mandant']."'";
			}
			if ($result['status'] == "none") {
				unset($result['status']);
			} else {
				$status = $result['status'];
				$prequery[] = " status = $status";
			}
			$implodeSelect = ' WHERE'.implode(" AND",$prequery).' ORDER BY Projekt.reg_date';
			$result = $this->searchByDateSql($implodeSelect);
			return $result;
		}
	}

	private function searchByDateSql ($implodeSelect) {
		$sql = 'SELECT Projekt.id as id, Projekt.deliveryTime as liefdate, Projekt.name as proname, Projekt.auftragsnummer as aufnumb FROM Projekt INNER JOIN Auftraggeber ON Projekt.auftraggeber = Auftraggeber.id'.$implodeSelect;
		$result=$this->dbHandler->prepare($sql);
		$result->execute();
		$this->setBenutzer($_SESSION['user']);
		$this->benutzer->setIsLogged();
		$this->benutzer->saveLastSql($sql);
		$searchResult = array();
		foreach ($result as $singleResult) {
			$searchResult[] = array( 'id' => $singleResult['id'], 'liefertermin' => $singleResult['liefdate'], 'name' => $singleResult['proname'], 'number' => $singleResult['aufnumb']);
		}
		return $searchResult;
	}

	private function setAnsprechpartner($id) {
		if (isset($this->ansprechpartner)) {
			$this->ansprechpartner = null;
		}
		$ansprechpartner = $this->creator->createProduct('ansprechpartner', $id);
		$ansprechpartner->setDates();
		$this->ansprechpartner = $ansprechpartner;
	}

	private function setAuftraggeber($id) {
		if (isset($this->auftraggeber)) {
			$this->auftraggeber = null;
		}
		$auftraggeber = $this->creator->createProduct('auftraggeber', $id);
		$auftraggeber->setDates();
		$auftraggeber->setAnsprechpartner();
		$auftraggeber->setRechnungsadresse();
		$this->auftraggeber = $auftraggeber;
	}

	private function setBenutzer($id) {
		if (isset($this->benutzer)) {
			$this->benutzer = null;
		}
		$benutzer = $this->creator->createProduct('benutzer', $id);
		$benutzer->setData();
		$this->benutzer = $benutzer;
	}

	public function setCustomDates( $array ) {
		$this->name = $array[0];
		$this->kundenauftragsnummer = $array[1];
		$auftraggeberId = $array[2];
		$this->setAuftraggeber($auftraggeberId);
		$rechnungsadresseId = $array[3];
		$this->setRechnungsadresse($rechnungsadresseId);
		$ansprechpartnerId = $array[4];
		$this->setAnsprechpartner($ansprechpartnerId);
		$benutzerId = $array[5];
		$this->setBenutzer($benutzerId);
		$this->mandant_select = $array[8];
		$this->vorgangsnummer = $array[9];
		$this->auftragsnummer = $array[10];
		$this->pattern = $array[11];
		$this->patternTo = $array[12];
		$this->amendmentTime = $array[13];
		$this->dateTime = $array[14];
		$this->proofTime = $array[15];
		$this->printTIme = $array[16];
		$this->showIndPrice = $array[17];
		$this->status = $array[18];
		$this->deliveryTime = $array[19];
		$this->individual_payment = $array[20];
		$this->individual_skonto = $array[21];
		$this->saveCustomDates($auftraggeberId, $rechnungsadresseId, $ansprechpartnerId, $benutzerId, $this->mandant_select, $this->vorgangsnummer, $this->auftragsnummer);
		$this->id = $this->getLastIdValue();
		$this->setLieferant($array[6], $array[7]);
	}

	public function setDates() {
		$sql='SELECT name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant, reg_date, mandant_select, vorgangsnummer, auftragsnummer, lieferant_id, lieferant_bemerkung, change_date, pattern, pattern_to, amendmentTime, dateTime, proofTime, printTime, showIndPrice, status, deliveryTime, individual_payment, individual_skonto FROM Projekt WHERE id= :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$dates=$result->fetch();
		$this->name = $dates['name'];
		$this->kundenauftragsnummer = $dates['kundenauftragsnummer'];
		$this->setAuftraggeber($dates['auftraggeber']);
		$this->setRechnungsadresse($dates['rechnungsadresse']);
		$this->setAnsprechpartner($dates['ansprechpartner']);
		$this->setBenutzer($dates['mandant']);
		$this->reg_date = $dates['reg_date'];
		$this->mandant_select = $dates['mandant_select'];
		$this->vorgangsnummer = $dates['vorgangsnummer'];
		$this->auftragsnummer = $dates['auftragsnummer'];
		$carrier = $dates['lieferant_id'];
		$helper = $this->creator->createProduct('helpers');
		$this->lieferant = $helper->setLieferant($carrier);
		$this->lieferant_bemerkung = $dates['lieferant_bemerkung'];
		$this->changeDate = $dates['change_date'];
		$this->pattern = $dates['pattern'];
		$this->patternTo = $dates['pattern_to'];
		$amendmentTime = explode("-", $dates['amendmentTime']);
		$this->amendmentTime = $amendmentTime[2].'/'.$amendmentTime[1].'/'.$amendmentTime[0];
		$dateTime = explode("-", $dates['dateTime']);
		$this->dateTime = $dateTime[2].'/'.$dateTime[1].'/'.$dateTime[0];
		$proofTime = explode("-", $dates['proofTime']);
		$this->proofTime = $proofTime[2].'/'.$proofTime[1].'/'.$proofTime[0];
		$printTime = explode("-", $dates['printTime']);
		$this->printTime = $printTime[2].'/'.$printTime[1].'/'.$printTime[0];
		$this->showIndPrice = $dates['showIndPrice'];
		$vorstufe = $this->creator->createProduct('Vorstufe');
		$this->vorstufe = $vorstufe->getByProjectId($this->id);
		$drucksache = $this->creator->createProduct('Drucksache');
		$this->drucksache = $drucksache->getByProjectId($this->id);
		$fremdsache = $this->creator->createProduct('Fremdsache');
		$this->fremdsache = $fremdsache->getByProjectId($this->id);
		$this->status = $helper->getSingleStatus($dates['status']);
		$this->deliveryTime = $dates['deliveryTime'];
		$this->individual_payment = $dates['individual_payment'];
		$this->individual_skonto = $dates['individual_skonto'];
	}

	public function setLieferant($id, $bemerkung = null) {
		$lieferant = $this->creator->createProduct('lieferant', $id);
		$this->saveTwoBinds('lieferant_id', $id);
		$this->lieferant = $id;
		if (isset ($bemerkung)) {
			$this->saveTwoBinds('lieferant_bemerkung', $bemerkung);
			$this->lieferant_bemerkung = $bemerkung;
		}
	}
	/*
	public function setMandantSelect($id, $value) {
	      $sql = "UPDATE Projekt SET mandant_select = :value WHERE id = :id";
	      $result=$this->dbHandler->prepare($sql);
	      $result->bindValue(':id', $id);
	      $result->bindValue(':value', $value);
	      if ( $result->execute() ) {
		return 'done';
	    } else {
		return 'failure';
	    }
	}
	*/
	private function setRechnungsadresse($id) {
		if (isset($this->rechnungsadresse)) {
			$this->rechnungsadresse = null;
		}	
		$rechnungsadresse = $this->creator->createProduct('rechnungsadresse', $id);
		$rechnungsadresse->setDates();
		$this->rechnungsadresse = $rechnungsadresse;
	}
	/*
	public function setStatus($id, $value) {
	    $sql = "UPDATE Projekt SET status = :value WHERE id = :id";
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':id', $id);
	    $result->bindValue(':value', $value);
	    if ( $result->execute() ) {
		return 'done';
	    } else {
		return 'failure';
	    }
	}
	*/
	public function updateDate($id, $column, $value) {
	    if ($value == "null") {
		 $sql = "UPDATE Projekt SET ".$column." = $value WHERE id = :id";
	    } else {
		 $sql = "UPDATE Projekt SET ".$column." = :value WHERE id = :id";
	    }
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':id', $id);
	    if ($value != "null") {
		$result->bindValue(':value', $value);
	    }
	    if ( $result->execute() ) {
		return 'done';
	    } else {
		return 'failure';
	    }
	}
}