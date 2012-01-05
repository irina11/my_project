<?php

class TableGateWay
{
 private $tablename;
 private $database;
 public function __construct($db,$tbname) 
 {
     $this->tablename = $tbname;
     $this->database = $db;
 
 }
 
 protected function arrayFieldsType() {
   $arrayColumn=$this->database->query('SHOW columns FROM '.$this->tablename);   
   while ($row = mysql_fetch_assoc($arrayColumn))
   {
    $arrayFieldsType[$row[Field]] = $row[Type];
   }
    return $arrayFieldsType;  
  }
 
 protected function fieldCorrectedValue($FieldType,$value)
 {
   $FieldTypeShort = explode('(', $FieldType);
   $FieldTypeShort = strtolower(trim($FieldTypeShort[0]));  
   if (in_array($FieldTypeShort,array('int','bigint','tinyint','smallint','mediumint')))
   {
    $value=intval($value);    
   }
   elseif (in_array($FieldTypeShort,array('decimal','float','double','real')))
   {    
    $value=floatval($value);     
   } 
   elseif ($FieldTypeShort=='bool')
   {    
    $value=$value;     
   } 
   else 
  {
   $value='\'' .addslashes($value ).'\'' ;   
  }
   return $value;     
 } 
  
 function insertTbl($fields,$duplicateExpression)
 { 
    reset($fields);
    $firstKey=trim(key($fields)); 
    $quantityMin=0;  
    $keys='';
    foreach($fields as $name=>$value)
    {
       if (trim($name) <> $firstKey)
       {
          $keys.=',';
       } 
       $keys.=$name;
       $quantityValue=sizeof($fields[$name]);
       if($quantityMin>$quantityValue)
       {
           $quantityMin=$quantityValue;
       }    
    }

    if ($quantityMin==0) {return 0;}
    
    $arrayFieldsType=$this->arrayFieldsType(); 
    $Values='';
    
    for ($i=0; $i<$quantityMin; $i++)
    {
        $values.='(';
        foreach ($fields as $name=>$val) 
        {
            if (trim($name) <> $firstKey)
            {
                $values.=',';
            }
            $values.= $this->fieldCorrectedValue($name,$val[i]);
        }
        $values.=')';
        if (i<($quantityMin-2))
        {
           $values.=','; 
        }
    }
    $Sql = 'INSERT INTO '.$this->tablename.' ('.$keys.') VALUES '.$values;
    if (!empty($duplicateExpression)) 
    {
       $Sql.=' ON DUPLICATE KEY UPDATE '.$duplicateExpression; 
    }
    $this->database->query($Sql);
    return mysql_affected_rows();
 }   
  
 function findAllTbl()
 {
   $Sql = 'SELECT * FROM '.$this->tablename;
   $queryResult=$this->database->query($Sql);   
   if (mysql_num_rows($queryResult)== 0) return $resultsList;
   while ($row = mysql_fetch_assoc($queryResult))
   {
    $resultsList[] = $row;
   }
   mysql_free_result($queryResult);
   return $resultsList;   
      
 }
  
 function deleteTbl($fields) 
 {
    $arrayFieldsType=$this->arrayFieldsType();  
    $expression='';
    reset($fields);
    $firstKey=key($fields);
   
    foreach($fields as $name=>$val)
    {
  
        if (trim($name) == $firstKey)
        {
        $expression.=' WHERE ';
        }
        else
        {
          $expression.=' AND ';

        }
        $expression.=$name.'='.$this->fieldCorrectedValue($arrayFieldsType[$name],$val);
    }
    $Sql = 'DELETE FROM '.$this->tablename.$expression;
 
    $this->database->query($Sql);
    return mysql_affected_rows();   
  
 }
 
 function updateTbl($fields,$where) 
 {
    $arrayFieldsType=$this->arrayFieldsType();  
    $expression='';
    reset($fields);
    $firstKey=key($fields);
   
    foreach($fields as $name=>$val)
    {
  
        if (trim($name) == $firstKey)
        {
        $expression.=' SET ';
        }
        else
        {
          $expression.=',';

        }
        $expression.=$name.'='.$this->fieldCorrectedValue($arrayFieldsType[$name],$val);
    }
    reset($where);
    $firstKey=key($where);
   
    foreach($where as $name=>$val)
    {
  
        if (trim($name) == $firstKey)
        {
        $expression.=' WHERE ';
        }
        else
        {
          $expression.=' AND ';

        }
        $expression.=$name.'='.$this->fieldCorrectedValue($arrayFieldsType[$name],$val);
    }
    $Sql = 'UPDATE '.$this->tablename.$expression;
    $this->database->query($Sql);
    
    return mysql_affected_rows();   


 }
  
}
?>
