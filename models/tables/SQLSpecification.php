<?php

class SQLSpecification {

    private $sql;
    private $db;
    private $nameTbl;

    public function __construct($db,$nameTbl) {
        $this->db = $db;
        $this->nameTbl=$nameTbl;
        $this->sql = '';
    }

    public function sortArray(array $fields) {
        $sql = '';
        foreach ($fields as $key => $arg) {
            $sql.=$arg;
            if ($key < (count($fields) - 1)) {
                $sql.=',';
            }
        }
        return $sql;
    }
    
    private function structure($keyWord,$fields,$default) {
        
        if (empty($this->sql)) {
            $this->sql = $keyWord;
        } else {
            $this->sql.= $keyWord;
        }
        
        if (empty($fields) | (empty($fields[0]))) {
            $this->sql.=$default;

        } elseif (is_array($fields[0])) {
            $this->sql.= $this->sortArray($fields[0]);
            
        } else {
            $this->sql.=$this->sortArray($fields);
        }
      
        return;

    }

    function select() {
        $fields = func_get_args();
        $this->structure('SELECT ', $fields, '*');
        return $this;
    }
        

    function from() {
        $tbname = func_get_args();
        $this->structure(' FROM ', $tbname, '');
        return $this;
    }

    function leftJoin() {
        $fields = func_get_args();
        if (!empty($fields)) {
            $this->sql.=' LEFT JOIN ' . $fields[0] . ' ON ' . $fields[1];
        }
        return $this;
    }

    function where($whereSimple, $whereComposite) {
        $parameters = func_get_args();
        if (isset($parameters) && !empty($parameters)) {
            $this->sql.=' WHERE ';
            if (isset($whereSimple) && !empty($whereSimple)) {
                $this->sql.=$whereSimple;
            } else {
                $param = array();
                $param = explode('??', $whereComposite);
                foreach ($param as $str) {
                    $expression = array();
                    $expression = explode('?', $str);
                    $n = count($expression);
                    for ($i = 0; $i < $n; $i++) {
                        if ($i == 0) {
                            $this->sql.=' ' . $expression[$i] . ' ';
                        } else {
                            $values = array();
                            $values = explode(',', $expression[$i]);

                            foreach ($values as $key => $val) {
                                $this->sql.=$this->value($val);
                                if ($key < (count($values) - 1)) {
                                    $this->sql.=',';
                                }
                            }
                        }
                        if ($i > 0 && $i < ($n - 1)) {
                            $this->sql.=',';
                        }
                    }
                }
            }
        }
        return $this;
    }

    function order() {
        $fields = func_get_args();
        $this->structure(' ORDER BY ', $fields, '');
        return $this;
    }

    function foo($foo) {
        if (!empty($foo) && !empty($this->sql)) {
            $this->sql.=' ' . $foo;
        }
        return $this;
    }

    function executeQuery() {

        return $this->db->query1($this->sql);
    }

}

?>
