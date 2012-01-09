<?php

class TableGateWay {

	private $tablename;
	private $database;
	private $arrayFieldsType=array();
	public function __construct($db, $tbname) {
		$this->tablename = $tbname;
		$this->database = $db;
	}

	
	protected function fieldCorrectedValue($nameField, $value) {
		if(!isset($this->arrayFieldsType)||empty ($this->arrayFieldsType[$this->tablename]))
		{
			$this->arrayFieldsType[$this->tablename] = $this->database->arrayFieldsType($this->tablename);
		}
		$FieldTypeShort = explode('(', $this->arrayFieldsType[$this->tablename][$nameField]);
		$FieldTypeShort = strtolower(trim($FieldTypeShort[0]));
		if (in_array($FieldTypeShort, array('int', 'bigint', 'tinyint', 'smallint', 'mediumint'))) {
			$value = intval($value);
		} elseif (in_array($FieldTypeShort, array('decimal', 'float', 'double', 'real'))) {
			$value = floatval($value);
		} elseif ($FieldTypeShort == 'bool') {
			$value = (bool)$value;
		} else {
			$value = '\'' . addslashes($value) . '\'';
		}
		return $value;
	}

	protected function formationWhere($reservedWord,$where) {
		$expression = '';
		
		reset($where);
		$firstKey = key($where);

		foreach ($where as $name => $val) {

			if (trim($name) == $firstKey) {
				$expression.=' WHERE ';
			} else {
				$expression.=' AND ';
			}
			switch ($reservedWord) {
				case 'IN':
					$expression.=$name . ' IN ('; 
					foreach ($val as $valVal) {
						$expression.=$this->fieldCorrectedValue($name, $valVal);
					}
					$expression.=') ';
				default:
					$expression.=$name . '=' . $this->fieldCorrectedValue($name, $val);
			}
		}
		return $expression;
	}
	protected function formationOrder($order) {
		$expression = '';
		reset($where);
		$firstKey = key($where);

		foreach ($where as $name => $val) {

			if (trim($name) == $firstKey) {
				$expression.=' ORDER BY ';
			} else {
				$expression.=',';
			}
			$expression.=$name; 
			if (isset($val)&&!empty($val)) {
				$expression.=' '.$val;
			}
			$expression.=') ';
		}
		return $expression;
	}

		function insertTbl($fields, $duplicateExpression) {
		reset($fields);
		$firstKey = trim(key($fields));
		$quantityMin = 0;
		$keys = '';
		foreach ($fields as $name => $value) {
			if (trim($name) <> $firstKey) {
				$keys.=',';
			}
			$keys.=$name;
			$quantityValue = sizeof($fields[$name]);
			if ($quantityMin > $quantityValue) {
				$quantityMin = $quantityValue;
			}
		}
		if ($quantityMin == 0) {
			return 0;
		}
		$Values = '';

		for ($i = 0; $i < $quantityMin; $i++) {
			$values.='(';
			foreach ($fields as $name => $val) {
				if (trim($name) <> $firstKey) {
					$values.=',';
				}
				$values.= $this->fieldCorrectedValue($name, $val[i]);
			}
			$values.=')';
			if (i < ($quantityMin - 2)) {
				$values.=',';
			}
		}
		$Sql = 'INSERT INTO ' . $this->tablename . ' (' . $keys . ') VALUES ' . $values;
		if (!empty($duplicateExpression)) {
			$Sql.=' ON DUPLICATE KEY UPDATE ' . $duplicateExpression;
		}
		$this->database->query($Sql);
		return mysql_affected_rows();
	}

	function resultSelect($Sql) {
		$queryResult = $this->database->query($Sql);
		$resultsList=array();
		if (mysql_num_rows($queryResult) == 0)
			return $resultsList;
		while ($row = mysql_fetch_assoc($queryResult)) {
			$resultsList[] = $row;
		}
		mysql_free_result($queryResult);
		return $resultsList;
	}
	
	function selectTbl($where,$order) {
		$Sql = 'SELECT * FROM ' . $this->tablename.$this->formationWhere('IN',$where).$this->formationOrder($order);
		return $this->resultSelect($Sql);
	}

	function deleteTbl($fields) {
		$Sql = 'DELETE FROM ' . $this->tablename . $this->formationWhere('',$fields);
		$this->database->query($Sql);
		return mysql_affected_rows();
	}

	function updateTbl($fields, $where) {
		$expression = '';
		reset($fields);
		$firstKey = key($fields);

		foreach ($fields as $name => $val) {
			if (trim($name) == $firstKey) {
				$expression.=' SET ';
			} else {
				$expression.=',';
			}
			$expression.=$name . '=' . $this->fieldCorrectedValue($name, $val);
		}
		$Sql = 'UPDATE ' . $this->tablename . $expression . $this->formationWhere('',$where);
		$this->database->query($Sql);

		return mysql_affected_rows();
	}

}

?>
