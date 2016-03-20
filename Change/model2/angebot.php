<?php

class Angebot
{
	private $dbHandler;
	private $id;
	private $projekt_id;
	private $firma_id;
	private $benutzer_id;
	private $clientOfferNumber; //Kundenauftragsnr
	private $format;
	private $size; //Umfang
	private $color; //Farbe
	private $paper; //Papier
	private $remodelling; //Verarbeitung
	private $printing; //Auflage
	private $costs; //Druckkosten in Euro
	private $type; //Art
	private $vorstufeDesc; //Vorstufebemerkungen
	private $drucksachenDesc; //Druckkostenbemerkungen
	private $fremdkostenDesc; //Fremdkostenbemerkungen
	private $zusammenfassungDesc; //Zussammenkostenbemerkungen
	private $reg_date;
	private $projectName;
	private $userName = array();
	private $companyDates = array();
	private $auftragszettel;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;

		if ($id != null) {
			$this->id = $id;
		}
	}

	public function createAngebot($dates) {
		$firmaId = $this->getFirmaId($dates['projekt_id']);
		$sql = 'INSERT INTO Angebot (projekt_id, firma_id, clientOfferNumber, benutzer_id, format, size, color, paper, remodelling, printing, costs, type, vorstufeDesc, drucksachenDesc, fremdkostenDesc, zusammenfassungDesc, reg_date) 
			VALUES ( :projekt_id, :firma_id, :clientOfferNumber, :benutzer_id, :format, :size, :color, :paper, :remodelling, :printing, :costs, :type, :vorstufeDesc, :drucksachenDesc, :fremdkostenDesc, :zusammenfassungDesc, NOW() )';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':projekt_id', $dates['projekt_id']);
		$result->bindValue(':firma_id', $firmaId);
		$result->bindValue(':benutzer_id', $dates['benutzer_id']);
		$result->bindValue(':clientOfferNumber', $dates['clientOfferNumber']);
		$result->bindValue(':format', $dates['format']);
		$result->bindValue(':size', $dates['size']);
		$result->bindValue(':color', $dates['color']);
		$result->bindValue(':paper', $dates['paper']);
		$result->bindValue(':remodelling', $dates['remodelling']);
		$result->bindValue(':printing', $dates['printing']);
		$result->bindValue(':costs', $dates['costs']);
		$result->bindValue(':type', $dates['type']);
		$result->bindValue(':vorstufeDesc', $dates['vorstufeDesc']);
		$result->bindValue(':drucksachenDesc', $dates['drucksachenDesc']);
		$result->bindValue(':fremdkostenDesc', $dates['fremdkostenDesc']);
		$result->bindValue(':zusammenfassungDesc', $dates['zusammenfassungDesc']);
		if ($result->execute()) {
			$id = $this->getLastId();
			$this->id = $id;
			if ($this->setOfferDate($id)) {
				$result = array('success' => 'true', 'message' => 'Das Angebot wurde begründet.');
			} else {
				$result = array('success' => 'false', 'message' => 'Das Angebot konnte nicht begründet werden.');
			}
			return $result;
		} else {
			$result = array('success' => 'false', 'message' => 'Das Angebot konnte nicht begründet werden.');
			return $result;
		}
	}

	public function createAuftragszettel() {
		$auftragszettel = new TvsatzCreator($this->dbHandler);
		$this->auftragszettel = $auftragszettel->createProduct('auftragszettel', $this->id);
	}

	public function deleteOffer($id) {
		$sql = 'DELETE FROM Angebot WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$this->unsetFields();
	}

	private function getCompanyDates() {
		$sql = 'SELECT * FROM Auftraggeber WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->firma_id);
		$result->execute();
		$data = $result->fetch();
		$array = array('name' => $data['name'], 'address' => $data['anschrift'], 'address2' => $data['anschrift2'], 'city' => $data['ort'], 'postal_code' => $data['plz'], 'phone' => $data['telefon'], 'fax' => $data['fax'], 'mail' => $data['mail']);
		return $array;
	}

	private function getFirmaId($project_id) {
		$sql = 'SELECT auftraggeber FROM Projekt WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $project_id);
		$result->execute();
		$number = $result->fetch();
		$id = $number['auftraggeber'];
		return $id;
	}

	private function getLastId() {
		$sql = 'SELECT id FROM Angebot ORDER BY id DESC LIMIT 1';
		$result = $this->dbHandler->prepare($sql);
		$result->execute();
		$number = $result->fetch();
		$id = $number['id'];
		return $id;
	}

	private function getProjectName() {
		$sql = 'SELECT name FROM Projekt WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->projekt_id);
		$result->execute();
		$value = $result->fetch();
		$name = $value['name'];
		return $name;
	}

	private function getUserName() {
		$sql='SELECT Benutzer.name as name, Rolle.id as rolleId, Rolle.name as rolleName FROM Benutzer INNER JOIN Rolle ON Benutzer.rolle_id = Rolle.id WHERE Benutzer.id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->benutzer_id);
		$result->execute();
		$date = $result->fetch();
		$dateArray = array ('name' => $date['name'], 'rolle_id' => $date['rolleId'], 'rolle_name' => $date['rolleName']);
		return $dateArray;
	}

	private function selectDate() {
		$sql = 'SELECT * FROM Angebot WHERE id = :id';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':id', $this->id);
		$result->execute();
		$dates = $result->fetch();
		return $dates;
	}

	public function setOfferDate($id) {
		$this->id = $id;
		$dates = $this->selectDate();
		$this->projekt_id = $dates['projekt_id'];
		$this->firma_id = $dates['firma_id'];
		$this->benutzer_id = $dates['benutzer_id'];
		$this->clientOfferNumber = $dates['clientOfferNumber'];
		$this->format = $dates['format'];
		$this->size = $dates['size'];
		$this->color = $dates['color'];
		$this->paper = $dates['paper'];
		$this->remodelling = $dates['remodelling'];
		$this->printing = $dates['printing'];
		$this->costs = $dates['costs'];
		$this->type = $dates['type'];
		$this->vorstufeDesc = $dates['vorstufeDesc'];
		$this->drucksachenDesc = $dates['drucksachenDesc'];
		$this->fremdkostenDesc = $dates['fremdkostenDesc'];
		$this->zusammenfassungDesc = $dates['zusammenfassungDesc'];
		$this->reg_date = $dates['reg_date'];
		$projectName = $this->getProjectName();
		$this->projectName = $projectName;
		$user = $this->getUserName();
		$this->userName = $user;
		$company = $this->getCompanyDates();
		$this->companyDates = $company;
	}

	private function unsetFields() {
		unset ($this->id);
		unset ($this->projekt_id);
		unset ($this->firma_id);
		unset ($this->benutzer_id);
		unset ($this->clientOfferNumber);
		unset ($this->format);
		unset ($this->size);
		unset ($this->color);
		unset ($this->paper);
		unset ($this->remodelling);
		unset ($this->printing);
		unset ($this->costs);
		unset ($this->type);
		unset ($this->vorstufeDesc);
		unset ($this->drucksachenDesc);
		unset ($this->fremdkostenDesc);
		unset ($this->zusammenfassungDesc);
		unset ($this->reg_date);
		unset ($this->projectName);
		unset ($this->userName);
		unset ($this->auftragszettel);
	}
}