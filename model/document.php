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
		date_default_timezone_set('Germany/Berlin');
		$date = date('d.m.Y', time());
		$template = '
			<div style="height: 92%;">
			<div>
			<div style="width: 60%; height: 20%; float: left;">
			<div style="height: 9%;"></div>
			<table>
			<tr>
			<td><h6>tv satzstudio gmbh</h6></td>
			<td style="color:#00FFFF;">|</td>
			<td><h6>neidhardswinden 63</h6></td>
			<td style="color:#00FFFF;">|</td>
			<td><h6>91448 Emskirchen</h6></td>
			</tr>
			</table>
			<div style="margin-top: 5%; text-align: left;">
			'.$address.'
			</div>
			</div>
			<div style="width: 39%; height: 20%; float: left; background-color:grey;">
			</div>
			<div style="text-aign: right;margin-left:80%; margin-top: 3%;">'.$date.'</div>
			<div style="height: 5%;"></div>
			<div>
				<h2>LIEFERSCHEIN:</h2>
				<table>
					<tr style="margin-top:5%;">
						<td>Kunderauftragsnummer:
						</td>
						<td>'.$clientNumber.'</td>
					</tr>
					<tr>
					<td></td>
					</tr>
					<tr>
						<td>Auftragsnummer:
						</td>
						<td></td>
					</tr>
					<tr>
					<td></td>
					</tr>
					<tr>
						<td>Projektname:
						</td>
						<td><b>'.$projectName.'</b></td>
					</tr>
				</table>
			</div>
		';
		if ($text) {
			$template .= '
				<div style="height: 5%;"></div>
				<h3>Lieferschein Positionstext:</h3>
				<div>'.$text.'</div>
			';
		}
		if($description) {
			$template .= '
				<div style="height: 5%;"></div>
				<h3>Bemerkungen:</h3>
			';
		  foreach ($description as $single) {
			  $template .= '
				  <div style="margin-top: -2%;">'.$single.'</div><br>
			  ';
		  }
		}
		$template .= '
			<div style="height: 6%;"></div>
			<div><b>Datum: ______________________________  Unterschrift Kunde: ____________________________________</b></div>

			</div>
			</div>
			<div style="height: 8%;">
			<table>
			<tr>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;"><div>sparkasse emskirchen 240 003 756 (762 510 20) 
				<span style="color:#00FFFF;"> | </span> IBAN: DE79 7625 1020 0240 0037 56, BIC: BYLADEM1NEA</td>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">geschäftsführer: peter vogler
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;">hypobank langenzenn 35 90 263 744 (762 200 73)
				<span style="color:#00FFFF;"> | </span>  IBAN: DE75 7622 0073 3590 2637 44, BIC: HYVEDEMM419</td>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">handelsregister fürth, HRB 1584
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;">vr-bank neustadt 331 643 (760 695 59)
				<span style="color:#00FFFF;"> | </span> IBAN: DE38 7606 9559 0000 3316 43, BIC: GENODEF1NEA</td>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">steuer-nr. 9203 18443803
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;">cvw-privatbank wilhermsdorf 40 08 839 (762 119 00)
				<span style="color:#00FFFF;"> | </span> IBAN: DE14 7621 1900 0004 0088 39, BIC: GENODEF1WHD</td>
			</tr>
			<tr>
				<td style="width: 200px; height: 10%; font-size: 10px;">ust-id-nr.DE 131 945 322
				</td>
				<td style="width: 620px; height: 10%; font-size: 10px;"><b>wochenblatt-konto:</b> sparkasse emskirchen 227 073 782 (762 510 20)
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

	public function innerForm($client, $userList) {
		$template = '
			<body>
			<div class="container-name">
			<div class="div1" style="width: 67%; height: 99.9%; margin:5px; float:left;">
				<div class="div1" style="width: 90%; margin-top: -7%;;border: 1px solid gray; margin-left:5%; float:left;">
					<p style="text-aling: center; font-size:44px; font-weight: bolder">AUFTRAGSZETTEL</p>
				</div>
				<div class="div1" style="width: 94%;border: 1px solid gray; margin-top: -5%; margin-left:3%; float:left;">
					<div style="width: 40%; float:left; font-size:15px;"><div>Auftraggeber/</div><div>Sachbearbeiter</div></div>
					<div style="width: 59%; float:left; font-size: 18px; border-bottom: 1px dotted;">'.$client['name'].'</div>
					<div style="width: 40%; height: 10%; float:left;">
					  <div style="font-size: 15px; margin-top: 2.5%;">Rechnungsadresse</div>
					  <div style="font-size: 15px; margin-top: 19%;">Kundennummer</div>
					  <div style="font-size: 15px;">'.$client['clientNumber'].'</div>
					</div>
					<div style="width: 59%; height: 10%; float:left;">
					  <div style="font-size: 18px; border-bottom: 1px dotted;">'.$client['addressName'].'</div>
					  <div style="font-size: 18px; border-bottom: 1px dotted;">'.$client['addressDepartment'].'</div>
					  <div style="font-size: 18px; border-bottom: 1px dotted;">'.$client['address'].'</div>';
		if ($client['address2']) {
		    $template .= '<div style="font-size: 18px; border-bottom: dotted;">'.$client['address2'].'</div>';
		} ;
		$template .=	'
					  <div style="font-size: 18px; border-bottom: 1px dotted;">'.$client['addressPlace'].'</div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px;">
					  Projektname
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					  <div style="font-size: 18px; border-bottom: 1px dotted;">
						'.$client['projectName'].'
					    </div>
					</div>
					<div style="width: 40%; height: 3%;float:left; font-size: 15px;">
					  Kundenauftragsnummer
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					    <div style="font-size: 18px; border-bottom: 1px dotted;">
						'.$client['orderNumber'].'
					    </div>
					</div>
					<div style="float:left; background-color: black; color: white; width: 30%; height: 3%; font-size: 22px;">
					  DRUCK
					</div>
					<div style="margin-top: 0.2%; float:left; width: 69.8%; height: 3%; font-size: 22px; border-bottom: 2px solid black;">
					</div>
					<div style="width: 100%; height: 25%; border: 1px solid gray;">
					</div>
					<div style="width: 40%; height: 4%; float:left; font-size: 15px;">
					  Lieferung per
					</div>
					<div style="width: 55%; height: 4%; float:left;">
					    <div style="font-size: 15px; padding-bottom: 4%">';
					    foreach ($client['carrierList'] as $single) {
			$template .=		 '<input type="radio"';
			if ($single['name'] == $client['deliverer']) {
			    $template .= 	' checked="checked" ';
			}
			$template .=		 '>'. $single["name"].' ';   
					    }
			$template .=	'</div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px;">
					  Lieferung an
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					    <div style="font-size: 15px;">';
			if (isset($client['anotherDelivery'])) {
			    $template .= '<div style="padding-bottom: 4%; border-bottom: 1px dotted;">'.$client['anotherDelivery'].'</div>';
			} else {
			    $template .= '<div style="border-bottom: 1px dotted;">'.$client['deliveryName'].'</div>
					  <div style="border-bottom: 1px dotted;">'.$client['deliveryAddress'].'</div>';
			if (isset($client['deliveryAddress2'])) {
			    $template .= '<div style="border-bottom: 1px dotted;">'.$client['deliveryAddress2'].'</div>';
			}
			$template .=    '<div style="border-bottom: 1px dotted;">'.$client['deliveryPlace'].'</div>';
			}
			$template .=	   '</div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px; padding-top: 3%;">
					  Muster in Tasche
					</div>
					<div style="width: 59%; height: 3%; float:left; padding-top: 3%;">
					    <div style="font-size: 15px; border-bottom: 1px dotted;">
						'.$client['pattern'].'
					    </div>
					</div>
					<div style="width: 40%; height: 3%; float:left; font-size: 15px;">
					  Stück Muster an
					</div>
					<div style="width: 59%; height: 3%; float:left;">
					    <div style="font-size: 15px; border-bottom: 1px dotted;">
						'.$client['patternTo'].'
					    </div>
					</div>
					<div style="width: 100%; height: 10%; border: 2px solid black; float:left; font-size: 15px;">'.$client['internDesc'].'</div>
				</div>
			</div>
			<div class="div2" style="width: 30%; height: 97%; background-color: #C0C0C0; margin:10px; float:left; margin :10px;">
				<div class="div2" style="float: left; width: 100%; height: 48%;">
				<div style="width: 100%; height: 20%; background-color: gray;"></div>
				    <div style="width: 100%; height: 30%; background-color: #C0C0C0;">
					<div style="padding-bottom: 3%;">
					    <div style="float: left; font-size: 18px; width: 49%; margin-left: 5%;">
						<input type="radio"> Angebot
					    </div>
					    <div style="float: left; font-size: 18px; width: 49%; margin-left: -3.5%;">
						<input type="radio"> Auftrag
					    </div>
					</div>
					<div style="padding-bottom: 3%; margin-left: 5%;">Eingang: '.$client['startDate'].'</div>
					<div style="padding-bottom: 3%;">Auftr. nr. TV</div>
					<div>Termin Korrektur</div>
					<div style="padding-bottom: 3%;">'.$client['projectDates'][0].'</div>
					<div>Termin Daten</div>
					<div style="padding-bottom: 3%;">'.$client['projectDates'][1].'</div>
					<div>Termin Proof / Andruck</div>
					<div style="padding-bottom: 3%;">'.$client['projectDates'][2].'</div>
					<div>Termin Andruck</div>
					<div style="padding-bottom: 3%;">'.$client['projectDates'][3].'</div>
					<div>Termin Lieferung</div>
					<div>'.$client['deliveryTime'].'</div>
				    </div>
				</div>
				<div class="div2" style="float: left; width: 100%; height: 1%; background-color: black;"></div>
				<div class="div2" style="margin-left: 10%; margin-top: 10%; float: left; width: 100%; height: 48%;">
				<h3>Bearbeiten von:</h3>';
		foreach ($userList as $single) {
			$template .=	'<div>
			    <input type="checkbox" checked="checked">        '.$single['name'].' - '.$single['id'].'
			</div>';
		}
		$template .=	'</div>
			</div>
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