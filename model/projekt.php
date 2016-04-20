<?php

class Projekt
{
	public $id;
	private $name;
	private $kundenauftragsnummer;
	public $auftraggeber;
	public $rechnungsadresse;
	public $ansprechpartner;
	public $bemerkung;
	public $benutzer;
	public $kalkulation;
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
	private $lieferschein_text;
	private $lieferadresse_ab;
	private $lieferanweisung;
	private $abweichend;
	private $auflage1;
	private $auflage2;
	private $auflage3;
	private $auflage4;
	private $descToPrint1;
	private $descToPrint2;
	private $descToPrint3;
	private $descToPrint4;
	private $descToPrint5;
	private $output;
	public $error;

	private $creator;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		$this->creator = new TvsatzCreator($dbHandler);
		$this->output = new OutputController($dbHandler);

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
		if ($result->execute()) {
			$success = $result->fetch();
			return $success;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function cloneProject() {
		$previousId = $this->id;
		$path = explode('/',$_SERVER["REQUEST_URI"]);
		$finalPath = '/'.$path[1].'/Erfassung/'.$this->id;
		$client = $this->auftraggeber->getObjectId();
		$clientEmployee = $this->ansprechpartner->getObjectId();
		$address = $this->rechnungsadresse->getObjectId();
		$success = $this->insertProject($client, $clientEmployee, $address);
		if ($success == true) {
			$this->bemerkung = $this->setBemerkung($previousId);
			$this->kalkulation = $this->setKalkulation($previousId);
			$tableSuccess = $this->insertTables();
			if ($tableSuccess == true) {
				$path = explode('/',$_SERVER["REQUEST_URI"]);
				$finalPath = '/'.$path[1].'/Erfassung/'.$this->id;
				$_SESSION['projectId'] = $this->id;
				header( 'Location:'.$finalPath );
			} else {
				$this->output->displayPhpError();
			}
		} else {
			$this->output->displayPhpError();
		}
	}

	public function deleteCurrentDates() {
		$sql = "DELETE FROM Projekt WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
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
			unset($this->lieferschein_text);
			unset($this->lieferadresse_ab);
			unset($this->lieferanweisung);
			unset($this->abweichend);
			unset($this->auflage1);
			unset($this->auflage2); 
			unset($this->auflage3);
			unset($this->auflage4);
		} else {
			$this->output->displayPhpError();
		}
	}
	
	public function deleteDescToPrint($id, $column) {
		$sql = "UPDATE Projekt SET ".$column." = '' WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	public function getAnsprechpartner() {
		return $this->ansprechpartner;
	}

	public function getAuftraggeber() {
		return $this->auftraggeber;
	}

	public function getBemerkung() {
		return $this->bemerkung;
	}

	public function getBenutzer() {
		return $this->benutzer;
	}
	
	public function getCalculationTitles() {
	      $title = array($this->auflage1, $this->auflage2, $this->auflage3, $this->auflage4);
	      return $title;
	}

	public function getChangeDate() {
		return $this->changeDate;
	}
	
	public function getDatesProject() {
		$array = array(
		'amendmentTime' => $this->amendmentTime,
		'dateTime' => $this->dateTime,
		'proofTime' => $this->proofTime,
		'printTime' => $this->printTime
		);
		return $array;
	}
	
	public function getDeliverySql($id) {
		$sql = 'SELECT abweichend, lieferschein_text, lieferadresse_ab, lieferanweisung, auftraggeber, auftragsnummer, kundenauftragsnummer, name FROM Projekt WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$array = $result->fetch();
			$final = array(
				'ifCustom' => $array['abweichend'], 
				'deliveryText' => $array['lieferschein_text'], 
				'customAddress' => $array['lieferadresse_ab'], 
				'deliveryDesc' => $array['lieferanweisung'], 
				'customerId' => $array['auftraggeber'],
				'orderNr' => $array['auftragsnummer'],
				'clientNumber' => $array['kundenauftragsnummer'],
				'name' => $array['name']
			);
			return $final;
		} else {
			$this->output->displayPhpError();
		}
	}

	public function getDeliveryTime() {
		return $this->deliveryTime;
	}

	public function getDeliveryConditions() {
		$delivery = array( 
			'text' => $this->lieferschein_text, 
			'ifOtherAddress' => $this->abweichend, 
			'otherAddress' => $this->lieferadresse_ab, 
			'notes' => $this->lieferanweisung
		);
		return $delivery;
	}
	
	public function getDescToPrint($id, $column) {
		$sql = 'SELECT '.$column.' FROM Projekt WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
		     $final = $result->fetch();
		     return $final[$column];
		} else {
		     return 'false';
		}
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
	
	public function getKalkulation() {
		return $this->kalkulation;
	}

	public function getKundenautragsnummer() {
		return $this->kundenauftragsnummer;
	}

	private function getLastIdValue() {
		$sql = 'SELECT id FROM Projekt ORDER BY id DESC LIMIT 1';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$id = $result->fetch();
			$id = $id['id'];
			return $id;
		} else {
			$this->output->displayPhpError();
		}
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
	
	public function getPrintDesc() {
	    $array = array(
		$this->descToPrint1, 
		$this->descToPrint2, 
		$this->descToPrint3, 
		$this->descToPrint4, 
		$this->descToPrint1
	    );
	    return $array;
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
	
	public function insertNewProject() {
		if(!isset($_POST['mandant'])) {
			$_POST['mandant'] = null;
		}
		if(isset($_POST['projectStatus']) && $_POST['projectStatus'] == 'none') {
			$_POST['projectStatus'] = 1;
		}
		if($_POST['lieferterminInput'] == '') {
			$_POST['lieferterminInput'] = null;
		} else {
		    $temp = explode('/', $_POST['lieferterminInput']);
		    $_POST['lieferterminInput'] = $temp[2].'-'.$temp[1].'-'.$temp[0];
		}
		if(!isset($_POST['individual_payment'])) {
			if(!isset($_POST["individual_paymentOpt"])) {
				$_POST['individual_payment'] = null;
			} else {
				$_POST['individual_payment'] = $_POST["individual_paymentOpt"];
			}
		}
		if(!isset($_POST['individual_skonto'])) {
			$_POST['individual_skonto'] = null;
		}
		if(!isset($_POST['pattern'])) {
			$_POST['pattern'] = null;
		}
		if(!isset($_POST['pattern_to'])) {
			$_POST['pattern_to'] = null;
		}
		if(!isset($_POST['lieferant_id'])) {
			$_POST['lieferant_id'] = null;
		}
		if(!isset($_POST['desc1_an'])) {
			$_POST['desc1_an'] = 0;
		} elseif ($_POST['desc1_an'] == 'on') {
			$_POST['desc1_an'] = 1;
		}
		if(!isset($_POST['desc1_au'])) {
			$_POST['desc1_au'] = 0;
		} elseif ($_POST['desc1_au'] == 'on') {
			$_POST['desc1_au'] = 1;
		}
		if(!isset($_POST['desc1_pm'])) {
			$_POST['desc1_pm'] = 0;
		} elseif ($_POST['desc1_pm'] == 'on') {
			$_POST['desc1_pm'] = 1;
		}
		if(!isset($_POST['desc1_re'])) {
			$_POST['desc1_re'] = 0;
		} elseif ($_POST['desc1_re'] == 'on') {
			$_POST['desc1_re'] = 1;
		}
		if(!isset($_POST['desc1_li'])) {
			$_POST['desc1_li'] = 0;
		} elseif ($_POST['desc1_li'] == 'on') {
			$_POST['desc1_li'] = 1;
		}
		if(!isset($_POST['desc1'])) {
			$_POST['desc1'] = null;
		}
		if(!isset($_POST['desc1'])) {
			$_POST['desc5'] = null;
		}
	    $sql = 'INSERT INTO Projekt (name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant_select, status, deliveryTime, individual_payment, individual_skonto, pattern, pattern_to, lieferant_id) 
			VALUES
	      	(:name, :kundenauftragsnummer, :auftraggeber, :rechnungsadresse, :ansprechpartner, :mandant, :projectStatus, :lieferterminInput, :individual_payment, :individual_skonto, :pattern, :pattern_to, :lieferant_id)';
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':name', $_POST['projektname']);
	    $result->bindValue(':kundenauftragsnummer', $_POST['kundenauftragsnummer']);
	    $result->bindValue(':auftraggeber', $_POST['clientId']);
	    $result->bindValue(':rechnungsadresse', $_POST['personId']);
	    $result->bindValue(':ansprechpartner', $_POST['addressId']);
	    $result->bindValue(':mandant', $_POST['mandant']);
	    $result->bindValue(':projectStatus', $_POST['projectStatus']);
	    $result->bindValue(':lieferterminInput', $_POST['lieferterminInput']);
	    $result->bindValue(':individual_payment', $_POST['individual_payment']);
	    $result->bindValue(':individual_skonto', $_POST['individual_skonto']);
	    $result->bindValue(':pattern', $_POST['pattern']);
	    $result->bindValue(':pattern_to', $_POST['pattern_to']);
	    $result->bindValue(':lieferant_id', $_POST['lieferant_id']);
	    if ($result->execute()) {
			$this->id = $this->getLastIdValue();
			$array = array($this->id, $_POST['desc1'], $_POST['desc5'], $_POST['desc1_an'], $_POST['desc1_au'], $_POST['desc1_pm'], $_POST['desc1_li'], $_POST['desc1_re']);
			$bemerkung = $this->creator->createProduct('bemerkung');
			$result = $bemerkung->createNewList($array);
			if ($result == false) {
				$this->error = 'Project created but bemerkung list is unavailable.';
				$this->output->displayPhpError($this->error);
			} else {
				$calculation = $this->creator->createProduct('calculation');
				$result = $calculation->createEmptyList($this->id);
				if ($result == false) {
					$this->error = 'Project created but no calculation list available.';
					$this->output->displayPhpError($this->error);
				}
			}
			$path = explode('/',$_SERVER["REQUEST_URI"]);
			$finalPath = '/'.$path[1].'/Erfassung/'.$this->id;
			$_SESSION['projectId'] = $this->id;
			header( 'Location:'.$finalPath );
		} else {
			$this->error = 'No project created.';
			$this->output->displayPhpError($this->error);
		}
	}

	private function insertProject($client, $employee, $address) {
		$amendment = explode('/', $this->amendmentTime); 
		$amendmentTime = $amendment[2].'-'.$amendment[1].'-'.$amendment[0];
		$dateT = explode('/', $this->dateTime); 
		$dateTime = $dateT[2].'-'.$dateT[1].'-'.$dateT[0];
		$proof = explode('/', $this->proofTime); 
		$proofTime = $proof[2].'-'.$proof[1].'-'.$proof[0];
		$print = explode('/', $this->printTime); 
		$printTime = $print[2].'-'.$print[1].'-'.$print[0];
		$sql = 'INSERT INTO Projekt (name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, reg_date, lieferant_id, lieferant_bemerkung, mandant_select, vorgangsnummer, auftragsnummer, 
			pattern, pattern_to, amendmentTime, dateTime, proofTime, printTime, showIndPrice, status, deliveryTime, individual_payment, individual_skonto, lieferschein_text, lieferadresse_ab, lieferanweisung, abweichend, auflage1, 
			auflage2, auflage3, auflage4 ) 
			VALUES ( :name, :kundenauftragsnummer, :auftraggeber, :rechnungsadresse, :ansprechpartner, NOW(), :lieferant_id, :lieferant_bemerkung, :mandant_select, :vorgangsnummer, :auftragsnummer, 
			:pattern, :patternTo, :amendmentTime, :dateTime, :proofTime, :printTime, :showIndPrice, :status, :deliveryTime, :individual_payment, :individual_skonto, :lieferschein_text, :lieferadresse_ab, :lieferanweisung, :abweichend, :auflage1, 
			:auflage2, :auflage3, :auflage4 )';
		$lieferant = $this->lieferant[0]['id'];
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':kundenauftragsnummer', $this->kundenauftragsnummer);
		$result->bindValue(':auftraggeber', $client);
		$result->bindValue(':rechnungsadresse', $address);
		$result->bindValue(':ansprechpartner', $employee);
		$result->bindValue(':lieferant_id', $lieferant);
		$result->bindValue(':lieferant_bemerkung', $this->lieferant_bemerkung);
		$result->bindValue(':mandant_select', $this->mandant_select);
		$result->bindValue(':vorgangsnummer', $this->vorgangsnummer);
		$result->bindValue(':auftragsnummer', $this->auftragsnummer);
		$result->bindValue(':pattern', $this->pattern);
		$result->bindValue(':patternTo', $this->patternTo);
		$result->bindValue(':amendmentTime', $amendmentTime);
		$result->bindValue(':dateTime', $dateTime);
		$result->bindValue(':proofTime', $proofTime);
		$result->bindValue(':printTime', $printTime);
		$result->bindValue(':showIndPrice', $this->showIndPrice);
		$result->bindValue(':status', $this->status['id']);
		$result->bindValue(':deliveryTime', $this->deliveryTime);
		$result->bindValue(':individual_payment', $this->individual_payment);
		$result->bindValue(':individual_skonto', $this->individual_skonto);
		$result->bindValue(':lieferschein_text', $this->lieferschein_text);
		$result->bindValue(':lieferadresse_ab', $this->lieferadresse_ab);
		$result->bindValue(':lieferanweisung', $this->lieferanweisung);
		$result->bindValue(':abweichend', $this->abweichend);
		$result->bindValue(':auflage1', $this->auflage1);
		$result->bindValue(':auflage2', $this->auflage2);
		$result->bindValue(':auflage3', $this->auflage3);
		$result->bindValue(':auflage4', $this->auflage4);
		
		if ($result->execute()) {
			$this->id = $this->getLastIdValue();
			return success;
		} else {
			$this->error = 'No project cloned.';
			$this->output->displayPhpError($this->error);
		}
	}
	
	private function insertTables() {
		if ( isset( $this->vorstufe )) {
			$vorstufe = $this->creator->createProduct('Vorstufe');
			foreach ($this->vorstufe as $singleVorstufe) {
				$success = $vorstufe->insert($singleVorstufe, $this->id);
				if ($success == false) {
					$this->error = 'Vorstufe table not cloned.';
					$this->output->displayPhpError($this->error);
				}
			}
		}
		if ( isset( $this->drucksache )) {
			$drucksache = $this->creator->createProduct('Drucksache');
			foreach ($this->drucksache as $singleDrucksache) {
				$success = $drucksache->insert($singleDrucksache, $this->id);
				if ($success == false) {
					$this->error = 'Drucksache table not cloned.';
					$this->output->displayPhpError($this->error);
				}
			}
		}
		if ( isset( $this->fremdsache )) {
			$fremdsache = $this->creator->createProduct('Fremdsache');
			foreach ($this->fremdsache as $singleFremdsache) {
				$success = $fremdsache->insert($singleFremdsache, $this->id);
				if ($success == false) {
					$this->error = 'Fremdarbeiten table not cloned.';
					$this->output->displayPhpError($this->error);
				}
			}
		}
		if ( isset( $this->bemerkung )) {
			$bemerkung = $this->creator->createProduct('bemerkung');
			$success = $bemerkung->insert($this->bemerkung, $this->id);
			if ($success == false) {
				$this->error = 'No bemerkung cloned to the project.';
				$this->output->displayPhpError($this->error);
			}
		}
		if ( isset( $this->kalkulation )) {
			$calculation = $this->creator->createProduct('calculation');
			$success = $calculation->insert($this->kalkulation, $this->id);
			if ($success == false) {
				$this->error = 'No calculation list cloned to the project.';
				$this->output->displayPhpError($this->error);
			}
		}
		if (!isset($this->error)) {
			return true;
		} else {
			exit();
		}
	}

	private function saveCustomDates($auftraggeberId, $rechnungsadresseId, $ansprechpartnerId, $benutzerId, $mandant_select, $vorgangsnummer, 		$auftragsnummer, $changeDate, $pattern, $patternTo, $amendmentTime, $dateTime, $proofTime, $printTime, $showIndPrice, $status, 			$deliveryTime, $lieferschein_text, $lieferadresse_ab, $lieferanweisung, $abweichend, $auflage1, $auflage2, $auflage3, $auflage4) {
		$now = new DateTime();
		$changeDate = $now->format('Y-m-d');

		$sql = 'INSERT INTO Projekt (name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant, reg_date, 			lieferant_id, lieferant_bemerkung, mandant_select, vorgangsnummer, 
			auftragsnummer, changeDate, pattern, pattern_to, amendmentTime, dateTime, proofTime, printTime, showIndPrice, status, deliveryTime, individual_payment, individual_skonto, lieferschein_text, lieferadresse_ab, lieferanweisung, abweichend, 
			auflage1, auflage2, auflage3, auflage4 ) 
			VALUES ( :name, :kundenauftragsnummer, :auftraggeber, :rechnungsadresse, :ansprechpartner, :benutzer, NOW(), :mandant_select, :vorgangsnummer, :auftragsnummer, :changeDate, :pattern, :patternTo, :amendmentTime, :dateTime, :proofTime, :printTime, :showIndPrice, :status, :deliveryTime, :individual_payment, individual_skonto, :individual_payment, :individual_skonto, :lieferschein_text, :lieferadresse_ab, :lieferanweisung, :abweichend, :auflage1, :auflage2, :auflage3, auflage4 )';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':name', $this->name);
		$result->bindValue(':kundenauftragsnummer', $this->kundenauftragsnummer);
		$result->bindValue(':auftraggeber', $auftraggeberId);
		$result->bindValue(':rechnungsadresse', $rechnungsadresseId);
		$result->bindValue(':ansprechpartner', $ansprechpartnerId);
		$result->bindValue(':benutzer', $benutzerId);
		$result->bindValue(':mandant_select', $mandant_select);
		$result->bindValue(':lieferant_id', $lieferant_id);
		$result->bindValue(':lieferant_bemerkung', $lieferant_bemerkung);
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
		$result->bindValue(':lieferschein_text', $lieferschein_text);
		$result->bindValue(':lieferadresse_ab', $lieferadresse_ab);
		$result->bindValue(':lieferanweisung', $lieferanweisung);
		$result->bindValue(':abweichend', $abweichend);
		$result->bindValue(':auflage1', $auflage1);
		$result->bindValue(':auflage2', $auflage2);
		$result->bindValue(':auflage3', $auflage3);
		$result->bindValue(':auflage4', $auflage4);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	private function saveTwoBinds($column, $value) {
		$sql = "UPDATE Projekt SET $column = :value WHERE id = :projektId";
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':value', $value);
		$result->bindValue(':projektId', $this->id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}

	public function searchByDates($params) {
		$benutzer = $this->creator->createProduct('benutzer');
		$benutzer->saveLastSearch($params[0]);
		$conditions = $benutzer->getLastSearch($params[0]['status']);
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
		if ($result->execute()) {
			$this->setBenutzer($_SESSION['user']);
			$this->benutzer->setIsLogged();
			$this->benutzer->saveLastSql($sql);
			$searchResult = array();
			foreach ($result as $singleResult) {
				$searchResult[] = array( 'id' => $singleResult['id'], 'liefertermin' => $singleResult['liefdate'], 'name' => $singleResult['proname'], 'number' => $singleResult['aufnumb']);
			}
			return $searchResult;
		} else {
			$this->output->displayPhpError();
		}
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

	public function setBemerkung($id = null) {
		$bemerkung = $this->creator->createProduct('bemerkung');
		if (!isset($id)) {
		    $id = $this->id;
		}
		$this->bemerkung = $bemerkung->getBemerkungList($id);
		$final = $this->getBemerkung();
		return $final;
	}
	
	public function setKalkulation($id = null) {
		$calculation = $this->creator->createProduct('calculation');
		if (!isset($id)) {
		    $id = $this->id;
		}
		$this->kalkulation = $calculation->getDates($id);
		$final = $this->getKalkulation();
		return $final;
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
		$this->saveCustomDates($auftraggeberId, $rechnungsadresseId, $ansprechpartnerId, $benutzerId, $this->mandant_select, 
		$this->vorgangsnummer, $this->auftragsnummer);
		$this->id = $this->getLastIdValue();
		$this->setLieferant($array[6], $array[7]);
		$this->lieferschein_text = $array[22];
		$this->lieferadresse_ab = $array[23];
		$this->lieferanweisung = $array[24];
		$this->abweichend = $array[25];
		$this->auflage1 = $array[26];
		$this->auflage2 = $array[27];
		$this->auflage3 = $array[28];
		$this->auflage4 = $array[29];
	}

	public function setDates() {
		$sql='SELECT name, kundenauftragsnummer, auftraggeber, rechnungsadresse, ansprechpartner, mandant, 	reg_date, mandant_select, vorgangsnummer, auftragsnummer, lieferant_id, lieferant_bemerkung, change_date, pattern, pattern_to, amendmentTime, dateTime, proofTime, printTime, showIndPrice, status, deliveryTime, individual_payment, individual_skonto, lieferschein_text, lieferadresse_ab, lieferanweisung, abweichend, auflage1, auflage2, auflage3, auflage4, descToPrint1, descToPrint2, descToPrint3, 
		descToPrint4, descToPrint5 FROM Projekt WHERE id= :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		if ($result->execute()) {
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
			$this->lieferschein_text = $dates['lieferschein_text'];
			$this->lieferadresse_ab = $dates['lieferadresse_ab'];
			$this->lieferanweisung = $dates['lieferanweisung'];
			$this->abweichend = $dates['abweichend'];
			$this->auflage1 = $dates['auflage1'];
			$this->auflage2 = $dates['auflage2'];
			$this->auflage3 = $dates['auflage3'];
			$this->auflage4 = $dates['auflage4'];
			$this->descToPrint1 = $dates['descToPrint1'];
			$this->descToPrint2 = $dates['descToPrint2'];
			$this->descToPrint3 = $dates['descToPrint3'];
			$this->descToPrint4 = $dates['descToPrint4'];
			$this->descToPrint5 = $dates['descToPrint5'];
		} else {
			$this->output->displayPhpError();
		}
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

	public function updateAuftragsnummer($projectId, $number) {
		$sql = "UPDATE Projekt SET auftragsnummer = :projNumber WHERE id = :projectId";
		$result = $this->dbHandler->prepare($sql);
	    $result->bindValue(':projNumber', $number);
	    $result->bindValue(':projectId', $projectId);
	    if ( $result->execute() ) {
			return 'success';
	    } else {
			return 'failure';
	    }
	}

	public function updateDate($id, $column, $value) {
	    $sql = "UPDATE Projekt SET ".$column." = :value WHERE id = :id";
	    if ($value == "") {
		 $sql = "UPDATE Projekt SET ".$column." = NULL WHERE id = ".$id;
	    } else {
		 $sql = "UPDATE Projekt SET ".$column." = :value WHERE id = :id";
	    }
	    $result = $this->dbHandler->prepare($sql);
	    $result->bindValue(':id', $id);
	    if ($value != "null") {
		$result->bindValue(':value', $value);
	    }
	    if ( $result->execute() ) {
		return 'success';
	    } else {
		return 'failure';
	    }
	}
	
	public function updateDateChange($id) {
		$sql = "UPDATE Projekt SET change_date = NOW() WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if (!$result->execute()) {
			$this->output->displayPhpError();
		}
	}
}