<?php
class FrontController {

    private $pathC;
    private $tf;
    private $pathT;

    public function __construct($pathToControllers, $tf, $pathToTemplates) {
        session_start();
        $this->pathC = $pathToControllers;
        $this->tf = $tf;
        $this->pathT = $pathToTemplates;
        
    }

    private function getController($nameControllers) {

        try {
            if (file_exists($this->pathC . 'Controller.php')) {
                require_once($this->pathC . 'Controller.php');
                if ($nameControllers == 'null') {
                    if (file_exists($this->pathC . 'DefaultController.php')) {
                        require_once($this->pathC . 'DefaultController.php');
                        return new DefaultController($this->tf,$this->pathT);
                    } else {
                        throw new Exception('Файл ' . $this->pathC . 'DefaultController.php' . ' не найден!');
                    }
                } else {
                    $nameControllers = ucwords($nameControllers);
                    if (file_exists($this->pathC . $nameControllers . 'Controller.php')) {
                        require_once($this->pathC . $nameControllers . 'Controller.php');
                        $nameControllers.='Controller';
                        $a=new $nameControllers($this->tf,$this->pathT);
                        return $a;
                    } else {
                        throw new Exception('Файл ' . $this->pathC . $nameControllers . 'Controller.php' . ' не найден!');
                    }
                }
            } else {
                throw new Exception('Файл ' . $this->pathC . 'Controller.php' . ' не найден!');
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

    public function dispatch(Request $r) {
        $c = $this->getController($r->getParam('cntr'));
        $c->{$r->getParam('action')}($r);
        echo $c->getHTML();

/*
        $method = $r->getParam('action');
        try{
            if method_exists($c,$method) {
                $c->{$method}($r);
                echo $c->getHTML();
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
*/

    }
 

}

?>