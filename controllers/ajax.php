<?php

class Ajax
{
	private $dbHandler;
	private $creator;
	private $project;
	private $user;
	private $path;

	public function __construct($dbHandler, $post) {
		$this->dbHandler = $dbHandler;
		$this->creator = new TvsatzCreator($dbHandler);
		$suffix = Helpers::getSettings('suffix');
		if ($suffix != '' ) {
		    $this->path = $_SERVER['DOCUMENT_ROOT'].'/'.$suffix;
		} else {
		    $this->path = $_SERVER['DOCUMENT_ROOT'].'/';
		}
		if( isset($_POST['concrete'] )) {
			$action = $_POST['concrete'];
			$value = $_POST['value'];
			if (isset($_POST['singleAction'])) {
			    $single = $_POST['singleAction'];
			    if(!isset($_POST['object'])) {
				$this->$action($single, $value);
			    }
			    $object = $_POST['object'];
			    $this->$action($single, $object, $value);
			    exit();
			}
		} elseif ( isset($_GET['concrete'] )) {
			$action = $_GET['concrete'];
			$value = $_GET['value'];
		}
		$this->$action($value);
	}

    private function addressSearch($value) {
	$this->searchResult('rechnungsadresse', $value);
    }
    
    private function addressSearchByName($value) {
	$object = $this->creator->createProduct('rechnungsadresse');
    	$result = $object->searchByChosenName($value);
    	echo $result;
    }

    private function amount($value) {
    	$table = explode('-', $value);
    	$table = $table[0];
    	$object = $this->creator->createProduct($table);
    	$value = $object->getTotalAmount($value);
    	echo $value;
    }

    private function changePassword($value) {
    	if ($value[1] != $value[2]) {
    		$error = 'Beide Passwörter sind nicht wesensgleich!';
    		$data = array(
    			'success' => 'false',
    			'name' => $error
    		);
			echo json_encode($data);
    	} else {
    		$password = md5($value[1]);
    		$this->user = $this->creator->createProduct('benutzer');
    		$user = $this->user->checkByToken( $value[3], $value[0] );
    		$this->user->saveNewPassword($user['id'], $password);
		$data = array(
	      		'success' => 'true',
	      		'name' => $user['name']
	   		);
    		echo json_encode($data);
    	}
    }
    
    public function clientAddressData($value) {
	$client = $this->creator->createProduct('rechnungsadresse');
	$result = $client->getAllRechnungsadressen($value) ;
	if (is_array($result)) {
	    echo json_encode($result);
	} else {
	    echo 'false';
	}
    }
    
    public function clientData($value) {
	$client = $this->creator->createProduct('auftraggeber');
	$result = $client->getAjaxDates($value);
	echo $result;
    }
    
    public function clientUserData($value) {
	$client = $this->creator->createProduct('ansprechpartner');
	$result = $client->getAllAnsprechpartner($value);
	if (is_array($result)) {
	    echo json_encode($result);
	} else {
	    echo 'false';
	}
    }
    
    private function clientOption($value, $dates) {
	$explode = explode('<>', $dates);
	$table = $explode[0];
	$column = $explode[1];
	$rowId = $explode[2];
	$object = $this->creator->createProduct($table);
	$success = $object->updateRow($column, $rowId, $value);
	echo $success; exit();
    }

    private function clientSearch($value) {
    	$this->searchResult('auftraggeber', $value);
    }
    
    private function clientSearchByName($value) {
    	$this->searchResult('auftraggeber', $value, 'single');
    }
    
    private function config($single, $object, $value) {
	$helper = $this->creator->createProduct('helpers');
	$result = $helper->$single($object, $value);
	echo $result;
    }
  
    private function dates($date, $path) {
        if (is_array($path)) {
            $values = $path;
        } else {
            $values = explode('<>', $path);
            $insert = explode('/', $date);
            if(isset($insert[1])) {
                $date = $insert[2].'-'.$insert[1].'-'.$insert[0];
            } else {
                $date = $insert[0];
            }
        }
        if ($path[0] == 'deliveryTime') {
	  $insert = explode('/', $date);
	  $date = $insert[2].'-'.$insert[1].'-'.$insert[0];
        }
        $column = $values[0];
        $projectId = $values[1];
        $project = $this->creator->createProduct('projekt');
        if ($column == 'Rechnungsadressen') {
            $object = $this->creator->createProduct('rechnungsadresse');
            $success = $object->$date($values);
        } elseif ($column == 'Ansprechpartner') {
            $object = $this->creator->createProduct('ansprechpartner');
            $success = $object->$date($values);
        } elseif ($column == 'Project_Calculation') {
            $object = $this->creator->createProduct('calculation');
            $success = $object->$date($values);
        } elseif ($column == 'user') {
            $object = $this->creator->createProduct('helpers');
            $success = $object->manageProjectUser($date, $projectId, $values[2]);
        } else {
           $success = $project->updateDate($projectId, $column, $date);
        }
        if ($success == 'success') {
            $project->updateDateChange($projectId);
        }
        echo $success; exit();
    }
    
    private function description($value) {
    	$bemerkung = $this->creator->createProduct('bemerkung');
    	$action = $bemerkung->update($value);
    	echo $action;
    }
    
    private function employeeSearchByName($value) {
    	$this->searchResult('ansprechpartner', $value, 'single');
    }

    private function employeeSearch($value) {
    	$this->searchResult('ansprechpartner', $value);
    }

    private function forgottenPassword($value) {
	    $this->user = $this->creator->createProduct('benutzer');
	    $result = $this->user->getBenutzerByMail($value);
	    if ($result['success'] == 'true') {
		$email = $value[0];
		$name = $result['name'];
		require($this->path."/vendor/phpmailer/class.phpmailer.php");
		require_once($this->path."/vendor/phpmailer/class.smtp.php");
		$dbHandler = $this->getDbHandler();
		$url = Helpers::getSettings('secondHost').'index.php?token='.$result['token'];
		$subject = 'Passwort ändern';
		$message = "=?UTF-8?B?".base64_encode("Passwort ändern")."?=";
		$message ="";
		$message.='<p>Hallo '.$name.',</p>';
		$message.='<p>Klicken Sie bitte hier: <a href = "'.$url.'">'.$url.'</a> um Ihr Passwort zu ändern.</p>';
		$message.='<p>Mit Grüßen,<br />
		    '.Helpers::getSettings('portal_name').' Team</p>';
		$message.='<p>Dieser Bericht wurde automatisch erstellt. Sie müssen darauf nicht antworten.</p>';
		
		$this->headers = 'MIME-Version: 1.0' . "\r\n";
		$this->headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$this->headers .= 'From: ad9bis@gmail.com' . "\r\n";
		$this->to = $email;
		$this->message = $message;
		$this->subject = $subject;
		if(mail($this->to, $this->subject, $this->message, $this->headers)) {
		    $data = array('success' => 'true', 'name' => $name);
		} else {
		    $error = 'E-mail konnte nicht geschickt werden!';
		    $data = array('success' => 'false', 'name' => $error);
		}
		/*
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->PluginDir = $this->path."/vendor/phpmailer/";
		$mail->From = Helpers::getSettings('smtp_user');
		$mail->FromName = Helpers::getSettings('portal_name');
		$mail->Host = Helpers::getSettings('smtp_host');
		$mail->Mailer = "smtp";
		$mail->Username =  Helpers::getSettings('smtp_user');
		$mail->Password = Helpers::getSettings('smtp_password');
		$mail->SMTPAuth = true;
		$mail->SetLanguage("de", $this->path."/vendor/phpmailer/language/");
		$mail->IsHTML(true);
		$mail->IsSMTP();
		$mail->SMTPDebug  = 0; 
		$mail->Subject = $subject;
		$mail->AddAddress($email);
		$mail->Body = $message;

		if ($mail->Send()) {
		    $data = array('success' => 'true', 'name' => $name);
		} else {
		    $error = 'E-mail konnte nicht geschickt werden!';
		    $data = array('success' => 'false', 'name' => $error);
		}
		*/
	    } else {
		$error = 'Email konnte nicht gefunden werden!';
		$data = array('success' => 'false', 'name' => $error);
	    }
	    echo json_encode($data);
    }
    
    private function getClientDetails($value) {
        $auftraggeber = $this->creator->createProduct('auftraggeber');
        $result = $auftraggeber->getClientDetails($value);
    }

    private function getDbHandler() {
        return $this->dbHandler;
    }

    private function getDeliveryAddress($id) {
        $auftraggeber = $this->creator->createProduct('auftraggeber');
        $result = $auftraggeber->getDeliveryAddress($id);
        if (is_array($result)) {
            $final = $result["name"].': '.$result["department"].' - '.$result["address"].' '.$result["address2"].', '.$result["code"].' '.$result["place"];
            echo $final;
        } else {
            echo 'false';
        }
    }
    
    private function getDocuments($projectId) {
    	$helpers = $this->creator->createProduct('helpers');
    	$data = $helpers->getDocuments($projectId);
    	echo json_encode($data);
    }

    private function getPaymentOption() {
        $helpers = $this->creator->createProduct('helpers');
        $helpers->getAjaxZahlungsziel();
    }
    
    private function mailCheck($value) {
	$dates = explode('<>', $value);
	$model = $dates[0];
	$pattern = $dates[1];
	$object = $this->creator->createProduct( $model );
	$result = $object->emailCheck($pattern);
    }
    
    private function newClient($value) {
        $client = $this->creator->createProduct( 'auftraggeber' );
        $success = $client->setCustomDates($value);
        echo $success;
    }

    private function row($value) {
    	$task = explode('-', $value);
    	$action = $task[0];
    	$table = $task[1];
    	$data = $task[2];
    	$object = $this->creator->createProduct( $table );
    	if ($action == 'delete') {
    		$success = $object->deleteSql( $data );
    		echo $success;
    	} elseif ($action == 'insert') {
    		$success = $object->insertSql( $data );
    		echo $success;
    	}
    }

    private function rowClone($value) {
		$object = $this->creator->createProduct('Drucksache');
		$data = $object->getById($value);
		$object->save($data[0]);
		$result = $object->getLastId($data[0]);
	    	echo $result;
    }

    private function searchResult($name, $value, $single = null) {
        $object = $this->creator->createProduct($name);
        $result = $object->searchByName($value);
        if (isset($single)) {
            $result = $object->searchByUniqueName($value);
        } else {
            $result = $object->searchByName($value);
        }
        if ($result == null) {
            $error = 'Nichts gedunden!';
            $data = array(
                'success' => 'false', 
                'name' => $error
            );
            echo json_encode($data);
        } elseif ($single == 'single') {
            echo $result['id'];
        } else {
            echo json_encode($result);
        }
    }

    private function select($value) {
    	$table = explode('-', $value);
    	$table = $table[0];
    	$helper = $this->creator->createProduct('helpers');
    	$value = $helper->getSingleSelect($value);
    	echo $value;
    }

    public function tableUpdate($value) {
    	$values = explode('<>', $value);
    	$origin = $values[0];
    	$rowId = $values[1];
    	$column = $values[2];
    	$value = $values[3];
    	if ($column == 'textDate' || $column == 'performanceTime') {
    		$value = str_replace('/', '-', $value);
    	}
    	$table = $this->creator->createProduct($origin);
    	$result = $table->row($origin, $rowId, $column, $value);
    	echo $result;
    }
}