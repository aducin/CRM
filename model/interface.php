<?php


interface TvsatzInterface
{
	function __construct($dbHandler, $id);

	public function getDates();

	public function deleteCurrentDates();

	public function saveCustomDates();

    public function setDates();

    public function setCustomDates( $array );
}
