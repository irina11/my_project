<?php

class DefaultController extends ControllerBlog {

    public function null(Request $r) {
        $v=new View($this->getTplName('start'));
        echo $v->render('');
    }

    
}

?>
