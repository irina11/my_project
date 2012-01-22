<?php

class TableGateWay {

    private $tablename;
    private $database;
    private $arrayFieldsTypeTbl = array();

    public function __construct($db, $tbname) {
        $this->tablename = $tbname;
        $this->database = $db;
    }

    protected function fieldCorrectedValue($nameField, $value) {



        if (!isset($this->arrayFieldsTypeTbl) || empty($this->arrayFieldsTypeTbl)) {
            $this->arrayFieldsTypeTbl = $this->database->arrayFieldsType($this->tablename);
        }

        $FieldTypeShort = array();
        $FieldTypeShort = explode('(', $this->arrayFieldsTypeTbl[$nameField]);
        $FieldTypeShort = strtolower(trim($FieldTypeShort[0]));
        if (in_array($FieldTypeShort, array('int', 'bigint', 'tinyint', 'smallint', 'mediumint'))) {
            $value = intval($value);
        } elseif (in_array($FieldTypeShort, array('decimal', 'float', 'double', 'real'))) {
            $value = floatval($value);
        } elseif ($FieldTypeShort == 'bool') {
            $value = (bool) $value;
        } else {
            $value = '\'' . addslashes($value) . '\'';
        }
        return $value;
    }

    protected function formationWhere($reservedWord, array $where) {
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
                    foreach ($val as $keyvalVal => $valVal) {
                        $expression.=$this->fieldCorrectedValue($name, $valVal);
                        if ($keyvalVal < (count($val) - 1)) {
                            $expression.=',';
                        }
                    }
                    $expression.=') ';
                    break;
                default:
                    $expression.=$name . '=' . $this->fieldCorrectedValue($name, $val);
            }
        }
        return $expression;
    }

    protected function formationOrder(array $order) {
        $expression = '';
        reset($order);
        $firstKey = key($order);

        foreach ($order as $name => $val) {

            if (trim($name) == $firstKey) {
                $expression.=' ORDER BY ';
            } else {
                $expression.=',';
            }
            $expression.=$name;
            if (isset($val) && !empty($val)) {
                $expression.=' ' . $val;
            }

        }
        return $expression;
    }

    function insertTbl(array $fields, $duplicateExpression) {
        reset($fields);
        $firstKey = trim(key($fields));
        $quantityMin = 1;
        $keys = '';
        foreach ($fields as $name => $value) {
            if (trim($name) <> $firstKey) {
                $keys.=',';
            } else {
                $quantityMin = sizeof($fields[$name]);
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
        $values = '';

        for ($i = 0; $i < $quantityMin; $i++) {
            $values.='(';
            foreach ($fields as $name => $val) {
                if (trim($name) <> $firstKey) {
                    $values.=',';
                }
                $values.= $this->fieldCorrectedValue($name, $val[$i]);
            }
            $values.=')';
            if ($i < ($quantityMin - 1)) {
                $values.=',';
            }
        }
        $Sql = 'INSERT INTO ' . $this->tablename . ' (' . $keys . ') VALUES ' . $values;
        if (!empty($duplicateExpression)) {
            $Sql.=' ON DUPLICATE KEY UPDATE ' . $duplicateExpression;
        }
        return $this->database->query($Sql);
    }

    function selectTbl(array $where, array $order) {
        $Sql = 'SELECT * FROM ' . $this->tablename . $this->formationWhere('IN', $where) . $this->formationOrder($order);
        return $this->database->query($Sql);
    }

    function deleteTbl(array $fields) {
        $Sql = 'DELETE FROM ' . $this->tablename . $this->formationWhere('', $fields);
        return $this->database->query($Sql);
    }

    function updateTbl(array $fields, array $where) {
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
        $Sql = 'UPDATE ' . $this->tablename . $expression . $this->formationWhere('', $where);
        return $this->database->query($Sql);
    }

    function select() {
        $sp = new SQLSpecification($this->database,  $this->tablename);
        $fields = func_get_args();
        $sp->select($fields);
        return $sp;
    }

}

?>
