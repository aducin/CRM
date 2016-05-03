<?php

class Helpers 
{
	private static $handler;
	private $dbHandler;
	private $art = array();
	private $kalkulationsfelder = array();
	private $mandant = array ('TVS', 'MW-Vogler');
	private $zahlungsziel = array();
	private $benutzerList = array();
	private $rolle = array();
	private $lieferant = array();
	private $machine;
	private $ansprechpartner = array();
	private $status = array();
	
	function __construct($dbHandler) {

		$this->dbHandler = $dbHandler;
		self::$handler = $dbHandler;

	}

	public static function getSettings($name) {
		$sql = 'SELECT value FROM Settings WHERE name = :name';
		$result = self::$handler->prepare($sql);
		$result->bindValue(':name', $name);
		if ($result->execute()) {
			$value = $result->fetch();
			$singleValue = $value['value'];
			return $singleValue;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function configDelete($table, $rowId) {
		if ($table == 'Zahlungsziel') {
			$sql = 'UPDATE '.$table.' SET active = 0 WHERE id = '.$rowId;
			$result=$this->dbHandler->prepare($sql);
		} else {
			$sql = 'UPDATE '.$table.' SET active = 0 WHERE id = '.$rowId;
			$result=$this->dbHandler->prepare($sql);
		}
		if ($result->execute()) {
		    return 'success';
		} else {
		    return 'false';
		}
	}
	
	public function configSave($object, $data) {
		$values = explode('<>', $object);
		$table = $values[0];
		$name = $values[1];
		if ($table == 'Zahlungsziel') {
		    $sql = "INSERT INTO Zahlungsziel (name, beschreibung, active) VALUES (:name, :description, 1)";
		    $result=$this->dbHandler->prepare($sql);
		    $result->bindValue(':name', $name);
		    $result->bindValue(':description', $data);
		} elseif ($table == 'Benutzer') {
		    $password = md5($data);
		    $mail = $values[2];
		    $role = $values[3];
		    $sql = "INSERT INTO Benutzer (name, mail, passwort, rolle_id, active) VALUES (:name, :mail, :password, :role, 1)";
		    $result=$this->dbHandler->prepare($sql);
		    $result->bindValue(':name', $name);
		    $result->bindValue(':mail', $mail);
		    $result->bindValue(':password', $password);
		    $result->bindValue(':role', $role);
		}
		if ($result->execute()) {
		    $id = $this->getLastId($table);
		    return $id;
		} else {
		    $error = 'Unable to save data';
		    return $error;
		}
	}
	
	public function configUpdate($object, $data) {
		$values = explode('<>', $object);
		$table = $values[0];
		$column = $values[1];
		$rowId = $values[2];
		$sql = "UPDATE ".$table." SET ".$column."= :value WHERE id = :rowId";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':value', $data);
		$result->bindValue(':rowId', $rowId);
		if ($result->execute()) {
		    return 'success';
		} else {
		    $error = 'No config change possible';
		    return $error;
		}
	}
	
	public function getAnsprechpartner() {
		$sql = 'SELECT id, name, vorname FROM Ansprechpartner';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
				$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"].' '.$singleResult["vorname"]);
			}
			$this->ansprechpartner = $list;
			return $list;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function getArt() {
		return $this->art;
	}
	
	public function getBenutzerList() {
		return $this->benutzerList;
	}
	
	public function getCompleteBenutzerList() {
		$sql = 'SELECT Benutzer.id as id, Benutzer.name as name, Rolle.name as rolle FROM Benutzer INNER JOIN Rolle ON Benutzer.rolle_id = Rolle.id WHERE active = 1';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			foreach ($result as $singleResult) {
				$list[] = array('id' => $singleResult['id'], 'name' => $singleResult["name"], 'rolle' => $singleResult["rolle"]);
			}
			return $list;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function getDocuments($projectId) {
	    $sql = 'SELECT Akte.id as id, Akte.name as document, Benutzer.name as userName, Akte.bezeichnung as description, Akte.reg_date, Akte.file 
	        FROM Akte INNER JOIN Benutzer ON Akte.mitarbeiter = Benutzer.id WHERE projectId = :projectId ORDER BY Akte.name';
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':projectId', $projectId);
	    if ($result->execute()) {
			$final = array();
			foreach ($result as $finalResult) {
			    if ($finalResult['description'] == null) {
				$finalResult['description'] = '';
			    }
			    $date = explode(' ', $finalResult['reg_date']);
			    $first = explode('-', $date[0]);
			    $second = explode(':', $date[1]);
			    $finalResult['reg_date'] = $first[2].'.'.$first[1].'.'.$first[0].' '.$second[0].':'.$second[1];
			    $final[] = array(
				'id' => $finalResult['id'],
				'documentName' => $finalResult['document'], 
				'userName' => $finalResult['userName'], 
				'date' => $finalResult['reg_date'],
				'description' => $finalResult['description'],
				'file' => $finalResult['file']
			    );
			}
			return $final;
	    } else {
			return 'false';
	    }
	}

	public function getInvoiceNumber($projectId, $file) {
		$sql = 'SELECT id FROM Rechnungsnummer WHERE projectId = :projectId AND file = :file';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':file', $file);
		if ($result->execute()) {
			$final = $result->fetch();
			return $final['id'];
		} else {
			return 'false';
		}
	}
	
	public function getKalkulationsfelder() {
		return $this->kalkulationsfelder;
	}
	
	private function getLastId($table) {
	    $sql = "SELECT id FROM ".$table." ORDER BY id DESC LIMIT 1";
	    $result=$this->dbHandler->prepare($sql);
	    if ($result->execute()) {
			$singleId = $result->fetch();
			return $singleId['id'];
	    } else {
			return 'false';
	    }
	}

	public function getLastInvoiceNumber($id) {
		$sql = 'SELECT id FROM Rechnungsnummer WHERE projectId = :id ORDER BY id DESC LIMIT 1';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$final = $result->fetch();
			return $final['id'];
		} else {
			return 'false';
		}
	}
	
	public function getMandant() {
		return $this->mandant;
	}
	
	public function getMachine() {
		return $this->machine;
	}

	public function getOfferNumber($projectId, $file) {
		$sql = 'SELECT id FROM Angebotsnummer WHERE projectId = :projectId AND file = :file';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':file', $file);
		if ($result->execute()) {
			$final = $result->fetch();
			return $final['id'];
		} else {
			return 'false';
		}
	}

	public function getOrderNumber($projectId, $file) {
		$sql = 'SELECT id FROM Auftragsnummer WHERE projectId = :projectId AND file = :file';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':file', $file);
		if ($result->execute()) {
			$final = $result->fetch();
			return $final['id'];
		} else {
			return 'false';
		}
	}
	
	public function getProjectUser($projectId) {
		$sql = 'SELECT benutzerId FROM Benutzer_Projekt WHERE projektId = :projectId ORDER BY benutzerId';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		if ($result->execute()) {
			$userList = array();
			foreach ($result as $single) {
				$userList[] = array( 'userId' => $single['benutzerId']);
			}
			return $userList;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}

	public function getProjectUserName($projectId) {
		$sql = 'SELECT Benutzer.name, Benutzer.id FROM Benutzer INNER JOIN Benutzer_Projekt ON Benutzer.id = Benutzer_Projekt.benutzerId WHERE Benutzer_Projekt.projektId = :projectId ORDER BY Benutzer_Projekt.benutzerId';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		if ($result->execute()) {
			$userList = array();
			foreach ($result as $single) {
				$userList[] = array( 'name' => $single['name'], 'id' => $single['id']);
			}
			return $userList;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
		
	}
	
	public function getSingleArt($id) {
		$sql='SELECT name FROM Art WHERE id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$name = $result->fetch();
			$singleName = $name['name'];
			return $singleName;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function getSingleBenutzer($id) {
		$sql='SELECT Benutzer.name as name, Rolle.id as rolleId, Rolle.name as rolleName FROM Benutzer INNER JOIN Rolle ON Benutzer.rolle_id = Rolle.id WHERE Benutzer.id = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$date = $result->fetch();
			$dateArray = array ('name' => $date['name'], 'rolle_id' => $date['rolleId'], 'rolle_name' => $date['rolleName']);
			return $dateArray;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
		
	}

	public function getSingleStatus($id) {
		$sql = "SELECT id, name FROM Status WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$date = $result->fetch();
			$result = array('id' => $date['id'], 'name' => $date['name']);
			return $result;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function getSingleZahlungsziel($id) {
		$sql = "SELECT name FROM Zahlungsziel WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$date = $result->fetch();
			return $date['name'];
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}

	public function getSingleZahlungszielDesc($id) {
		$sql = "SELECT beschreibung FROM Zahlungsziel WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		if ($result->execute()) {
			$date = $result->fetch();
			return $date['beschreibung'];
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function getRolle() {
		return $this->rolle;
	}

	public function getSingleSelect($name) {
		$dates = explode('-', $name);
		$table = $dates[0];
		$projectId = $dates[1];
		$sql = "SELECT name FROM ".$table." WHERE id = :id";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $projectId);
		if ($result->execute()) {
			$name = $result->fetch();
			return $name['name'];
		} else {
			return 'false';
		}
	}
	
	public function getStatusList() {
		$sql = "SELECT id, name FROM Status";
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
				$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
			}
			$this->status = $list;
			return $list;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}

	public function getAjaxZahlungsziel() {
		$sql = 'SELECT id, name, beschreibung FROM Zahlungsziel WHERE active = 1';
		$result = $this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
				$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"], 'beschreibung' => $singleResult["beschreibung"]);
			}
			echo json_encode($list);
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function getZahlungsziel() {
		$sql = 'SELECT id, name, beschreibung FROM Zahlungsziel WHERE active = 1';
		$result = $this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$ziel = array();
			foreach ($result as $singleResult) {
				$ziel[] = array("id" => $singleResult['id'], "name" => $singleResult['name'], "beschreibung" => $singleResult["beschreibung"]);
			}
			$this->zahlungsziel = $ziel;
			return $ziel;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function ifUserActive($projectId, $userId) {
		$sql = 'SELECT id FROM Benutzer_Projekt WHERE projektId = :projectId AND benutzerId = :userId';
		$result = $this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':userId', $userId);
		if ($result->execute()) {
			$final = $result->fetch();
			return $final;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function manageProjectUser($date, $projectId, $userId) {
		$insertOrDelete = $this->ifUserActive($projectId, $userId);

		if (is_array($insertOrDelete)) {
			$success = $this->projectUserDelete($projectId, $userId);
		} elseif ($insertOrDelete == false) {
			$success = $this->projectUserInsert($projectId, $userId);
		}
		return $success;
	}

	private function projectUserDelete($projectId, $userId) {
		$sql = "DELETE FROM Benutzer_Projekt WHERE projektId = :projectId AND benutzerId = :userId";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':userId', $userId);
		if ($result->execute()) {
			return 'deleted successfully';
		} else {
			return 'false';
		}
	}

	private function projectUserInsert($projectId, $userId) {
		$sql = "INSERT INTO Benutzer_Projekt (projektId, benutzerId) VALUES (:projectId, :userId)";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':userId', $userId);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}
	
	public function setArt() {
		$sql='SELECT id, name FROM Art';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$ziel = array();
			foreach ($result as $singleResult) {
				$artList[] = array("id" => $singleResult['id'], "name" => $singleResult['name']);
			}
			$this->art = $artList;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}

	public function setInvoiceNumber($projectId, $file) {
		$sql = 'INSERT INTO Rechnungsnummer (projectId, reg_date, file) VALUES (:id, NOW(), :file)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $projectId);
		$result->bindValue(':file', $file);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}

	public function setOfferNumber($projectId, $file) {
		$sql = 'INSERT INTO Angebotsnummer (projectId, reg_date, file) VALUES (:id, NOW(), :file)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $projectId);
		$result->bindValue(':file', $file);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}

	public function setOrderNumber($projectId, $file) {
		$sql = 'INSERT INTO Auftragsnummer (projectId, reg_date, file) VALUES (:id, NOW(), :file)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $projectId);
		$result->bindValue(':file', $file);
		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}
	
	public function setBenutzerList() {
		$sql='SELECT id, name FROM Benutzer WHERE active = 1';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$list = array();
			$counter = 0;
			foreach ($result as $singleResult) {
				$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"], 'counter' => $counter );
				$counter++;
			}
			$this->benutzerList = $list;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function setKalkulationsfelder() {
		$sql='SELECT id, name FROM Kalkulationsfelder';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$fields = array();
			foreach ($result as $singleResult) {
				$fields[] = array( 'id' => $singleResult["id"], 'name' => $singleResult["name"] );
			}
			$this->kalkulationsfelder = $fields;
			return $fields;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function setLieferant( $id = null ) {
		$sql='SELECT id, name FROM Lieferant';
		if (isset($id)) {
			$sql.= ' WHERE id = :id';
		}
		$result=$this->dbHandler->prepare($sql);
		if (isset($id)) {
			$result->bindValue(':id', $id);
		}
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
				$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
			}
			$this->lieferant = $list;
			return $list;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function setMachine() {
		$sql='SELECT id, name FROM Maschine';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
				$list[] = array('id'=>$singleResult['id'], 'name'=>$singleResult["name"]);
			}
			$this->machine = $list;
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public function setRolle($return = null) {
		$sql='SELECT id, name FROM Rolle';
		$result=$this->dbHandler->prepare($sql);
		if ($result->execute()) {
			$list = array();
			foreach ($result as $singleResult) {
				$list[] = array('id' => $singleResult['id'], 'name' => $singleResult["name"]);
			}
			$this->rolle = $list;
			if (isset($return) && $return = 'true') {
				return $list;
			}
		} else {
			$output = new OutputController($dbHandler);
			$this->output->displayPhpError();
		}
	}
	
	public static function settings($dbHandler, $row) {
		$sql = "SELECT value FROM Settings WHERE name = '".$row."'";
		$result = $dbHandler->prepare($sql);
		if ($result->execute()) {
			$final = $result->fetch();
			return $final['value'];
		} else {
			return 'false';
		}
	}
	
	public function standardText($text) {
	      $sql = 'UPDATE Settings SET value = :text WHERE name = "standardText"';
	      $result=$this->dbHandler->prepare($sql);
	      $result->bindValue(':text', $text);
	      if ($result->execute()) {
		    return 'success';
	      } else {
		    $error = 'No text change possible';
		    return $error;
	      }
	}
}
