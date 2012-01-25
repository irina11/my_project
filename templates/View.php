<?php

class View {

    private $tpl;

    public function __construct($tpl) {
        $this->tpl = $tpl;
    }

    public function render($data) {
        if (is_array($data)) {
            foreach ($data as $nameParam => $value) {
                $$nameParam = $value;
            }
        }
        $html = '';
        if (file_exists($this->tpl)) {
            ob_start();
            require_once($this->tpl);
            $html = ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }
    
    public function output($data) {
        return json_encode($data);
    }

}

?>
