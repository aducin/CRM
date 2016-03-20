<?php
interface creator{
    public function createProduct($item, $id);
}
class TvsatzCreator implements creator{
    
	private $dbHandler;

	public function __construct($dbHandler){
		$this->dbHandler = $dbHandler;
	}

    public function createProduct($item, $data = null) {
        switch($item) {
            case 'loginController':
                return new LoginController($this->dbHandler, $data);
                break;
            case 'projekt':
                return new Projekt($this->dbHandler, $data);
                break;
            case 'Ajax':
                return new Ajax($this->dbHandler, $data);
                break;
            case 'angebot':
                return new Angebot($this->dbHandler, $data = null);
                break;
            case 'ansprechpartner':
                return new Ansprechpartner($this->dbHandler, $data);
                break;
            case 'auftraggeber':
                return new Auftraggeber($this->dbHandler, $data);
                break;
            case 'auftragszettel':
                return new Auftragszettel($this->dbHandler, $data);
                break;
            case 'benutzer':
                return new Benutzer($this->dbHandler, $data);
                break;
            case 'Drucksache':
                return new Drucksache($this->dbHandler, $item);
                break;
            case 'Fremdsache':
                return new Fremdsache($this->dbHandler, $item);
                break;
            case 'helpers':
                return new Helpers($this->dbHandler);
                break;
            case 'lieferant':
                return new Lieferant($this->dbHandler, $data);
                break;
            case 'rechnungsadresse':
                return new Rechnungsadresse($this->dbHandler, $data);
                break;
            case 'Vorstufe':
                return new Vorstufe($this->dbHandler, $item);
                break;
            case 'zahlungsziel':
                return new Zahlungsziel($this->dbHandler, $data);
                break;
        }
    }
}