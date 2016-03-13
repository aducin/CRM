<?php

abstract class Crm
{
	protected $id;
	protected $dbHandler;

	function __construct($dbHandler, $id) {

		$this->dbHandler = $dbHandler;

		if ($id != null) {
			$this->id = $id;
		}
	}

}