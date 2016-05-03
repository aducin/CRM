<?php

class OutputController
{
    private $twig;
    private $dbHandler;
    private $creator;
    private $helper;
    private $user;
    private $path;
    private $root;
    private $employees;
    private $mandant;
    private $error;

	public function __construct($dbHandler = null) {
    
	$suffix = explode('/', $_SERVER["PHP_SELF"]);
	if ($suffix[1] != 'index.php') {
	    $suffix = '/'.$suffix[1];
	} else {
	$suffix = '';
	}
        $root_dir = $_SERVER['DOCUMENT_ROOT'].$suffix;
        $vendor_dir = $root_dir.'/vendor';
        $cache_dir = $root_dir.'/cache';
        $templates_dir = $root_dir.'/view/templates';
        $twig_lib = $vendor_dir.'/twig/lib/Twig';
        require_once $twig_lib . '/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($templates_dir);
        $this->twig = new Twig_Environment($loader, array(
            'cache' => $cache_dir,
        ));
        $this->error = 'Es ist leider ein Fehler aufgetreten. Versuchen Sie bitte später.';
        $this->path = ('http://'.$_SERVER["HTTP_HOST"]).$suffix.'/view/';
        $this->root = ('http://'.$_SERVER["HTTP_HOST"]).$suffix;
        if (isset($dbHandler)) {
            $this->dbHandler = $dbHandler;
	        $this->creator = new TvsatzCreator($dbHandler);
	        $this->helper = $this->creator->createProduct('helpers');
            $this->helper->setBenutzerList();
            $this->employees = $this->helper->getBenutzerList();
            $this->mandant = $this->helper->getMandant();
        } else {
            $this->displayError($this->twig, $this->path, $error = null);
        }
    }

    public static function displayError($twig, $path, $error = null) {
        if (!isset($error)) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']);
        }
        $output = $twig->render('/error.html', array(
            'message' => $error,
            'path' => $path
        ));
        echo $output;
        exit();
    }
    
    public function displayPhpError() {
        $output = $this->twig->render('/error.html', array(
            'message' => $this->error,
            'path' => $this->path,
            'root' => $this->root,
            'back' => true
        ));
        echo $output;
        exit();
    }

    public function render404() {
        include ('view/templates/404.html');
    }

    public function renderConfigSite($paymentOptions, $role, $users) {
        $text = Helpers::getSettings('standardText');
        $output = $this->twig->render('/config.html', array(
            'root' => $this->root,
            'path' => $this->path,
            'paymentOptions' => $paymentOptions,
            'role' => $role,
            'text' => $text,
            'users' => $users
        ));
    echo $output;
    exit();
    }

    public function renderErfassung($user, $project = null) {
        $this->user = $user;
        $userFunction = $this->user->getRolle();
        $carrierList = $this->helper->setLieferant();
        $paymentOpt = $this->helper->getZahlungsziel();
        $status = $this->helper->getStatusList();
        $calculationField = $this->helper->setKalkulationsfelder();
        $this->helper->setArt();
        $art = $this->helper->getArt();
        $this->helper->setMachine();
        $machine = $this->helper->getMachine();
        if ($project != null) {
           $id = $project->getId();
           if ($id == null) {
                $address = 'Location: '.$this->root.'/Erfassung';
                header( $address );
           }
        }
        if (isset($id) && $id != null) {
           $projectName = $project->getName();
            if ($projectName == null) {
                $address = 'Location: '.$this->root.'/Erfassung';
                header( $address );
            }
        }
        if ($project) {
            if($project->getId() == null) {
                unset( $project );
            }
        }
        if ($project) {
            $projectId = $project->getId();
            $individual = $project->getIndividuals();
            $invoiceNumber = $this->helper->getLastInvoiceNumber($projectId);
            if ($invoiceNumber == 'false') {
                unset($invoiceNumber);
            }
            $userList = $this->helper->getProjectUser($projectId);
            foreach ($this->employees as $single) {
                foreach ($userList as $singleUser) {
                    if ($single['id'] == $singleUser['userId']) {
                        $this->employees[$single['counter']]['checked'] = 1;
                    }
                }
            }
            $bemerkung = $project->setBemerkung();
            $customer = $project->auftraggeber->getDates();
            $sellerList = $project->ansprechpartner->getAllAnsprechpartner($customer['id']);
            $addressList = $project->rechnungsadresse->getAllRechnungsadressen($customer['id']);
            $singleAddress = $project->rechnungsadresse->selectDates();
            $printDesc = $project->getPrintDesc();
            $seller = $project->ansprechpartner->getDates();
            $address = $project->rechnungsadresse->getDates();
            $patterns = $project->getPatterns();
            $carrier = $project->getLieferant();
            $vorstufe = $project->getVorstufe();
            $time = explode("/", $vorstufe[1]);
            $vorstufe[1] = $time[1].'/'.$time[0].'/'.$time[2];
            $time = explode("/", $vorstufe[2]);
            $vorstufe[2] = $time[1].'/'.$time[0].'/'.$time[2];
            $time = explode("/", $vorstufe[3]);
            $vorstufe[3] = $time[1].'/'.$time[0].'/'.$time[2];
            $amount = 0;
            $counter = 0;
            if (isset($vorstufe[0])) {
    	        foreach($vorstufe[0] as $singleRow) {
    		        $amount += $singleRow['amount'];;
                    $textDate = explode('.', $singleRow["performanceTime"]);
                    $vorstufe[0][$counter]["performanceTime"] = $textDate[0].'/'.$textDate[1].'/'.$textDate[2];
                    $vorstufe[0][$counter]["performanceTime2"] = $textDate[1].'/'.$textDate[0].'/'.$textDate[2];
                    $vorstufe[0][$counter]["amount"] = number_format($vorstufe[0][$counter]["amount"], 2);
                    $counter++;
    	        }
            }
            $amountVorstufe = number_format($amount, 2);
            $drucksachen = $project->getDrucksachen();
            $time = explode("/", $drucksachen[1]);
            $drucksachen[1] = $time[1].'/'.$time[0].'/'.$time[2];
            $amount = 0;
            $counter = 0;
            if (isset($drucksachen[0])) {
	            foreach($drucksachen[0] as $singleRow) {
		            $amount += $singleRow['amount'];
                    $drucksachen[0][$counter]["amount"] = number_format($drucksachen[0][$counter]["amount"], 2);
                    $counter++;
	            }
            }
            $amountDrucksachen = number_format($amount, 2);
            $fremdarbeiten = $project->getFremdsache();
            $time = explode("/", $fremdarbeiten[1]);
            $fremdarbeiten[1] = $time[1].'/'.$time[0].'/'.$time[2];
            $time = explode("/", $fremdarbeiten[2]);
            $fremdarbeiten[2] = $time[1].'/'.$time[0].'/'.$time[2];
            $amount = 0;
            if (isset($fremdarbeiten[0])) {
                $counter = 0;
	            foreach($fremdarbeiten[0] as $singleRow) {
		            $amount += $singleRow['sellPrice'];
                    if ($singleRow['textDate'] != '') {
                        $textDate = explode('-', $singleRow['textDate']);
                        $fremdarbeiten[0][$counter]['textDate'] = $textDate[2].'/'.$textDate[1].'/'.$textDate[0];
                        $fremdarbeiten[0][$counter]['textDate2'] = $textDate[1].'/'.$textDate[2].'/'.$textDate[0];
                        $fremdarbeiten[0][$counter]["purchasePrice"] = number_format($fremdarbeiten[0][$counter]["purchasePrice"], 2);
                        $fremdarbeiten[0][$counter]["sellPrice"] = number_format($fremdarbeiten[0][$counter]["sellPrice"], 2);
                    }       
                    $counter++;
	            }
            }
            $amountFremdarbeiten = number_format($amount, 2);
            $regDate = explode(' ', $project->getRegDate());
            $projectStatus = $project->getStatus();
            $deliveryTime = $project->getDeliveryTime();
            $time = explode('-', $deliveryTime);
            $deliveryTime = $time[1].'/'.$time[2].'/'.$time[0];
            $regDate = explode(' ', $project->getRegDate());
            $exploded = explode('-', $regDate[0]);
            $regDate = $exploded[2].'/'.$exploded[1].'/'.$exploded[0];
            $changeDate = explode('-', $project->getChangeDate());
            if ($changeDate[0] != '') {
		$changeDate = $changeDate[2].'/'.$changeDate[1].'/'.$changeDate[0];
            } else {
		$changeDate = null;
            }
            $calculation = $this->creator->createProduct('calculation');
            $calcTable = $calculation->getDates($projectId);
            $counter = 0;
            $firstTable = array();
            $secondTable = array();
            $thirdTable = array();
            $fourthTable = array();
            foreach ($calculationField as $singleRow) {
    		    $field = 'checkbox'.$singleRow['id'];
    		    $calculationField[$counter]['checkbox'] = $calcTable[$field];	  
    		    $firstTime = 'zeit_1_'.$singleRow['id'];
    		    $firstAmount = 'preis_1_'.$singleRow['id'];
    		    $secondTime = 'zeit_2_'.$singleRow['id'];
    		    $secondAmount = 'preis_2_'.$singleRow['id'];
    		    $thirdTime = 'zeit_3_'.$singleRow['id'];
    		    $thirdAmount = 'preis_3_'.$singleRow['id'];
    		    $fourthTime = 'zeit_4_'.$singleRow['id'];
    		    $fourthAmount = 'preis_4_'.$singleRow['id'];
                if ($calcTable[$firstAmount] == null) {
                    $firstTable[] = array('timeId' => $firstTime, 'time' => $calcTable[$firstTime], 'amountId' => $firstAmount, 'amount' => $calcTable[$firstAmount]);
                } else {
                    $firstTable[] = array('timeId' => $firstTime, 'time' => $calcTable[$firstTime], 'amountId' => $firstAmount, 'amount' => number_format( $calcTable[$firstAmount], 2 ));
                }
                if ($calcTable[$secondAmount] == null) {
                    $secondTable[] = array('timeId' => $secondTime, 'time' => $calcTable[$secondTime], 'amountId' => $secondAmount, 'amount' => $calcTable[$secondAmount]);
                } else {
                    $secondTable[] = array('timeId' => $secondTime, 'time' => $calcTable[$secondTime], 'amountId' => $secondAmount, 'amount' => number_format( $calcTable[$secondAmount], 2 ));
                }
                if ($calcTable[$thirdAmount] == null) {
        		    $thirdTable[] = array('timeId' => $thirdTime, 'time' => $calcTable[$thirdTime], 'amountId' => $thirdAmount, 'amount' => $calcTable[$thirdAmount]);
                } else {
                    $thirdTable[] = array('timeId' => $thirdTime, 'time' => $calcTable[$thirdTime], 'amountId' => $thirdAmount, 'amount' => number_format( $calcTable[$thirdAmount], 2 ));
                }
                if ($calcTable[$fourthAmount] == null) {
        		    $fourthTable[] = array('timeId' => $fourthTime, 'time' => $calcTable[$fourthTime], 'amountId' => $fourthAmount, 'amount' => $calcTable[$fourthAmount]);
                } else {
                    $fourthTable[] = array('timeId' => $fourthTime, 'time' => $calcTable[$fourthTime], 'amountId' => $fourthAmount, 'amount' => number_format( $calcTable[$fourthAmount], 2 ));
                }
    		    $counter++;
            }
            $firstCount = 0;
            $secondCount = 0;
            $thirdCount = 0;
            $fourthTime = 0;
            $calcCount = array();
            foreach ($firstTable as $table) {
		        $calcCount[1] = $calcCount[1] + $table['amount'];
            }
            $calcCount[1] = number_format( $calcCount[1], 2 );
            foreach ($secondTable as $table) {
		        $calcCount[2] = $calcCount[2] + $table['amount'];
            }
            $calcCount[2] = number_format( $calcCount[2], 2 );
            foreach ($thirdTable as $table) {
		        $calcCount[3] = $calcCount[3] + $table['amount'];
            }
            $calcCount[3] = number_format( $calcCount[3], 2 );
            foreach ($fourthTable as $table) {
		        $calcCount[4] = $calcCount[4] + $table['amount'];
            }
            $calcCount[4] = number_format( $calcCount[4], 2 );
            $vorgang = $project->getVorgangsnummer();
            if ($vorgang == null) {
                $vorgang = 'false';
            }
            $dates = array(
                'name' => $project->getName(),
                'regDate' => $regDate,
                'changeDate' => $changeDate,
                'kundenNummer' => $project->getKundenautragsnummer(),
                'mandantSelect' => $project->getMandantSelect(),
                'projectId' => $projectId,
                'amountVorstufe' => $amountVorstufe,
                'amountDrucksachen' => $amountDrucksachen,
                'amountFremdarbeiten' => $amountFremdarbeiten,
                'projectStatus' => $projectStatus,
                'deliveryTime' => $deliveryTime,
                'bemerkung' => $bemerkung,
                'delivery' => $project->getDeliveryConditions(),
                'calcTitle' => $project->getCalculationTitles(),
                'invoice' => $invoiceNumber
            );
            $individuals = array();
            if ($individual['payment'] != null) {
		if ($customer['zahlungsziel']['id'] != $individual['payment']) {
		    $dates['payment_name'] = $this->helper->getSingleZahlungsziel($individual['payment']);
		    $dates['payment'] = $individual['payment'];
		}
            }
            if ($individual['skonto'] != null) {
		if ($customer['skonto'] != $individual['skonto']) {
		    $dates['skonto'] = $individual['skonto'];
		}
            }
            $document = $this->creator->createProduct('document');
            $documentList = $document->getDocumentList($projectId);
            if (empty($documentList)) {
		$documentList = null;
            }
            $output = $this->twig->render('/zusammenfassung.html', array(
                'root' => $this->root,
                'path' => $this->path,
                'user' => $userFunction['id'],
                'customer' => $customer,
                'seller' => $seller,
                'address' => $address,
                'patterns' => $patterns,
                'carrier' => $carrier[0],
                'carrierList' => $carrierList,
                'mandant' => $this->mandant,
                'employees' => $this->employees,
                'paymentOpt' => $paymentOpt,
                'dates' => $dates,
                'vorstufe' => $vorstufe, 
                'drucksachen' => $drucksachen,
                'fremdarbeiten' => $fremdarbeiten,
                'art' => $art,
                'machine' => $machine,
                'status' => $status,
                'sellerList' => $sellerList,
                'addressList' => $addressList,
                'singleAddress' => $singleAddress,
                'calculationFields' => $calculationField,
                'firstCalc' => $firstTable,
                'secondCalc' => $secondTable,
                'thirdCalc' => $thirdTable,
                'fourthCalc' => $fourthTable,
                'calcCount' => $calcCount,
                'documentList' => $documentList,
                'vorgangsnummer' => $vorgang,
            ));
        } else {
            $output = $this->twig->render('/zusammenfassung.html', array(
                'root' => $this->root,
                'path' => $this->path,
                'user' => $userFunction['id'],
                'carrierList' => $carrierList,
                'mandant' => $this->mandant,
                'employees' => $this->employees,
                'paymentOpt' => $paymentOpt,
                'art' => $art,
                'machine' => $machine,
                'status' => $status,
                'calculationFields' => $calculationField,
                'vorgangsnummer' => 'false',
            ));
        }  
    echo $output;
        exit();
    }

    public function renderListe($user, $result, $params) { //$params może być null
        $this->user = $user;
        $status = $this->helper->getStatusList();
        if (isset($params['begin']) && $params['begin'] != '') {
            $begin = explode("/", $params['begin']);
            $params['begin'] = $begin[1].'/'.$begin[0].'/'.$begin[2];
        }
        if (isset($params['endDate']) && $params['endDate'] != '') {
            $begin = explode("/", $params['endDate']);
            $params['endDate'] = $begin[1].'/'.$begin[0].'/'.$begin[2];
        }
        //var_dump($params); exit();
        $userFunction = $this->user->getRolle();
        $output = $this->twig->render('/listeTwig.html', array(
            'root' => $this->root,
            'path' => $this->path,
            'user' => $userFunction['id'],
            'mandant' => $this->mandant,
            'result' => $result,
            'params' => $params,
            'status' => $status
        ));
    echo $output;
    exit();
    }

    public function renderLoginPage() {
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $this->user = $this->creator->createProduct('benutzer');
            $ifAvailable = $this->user->checkByToken($token);
            if ($ifAvailable == null) {
                unset($token);
            }
            $output = $this->twig->render('/loginTwig.html', array(
                'path' => $this->path,
                'token' => $token
            ));
        } else {
            $output = $this->twig->render('/loginTwig.html', array(
                'path' => $this->path
            ));
        }
	    echo $output;
        exit();
    }
}