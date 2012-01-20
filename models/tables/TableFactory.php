<?php

class TableFactory {

    private $db;
    private $PathToTable;
    private $PathToDatabase;
    private $object = array();

    public function __construct($db, $PathTo) {
        $this->PathToDatabase = $PathTo . 'database/';
        $this->PathToTable = $PathTo . 'tables/';
        $this->db = $db;
    }

    private function existsIncludeFile($nameFile, $Path) {
        try {
            if (file_exists($Path . $nameFile)) {
                require_once $Path . $nameFile;
            } else {
                throw new Exception('Не найден файл ' . $Path . $nameFile);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die;
        }
    }

    private function getTbl($className) {

        if (isset($this->db) && !empty($this->db)) {
            $this->existsIncludeFile('TableGateway.php', $this->PathToTable);
            $this->existsIncludeFile($className . '.php', $this->PathToTable);
            $this->existsIncludeFile('SQLSpecification.php', $this->PathToTable);
            if (!array_key_exists($className, $this->object)) {
                $this->object[$className] = new $className($this->db);
            }
            return $this->object[$className];
        } else {
            echo 'Не создан объект БД';
            exit;
        }
    }

    public function __get($className) {
        return $this->getTbl($className);
    }

}

?>