<?php

class PrintController
{
	private $creator;
	private $dbHandler;
	private $projectId;
	private $project;

	public function __construct($dbHandler, $action) {
		$this->dbHandler = $dbHandler;
		$this->creator = new TvsatzCreator($dbHandler);
		$variable = explode('projId=', $_SERVER["REQUEST_URI"]);
		$this->projectId = $variable[1];
		$this->project = $this->creator->createProduct('projekt', $this->projectId);
		$action = str_replace('/', '' , $action);
		$finalAction = 'renderDocument'.$action;
		$this->$finalAction();
	}

	private function renderDocument1() {
		echo 'in Vorbereitung 1'; exit();
	}

	private function renderDocument2() {
		echo 'in Vorbereitung 2'; exit();
	}
	private function renderDocument3() {
		echo 'in Vorbereitung 3'; exit();
	}
	private function renderDocument4() {
		echo 'in Vorbereitung 4'; exit();
	}
	private function renderDocument5() {
		$this->project = $this->creator->createProduct('projekt', $this->projectId);
		$this->project->setDates();
		$client = array();
		$client['name'] = $this->project->auftraggeber->getName();
		$client['clientNumber'] = $this->project->auftraggeber->getCurrentId();
		$client['projectName'] = $this->project->getName();
		$client['orderNumber'] = $this->project->getKundenautragsnummer();
		$address = $this->project->rechnungsadresse->getDates();
		$client['addressName'] = $address['name'];
		$client['addressDepartment'] = $address['abteilung'];
		$client['address'] = $address['anschrift'];
		if ($address['anschrift2'] != '') {
			$client['address2'] = $address['anschrift2'];
		}
		$client['addressPlace'] = $address['plz'].' '.$address['ort'];
		$deliverer = $this->project->getLieferant();
		$client['deliverer'] = $deliverer[0]['name'];
		$conditions = $this->project->getDeliverySql($this->projectId);
		if ($conditions['ifCustom'] == 1) {
			$client['anotherDelivery'] = $conditions['customAddress'];
		} else {
			$addressDates = $this->project->auftraggeber->getDeliveryAddress($this->projectId);
			$client['deliveryName'] = $addressDates["name"];
			$client['deliveryAddress'] = $addressDates["address"];
			if ( $addressDates["address2"] != null) {
				$client['deliveryAddress2'] = $addressDates["address2"];
			}
			$client['deliveryPlace'] = $addressDates['code'].' '.$addressDates['place'];
		}
		$deliveryTime = $this->project->getDeliveryTime();
		$dateChange = explode('-', $deliveryTime);
		$client['deliveryTime'] = $dateChange[2].'.'.$dateChange[1].'.'.$dateChange[0];
		$pattern = $this->project->getPatterns();
		$client['pattern'] = $pattern['pattern'];
		$client['patternTo'] = $pattern['patternTo'];
		$helper = $this->creator->createProduct('helpers');
		$client['carrierList'] = $helper->setLieferant();
		$userList = $helper->getProjectUserName($this->projectId);
		$helper->setMachine();
		$machine = $helper->getMachine();
		$document = $this->creator->createProduct('document');
		$projectDates = $this->project->getDatesProject();
		$projectStart = $this->project->getRegDate();
		$temp = explode(' ', $projectStart);
		$tempStart = explode('-', $temp[0]);
		$client['startDate'] = $tempStart[2].'.'.$tempStart[1].'.'.$tempStart[0];
		$counter = 0;
		foreach ($projectDates as $singleDate) {
		    $client['projectDates'][$counter] = str_replace('/', '.', $singleDate);
		    $counter++;
		}
		$filename = 'auftragszettel-'.$this->projectId.'-'.(date("dmY_Hi")).".pdf";
		$filepath = $_SERVER['DOCUMENT_ROOT']."/CRM/akte/".$filename;
		$description = $this->creator->createProduct('bemerkung');
		$client['internDesc'] = $description->getInternDesc($this->projectId);
		$pattern = $document->innerForm($client, $userList);
		$success = $this->project->getDescToPrint($this->projectId, 'descToPrint5');
		if ($success != 'false') {
		    $success = $document->insert('Auftragszettel', $_SESSION["user"], $success, $filename, 
		    $this->projectId);
		    if ($success != 'false') {
			$this->project->deleteDescToPrint($this->projectId, 'descToPrint5');
		    }
		}
		if ($success == 'success') {
		    $this->renderPdf($pattern, $title, $filename, $filepath);
		} else {
		    echo 'error - printController.php line 101';
		}
	}
	private function renderDocument6() {
		$conditions = $this->project->getDeliverySql($this->projectId);
		if ($conditions['ifCustom'] == 0) {
			$client = $this->creator->createProduct('auftraggeber');
			$addressArray = $client->getDeliveryAddress($conditions['customerId']);
			if ($addressArray['address2'] == null) {
				$address = $addressArray['name'].'<br>'.$addressArray['address'].'<br>'.$addressArray['code'].' '.$addressArray['place'];
			} else {
				$address = $addressArray['name'].'<br>'.$addressArray['address'].'<br>'.$addressArray['address2'].'<br>'.$addressArray['code'].' '.$addressArray['place'];
			}
		} else {
			$addressToPerform = $conditions['customAddress'];
			$explode = explode('<>', $addressToPerform);
			$newAddress = '';
			foreach ($explode as $single) {
				$newAddress .= $single.'<br>';
			}
			$address = trim($newAddress, '<br>');
		}
		$description = $this->creator->createProduct('bemerkung');
		$projectDesc = $description->getDeliveryDesc($this->projectId);
		$descArray = array();
		if ($projectDesc['desc1_li'] == 1) {
			array_push($descArray, $projectDesc['desc1']);
		}
		if ($projectDesc['desc2_li'] == 1) {
			array_push($descArray, $projectDesc['desc2']);
		}
		if ($projectDesc['desc3_li'] == 1) {
			array_push($descArray, $projectDesc['desc3']);
		}
		if ($projectDesc['desc4_li'] == 1) {
			array_push($descArray, $projectDesc['desc4']);
		}
		if (empty($descArray)) {
			$descArray = false;
		}
		$document = $this->creator->createProduct('document');
		$pattern = $document->deliveryLetter($address, $conditions, $descArray);
		$title = 'Lieferschein';
		$filename = 'lieferschein-'.$this->projectId.'-'.(date("dmY_Hi")).".pdf";
		$filepath = $_SERVER['DOCUMENT_ROOT']."/CRM/akte/".$filename;
		$success = $this->project->getDescToPrint($this->projectId, 'descToPrint6');
		if ($success != 'false') {
		    $success = $document->insert('Lieferschein', $_SESSION["user"], $success, $filename, $this->projectId);
		    if ($success != 'false') {
			$this->project->deleteDescToPrint($this->projectId, 'descToPrint6');
		    }
		}
		if ($success == 'success') {
		    $this->renderPdf($pattern, $title, $filename, $filepath);
		} else {
		    echo 'error - printController.php line 157';
		}
	}

	private function renderPdf($pattern, $title, $filename, $filepath) {
		include('vendor/mpdf/mpdf.php');

		$mpdf = new mPDF('utf-8'); 

		$mpdf->SetTitle($title);
		
		$mpdf->WriteHTML($pattern);

		$mpdf->Output($filepath,"F");
		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($filepath)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: application/pdf; charset=utf-8');
		header('Content-Disposition: inline; filename="'.$filename.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($filepath));	// provide file size
		header('Connection: close');
        ob_clean();
        flush();
		readfile($filepath);
		exit();
	}
}