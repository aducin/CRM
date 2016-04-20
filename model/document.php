<?php

class Document

{
	private $dbHandler;
	private $root;

	function __construct($dbHandler, $id = null) {
		$this->dbHandler = $dbHandler;
		$this->root = ('http://'.$_SERVER["HTTP_HOST"]).'/CRM';
	}
	
	private function deleteDocument($data) {
		$sql = 'DELETE FROM Akte WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $data);
		if ($result->execute()) {
		    return 'success';
		} else {
		    return 'false';
		}
	}
	
	public function deleteSql( $data ) {
		$file = $this->getFileName($data);
		if ($file != 'false') {
		    $path = $this->root.'/akte/'.$file;
		    $success = $this->deleteDocument($data);
			  if ($success != 'false') {
				$success = $this->removeFile($file);
				if ($success == 'success') {
				    echo $success;
				} else {
				    echo 'error - document.php line 19';
				}
			  }
		} else {
		    echo 'error - document.php line 19';
		}
	}

	public function deliveryLetter($address, $conditions, $description) {
		if (!isset($conditions["clientNumber"])) {
			$conditions["clientNumber"] = '';
		}
		if (!isset($conditions["name"])) {
			$conditions["name"] = '';
		}
		if ($conditions["deliveryText"] != '') {
			$text = $conditions["deliveryText"];

		}
		if ($description == false) {
			unset($description);
		}
		$clientNumber = $conditions["clientNumber"];
		$projectName = $conditions["name"];
		$orderNr = $conditions["orderNr"];
		date_default_timezone_set('Germany/Berlin');
		$date = date('d.m.Y', time());
		$template = '
			<div style="height: 92%;">
			<div>
			<div style="width: 60%; height: 20%; float: left;">
			<div style="height: 9%;"></div>
			<table>
			<tr>
			<td style="font-family: Arial;"><h6>tv satzstudio gmbh</h6></td>
			<td style="font-family: Arial;" style="color:#00FFFF;">|</td>
			<td style="font-family: Arial;"><h6>neidhardswinden 63</h6></td>
			<td style=" font-family: Arial;color:#00FFFF;">|</td>
			<td style="font-family: Arial;"><h6>91448 Emskirchen</h6></td>
			</tr>
			</table>
			<div style="margin-top: 5%; text-align: left; font-family: Arial;">
			'.$address.'
			</div>
			</div>
			<div style="width: 40%; height: 20%; float: left; background-color:grey;">
			</div>
			<div style="text-align: right; margin-top: 3%; font-family: Arial;">'.$date.'</div>
			<div style="height: 5%;"></div>
			<div>
				<h2 style="font-family: Arial;">LIEFERSCHEIN:</h2>
				<table>
					<tr style="margin-top:5%;">
						<td style="font-family: Arial;">Kunderauftragsnummer:
						</td>
						<td style="font-family: Arial;">'.$clientNumber.'</td>
					</tr>
					<tr>
					<td></td>
					</tr>
					<tr>
						<td style="font-family: Arial;">Auftragsnummer:
						</td>
						<td style="font-family: Arial;">';
			if ($orderNr != '') {
				$template .= $orderNr;	
			} else {
				$template .= '<i>noch keine Daten</i>';
			}
			$template .= '</td>
					</tr>
					<tr>
					<td></td>
					</tr>
					<tr>
						<td style="font-family: Arial;">Projektname:
						</td>
						<td style="font-family: Arial;"><b>'.$projectName.'</b></td>
					</tr>
				</table>
			</div>
		';
		if ($text) {
			$template .= '
				<div style="font-family: Arial; height: 5%;"></div>
				<h3 style="font-family: Arial;">Lieferschein Positionstext:</h3>
				<div style="font-family: Arial;">'.$text.'</div>
			';
		}
		if($description) {
			$template .= '
				<div style="height: 5%; font-family: Arial;"></div>
				<h3 style="font-family: Arial;">Bemerkungen:</h3>
			';
		  foreach ($description as $single) {
			  $template .= '
				  <div style="margin-top: -2%; font-family: Arial;">'.$single.'</div><br>
			  ';
		  }
		}
		$template .= '
			<div style="height: 6%;"></div>
			<div style="font-family: Arial;"><b>Datum: ______________________________  Unterschrift Kunde: ____________________________________</b></div>

			</div>
			</div>
			<div style="height: 8%;">
			<table>
			<tr>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;"><div style="font-family: Arial;">sparkasse emskirchen 240 003 756 (762 510 20) 
				<span style="color:#00FFFF;"> | </span> IBAN: DE79 7625 1020 0240 0037 56, BIC: BYLADEM1NEA</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">geschäftsführer: peter vogler
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;">hypobank langenzenn 35 90 263 744 (762 200 73)
				<span style="color:#00FFFF;"> | </span>  IBAN: DE75 7622 0073 3590 2637 44, BIC: HYVEDEMM419</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">handelsregister fürth, HRB 1584
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;">vr-bank neustadt 331 643 (760 695 59)
				<span style="color:#00FFFF;"> | </span> IBAN: DE38 7606 9559 0000 3316 43, BIC: GENODEF1NEA</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">steuer-nr. 9203 18443803
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;">cvw-privatbank wilhermsdorf 40 08 839 (762 119 00)
				<span style="color:#00FFFF;"> | </span> IBAN: DE14 7621 1900 0004 0088 39, BIC: GENODEF1WHD</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">ust-id-nr.DE 131 945 322
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;"><b>wochenblatt-konto:</b> sparkasse emskirchen 227 073 782 (762 510 20)
				<span style="color:#00FFFF;"> | </span> IBAN: DE75 7625 1020 0225 0737 82, BIC: BYLADEM1NEA</td>
			</tr>
			</table>
			</div>
		';

		return $template;
	}
	
	public function getDocumentList($id) {
		$sql = 'SELECT Akte.id, Akte.name as fileName, Benutzer.name as userName, Akte.bezeichnung, Akte.reg_date, Akte.file 
		FROM Akte INNER JOIN Benutzer ON Akte.mitarbeiter = Benutzer.id WHERE projectId = :id ORDER BY Akte.reg_date';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
		    $final = array();
		    foreach ($result as $single) {
			 $firstExplode = explode(' ', $single['reg_date']);
			 $secondExplode = explode('-', $firstExplode[0]);
			 $thirdExplode = explode(':', $firstExplode[1]);
			 $finalDate = $secondExplode[2].'.'.$secondExplode[1].'.'.$secondExplode[0].' '.$thirdExplode[0].':'.$thirdExplode[1];
			 $final[] = array(
			    'id' => $single['id'],
			    'fileName' => $single['fileName'], 
			    'userName' => $single['userName'], 
			    'description' => $single['bezeichnung'], 
			    'regDate' => $finalDate,
			    'file' => $single['file']
			 );
		    }
		    return $final;
		} else {
		    return 'false';
		}
	}
	
	private function getFileName($data) {
		$sql = 'SELECT file FROM Akte WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $data);
		if ($result->execute()) {
		    $final = $result->fetch();
		    return $final['file'];
		} else {
		    return 'false';
		}
	}

	public function innerForm($client, $userList, $drucksachen) {
		$template = '
			<head>
			<title>AUFTRAGSZETTEL</title>
			</head>
			<body>
			<div class="container-name">
			<div class="div1" style="width: 74%; height: 91.9%; float:left;">
				<div class="div1" style="width: 90%; margin-top: -7%; margin-left:5%; float:left;">
					<p style="text-align: center; font-size:44px; font-weight: bolder; font-family: Arial">AUFTRAGSZETTEL</p>
				</div>
				<div class="div1" style="width: 100%; margin-top: -5%; float:left;">
					<div style="width: 40%; float:left; font-size:15px;"><div style="font-family: Arial">Auftraggeber/</div><div style="font-family: Arial">Sachbearbeiter</div></div>
					<div style="width: 59%; float:left; font-size: 18px; border-bottom: 1px dotted; font-family: Arial">'.$client['name'].'</div>
					<div style="width: 40%; height: 10%; float:left;">
					  <div style="font-size: 15px; margin-top: 2.5%; font-family: Arial">Rechnungsadresse</div>
					  <div style="font-size: 15px; margin-top: 19%; font-family: Arial">Kundennummer</div>
					  <div style="font-size: 15px; font-family: Arial">'.$client['clientNumber'].'</div>
					</div>
					<div style="width: 59%; height: 10%; float:left;">
					  <div style="font-size: 18px; border-bottom: 1px dotted; font-family: Arial">'.$client['addressName'].'</div>
					  <div style="font-size: 18px; border-bottom: 1px dotted; font-family: Arial">'.$client['addressDepartment'].'</div>
					  <div style="font-size: 18px; border-bottom: 1px dotted; font-family: Arial">'.$client['address'].'</div>';
		if ($client['address2']) {
		    $template .= '<div style="font-size: 18px; border-bottom: dotted; font-family: Arial">'.$client['address2'].'</div>';
		} ;
		$template .=	'
					  <div style="font-size: 18px; border-bottom: 1px dotted; font-family: Arial">'.$client['addressPlace'].'</div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px; font-family: Arial">
					  Projektname
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					  <div style="font-size: 18px; border-bottom: 1px dotted; font-family: Arial">
						'.$client['projectName'].'
					    </div>
					</div>
					<div style="width: 40%; height: 3%;float:left; font-size: 15px; font-family: Arial">
					  Kundenauftragsnummer
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					    <div style="font-size: 18px; border-bottom: 1px dotted; font-family: Arial">
						'.$client['orderNumber'].'
					    </div>
					</div>
					<div style="float:left; background-color: black; color: white; width: 30%; height: 3%; font-size: 22px; font-family: Arial">
					  DRUCK
					</div>
					<div style="margin-top: 0.2%; float:left; width: 69.8%; height: 3%; font-size: 22px; border-bottom: 2px solid black;">
					</div>
					<div style="width: 100%;">';
			if ($drucksachen != null) {
		$template .= '			<div style="width: 100%;">
									<div style="width: 100%; background-color: #E8E8E8;  border: 1px solid gray;">
										<table>
										<thead>
											<tr style="background-color: #D0D0D0;">
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Drucksache</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Maschine</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Art</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Auflage</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Format</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Umfang</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Farbe</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Papier</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Verarbeitung</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial">Betrag</th>
											</tr>
											</thead>';
	foreach ($drucksachen as $single) {
		$template .= '						<tbody><tr style="background-color: #E0E0E0;">
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["print"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["machineName"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["type"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["edition"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["format"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["size"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["color"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["paper"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["remodelling"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial">'.$single["amount"].'</td>
											</tr>';
	}
		$template .= '					</tbody></table>
									</div>';
	if (isset($client['amountDrucksachen'])) {
		$template .= '				<div style="text-align: right; font-family: Arial">Druckkosten gesamt: <b>'.$client['amountDrucksachen'].' EURO</b></div>';
	}
		$template .= '			</div>';
	}
					
					
					
		$template .= '	</div>
					<div style="width: 35%; padding-top: 4%; height: 4%; float:left; font-size: 15px; font-family: Arial">
					  Lieferung per
					</div>
					<div style="width: 64%; padding-top: 4%; height: 4%; float:left;">
					    <div style="font-size: 15px; padding-bottom: 4%; font-family: Arial;">';
					    foreach ($client['carrierList'] as $single) {
			$template .=		 '<input type="radio"';
			if ($single['name'] == $client['deliverer']) {
			    $template .= 	' checked="checked" ';
			}
			$template .=		 '>'. $single["name"].' ';   
					    }
			$template .=	'</div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px; font-family: Arial">
					  Lieferung an
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					    <div style="font-size: 15px;">';
			if (isset($client['anotherDelivery'])) {
			    $template .= '<div style="padding-bottom: 4%; border-bottom: 1px dotted; font-family: Arial">'.$client['anotherDelivery'].'</div>';
			} else {
			    $template .= '<div style="border-bottom: 1px dotted; font-family: Arial">'.$client['deliveryName'].'</div>
					  <div style="border-bottom: 1px dotted; font-family: Arial">'.$client['deliveryAddress'].'</div>';
			if (isset($client['deliveryAddress2'])) {
			    $template .= '<div style="border-bottom: 1px dotted; font-family: Arial">'.$client['deliveryAddress2'].'</div>';
			}
			$template .=    '<div style="border-bottom: 1px dotted; font-family: Arial">'.$client['deliveryPlace'].'</div>';
			}
			$template .=	   '</div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px; padding-top: 3%; font-family: Arial">
					  Muster in Tasche
					</div>
					<div style="width: 59%; height: 3%; float:left; padding-top: 3%;">
					    <div style="font-size: 15px; border-bottom: 1px dotted; font-family: Arial">
						'.$client['pattern'].'
					    </div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px; font-family: Arial">
					  Stück Muster an
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					    <div style="font-size: 15px; border-bottom: 1px dotted; font-family: Arial">
						'.$client['patternTo'].'
					    </div>
					</div>
					<div style="width: 100%; height: 10%; padding: 2%; border: 2px solid black; float:left; font-size: 15px; font-family: Arial">'.$client['internDesc'].'</div>
				</div>
			</div>
			<div class="div2" style="width: 24%; height: 91%; background-color: #C0C0C0; margin:10px; float:left; margin :10px;">
				<div class="div2" style="float: left; width: 100%; height: 48%;">
				<div style="width: 100%; height: 24%; background-color: gray;"></div>
				    <div style="width: 100%; height: 24%; background-color: #C0C0C0;">
					<div style="padding-bottom: 3%;">
					    <div style="float: left; font-size: 15px; width: 49%; margin-left: 4.5%; font-family: Arial; padding-top: 3%;">
						<input type="radio"';
						if ($client['status'] == 1) {
		$template .= ' checked="checked"';					
						}
		$template .='>Angebot
					    </div>
					    <div style="float: left; font-size: 15px; width: 49%; margin-left: -3%; font-family: Arial; padding-top: 3%;">
						<input type="radio"';
		if ($client['status'] == 2) {
			$template .= ' checked="checked"';							
		}
		$template .='>Auftrag
					    </div>
					</div>
					<div style="font-size: 13px; padding-bottom: 3%; margin-left: 5%; font-family: Arial">Eingang: '.$client['startDate'].'</div>
					<div style="font-size: 13px; margin-left: 5%; padding-bottom: 3%; font-family: Arial">Auftr. nr. TV</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial;">Termin Korrektur</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial; padding-bottom: 3%;">'.$client['projectDates'][0].'</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial;">Termin Daten</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial; padding-bottom: 3%;">'.$client['projectDates'][1].'</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial;">Termin Proof / Andruck</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial; padding-bottom: 3%;">'.$client['projectDates'][2].'</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial;">Termin Andruck</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial; padding-bottom: 3%;">'.$client['projectDates'][3].'</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial;">Termin Lieferung</div>
					<div style="font-size: 13px; margin-left: 5%; font-family: Arial; padding-bottom: 3%;">'.$client['deliveryTime'].'</div>
				    </div>
				</div>
				<div class="div2" style="font-family: Arial; float: left; width: 100%; height: 1%; background-color: black;"></div>
				<div class="div2" style="font-family: Arial; margin-left: 10%; margin-top: 10%; float: left; width: 100%; height: 37%;">
				<div style="font-size: 13px; font-family: Arial;"><b>Bearbeiten von:</b></div>';
		foreach ($userList as $single) {
			$template .=	'<div>
			    <input type="checkbox" style="font-size: 13px; font-family: Arial;" checked="checked">        '.$single['name'].' - '.$single['id'].'
			</div>';
		}
		$template .=	'</div>
			</div>
			</div>
			<div style="height: 8%;">
			<table>
			<tr>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;"><div style="font-family: Arial;">sparkasse emskirchen 240 003 756 (762 510 20) 
				<span style="color:#00FFFF;"> | </span> IBAN: DE79 7625 1020 0240 0037 56, BIC: BYLADEM1NEA</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">geschäftsführer: peter vogler
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;">hypobank langenzenn 35 90 263 744 (762 200 73)
				<span style="color:#00FFFF;"> | </span>  IBAN: DE75 7622 0073 3590 2637 44, BIC: HYVEDEMM419</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">handelsregister fürth, HRB 1584
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;">vr-bank neustadt 331 643 (760 695 59)
				<span style="color:#00FFFF;"> | </span> IBAN: DE38 7606 9559 0000 3316 43, BIC: GENODEF1NEA</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">steuer-nr. 9203 18443803
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;">cvw-privatbank wilhermsdorf 40 08 839 (762 119 00)
				<span style="color:#00FFFF;"> | </span> IBAN: DE14 7621 1900 0004 0088 39, BIC: GENODEF1WHD</td>
			</tr>
			<tr>
				<td style="font-family: Arial; width: 200px; height: 10%; font-size: 10px;">ust-id-nr.DE 131 945 322
				</td>
				<td style="font-family: Arial; width: 620px; height: 10%; font-size: 10px;"><b>wochenblatt-konto:</b> sparkasse emskirchen 227 073 782 (762 510 20)
				<span style="color:#00FFFF;"> | </span> IBAN: DE75 7625 1020 0225 0737 82, BIC: BYLADEM1NEA</td>
			</tr>
			</table>
			</div>
			</body>
		';
		return $template;
	}
	
	public function insert($name, $user, $description, $file, $projectId) {
	    $sql = 'INSERT INTO Akte (name, mitarbeiter, bezeichnung, reg_date, file, projectId) 
		VALUES (:name, :user, :description, NOW(), :file, :projectId)';
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':name', $name);
	    $result->bindValue(':user', $user);
	    $result->bindValue(':description', $description);
	    $result->bindValue(':file', $file);
	    $result->bindValue(':projectId', $projectId);
	    if ($result->execute()) {
		return 'success';
	    } else {
		return 'false';
	    }
	    
	}

	public function mainForm($dates, $vorstufe, $drucksachen, $fremdarbeiten) {
		$template = '
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Dokument</title>
			</head>
			<body>
				<div style="width: 100%; height: 100%;">
					<div style="width: 100%; height: 90%;">
						<div style="width: 100%; height: 30%;">
							<div style="float: right; width: 29%; height: 30%;">
								<div style="background-color: #E8E8E8; height: 7%; text-align: center;"></div>
								<div style="text-align: right; font-family: Arial;">telefon 09102|9392-00</div>
								<div style="text-align: right; font-family: Arial;">fax 09102|9392-20</div>
								<div style="text-align: right; font-family: Arial;">isdn 09102|9392-175</div>
								<div style="text-align: right; font-family: Arial;">info@tvsatzstudio.de</div>
								<div style="text-align: right; font-family: Arial;">www.tvsatzstudio.de</div>
								<br>
								<div style="text-align: right; font-family: Arial;">Ihre Ansprechpartner:</div>
								<div style="text-align: right; font-family: Arial;">'.$dates['name'].'</div>
							</div>
							<div style="float: left; width: 70%; height: 12%; background-color: #808080;"></div>
							<div style="float: left; width: 70%; height: 18%;">
								<div style="font-family: Arial; font-size: 17px; padding-top: 13%;">'.$dates['clientName'].'</div>
								<div style="font-family: Arial; font-size: 17px; padding-top: 1%;">'.$dates['clientAddress'].'</div>';
		if (isset($dates['clientAddress2'])) {
			$template .= '		<div style="font-family: Arial; font-size: 17px; padding-top: 1%;">'.$dates["clientAddress2"].'</div>';
		}						
		$template .=	   '	<div style="font-family: Arial; font-size: 17px; padding-top: 1%;"><b>'.$dates['clientCity'].'</b></div>
							</div>
							<div style="float: left; width: 100%; height: 2%;"></div>
							<div style="float: left; width: 85%; height: 7%;">
								<div style="font-size: 20px; font-family: Arial;">Sehr geehrte Damen und Herren,</div>
								<div style="font-size: 16px; font-family: Arial;">wir bedanken uns für Ihre Anfrage und hoffen, mit ';
		if ($dates['title'] == 'Angebot')	{
			$template .=    'unserem';
		} else {
			$template .=    'unserer';
		}
								
		$template .=		' nachfolgenden</div>
								<div style="font-size: 16px; font-family: Arial;">'.$dates['title'].' Ihren Erwartungen entsprechen zu können.</div>
							</div>
							<div style="float: left; width: 15%; height: 7%;">
								<div style="text-align: right; font-size: 17px; padding-top: 25%; font-family: Arial;">'.$dates['date'].'</div>
							</div>
							<div style="float: left; width: 100%; height: 3%;"></div>
							<div style="font-size: 20px; font-family: Arial;"><u>'.$dates['upperTitle'].'</u></div>
							<div style="float: left; width: 30%; height: 8%;">
								<div style="font-size: 16px; padding-top: 2%; font-family: Arial;">Kundenauftragsnr.:</div>';
		if (isset($dates['offerNumber'])) { 
			$template .=	'<div style="font-size: 16px; padding-top: 2%; font-family: Arial;">Angebotsnr:</div>';
		} elseif(isset($dates['orderNumber'])) {
			$template .=	'<div style="font-size: 16px; padding-top: 2%; font-family: Arial;">';
			if ($dates['title'] == 'Rechnung') {
				$title = 'Rechnungsnr:';
			} else {
				$title = 'Auftragsnr:';
			}
			$template .= ' '.$title.'</div>';
		}
			$template .=	'	<div style="font-size: 16px; padding-top: 2%; font-family: Arial;">Projektname:</div>
							</div>
							<div style="float: left; width: 70%; height: 8%;">
								<div style="font-size: 16px; padding-top: 1%; font-family: Arial;">'.$dates['clientOrderNumber'].'</div>';
		if (isset($dates['offerNumber'])) {
			$template .=	   '<div style="font-size: 16px; padding-top: 0.8%; font-family: Arial;">'.$dates['offerNumber'].'</div>';
		} elseif(isset($dates['orderNumber'])) {
			$template .=	   '<div style="font-size: 16px; padding-top: 0.8%; font-family: Arial;">'.$dates['orderNumber'].'</div>';
		}
			$template .=	   '<div style="font-size: 16px; padding-top: 1%; font-family: Arial;">'.$dates['projectName'].'</div>
							</div>
						</div>
						<div>
						</div>
						<div>
							<div style="background-color: #F0F0F0;">';
	if ($vorstufe != null) {
		$template .= '			<div style="width: 98%; margin-left: 1%;">
									<div style="font-size; 20px; padding-top: 3%; font-family: Arial;"><b>VORSTUFEKOSTEN</b></div>
									<div style="width: 100%;">
										<table style="border: 1px solid gray; background-color: #E8E8E8;">
											<tr style="background-color: #D0D0D0;">
												<th style="font-size: 12px; width: 8%; text-align: left; font-family: Arial;">Art</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">erledigt</th>
												<th style="font-size: 12px; width: 15%; text-align: left; font-family: Arial;">Mitarbeiter</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Tätigkeit</th>
												<th style="font-size: 12px; width: 7%; text-align: left; font-family: Arial;">Zeit Angebot</th>
												<th style="font-size: 12px; width: 7%; text-align: left; font-family: Arial;">Zeit tatsächlich</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Zeit verrechenbar</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Betrag</th>
												<th style="font-size: 12px; width: 5%; text-align: left; font-family: Arial;">verrech.</th>
											</tr>';
	foreach ($vorstufe as $single) {
		$template .= '						<tr style="background-color: #E0E0E0;">
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["typeName"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["performanceTime"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["employeeName"]["name"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["description"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["timeProposal"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["timeReal"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["timeSettlement"].'</td>
												<td style="font-size: 12px; font-family: Arial; text-align: left;">'.$single["amount"].'</td>
												<td style="text-align: left;"><input type="checkbox"';
	if ($single["settlement"] == 1) {
		$template .= '							checked="checked"';
	}
		$template .= '							></td>
											</tr>';
	}
		$template .= '					</table>
									</div>';
	if (isset($dates['amountVorstufe'])) {
		$template .= '				<div style="text-align: right; font-family: Arial;">Vorstufenkosten gesamt: <b>'.$dates['amountVorstufe'].' EURO</b></div>';
	}
		$template .= '			</div>';
	} else {
		$template .= '			<div style="width: 98%; margin-top: 2%;">'.$dates['offer'].'</div>';
	}
	if (isset($dates["druckDescription"])) {	
		$template .= '			<div style="padding-top: 3%; font-family: Arial; margin-left: 1%;"><b>Bemerkungen - Vorstufekosten:</b></div>
								<div style="font-family: Arial; margin-left: 1%;">'.$dates['vorstufeDescription'].'</div>';
	}

	if ($drucksachen != null) {
		$template .= '			<div style="width: 98%; margin-left: 1%;">
									<div style="font-size; 20px; padding-top: 3%; font-family: Arial;"><b>DRUCKOSTEN</b></div>
									<div style="width: 100%; border: 1px solid gray; background-color: #E8E8E8;">
										<table>
											<tr style="background-color: #D0D0D0;">
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Drucksache</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Maschine</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Art</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Auflage</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Format</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Umfang</th>
												<th style="font-size: 12px; width: 12%; text-align: left; font-family: Arial;">Farbe</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Papier</th>
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Verarbeitung</th>
												<th style="font-size: 12px; width: 8%; text-align: left; font-family: Arial;">Betrag</th>
											</tr>';
	foreach ($drucksachen as $single) {
		$template .= '						<tr style="background-color: #E0E0E0;">
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["print"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["machineName"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["type"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["edition"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["format"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["size"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["color"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["paper"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["remodelling"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["amount"].'</td>
											</tr>';
	}
		$template .= '					</table>
									</div>';
	if (isset($dates['amountDrucksachen'])) {
		$template .= '				<div style="text-align: right; font-family: Arial;">Druckkosten gesamt: <b>'.$dates['amountDrucksachen'].' EURO</b></div>';
	}
	if (isset($dates["druckDescription"])) {	
		$template .= '			<div style="padding-top: 3%; font-family: Arial;"><b>Bemerkungen - Druckkosten:</b></div>
								<div style="font-family: Arial;">'.$dates["druckDescription"].'</div>';
	}
		$template .= '			</div>';
	}

	if ($fremdarbeiten != null) {
		$template .= '			<div style="width: 98%; margin-left: 1%;">
									<div style="font-size; 20px; padding-top: 3%; font-family: Arial;"><b>FREMDKOSTEN</b></div>
									<div style="width: 100%; border: 1px solid gray; background-color: #E8E8E8;">
										<table>
											<tr style="background-color: #D0D0D0;">
												<th style="font-size: 12px; width: 10%; text-align: left; font-family: Arial;">Datum</th>
												<th style="font-size: 12px; width: 24%; text-align: left; font-family: Arial;">Lieferant</th>
												<th style="font-size: 12px; width: 30%; text-align: left; font-family: Arial;">Beschreibung</th>
												<th style="font-size: 12px; width: 13%; text-align: left; font-family: Arial;">Einkaufspreis</th>
												<th style="font-size: 12px; width: 13%; text-align: left; font-family: Arial;">Verkaufspreis</th>
											</tr>';
	foreach ($fremdarbeiten as $single) {
		$template .= '						<tr style="background-color: #E0E0E0;">
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["textDate"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["deliverer"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["description"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["purchasePrice"].'</td>
												<td style="font-size: 12px; text-align: left; font-family: Arial;">'.$single["sellPrice"].'</td>
											</tr>';
	}
		$template .= '					</table>
									</div>';
	if (isset($dates['amountFremdarbeiten'])) {
		$template .= '				<div style="text-align: right; font-family: Arial;">Fremdkosten gesamt: <b>'.$dates['amountFremdarbeiten'].' EURO</b></div>';
	}								
	if (isset($dates["fremdDescription"])) {	
		$template .= '			<div style="padding-top: 3%; font-family: Arial;"><b>Bemerkungen - Fremdkosten:</b></div>
								<div style="font-family: Arial;">'.$dates["fremdDescription"].'</div>';
	}
		$template .= '			</div>';
	}

	if (isset($vorstufe) OR isset($drucksachen) OR isset($fremdarbeiten)) {
		$amountVorstufe = str_replace(',' , '', $dates['amountVorstufe']);
		$finalVorstufe = floatval($amountVorstufe) * 1.19;
		$finalVorstufe = round($finalVorstufe, 2);
		$amountDrucksachen = str_replace(',' , '', $dates['amountDrucksachen']);
		$finalDrucksachen = floatval($amountDrucksachen) * 1.19;
		$finalDrucksachen = round($finalDrucksachen, 2);
		$amountFremdarbeiten = str_replace(',' , '', $dates['amountFremdarbeiten']);
		$finalFremdarbeiten = floatval($amountFremdarbeiten) * 1.19;
		$finalFremdarbeiten = round($finalFremdarbeiten, 2);
		$finalZusammenfassung = $finalVorstufe + $finalDrucksachen + $finalFremdarbeiten;
		$finalAmount = floatval($amountVorstufe) + floatval($amountDrucksachen) + floatval($amountFremdarbeiten);
		$template .= '			<div style="margin-left: 1%; width: 98%;">
									<div style="font-size; 20px; padding-top: 3%; text-align: left; font-family: Arial;"><b>SUMMENFELD</b></div>
										<div style="width: 70%; margin-left: 15%; border: 1px solid gray; background-color: #E8E8E8;">
											<table>
												<tr>
													<th style="font-size: 14px; width: 40%; text-align: left; font-family: Arial;">Position</th>
													<th style="font-size: 14px;width: 20%; text-align: left; font-family: Arial;">Wert</th>
													<th style="font-size: 14px;width: 15%; text-align: left; font-family: Arial;">MwSt.</th>
													<th style="font-size: 14px;width: 25%; text-align: left; font-family: Arial;">Bruttobetrag</th>
												</tr>';
				if ($finalVorstufe != 0) {
					$template .= '				<tr>
													<td style="font-size: 14px;font-family: Arial;">Vorstufenkosten</td>
													<td style="font-size: 14px;font-family: Arial;">'.$dates['amountVorstufe'].'</td>
													<td style="font-size: 14px;font-family: Arial;">19%</td>
													<td style="font-size: 14px;font-family: Arial;">'.$finalVorstufe.' EURO</td>
												</tr>';
				}
				if ($finalDrucksachen != 0) {
					$template .= '				<tr>
													<td style="font-size: 14px;font-family: Arial;">Druckkosten</td>
													<td style="font-size: 14px;font-family: Arial;">'.$dates['amountDrucksachen'].'</td>
													<td style="font-size: 14px;font-family: Arial;">19%</td>
													<td style="font-size: 14px;font-family: Arial;">'.$finalDrucksachen.' EURO</td>
												</tr>';
				}
				if ($finalFremdarbeiten != 0) {
					$template .= '				<tr>
													<td style="font-size: 14px;font-family: Arial;">Fremdkosten</td>
													<td style="font-size: 14px;font-family: Arial;">'.$dates['amountFremdarbeiten'].'</td>
													<td style="font-size: 14px;font-family: Arial;">19%</td>
													<td style="font-size: 14px;font-family: Arial;">'.$finalFremdarbeiten.' EURO</td>
												</tr>';
				}
					$template .= '				<tr>
													<td style="font-size: 14px;font-family: Arial;"><b>ZUSAMMENFASSUNG:</b></td>
													<td style="font-size: 14px;font-family: Arial;"><b>'.$finalAmount.'</b></td>
													<td style="font-size: 14px;font-family: Arial;">19%</td>
													<td style="font-size: 14px;font-family: Arial;"><b>'.$finalZusammenfassung.' EURO</b></td>
												</tr>
											</table>
										</div>
									</div>';
	}

	if (isset($dates['description'])) {	
		$template .= '			<div style="padding-top: 3%; font-family: Arial; margin-left: 1%;"><b>Bemerkungen:</b></div>
								<div style="padding-bottom: 3%; font-family: Arial; margin-left: 1%;">'.$dates['description'].'</div>';
	}
		$template .= '			<div style="font-size: 14px; text-align: justify; padding-top: 2%; background-color: white; font-family: Arial;">Sämtliche Preise verstehen sich zuzüglich 19% Mehrwertsteuer (';
	if (isset($dates["skonto"])) {	
		$template .= 'zahlbar 10 Tage '.$dates["skonto"].'% Skonto, ';						
	}				
	if (isset($dates["paymentOpt"])) {	
		$template .=	$dates["paymentOpt"];
	}	
		$template .=				'). Für eventuelle Rückfragen stehen Ihnen die oben aufgeführten Ansprechpersonen
									gerne unter der genannten Durchwahl zur Verfügung. Sorgfältige und termingerechte Ausführung
									sichern wir Ihnen zu und verbleiben
								</div>
								<div style="font-size: 14px; padding-top: 2%; background-color: white; font-family: Arial;">mit freundlichen Grüßen</div>
								<div style="font-size: 14px; padding-top: 2%; background-color: white; font-family: Arial;">TV Satzstudio GmbH</div>
							</div>
						</div>
					</div>
					<div style="float: left; width: 100%; height: 10%; padding-top: 2%;">
						<div style="font-size: 14px; font-family: Arial;">* Sie als Auftraggeber sichern uns mit Auftragserteilung zu, dass Urheber- und sonstige gewerblichen</div>
						<div style="font-size: 14px; margin-left: 1.5%; padding-bottom: 2%; font-family: Arial;">Schutzrechte und – soweit erforderlich – Lizenzen der Urheber gewahrt wurden.</div>
						<div style="font-size: 12.5px; font-family: Arial;">tv satzstudio gmbh <span style="color:#00FFFF;">|</span> neidhardswinden 63 <span style="color:#00FFFF;">|</span> 91448 emskirchen</div>
						<div style="font-size: 12.5p; font-family: Arial;">geschäftsführer: peter vogler <span style="color:#00FFFF;">|</span> handelsregister fürth, hrb 1584 <span style="color:#00FFFF;">|</span> steuer-nr. 9203 18443803 <span style="color:#00FFFF;">|</span> ust-id-nr. DE 131 945 322</div>
					</div>
				</div>
			</body>
		';
		return $template;
	}
	
	public function removeFile($file) {
	    $path = $_SERVER['DOCUMENT_ROOT'].'/CRM/akte/'.$file;
	    //$path = '/home/ad9bis/domains/ad9bis.vot.pl/public_html/CRM/akte/'.$file;
	    if (file_exists($path)) { 
		chmod($path, 0777);
	        if (@unlink($path))
		{
		    return 'success';
		}
	    else
		{
		    return 'false';
		}
	    }
	}
}