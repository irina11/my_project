<?php
class View {

    private $pathToTemplates;

    public function __construct($pathToTemplates) {
        $this->pathToTemplates = $pathToTemplates;
    }
    
    public function render($vblock,$data){
        foreach ($data as $nameParam => $value) {
            $$nameParam=$value;
        }
        foreach ($vblock as $nblock => $tp) {
            if (!empty ($tp)&&file_exists($this->pathToTemplates.$tp.'.php')) {
                ob_start();
                require_once($this->pathToTemplates.$tp.'.php');
                $$nblock=ob_get_contents();
                ob_end_clean();
            }
        }
        if (file_exists($this->pathToTemplates.'main_template.php')) {
                ob_start();
                require_once($this->pathToTemplates.'main_template.php');
                $html=ob_get_contents();
                ob_end_clean();
        }
        
        return $html;
    }
}
?>
