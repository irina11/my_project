<?php

class MySqlDb extends Database {

    private $connect;
    private $mysqlConnect;

    function __construct(array $ArrayDbParams) {
        $this->connect = $ArrayDbParams;
    }

    function __destruct() {
        return mysql_close($this->mysqlConnect);
    }

//	function execute(){}
    function connectBd() {
        try {
            $this->mysqlConnect = mysql_connect($this->connect[0], $this->connect[1], $this->connect[2]);
            if (!$this->mysqlConnect) {
                throw new Exception('Невозможно подключиться к бд');
            }
            if (!mysql_select_db($this->connect[3])) {
                throw new Exception('Не находит бд');
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
        return;
    }
    
    public function query($Sql) {
        mysql_query('SET NAMES utf8');
        return mysql_query($Sql);
    }


    public function query1($Sql) {
        $resultsList = array();
        mysql_query('SET NAMES utf8');
        try {
        $queryResult = mysql_query($Sql);
        if (!$queryResult) {
            throw new Exception('Не выполнился запрос ' . $Sql); 
        }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
        $SQLParam = explode(' ', $Sql);
        $SQLWord = strtolower($SQLParam[0]);
        if ($SQLWord == 'select') {
            if (mysql_num_rows($queryResult) == 0)
                return $resultsList;
            while ($row = mysql_fetch_assoc($queryResult)) {
                $resultsList[] = $row;
            }
            mysql_free_result($queryResult);
            return $resultsList;
        } else {
            return mysql_affected_rows();
        }

        
    }

    public function arrayFieldsType($tablename) {
        try {
            $arrayColumn = $this->query('SHOW columns FROM ' . $tablename);
            while ($row = mysql_fetch_assoc($arrayColumn)) {
                $arrayFieldsTypeTbl[$row['Field']] = $row['Type'];
            }
            return $arrayFieldsTypeTbl;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

}

?>
