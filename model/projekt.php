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
	private $liefertermin;
	private $drucksache = array();
	private $fremdsache;
	private $vorstufe;
	private $changeDate;
	private $pattern;
	private $patternTo;
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
		unset($this->liefertermin);
		unset($this->changeDate);
		unset($this->pattern);
		unset($this->patternTo);
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

	public function getVorgangsnummer() {
		return $this->vorgangsnummer;
	}

	private function saveCustomDates($auftraggeberId, $rechnungsadresseId, $ansprechpartnerId, $benutzerId, $mandant_select, $vorgangsnummer, $auftragsnummer, $liefertermin) {
		$now = new DateTime();
		$changeDate = $now->format('Y-m-d');

		$sql = 'INSERT INTO Projekt (name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant, reg_date, mandant_select, vorgangsnummer, auftragsnummer, liefertermin, changeDate, pattern, pattern_to) 
			VALUES ( :name, :kundenauftragsnummer, :auftraggeber, :rechnungsadresse, :ansprechpartner, :benutzer, NOW(), :mandant_select, :vorgangsnummer, :auftragsnummer, :liefertermin, :changeDate, :pattern, :patternTo )';
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
		$result->bindValue(':liefertermin', $liefertermin);
		$result->bindValue(':changeDate', $changeDate);
		$result->bindValue(':pattern', $pattern);
		$result->bindValue(':patternTo', $patternTo);
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
			$implodeSelect = ' WHERE'.implode(" AND",$prequery).' ORDER BY Projekt.reg_date';
			$result = $this->searchByDateSql($implodeSelect);
			return $result;
		}
	}

	private function searchByDateSql ($implodeSelect) {
		$sql = 'SELECT Projekt.id as id, Projekt.liefertermin as liefdate, Projekt.name as proname, Projekt.auftragsnummer as aufnumb FROM Projekt INNER JOIN Auftraggeber ON Projekt.auftraggeber = Auftraggeber.id'.$implodeSelect;
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
		$this->liefertermin = $array[11];
		$this->saveCustomDates($auftraggeberId, $rechnungsadresseId, $ansprechpartnerId, $benutzerId, $this->mandant_select, $this->vorgangsnummer, $this->auftragsnummer, $this->liefertermin);
		$this->id = $this->getLastIdValue();
		$this->setLieferant($array[6], $array[7]);
	}

	public function setDates() {
		$sql='SELECT name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant, reg_date, mandant_select, vorgangsnummer, auftragsnummer, liefertermin, lieferant_id, lieferant_bemerkung, change_date, pattern, pattern_to FROM Projekt 
		WHERE id= :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$id=$result->fetch();
		$this->name = $id['name'];
		$this->kundenauftragsnummer = $id['kundenauftragsnummer'];
		$this->setAuftraggeber($id['auftraggeber']);
		$this->setRechnungsadresse($id['rechnungsadresse']);
		$this->setAnsprechpartner($id['ansprechpartner']);
		$this->setBenutzer($id['mandant']);
		$this->reg_date = $id['reg_date'];
		$this->mandant_select = $id['mandant_select'];
		$this->vorgangsnummer = $id['vorgangsnummer'];
		$this->auftragsnummer = $id['auftragsnummer'];
		$this->liefertermin = $id['liefertermin'];
		$carrier = $id['lieferant_id'];
		$helper = $this->creator->createProduct('helpers');
		$this->lieferant = $helper->setLieferant($carrier);
		$this->lieferant_bemerkung = $id['lieferant_bemerkung'];
		$this->changeDate = $id['change_date'];
		$this->pattern = $id['pattern'];
		$this->patternTo = $id['pattern_to'];
		$vorstufe = $this->creator->createProduct('Vorstufe');
		$this->vorstufe = $vorstufe->getByProjectId($this->id);
		$drucksache = $this->creator->createProduct('Drucksache');
		$this->drucksache = $drucksache->getByProjectId($this->id);
		$fremdsache = $this->creator->createProduct('Fremdsache');
		$this->fremdsache = $fremdsache->getByProjectId($this->id);
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


	private function setRechnungsadresse($id) {
		if (isset($this->rechnungsadresse)) {
			$this->rechnungsadresse = null;
		}
		$rechnungsadresse = $this->creator->createProduct('rechnungsadresse', $id);
		$rechnungsadresse->setDates();
		$this->rechnungsadresse = $rechnungsadresse;
	}
}