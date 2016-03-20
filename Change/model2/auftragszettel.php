<?php

class Auftragszettel
{
	private $client; //Kundennummer - not in DB
	private $invoiceAddress; //Rechnungsadresse
	private $offer_id;
	private $clientName; //Auftraggeber - not in DB
	private $projectName; //Projektname - not in DB
	private $clientOfferNumber; //Kundenauftragsnummer - not in DB
	private $origin; //Angebot oder Auftrag
	private $machine;
	private $printing; //Auflage - not in DB
	private $format; // - not in DB
	private $size; //Umfang - not in DB
	private $color; //Farbe - not in DB
	private $paper; //Papier - not in DB
	private $remodelling; //Verarbeitung - not in DB
	private $supplier; //Lieferant
	private $supplyAddress; //Lieferung an - just integer with address number                                                             ---------------------- FOREIGN KEY MUST BE ADDED IN THIS TABLE AFTER CREATING LIERADRESSE TABLE
	private $patternInBag; //Muster in Tasche
	private $patternQuantity; //Stück Muster an
	private $amendmentDate; //TerminKorrektur
	private $date_time; //Datentermin
	private $internalInformation;//Bemerkungen (Intern)

	function __construct($dbHandler, $id) {

		$this->dbHandler = $dbHandler;
		$this->client = $id;
	}


}