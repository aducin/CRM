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
		$this->path = $_SERVER['DOCUMENT_ROOT']."/CRM";
		if( isset($_POST['concrete'] )) {
			$action = $_POST['concrete'];
			$value = $_POST['value'];
		} elseif ( isset($_GET['concrete'] )) {
			$action = $_GET['concrete'];
			$value = $_GET['value'];
		}
		$this->$action($value);
	}

	private function addressSearch($value) {
		$this->searchResult('rechnungsadresse', $value);
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

    private function clientSearch($value) {
    	$this->searchResult('auftraggeber', $value);
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
		$url = Helpers::getSettings('second host').'index.php?token='.$result['token'];
		$subject = 'Passwort ändern';
		$message = "=?UTF-8?B?".base64_encode("Passwort ändern")."?=";
		$message ="";
		$message.='<p>Hallo '.$name.',</p>';
		$message.='<p>Klicken Sie bitte hier: <a href = "'.$url.'">'.$url.'</a> um Ihr Passwort zu ändern.</p>';
		$message.='<p>Mit Grüßen,<br />
		    '.Helpers::getSettings('portal_name', $dbHandler).' Team</p>';
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

    private function getDbHandler() {
        return $this->dbHandler;
    }

    private function getToTheProject($value) {
    	$this->project = $this->creator->createProduct('projekt', $value);
    	$this->project->setDates();
    	$this->user = $this->creator->createProduct('benutzer', $_SESSION['user']);
    	$this->user->setData();
    	unset ($this->project->benutzer);
    	echo 'stopped in ajaxController -- ';
    	var_dump($this->project); exit();
    }

    private function searchResult($name, $value) {
    	$object = $this->creator->createProduct($name);
    	$result = $object->searchByName($value);
    	if ($result == null) {
    		$error = 'Nichts gedunden!';
    		$data = array(
    			'success' => 'false', 
    			'name' => $error
    		);
			echo json_encode($data);
    	} else {
    		echo json_encode($result);
    	}
    }
}