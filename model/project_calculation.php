<?php

class Project_Calculation
{

    private $dbHandler;
    private $output;

    public function __construct($dbHandler) {

	$this->dbHandler = $dbHandler;
	$this->output = new OutputController($dbHandler);

    }
    
    public function createEmptyList($id) {
    	$sql = "INSERT INTO Project_Calculation (projectId) VALUES (:id)";
    	$result=$this->dbHandler->prepare($sql);
	  	$result->bindValue(':id', $id);
	  	if ( $result->execute()) {
			return true;
		} else {
			return false;
		}
    }

    public function getDates($id) {
    	$sql = 'SELECT * FROM Project_Calculation WHERE projectId = :id';
    	$result=$this->dbHandler->prepare($sql);
    	$result->bindValue(':id', $id);
    	if ($result->execute()) {
    		$singleResult = $result->fetch();
    		$calcTable[] = array(
    			'checkbox1' => $singleResult['checkbox1'],
    			'checkbox2' => $singleResult['checkbox2'],
    			'checkbox3' => $singleResult['checkbox3'],
    			'checkbox4' => $singleResult['checkbox4'],
    			'checkbox5' => $singleResult['checkbox5'],
    			'checkbox6' => $singleResult['checkbox6'],
    			'checkbox7' => $singleResult['checkbox7'],
    			'checkbox8' => $singleResult['checkbox8'],
    			'checkbox9' => $singleResult['checkbox9'],
    			'checkbox10' => $singleResult['checkbox10'],
    			'checkbox11' => $singleResult['checkbox11'],
    			'checkbox12' => $singleResult['checkbox12'],
    			'checkbox13' => $singleResult['checkbox13'],
    			'checkbox14' => $singleResult['checkbox14'],
    			'checkbox15' => $singleResult['checkbox15'],
    			'checkbox16' => $singleResult['checkbox16'],
    			'checkbox17' => $singleResult['checkbox17'],
    			'checkbox18' => $singleResult['checkbox18'],
    			'zeit_1_1' => $singleResult['zeit_1_1'],
    			'preis_1_1' => $singleResult['preis_1_1'],
    			'zeit_1_2' => $singleResult['zeit_1_2'],
    			'preis_1_2' => $singleResult['preis_1_2'],
    			'zeit_1_3' => $singleResult['zeit_1_3'],
    			'preis_1_3' => $singleResult['preis_1_3'],
    			'zeit_1_4' => $singleResult['zeit_1_4'],
    			'preis_1_4' => $singleResult['preis_1_4'],
    			'zeit_1_5' => $singleResult['zeit_1_5'],
    			'preis_1_5' => $singleResult['preis_1_5'],
    			'zeit_1_6' => $singleResult['zeit_1_6'],
    			'preis_1_6' => $singleResult['preis_1_6'],
    			'zeit_1_7' => $singleResult['zeit_1_7'],
    			'preis_1_7' => $singleResult['preis_1_7'],
    			'zeit_1_8' => $singleResult['zeit_1_8'],
    			'preis_1_8' => $singleResult['preis_1_8'],
    			'zeit_1_9' => $singleResult['zeit_1_9'],
    			'preis_1_9' => $singleResult['preis_1_9'],
    			'zeit_1_10' => $singleResult['zeit_1_10'],
    			'preis_1_10' => $singleResult['preis_1_10'],
    			'zeit_1_11' => $singleResult['zeit_1_11'],
    			'preis_1_11' => $singleResult['preis_1_11'],
    			'zeit_1_12' => $singleResult['zeit_1_12'],
    			'preis_1_12' => $singleResult['preis_1_12'],
    			'zeit_1_13' => $singleResult['zeit_1_13'],
    			'preis_1_13' => $singleResult['preis_1_13'],
    			'zeit_1_14' => $singleResult['zeit_1_14'],
    			'preis_1_14' => $singleResult['preis_1_14'],
    			'zeit_1_15' => $singleResult['zeit_1_15'],
    			'preis_1_15' => $singleResult['preis_1_15'],
    			'zeit_1_16' => $singleResult['zeit_1_16'],
    			'preis_1_16' => $singleResult['preis_1_16'],
    			'zeit_1_17' => $singleResult['zeit_1_17'],
    			'preis_1_17' => $singleResult['preis_1_17'],
    			'zeit_1_18' => $singleResult['zeit_1_18'],
    			'preis_1_18' => $singleResult['preis_1_18'],
    			'zeit_2_1' => $singleResult['zeit_2_1'],
    			'preis_2_1' => $singleResult['preis_2_1'],
    			'zeit_2_2' => $singleResult['zeit_2_2'],
    			'preis_2_2' => $singleResult['preis_2_2'],
    			'zeit_2_3' => $singleResult['zeit_2_3'],
    			'preis_2_3' => $singleResult['preis_2_3'],
    			'zeit_2_4' => $singleResult['zeit_2_4'],
    			'preis_2_4' => $singleResult['preis_2_4'],
    			'zeit_2_5' => $singleResult['zeit_2_5'],
    			'preis_2_5' => $singleResult['preis_2_5'],
    			'zeit_2_6' => $singleResult['zeit_2_6'],
    			'preis_2_6' => $singleResult['preis_2_6'],
    			'zeit_2_7' => $singleResult['zeit_2_7'],
    			'preis_2_7' => $singleResult['preis_2_7'],
    			'zeit_2_8' => $singleResult['zeit_2_8'],
    			'preis_2_8' => $singleResult['preis_2_8'],
    			'zeit_2_9' => $singleResult['zeit_2_9'],
    			'preis_2_9' => $singleResult['preis_2_9'],
    			'zeit_2_10' => $singleResult['zeit_2_10'],
    			'preis_2_10' => $singleResult['preis_2_10'],
    			'zeit_2_11' => $singleResult['zeit_2_11'],
    			'preis_2_11' => $singleResult['preis_2_11'],
    			'zeit_2_12' => $singleResult['zeit_2_12'],
    			'preis_2_12' => $singleResult['preis_2_12'],
    			'zeit_2_13' => $singleResult['zeit_2_13'],
    			'preis_2_13' => $singleResult['preis_2_13'],
    			'zeit_2_14' => $singleResult['zeit_2_14'],
    			'preis_2_14' => $singleResult['preis_2_14'],
    			'zeit_2_15' => $singleResult['zeit_2_15'],
    			'preis_2_15' => $singleResult['preis_2_15'],
    			'zeit_2_16' => $singleResult['zeit_2_16'],
    			'preis_2_16' => $singleResult['preis_2_16'],
    			'zeit_2_17' => $singleResult['zeit_2_17'],
    			'preis_2_17' => $singleResult['preis_2_17'],
    			'zeit_2_18' => $singleResult['zeit_2_18'],
    			'preis_2_18' => $singleResult['preis_2_18'],
    			'zeit_3_1' => $singleResult['zeit_3_1'],
    			'preis_3_1' => $singleResult['preis_3_1'],
    			'zeit_3_2' => $singleResult['zeit_3_2'],
    			'preis_3_2' => $singleResult['preis_3_2'],
    			'zeit_3_3' => $singleResult['zeit_3_3'],
    			'preis_3_3' => $singleResult['preis_3_3'],
    			'zeit_3_4' => $singleResult['zeit_3_4'],
    			'preis_3_4' => $singleResult['preis_3_4'],
    			'zeit_3_5' => $singleResult['zeit_3_5'],
    			'preis_3_5' => $singleResult['preis_3_5'],
    			'zeit_3_6' => $singleResult['zeit_3_6'],
    			'preis_3_6' => $singleResult['preis_3_6'],
    			'zeit_3_7' => $singleResult['zeit_3_7'],
    			'preis_3_7' => $singleResult['preis_3_7'],
    			'zeit_3_8' => $singleResult['zeit_3_8'],
    			'preis_3_8' => $singleResult['preis_3_8'],
    			'zeit_3_9' => $singleResult['zeit_3_9'],
    			'preis_3_9' => $singleResult['preis_3_9'],
    			'zeit_3_10' => $singleResult['zeit_3_10'],
    			'preis_3_10' => $singleResult['preis_3_10'],
    			'zeit_3_11' => $singleResult['zeit_3_11'],
    			'preis_3_11' => $singleResult['preis_3_11'],
    			'zeit_3_12' => $singleResult['zeit_3_12'],
    			'preis_3_12' => $singleResult['preis_3_12'],
    			'zeit_3_13' => $singleResult['zeit_3_13'],
    			'preis_3_13' => $singleResult['preis_3_13'],
    			'zeit_3_14' => $singleResult['zeit_3_14'],
    			'preis_3_14' => $singleResult['preis_3_14'],
    			'zeit_3_15' => $singleResult['zeit_3_15'],
    			'preis_3_15' => $singleResult['preis_3_15'],
    			'zeit_3_16' => $singleResult['zeit_3_16'],
    			'preis_3_16' => $singleResult['preis_3_16'],
    			'zeit_3_17' => $singleResult['zeit_3_17'],
    			'preis_3_17' => $singleResult['preis_3_17'],
    			'zeit_3_18' => $singleResult['zeit_3_18'],
    			'preis_3_18' => $singleResult['preis_3_18'],
    			'zeit_4_1' => $singleResult['zeit_4_1'],
    			'preis_4_1' => $singleResult['preis_4_1'],
    			'zeit_4_2' => $singleResult['zeit_4_2'],
    			'preis_4_2' => $singleResult['preis_4_2'],
    			'zeit_4_3' => $singleResult['zeit_4_3'],
    			'preis_4_3' => $singleResult['preis_4_3'],
    			'zeit_4_4' => $singleResult['zeit_4_4'],
    			'preis_4_4' => $singleResult['preis_4_4'],
    			'zeit_4_5' => $singleResult['zeit_4_5'],
    			'preis_4_5' => $singleResult['preis_4_5'],
    			'zeit_4_6' => $singleResult['zeit_4_6'],
    			'preis_4_6' => $singleResult['preis_4_6'],
    			'zeit_4_7' => $singleResult['zeit_4_7'],
    			'preis_4_7' => $singleResult['preis_4_7'],
    			'zeit_4_8' => $singleResult['zeit_4_8'],
    			'preis_4_8' => $singleResult['preis_4_8'],
    			'zeit_4_9' => $singleResult['zeit_4_9'],
    			'preis_4_9' => $singleResult['preis_4_9'],
    			'zeit_4_10' => $singleResult['zeit_4_10'],
    			'preis_4_10' => $singleResult['preis_4_10'],
    			'zeit_4_11' => $singleResult['zeit_4_11'],
    			'preis_4_11' => $singleResult['preis_4_11'],
    			'zeit_4_12' => $singleResult['zeit_4_12'],
    			'preis_4_12' => $singleResult['preis_4_12'],
    			'zeit_4_13' => $singleResult['zeit_4_13'],
    			'preis_4_13' => $singleResult['preis_4_13'],
    			'zeit_4_14' => $singleResult['zeit_4_14'],
    			'preis_4_14' => $singleResult['preis_4_14'],
    			'zeit_4_15' => $singleResult['zeit_4_15'],
    			'preis_4_15' => $singleResult['preis_4_15'],
    			'zeit_4_16' => $singleResult['zeit_4_16'],
    			'preis_4_16' => $singleResult['preis_4_16'],
    			'zeit_4_17' => $singleResult['zeit_4_17'],
    			'preis_4_17' => $singleResult['preis_4_17'],
    			'zeit_4_18' => $singleResult['zeit_4_18'],
    			'preis_4_18' => $singleResult['preis_4_18'],
    			);
			return $calcTable[0];
		} else {
			$this->output->displayPhpError();
		}
	}
    
    public function insert($values, $id) {
		$sql = 'INSERT INTO Project_Calculation (
		    projectId, checkbox1, checkbox2, checkbox3, checkbox4, checkbox5, checkbox6, checkbox7, checkbox8, checkbox9, 
		    checkbox10, checkbox11, checkbox12, checkbox13, checkbox14, checkbox15, checkbox16, checkbox17, checkbox18, 
		    zeit_1_1, preis_1_1, zeit_2_1, preis_2_1,zeit_3_1, preis_3_1, zeit_4_1, preis_4_1, 
		    zeit_1_2, preis_1_2, zeit_2_2, preis_2_2,zeit_3_2, preis_3_2, zeit_4_2, preis_4_2, 
		    zeit_1_3, preis_1_3, zeit_2_3, preis_2_3,zeit_3_3, preis_3_3, zeit_4_3, preis_4_3, 
		    zeit_1_4, preis_1_4, zeit_2_4, preis_2_4,zeit_3_4, preis_3_4, zeit_4_4, preis_4_4,
		    zeit_1_5, preis_1_5, zeit_2_5, preis_2_5,zeit_3_5, preis_3_5, zeit_4_5, preis_4_5,
		    zeit_1_6, preis_1_6, zeit_2_6, preis_2_6,zeit_3_6, preis_3_6, zeit_4_6, preis_4_6,
		    zeit_1_7, preis_1_7, zeit_2_7, preis_2_7,zeit_3_7, preis_3_7, zeit_4_7, preis_4_7,
		    zeit_1_8, preis_1_8, zeit_2_8, preis_2_8,zeit_3_8, preis_3_8, zeit_4_8, preis_4_8,
		    zeit_1_9, preis_1_9, zeit_2_9, preis_2_9,zeit_3_9, preis_3_9, zeit_4_9, preis_4_9,
		    zeit_1_10, preis_1_10, zeit_2_10, preis_2_10,zeit_3_10, preis_3_10, zeit_4_10, preis_4_10,
		    zeit_1_11, preis_1_11, zeit_2_11, preis_2_11,zeit_3_11, preis_3_11, zeit_4_11, preis_4_11,
		    zeit_1_12, preis_1_12, zeit_2_12, preis_2_12,zeit_3_12, preis_3_12, zeit_4_12, preis_4_12,
		    zeit_1_13, preis_1_13, zeit_2_13, preis_2_13,zeit_3_13, preis_3_13, zeit_4_13, preis_4_13,
		    zeit_1_14, preis_1_14, zeit_2_14, preis_2_14,zeit_3_14, preis_3_14, zeit_4_14, preis_4_14,
		    zeit_1_15, preis_1_15, zeit_2_15, preis_2_15,zeit_3_15, preis_3_15, zeit_4_15, preis_4_15,
		    zeit_1_16, preis_1_16, zeit_2_16, preis_2_16,zeit_3_16, preis_3_16, zeit_4_16, preis_4_16,
		    zeit_1_17, preis_1_17, zeit_2_17, preis_2_17,zeit_3_17, preis_3_17, zeit_4_17, preis_4_17,
		    zeit_1_18, preis_1_18, zeit_2_18, preis_2_18,zeit_3_18, preis_3_18, zeit_4_18, preis_4_18
		    ) VALUES (
		    :projectId, :checkbox1, :checkbox2, :checkbox3, :checkbox4, :checkbox5, :checkbox6, :checkbox7, :checkbox8, :checkbox9, 
		    :checkbox10, :checkbox11, :checkbox12, :checkbox13, :checkbox14, :checkbox15, :checkbox16, :checkbox17, :checkbox18, 
		    :zeit_1_1, :preis_1_1, :zeit_2_1, :preis_2_1, :zeit_3_1, :preis_3_1, :zeit_4_1, :preis_4_1, 
		    :zeit_1_2, :preis_1_2, :zeit_2_2, :preis_2_2, :zeit_3_2, :preis_3_2, :zeit_4_2, :preis_4_2, 
		    :zeit_1_3, :preis_1_3, :zeit_2_3, :preis_2_3, :zeit_3_3, :preis_3_3, :zeit_4_3, :preis_4_3, 
		    :zeit_1_4, :preis_1_4, :zeit_2_4, :preis_2_4, :zeit_3_4, :preis_3_4, :zeit_4_4, :preis_4_4,
		    :zeit_1_5, :preis_1_5, :zeit_2_5, :preis_2_5, :zeit_3_5, :preis_3_5, :zeit_4_5, :preis_4_5,
		    :zeit_1_6, :preis_1_6, :zeit_2_6, :preis_2_6, :zeit_3_6, :preis_3_6, :zeit_4_6, :preis_4_6,
		    :zeit_1_7, :preis_1_7, :zeit_2_7, :preis_2_7, :zeit_3_7, :preis_3_7, :zeit_4_7, :preis_4_7,
		    :zeit_1_8, :preis_1_8, :zeit_2_8, :preis_2_8, :zeit_3_8, :preis_3_8, :zeit_4_8, :preis_4_8,
		    :zeit_1_9, :preis_1_9, :zeit_2_9, :preis_2_9, :zeit_3_9, :preis_3_9, :zeit_4_9, :preis_4_9,
		    :zeit_1_10, :preis_1_10, :zeit_2_10, :preis_2_10, :zeit_3_10, :preis_3_10, :zeit_4_10, :preis_4_10,
		    :zeit_1_11, :preis_1_11, :zeit_2_11, :preis_2_11, :zeit_3_11, :preis_3_11, :zeit_4_11, :preis_4_11,
		    :zeit_1_12, :preis_1_12, :zeit_2_12, :preis_2_12, :zeit_3_12, :preis_3_12, :zeit_4_12, :preis_4_12,
		    :zeit_1_13, :preis_1_13, :zeit_2_13, :preis_2_13, :zeit_3_13, :preis_3_13, :zeit_4_13, :preis_4_13,
		    :zeit_1_14, :preis_1_14, :zeit_2_14, :preis_2_14, :zeit_3_14, :preis_3_14, :zeit_4_14, :preis_4_14,
		    :zeit_1_15, :preis_1_15, :zeit_2_15, :preis_2_15, :zeit_3_15, :preis_3_15, :zeit_4_15, :preis_4_15,
		    :zeit_1_16, :preis_1_16, :zeit_2_16, :preis_2_16, :zeit_3_16, :preis_3_16, :zeit_4_16, :preis_4_16,
		    :zeit_1_17, :preis_1_17, :zeit_2_17, :preis_2_17, :zeit_3_17, :preis_3_17, :zeit_4_17, :preis_4_17,
		    :zeit_1_18, :preis_1_18, :zeit_2_18, :preis_2_18, :zeit_3_18, :preis_3_18, :zeit_4_18, :preis_4_18
	    )';
	    $result=$this->dbHandler->prepare($sql);
	    $result->bindValue(':projectId', $id);
	    $result->bindValue(':checkbox1', $values["checkbox1"]);
	    $result->bindValue(':checkbox2', $values["checkbox2"]);
	    $result->bindValue(':checkbox3', $values["checkbox3"]);
	    $result->bindValue(':checkbox4', $values["checkbox4"]);
	    $result->bindValue(':checkbox5', $values["checkbox5"]);
	    $result->bindValue(':checkbox6', $values["checkbox6"]);
	    $result->bindValue(':checkbox7', $values["checkbox7"]);
	    $result->bindValue(':checkbox8', $values["checkbox8"]);
	    $result->bindValue(':checkbox9', $values["checkbox9"]);
	    $result->bindValue(':checkbox10', $values["checkbox10"]);
	    $result->bindValue(':checkbox11', $values["checkbox11"]);
	    $result->bindValue(':checkbox12', $values["checkbox12"]);
	    $result->bindValue(':checkbox13', $values["checkbox13"]);
	    $result->bindValue(':checkbox14', $values["checkbox14"]);
	    $result->bindValue(':checkbox15', $values["checkbox15"]);
	    $result->bindValue(':checkbox16', $values["checkbox16"]);
	    $result->bindValue(':checkbox17', $values["checkbox17"]);
	    $result->bindValue(':checkbox18', $values["checkbox18"]);
	    $result->bindValue(':zeit_1_1', $values["zeit_1_1"]);
	    $result->bindValue(':preis_1_1', $values["preis_1_1"]);
	    $result->bindValue(':zeit_2_1', $values["zeit_2_1"]);
	    $result->bindValue(':preis_2_1', $values["preis_2_1"]);
	    $result->bindValue(':zeit_3_1', $values["zeit_3_1"]);
	    $result->bindValue(':preis_3_1', $values["preis_3_1"]);
	    $result->bindValue(':zeit_4_1', $values["zeit_4_1"]);
	    $result->bindValue(':preis_4_1', $values["preis_4_1"]);
	    $result->bindValue(':zeit_1_2', $values["zeit_1_2"]);
	    $result->bindValue(':preis_1_2', $values["preis_1_2"]);
	    $result->bindValue(':zeit_2_2', $values["zeit_2_2"]);
	    $result->bindValue(':preis_2_2', $values["preis_2_2"]);
	    $result->bindValue(':zeit_3_2', $values["zeit_3_2"]);
	    $result->bindValue(':preis_3_2', $values["preis_3_2"]);
	    $result->bindValue(':zeit_4_2', $values["zeit_4_2"]);
	    $result->bindValue(':preis_4_2', $values["preis_4_2"]);
	    $result->bindValue(':zeit_1_3', $values["zeit_1_3"]);
	    $result->bindValue(':preis_1_3', $values["preis_1_3"]);
	    $result->bindValue(':zeit_2_3', $values["zeit_2_3"]);
	    $result->bindValue(':preis_2_3', $values["preis_2_3"]);
	    $result->bindValue(':zeit_3_3', $values["zeit_3_3"]);
	    $result->bindValue(':preis_3_3', $values["preis_3_3"]);
	    $result->bindValue(':zeit_4_3', $values["zeit_4_3"]);
	    $result->bindValue(':preis_4_3', $values["preis_4_3"]);
	    $result->bindValue(':zeit_1_4', $values["zeit_1_4"]);
	    $result->bindValue(':preis_1_4', $values["preis_1_4"]);
	    $result->bindValue(':zeit_2_4', $values["zeit_2_4"]);
	    $result->bindValue(':preis_2_4', $values["preis_2_4"]);
	    $result->bindValue(':zeit_3_4', $values["zeit_3_4"]);
	    $result->bindValue(':preis_3_4', $values["preis_3_4"]);
	    $result->bindValue(':zeit_4_4', $values["zeit_4_4"]);
	    $result->bindValue(':preis_4_4', $values["preis_4_4"]);
	    $result->bindValue(':zeit_1_5', $values["zeit_1_5"]);
	    $result->bindValue(':preis_1_5', $values["preis_1_5"]);
	    $result->bindValue(':zeit_2_5', $values["zeit_2_5"]);
	    $result->bindValue(':preis_2_5', $values["preis_2_5"]);
	    $result->bindValue(':zeit_3_5', $values["zeit_3_5"]);
	    $result->bindValue(':preis_3_5', $values["preis_3_5"]);
	    $result->bindValue(':zeit_4_5', $values["zeit_4_5"]);
	    $result->bindValue(':preis_4_5', $values["preis_4_5"]);
	    $result->bindValue(':zeit_1_6', $values["zeit_1_6"]);
	    $result->bindValue(':preis_1_6', $values["preis_1_6"]);
	    $result->bindValue(':zeit_2_6', $values["zeit_2_6"]);
	    $result->bindValue(':preis_2_6', $values["preis_2_6"]);
	    $result->bindValue(':zeit_3_6', $values["zeit_3_6"]);
	    $result->bindValue(':preis_3_6', $values["preis_3_6"]);
	    $result->bindValue(':zeit_4_6', $values["zeit_4_6"]);
	    $result->bindValue(':preis_4_6', $values["preis_4_6"]);
	    $result->bindValue(':zeit_1_7', $values["zeit_1_7"]);
	    $result->bindValue(':preis_1_7', $values["preis_1_7"]);
	    $result->bindValue(':zeit_2_7', $values["zeit_2_7"]);
	    $result->bindValue(':preis_2_7', $values["preis_2_7"]);
	    $result->bindValue(':zeit_3_7', $values["zeit_3_7"]);
	    $result->bindValue(':preis_3_7', $values["preis_3_7"]);
	    $result->bindValue(':zeit_4_7', $values["zeit_4_7"]);
	    $result->bindValue(':preis_4_7', $values["preis_4_7"]);
	    $result->bindValue(':zeit_1_8', $values["zeit_1_8"]);
	    $result->bindValue(':preis_1_8', $values["preis_1_8"]);
	    $result->bindValue(':zeit_2_8', $values["zeit_2_8"]);
	    $result->bindValue(':preis_2_8', $values["preis_2_8"]);
	    $result->bindValue(':zeit_3_8', $values["zeit_3_8"]);
	    $result->bindValue(':preis_3_8', $values["preis_3_8"]);
	    $result->bindValue(':zeit_4_8', $values["zeit_4_8"]);
	    $result->bindValue(':preis_4_8', $values["preis_4_8"]);
	    $result->bindValue(':zeit_1_9', $values["zeit_1_9"]);
	    $result->bindValue(':preis_1_9', $values["preis_1_9"]);
	    $result->bindValue(':zeit_2_9', $values["zeit_2_9"]);
	    $result->bindValue(':preis_2_9', $values["preis_2_9"]);
	    $result->bindValue(':zeit_3_9', $values["zeit_3_9"]);
	    $result->bindValue(':preis_3_9', $values["preis_3_9"]);
	    $result->bindValue(':zeit_4_9', $values["zeit_4_9"]);
	    $result->bindValue(':preis_4_9', $values["preis_4_9"]);
	    $result->bindValue(':zeit_1_10', $values["zeit_1_10"]);
	    $result->bindValue(':preis_1_10', $values["preis_1_10"]);
	    $result->bindValue(':zeit_2_10', $values["zeit_2_10"]);
	    $result->bindValue(':preis_2_10', $values["preis_2_10"]);
	    $result->bindValue(':zeit_3_10', $values["zeit_3_10"]);
	    $result->bindValue(':preis_3_10', $values["preis_3_10"]);
	    $result->bindValue(':zeit_4_10', $values["zeit_4_10"]);
	    $result->bindValue(':preis_4_10', $values["preis_4_10"]);
	    $result->bindValue(':zeit_1_11', $values["zeit_1_11"]);
	    $result->bindValue(':preis_1_11', $values["preis_1_11"]);
	    $result->bindValue(':zeit_2_11', $values["zeit_2_11"]);
	    $result->bindValue(':preis_2_11', $values["preis_2_11"]);
	    $result->bindValue(':zeit_3_11', $values["zeit_3_11"]);
	    $result->bindValue(':preis_3_11', $values["preis_3_11"]);
	    $result->bindValue(':zeit_4_11', $values["zeit_4_11"]);
	    $result->bindValue(':preis_4_11', $values["preis_4_11"]);
	    $result->bindValue(':zeit_1_12', $values["zeit_1_12"]);
	    $result->bindValue(':preis_1_12', $values["preis_1_12"]);
	    $result->bindValue(':zeit_2_12', $values["zeit_2_12"]);
	    $result->bindValue(':preis_2_12', $values["preis_2_12"]);
	    $result->bindValue(':zeit_3_12', $values["zeit_3_12"]);
	    $result->bindValue(':preis_3_12', $values["preis_3_12"]);
	    $result->bindValue(':zeit_4_12', $values["zeit_4_12"]);
	    $result->bindValue(':preis_4_12', $values["preis_4_12"]);
	    $result->bindValue(':zeit_1_13', $values["zeit_1_13"]);
	    $result->bindValue(':preis_1_13', $values["preis_1_13"]);
	    $result->bindValue(':zeit_2_13', $values["zeit_2_13"]);
	    $result->bindValue(':preis_2_13', $values["preis_2_13"]);
	    $result->bindValue(':zeit_3_13', $values["zeit_3_13"]);
	    $result->bindValue(':preis_3_13', $values["preis_3_13"]);
	    $result->bindValue(':zeit_4_13', $values["zeit_4_13"]);
	    $result->bindValue(':preis_4_13', $values["preis_4_13"]);
	    $result->bindValue(':zeit_1_14', $values["zeit_1_14"]);
	    $result->bindValue(':preis_1_14', $values["preis_1_14"]);
	    $result->bindValue(':zeit_2_14', $values["zeit_2_14"]);
	    $result->bindValue(':preis_2_14', $values["preis_2_14"]);
	    $result->bindValue(':zeit_3_14', $values["zeit_3_14"]);
	    $result->bindValue(':preis_3_14', $values["preis_3_14"]);
	    $result->bindValue(':zeit_4_14', $values["zeit_4_14"]);
	    $result->bindValue(':preis_4_14', $values["preis_4_14"]);
	    $result->bindValue(':zeit_1_15', $values["zeit_1_15"]);
	    $result->bindValue(':preis_1_15', $values["preis_1_15"]);
	    $result->bindValue(':zeit_2_15', $values["zeit_2_15"]);
	    $result->bindValue(':preis_2_15', $values["preis_2_15"]);
	    $result->bindValue(':zeit_3_15', $values["zeit_3_15"]);
	    $result->bindValue(':preis_3_15', $values["preis_3_15"]);
	    $result->bindValue(':zeit_4_15', $values["zeit_4_15"]);
	    $result->bindValue(':preis_4_15', $values["preis_4_15"]);
	    $result->bindValue(':zeit_1_16', $values["zeit_1_16"]);
	    $result->bindValue(':preis_1_16', $values["preis_1_16"]);
	    $result->bindValue(':zeit_2_16', $values["zeit_2_16"]);
	    $result->bindValue(':preis_2_16', $values["preis_2_16"]);
	    $result->bindValue(':zeit_3_16', $values["zeit_3_16"]);
	    $result->bindValue(':preis_3_16', $values["preis_3_16"]);
	    $result->bindValue(':zeit_4_16', $values["zeit_4_16"]);
	    $result->bindValue(':preis_4_16', $values["preis_4_16"]);
	    $result->bindValue(':zeit_1_17', $values["zeit_1_17"]);
	    $result->bindValue(':preis_1_17', $values["preis_1_17"]);
	    $result->bindValue(':zeit_2_17', $values["zeit_2_17"]);
	    $result->bindValue(':preis_2_17', $values["preis_2_17"]);
	    $result->bindValue(':zeit_3_17', $values["zeit_3_17"]);
	    $result->bindValue(':preis_3_17', $values["preis_3_17"]);
	    $result->bindValue(':zeit_4_17', $values["zeit_4_17"]);
	    $result->bindValue(':preis_4_17', $values["preis_4_17"]);
	    $result->bindValue(':zeit_1_18', $values["zeit_1_18"]);
	    $result->bindValue(':preis_1_18', $values["preis_1_18"]);
	    $result->bindValue(':zeit_2_18', $values["zeit_2_18"]);
	    $result->bindValue(':preis_2_18', $values["preis_2_18"]);
	    $result->bindValue(':zeit_3_18', $values["zeit_3_18"]);
	    $result->bindValue(':preis_3_18', $values["preis_3_18"]);
	    $result->bindValue(':zeit_4_18', $values["zeit_4_18"]);
	    $result->bindValue(':preis_4_18', $values["preis_4_18"]);
	    if ( $result->execute()) {
		    return true;
	    } else {
		    return false;
	    }
    }
    
    public function update($values){
	  $projectId = $values[1];
	  $column = $values[2];
	  $value = $values[3];
	  $sql = 'UPDATE Project_Calculation SET '.$column.' = :value WHERE projectId = :projectId';
	  $result=$this->dbHandler->prepare($sql);
	  $result->bindValue(':projectId', $projectId);
	  $result->bindValue(':value', $value);
	  if ($result->execute()) {
	      return 'success';
	  } else {
	      return 'false';
	  }
    }
}