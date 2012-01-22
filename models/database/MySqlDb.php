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

    function execute($Sql) {
        
    }

    function connectBd() {
        $this->mysqlConnect = mysql_connect($this->connect[0], $this->connect[1], $this->connect[2]);
        if (!$this->mysqlConnect) {
            throw new Exception('Невозможно подключиться к бд');
        }
        if (!mysql_select_db($this->connect[3])) {
            throw new Exception('Не находит бд');
        }
        $this->query('SET NAMES utf8');
        return;
    }

    public function query($Sql) {
        $resultsList = array();
        $queryResult = mysql_query($Sql);
        if (!$queryResult) {
            throw new Exception('Не выполнился запрос ' . $Sql);
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
            if ($SQLWord == 'insert') {
                return mysql_insert_id();
            } else {
                return mysql_affected_rows();
            }
        }
    }

    public function arrayFieldsType($tablename) {
        $arrayColumn = $this->query('SHOW columns FROM ' . $tablename);
        if (!$arrayColumn) {
            throw new Exception('Не выполнился запрос ' . $Sql);
        }
        while ($row = mysql_fetch_assoc($arrayColumn)) {
            $arrayFieldsTypeTbl[$row['Field']] = $row['Type'];
        }
        return $arrayFieldsTypeTbl;
    }

}

?>
