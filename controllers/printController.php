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

	private function render($column, $filename, $title, $offer = null) {
		$descColumn = $column[0];
		$vorstufeColumn = $column[1];
		$drucksachenColumn = $column[2];
		$fremdarbeitenColumn = $column[3];
		$dates = array();
		$this->project = $this->creator->createProduct('projekt', $this->projectId);
		$this->project->setDates();
		$filepath = $_SERVER['DOCUMENT_ROOT']."/CRM/akte/".$filename;
		$dates['name'] = $this->project->ansprechpartner->getName();
		$clientId = $this->project->auftraggeber->getCurrentId();
		$address = $this->project->auftraggeber->getDeliveryAddress($clientId);
		$dates['clientName'] = $address["name"];
		$dates['clientAddress'] = $address["address"];
		if ($address["address2"] != '') {
			$dates['clientAddress2'] = $address["address2"];
		}
		date_default_timezone_set('Europe/Berlin');
		$dates['date'] = date('d/m/Y ', time());
		$dates['date'] = str_replace('/', '.', $dates['date']);
		$dates['clientCity'] = $address["place"].' '.$address["code"];
		$dates['title'] = $title;
		$dates['upperTitle'] = strtoupper($title);
		$dates['clientOrderNumber'] = $this->project->getKundenautragsnummer();
		$dates['projectName'] = $this->project->getName();
		$helper = $this->creator->createProduct('helpers');
		if ($title == "Angebot") {
			$success = $helper->setOfferNumber($this->projectId, $filename);
			if ($success == 'success') {
				$success = $helper->getOfferNumber($this->projectId, $filename);
				if ($success != 'false') {
					$dates['offerNumber'] = $success;
				} else {
					echo 'error = printController  - renderDocument1';
				}
			} else {
				echo 'error = printController  - renderDocument1';
			}
		} elseif ($title == "Rechnung") {
			$success = $helper->setInvoiceNumber($this->projectId, $filename);
			if ($success == 'success') {
				$success = $helper->getInvoiceNumber($this->projectId, $filename);
				if ($success != 'false') {
					$dates['orderNumber'] = $success;
				} else {
					echo 'error = printController  - renderDocument4';
				}
			} else {
				echo 'error = printController  - renderDocument4';
			}
		}
		$payment = $this->project->getIndividuals();
		if ($payment["skonto"] != '') {
			$dates['skonto'] = $payment["skonto"];
		}
		if ($payment["payment"] != '') {
			$dates['paymentOpt'] = $helper->getSingleZahlungszielDesc($payment["payment"]);
		}
		$description = $this->creator->createProduct('bemerkung');
		$success = $description->getDescription($descColumn, $this->projectId);
		if ($success != 'false') {
			if ($success["ifSet"] == 1) {
				$dates['description'] = $success["description"];
			}
		} else {
			echo 'error = printController  - renderDocument1';
		}
		$vorstufe = $this->project->getVorstufe();
		if ($vorstufe[0] != null) {
			$amount = 0;
            $counter = 0;
            if (isset($vorstufe[0])) {
        	    foreach($vorstufe[0] as $singleRow) {
        		    $amount += $singleRow['amount'];;
                    $textDate = explode('.', $singleRow["performanceTime"]);
                    $vorstufe[0][$counter]["performanceTime"] = $textDate[0].'.'.$textDate[1].'.'.$textDate[2];
                    $vorstufe[0][$counter]["performanceTime2"] = $textDate[1].'.'.$textDate[0].'.'.$textDate[2];
                    $counter++;
        	    }
            }
            $vorstufe = $vorstufe[0];
            $dates['amountVorstufe'] = number_format($amount, 2);
            $success = $description->getDescription($vorstufeColumn, $this->projectId);
            if ($success != 'false') {
				if ($success["ifSet"] == 1) {
					$dates['vorstufeDescription'] = $success["description"];
				}
			} else {
				echo 'error = printController  - renderDocument1';
			}
		} else {
			$vorstufe = null;
			if ($offer == true) {
				$dates['offer'] = Helpers::settings($this->dbHandler, 'standardText');
			}
		}
		$drucksachen = $this->project->getDrucksachen();
		if (!empty($drucksachen)) {
			$amount = 0;
			if (isset($drucksachen[0])) {
				foreach($drucksachen[0] as $singleRow) {
					$amount += $singleRow['amount'];
				}
			}
			$drucksachen = $drucksachen[0];
			$dates['amountDrucksachen'] = number_format($amount, 2);
			$success = $description->getDescription($drucksachenColumn, $this->projectId);
			if ($success != 'false') {
				if ($success["ifSet"] == 1) {
					$dates['druckDescription'] = $success["description"];
				}
			} else {
				echo 'error = printController  - renderDocument1';
			}
		} else {
			$drucksachen = null;
		}
		$fremdarbeiten = $this->project->getFremdsache();
		if (!empty($fremdarbeiten)) {
			if (isset($fremdarbeiten[0])) {
				$counter = 0;
				$amount = 0;
				foreach($fremdarbeiten[0] as $singleRow) {
					$amount += $singleRow['sellPrice'];
					if ($singleRow['textDate'] != '') {
						$textDate = explode('-', $singleRow['textDate']);
                        $fremdarbeiten[0][$counter]['textDate'] = $textDate[2].'.'.$textDate[1].'.'.$textDate[0];
                        $fremdarbeiten[0][$counter]['textDate2'] = $textDate[1].'.'.$textDate[2].'.'.$textDate[0];
                    }       
                    $counter++;
    	        }
            }
            $fremdarbeiten = $fremdarbeiten[0];
            $dates['amountFremdarbeiten'] = number_format($amount, 2);
            $success = $description->getDescription($fremdarbeitenColumn, $this->projectId);
			if ($success != 'false') {
				if ($success["ifSet"] == 1) {
					$dates['fremdDescription'] = $success["description"];
				}
			} else {
				echo 'error = printController  - renderDocument1';
			}
		} else {
			$fremdarbeiten = null;
		}	
		$document = $this->creator->createProduct('document');
		$path = 'http://ad9bis.vot.pl/CRM/view/assets/images/logo.png';
		if ($dates["upperTitle"] == 'AUFTRAGSBESTäTIGUNG') {
			$dates["upperTitle"] = 'AUFTRAGSBESTÄTIGUNG';
		}
		$pattern = $document->mainForm($dates, $vorstufe, $drucksachen, $fremdarbeiten, $path);
		//$this->renderPdf($pattern, $title, $filename, $filepath);
		//var_dump($title); exit();
		if ($title == 'Angebot') {
			$desc = 'descToPrint1';
		} elseif ($title == 'Preismitteilung') {
			$desc = 'descToPrint3';
		} elseif ($title == 'Rechnung') {
			$desc = 'descToPrint4';
		} else {
			$desc = 'descToPrint2';
		}
		$success = $this->project->getDescToPrint($this->projectId, $desc);
		if ($success != 'false') {
		    $success = $document->insert($title, $_SESSION["user"], $success, $filename, $this->projectId);
		    if ($success != 'false') {
			$this->project->deleteDescToPrint($this->projectId, $desc);
		    }
		}
		if ($success == 'success') {
		    $this->renderPdf($pattern, $title, $filename, $filepath);
		} else {
		    echo 'error - printController.php line 359';
		}
	}

	private function renderDocument1() {
		$offer = true; // to display standarttext when no vorstufe rows
		$descColumn = array('desc1', 'desc1_an');
		$vorstufeColumn = array('desc2', 'desc2_an');
		$drucksachenColumn = array('desc3', 'desc3_an');
		$fremdarbeitenColumn = array('desc4', 'desc4_an');
		$title = 'Angebot';
		$filename = 'angebot-'.$this->projectId.'-'.(date("dmY_Hi")).".pdf";
		$column = array($descColumn, $vorstufeColumn, $drucksachenColumn, $fremdarbeitenColumn);

		$this->render($column, $filename, $title, $offer);
	}

	private function renderDocument2() {
		$descColumn = array('desc1', 'desc1_au');
		$vorstufeColumn = array('desc2', 'desc2_au');
		$drucksachenColumn = array('desc3', 'desc3_au');
		$fremdarbeitenColumn = array('desc4', 'desc4_au');
		$title = 'Auftragsbestätigung';
		$filename = 'auftragsbestatigung-'.$this->projectId.'-'.(date("dmY_Hi")).".pdf";
		$column = array($descColumn, $vorstufeColumn, $drucksachenColumn, $fremdarbeitenColumn);

		$this->render($column, $filename, $title);
	}
	private function renderDocument3() {
		$descColumn = array('desc1', 'desc1_pm');
		$vorstufeColumn = array('desc2', 'desc2_pm');
		$drucksachenColumn = array('desc3', 'desc3_pm');
		$fremdarbeitenColumn = array('desc4', 'desc4_pm');
		$title = 'Preismitteilung';
		$filename = 'preismitteilung-'.$this->projectId.'-'.(date("dmY_Hi")).".pdf";
		$column = array($descColumn, $vorstufeColumn, $drucksachenColumn, $fremdarbeitenColumn);
		
		$this->render($column, $filename, $title);
	}
	private function renderDocument4() {
		$descColumn = array('desc1', 'desc1_re');
		$vorstufeColumn = array('desc2', 'desc2_re');
		$drucksachenColumn = array('desc3', 'desc3_re');
		$fremdarbeitenColumn = array('desc4', 'desc4_re');
		$title = 'Rechnung';
		$filename = 'rechnung-'.$this->projectId.'-'.(date("dmY_Hi")).".pdf";
		$column = array($descColumn, $vorstufeColumn, $drucksachenColumn, $fremdarbeitenColumn);
		
		$this->render($column, $filename, $title);
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
		$description = $this->creator->createProduct('bemerkung');
		$drucksachen = $this->project->getDrucksachen();
		if (!empty($drucksachen)) {
			$amount = 0;
			if (isset($drucksachen[0])) {
				foreach($drucksachen[0] as $singleRow) {
					$amount += $singleRow['amount'];
				}
			}
			$drucksachen = $drucksachen[0];
			$client['amountDrucksachen'] = number_format($amount, 2);
		} else {
			$drucksachen = null;
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
		$pattern = $document->innerForm($client, $userList, $drucksachen);
		$success = $this->project->getDescToPrint($this->projectId, 'descToPrint5');
		$title = 'Auftragszettel';
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
		    echo 'error - printController.php line 359';
		}
	}

	private function renderPdf($pattern, $title, $filename, $filepath, $filepath = null) {
		include('vendor/mpdf/mpdf.php');

		if (!isset($filepath)) {
			$filepath = $_SERVER['DOCUMENT_ROOT']."/CRM/akte/".$filename;
		}

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