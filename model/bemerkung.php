<?php

class Bemerkung
{
	private $dbHandler;

	function __construct($dbHandler, $id = null) {

		$this->dbHandler = $dbHandler;
		
	}
	
	public function createNewList($array) {
		$sql = "INSERT INTO Bemerkung (projectId, desc1, desc5, desc1_an, desc1_au, desc1_pm, desc1_li, desc1_re) 
		VALUES (:projectId, :desc1, :desc5, :desc1_an, :desc1_au, :desc1_pm, :desc1_li, :desc1_re)";
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $array[0]);
		$result->bindValue(':desc1', $array[1]);
		$result->bindValue(':desc5', $array[2]);
		$result->bindValue(':desc1_an', $array[3]);
		$result->bindValue(':desc1_au', $array[4]);
		$result->bindValue(':desc1_pm', $array[5]);
		$result->bindValue(':desc1_li', $array[6]);
		$result->bindValue(':desc1_re', $array[7]);
		if ( $result->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function getBemerkungList($id) {
		$sql = 'SELECT * FROM Bemerkung WHERE projectId = :id';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':id', $id);
		$result->execute();
		$array = $result->fetch();
		$final = array(
			'first' => $array['desc1'], 
			'second' => $array['desc2'], 
			'third' => $array['desc3'], 
			'forth' => $array['desc4'], 
			'fifth' => $array['desc5'],
			'firstAn' => $array['desc1_an'], 
			'secondAn' => $array['desc2_an'], 
			'thirdAn' => $array['desc3_an'], 
			'forthAn' => $array['desc4_an'], 
			'firstAu' => $array['desc1_au'],
			'secondAu' => $array['desc2_au'],
			'thirdAu' => $array['desc3_au'],
			'forthAu' => $array['desc4_au'],
			'firstPm' => $array['desc1_pm'],
			'secondPm' => $array['desc2_pm'],
			'thirdPm' => $array['desc3_pm'],
			'forthPm' => $array['desc4_pm'],
			'firstLi' => $array['desc1_li'],
			'secondLi' => $array['desc2_li'],
			'thirdLi' => $array['desc3_li'],
			'forthLi' => $array['desc4_li'],
			'firstRe' => $array['desc1_re'],
			'secondRe' => $array['desc2_re'],
			'thirdRe' => $array['desc3_re'],
			'forthRe' => $array['desc4_re']
			);
		return $final;
	}

	public function insert($self, $projectId){
		$sql = 'INSERT INTO Bemerkung (projectId, desc1, desc2, desc3, desc4, desc1_an, desc1_au, desc1_pm, desc1_li, desc1_re, desc2_an, 		desc2_au, desc2_pm, desc2_li, desc2_re, desc3_au, desc3_an, desc3_pm, desc3_li, desc3_re, desc4_an,
			desc4_au, desc4_pm, desc4_li, desc4_re, desc5) VALUES (:projectId, :desc1, :desc2, :desc3, :desc4, :desc1_an, :desc1_au, :desc1_pm, :desc1_li, :desc1_re, :desc2_an, :desc2_au, :desc2_pm, :desc2_li, :desc2_re, :desc3_au, :desc3_an, :desc3_pm, :desc3_li, :desc3_re, :desc4_an,:desc4_au, :desc4_pm, :desc4_li, :desc4_re, :desc5)';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':projectId', $projectId);
		$result->bindValue(':desc1', $self['first']);
		$result->bindValue(':desc2', $self['second']);
		$result->bindValue(':desc3', $self['third']);
		$result->bindValue(':desc4', $self['forth']);
		$result->bindValue(':desc1_an', $self['firstAn']);
		$result->bindValue(':desc2_an', $self['secondAn']);
		$result->bindValue(':desc3_an', $self['thirdAn']);
		$result->bindValue(':desc4_an', $self['forthAn']);
		$result->bindValue(':desc1_au', $self['firstAu']);
		$result->bindValue(':desc2_au', $self['secondAu']);
		$result->bindValue(':desc3_au', $self['thirdAu']);
		$result->bindValue(':desc4_au', $self['forthAu']);
		$result->bindValue(':desc1_pm', $self['firstPm']);
		$result->bindValue(':desc2_pm', $self['secondPm']);
		$result->bindValue(':desc3_pm', $self['thirdPm']);
		$result->bindValue(':desc4_pm', $self['forthPm']);
		$result->bindValue(':desc1_li', $self['firstLi']);
		$result->bindValue(':desc2_li', $self['secondLi']);
		$result->bindValue(':desc3_li', $self['thirdLi']);
		$result->bindValue(':desc4_li', $self['forthLi']);
		$result->bindValue(':desc1_re', $self['firstRe']);
		$result->bindValue(':desc2_re', $self['secondRe']);
		$result->bindValue(':desc3_re', $self['thirdRe']);
		$result->bindValue(':desc4_re', $self['forthRe']);
		$result->bindValue(':desc5', $self['fifth']);
		if ( $result->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function update($value) {
		$projectId = $_SESSION['projectId'];
		$explode = explode('<><>', $value);
		$column = $explode[0];
		$data = $explode[1];
		$sql = 'UPDATE Bemerkung SET '.$column.' = :data WHERE projectId = :projectId';
		$result=$this->dbHandler->prepare($sql);
		$result->bindValue(':data', $data);
		$result->bindValue(':projectId', $projectId);

		if ($result->execute()) {
			return 'success';
		} else {
			return 'false';
		}
	}
}