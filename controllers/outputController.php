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

	public function __construct($dbHandler) {
	
    	$root_dir = $_SERVER['DOCUMENT_ROOT'].'/CRM';
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
        $this->path = ('http://'.$_SERVER["HTTP_HOST"]).'/CRM/view/';
        $this->root = ('http://'.$_SERVER["HTTP_HOST"]).'/CRM';
    	$this->dbHandler = $dbHandler;
        $this->creator = new TvsatzCreator($dbHandler);
        $this->helper = $this->creator->createProduct('helpers');
        $this->employees = $this->helper->getAnsprechpartner();
        $this->mandant = $this->helper->getMandant();
    }

    public static function displayError($error) {
        include ('view/error.html');
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
        $this->helper->setArt();
        $art = $this->helper->getArt();
        $this->helper->setMachine();
        $machine = $this->helper->getMachine();
        if ($project != null) {
	    $id = $project->getId();
        }
	if (isset($id) && $id != null) {
	    $projectName = $project->getName();
	    if ($projectName == null) {
		    $address = 'Location: '.$this->root.'/Erfassung/';
		    unset( $project );
		    header( $address );
	    }
        }
        if ($project) {
            $projectId = $project->getId();
            $individual = $project->getIndividuals();
            $customer = $project->auftraggeber->getDates();
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
             if (isset($vorstufe[0])) {
	      foreach($vorstufe[0] as $singleRow) {
		  $amount += $singleRow['amount'];
	      }
            }
            $amountVorstufe = number_format($amount, 2);
            $drucksachen = $project->getDrucksachen();
            $time = explode("/", $drucksachen[1]);
            $drucksachen[1] = $time[1].'/'.$time[0].'/'.$time[2];
            $amount = 0;
            if (isset($drucksachen[0])) {
	      foreach($drucksachen[0] as $singleRow) {
		  $amount += $singleRow['amount'];
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
	      foreach($fremdarbeiten[0] as $singleRow) {
		  $amount += $singleRow['sellPrice'];
	      }
            }
            $amountFremdarbeiten = number_format($amount, 2);
            $regDate = explode(' ', $project->getRegDate());
            $projectStatus = $project->getStatus();
            $deliveryTime = $project->getDeliveryTime();
            $time = explode('-', $deliveryTime);
            $deliveryTime = $time[1].'/'.$time[2].'/'.$time[0];
            $dates = array(
                'name' => $project->getName(),
                'regDate' => $regDate[0],
                'changeDate' => $project->getChangeDate(),
                'kundenNummer' => $project->getKundenautragsnummer(),
                'mandantSelect' => $project->getMandantSelect(),
                'vorgangsnummer' => $project->getVorgangsnummer(),
                'projectId' => $projectId,
                'amountVorstufe' => $amountVorstufe,
                'amountDrucksachen' => $amountDrucksachen,
                'amountFremdarbeiten' => $amountFremdarbeiten,
                'projectStatus' => $projectStatus,
                'deliveryTime' => $deliveryTime
            );
            $individuals = array();
            if ($individual[payment] != null) {
		if ($customer['zahlungsziel']['id'] != $individual[payment]) {
		    $dates['payment_name'] = $this->helper->getSingleZahlungsziel($individual[payment]);
		    $dates['payment'] = $individual[payment];
		}
            }
            if ($individual[skonto] != null) {
		if ($customer['skonto'] != $individual[skonto]) {
		    $dates['skonto'] = $individual[skonto];
		}
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
                'status' => $status
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
                'status' => $status
            ));
        }  
    echo $output;
        exit();
    }

    public function renderListe($user, $result, $params) { //$params może być null
        $this->user = $user;
        $status = $this->helper->getStatusList();
        if (isset($params['begin'])) {
            $begin = explode("/", $params['begin']);
            $params['begin'] = $begin[1].'/'.$begin[0].'/'.$begin[2];
        }
        if (isset($params['endDate'])) {
            $begin = explode("/", $params['endDate']);
            $params['endDate'] = $begin[1].'/'.$begin[0].'/'.$begin[2];
        }
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