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

	public function __construct($dbHandler) {
	
	$root_dir = $_SERVER['DOCUMENT_ROOT'].'/CRM';
	$vendor_dir = $root_dir.'/vendor';
	$cache_dir = $root_dir.'/cache';
	$templates_dir = $root_dir.'/view';
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

    }

    public static function displayError($error) {
        include ('view/error.html');
    }

    public function renderErfassungPage($user, $project = null) {
	$path = $this->path;
        $this->user = $user;
        $userFunction = $this->user->getRolle();
        $carrierList = $this->helper->setLieferant();
        $mandant = $this->helper->getMandant();
        $employees = $this->helper->getAnsprechpartner();
        $paymentOpt = $this->helper->getZahlungsziel();
        if (isset($project)) {
            $customer = $project->auftraggeber->getDates();
            $seller = $project->ansprechpartner->getDates();
            $address = $project->rechnungsadresse->getDates();
            $patterns = $project->getPatterns();
            $carrier = $project->getLieferant();
            $regDate = explode(' ', $project->getRegDate());
            $dates = array(
                'name' => $project->getName(),
                'regDate' => $regDate[0],
                'changeDate' => $project->getChangeDate(),
                'kundenNummer' => $project->getKundenautragsnummer(),
                'mandantSelect' => $project->getMandantSelect(),
                'vorgangsnummer' => $project->getVorgangsnummer()
            );
        }
        $output = $this->twig->render('/zusammenfassungTwig.html', array(
		'root' => $this->root,
		'path' => $this->path,
		'user' => $userFunction['id'],
		'customer' => $customer,
		'seller' => $seller,
		'address' => $address,
		'patterns' => $patterns,
		'carrier' => $carrier[0],
		'carrierList' => $carrierList,
		'mandant' => $mandant,
		'employees' => $employees,
		'paymentOpt' => $paymentOpt,
		'dates' => $dates
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
        }
    	$output = $this->twig->render('/loginTwig.html', array(
		'path' => $this->path,
		'token' => $token
	));
	echo $output;
        exit();
    }

    public function renderListe($user, $result, $params) { //$params może być null
    	$this->user = $user;
    	if (isset($params['begin'])) {
    	$begin = explode("/", $params['begin']);
    	$params['begin'] = $begin[1].'/'.$begin[0].'/'.$begin[2];
    	}
    	if (isset($params['endDate'])) {
    	$begin = explode("/", $params['endDate']);
    	$params['endDate'] = $begin[1].'/'.$begin[0].'/'.$begin[2];
    	}
        $userFunction = $this->user->getRolle();
        $mandant = $this->helper->getMandant();
        $output = $this->twig->render('/listeTwig.html', array(
		'root' => $this->root,
		'path' => $this->path,
		'user' => $userFunction['id'],
		'mandant' => $mandant,
		'result' => $result,
		'params' => $params
	));
	echo $output;
        exit();
    }
}