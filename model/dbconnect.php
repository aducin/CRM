<?php

class DbConnect
{
	private $pdo;

	function __construct($host, $login, $password){

		$this->host = $host;
		$this->login = $login;
		$this->password = $password;

		try
		{
			$this->pdo=new PDO($host, $login, $password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->exec('SET NAMES "utf8"');
		}
		catch(PDOException $e)
		{
			$error='Połączenie z bazą danych nie mogło zostać utworzone: ' . $e->getMessage();
		}
	}

	public function getDb() {
		if ($this->pdo instanceof PDO) {
			return $this->pdo;
		}
	}

	public function __destruct()
	{
		if(is_resource($this->pdo)) {
			$this->pdo -> closeCursor();
		}
	}
}