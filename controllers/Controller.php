<?php

class Controller {

    protected $tf;
    protected $pathToTemplates;
    
    

    public function __construct($tf, $pathToTemplates) {
        $this->tf = $tf;
        $this->pathToTemplates = $pathToTemplates;
    }

    public function redirect($controller, $action, $param) {
        $url = '?';
        if (isset($controller) && !empty($controller)) {
            $url.='cntr=' . $controller;
        }
        if (isset($action) && !empty($action)) {
            $url.='&action=' . $action;
        }
        if (isset($param) && !empty($param)) {
            $url.='&' . $param;
        }
        header('Location: ' . $url);
        exit;
    }

    
    public function arrayfieldsIsset() {
        $arrayfields = array();
        return $arrayfields;
    }

    public function controlParam($param, $controller, $action, $param) {
        if (!isset($param) || empty($param)) {
            parent::redirect($controller, $action, $param);
        }
    }
    
    public function getTplName($tpl) {
        return $this->pathToTemplates.$tpl.'.php';
    }
    
    public function render() {
      
    }

}

?>
