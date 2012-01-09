<?php

class MySqlDb extends Database {

	private $connect;
	private $mysqlConnect;

	function __construct($ArrayDbParams) {
		$this->connect = $ArrayDbParams;
	}

	function MySqlDb() {
		try {
			$conn = mysql_connect($this->connect[0], $this->connect[1], $this->connect[2]);
			if (!$conn) {
				throw new Exception('Невозможно подключиться к бд');
			} else {
				$this->mysqlConnect = $conn;
			}
			if (!mysql_select_db($this->connect[3])) {
				throw new Exception('Не находит бд');
			}
		} catch (Exception $ex) {
			return false;
		}
		return true;
	}

	public function query($Sql) {
		mysql_query('SET NAMES utf8');
		return mysql_query($Sql);
	}

	public function close() {
		return mysql_close($this->mysqlConnect);
	}

	public function arrayFieldsType($tablename) {
		try {
			$arrayColumn = $this->query('SHOW columns FROM ' . $tablename);
			while ($row = mysql_fetch_assoc($arrayColumn)) {
				$arrayFieldsType[$tablename][$row['Field']] = $row['Type'];
			}
			return $arrayFieldsType;
		} catch (Exception $ex) {
			echo $ex->getMessage();
			exit;
		}
	}

}

?>
