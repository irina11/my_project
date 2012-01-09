<?php
class TableFactory
{
    private $mysqlConnect;
    private $PathToTable;
    private $PathToDatabase;
    private $object=array();
    
    public function __construct($arrayConnect,$PathTo)
    {
        $this->PathToDatabase = $PathTo.'/database/';
        $this->PathToTable = $PathTo.'/tables/';
        $this->mysqlConnect = $arrayConnect;
    }
    
    private function existsIncludeFile($nameFile,$Path)
    {
       try
        {
          if(file_exists($Path.$nameFile))
          {
            require_once $Path.$nameFile;  
          }
          else
          {
          throw new Exception('Не найден файл '.$Path.$nameFile);  
          }
        }
        catch (Exception $ex)
        {
          echo $ex->getMessage();  
          die;
        }
    }
    
    private function getDb()
    {
       $this->existsIncludeFile('Database.php', $this->PathToDatabase);
       $this->existsIncludeFile('MySqlDb.php', $this->PathToDatabase); 
       if (!array_key_exists('MySqlDb', $this->object))
       {
          $this->object['MySqlDb'] = new MySqlDb($this->mysqlConnect);  
       }   
       return $this->object['MySqlDb'];
    }

    private function getTbl($className)
    {
       $this->existsIncludeFile($className.'.php', $this->$PathToTable);
       if (!array_key_exists($className, $this->object))
       {
         $this->object[$className] = new $className($this->getDb());  
       }
       return $this->object[$className];
    }
    
    private function __get($className)
    {
       if ($className == 'MysqlDb' )
       {
          if (array_key_exists($className, $this->object)) 
          {
              $this->object[$className]->close();
          }
       }
       else return $this->getTbl($className);
    }
    
}
?>