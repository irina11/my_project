<?php

class ChatController extends ControllerBlog {

    public function begin(Request $r) {
        echo $this->render('chatView', '', array('flag' => 1));
    }

    public function insert(Request $r) {
        $chat = $r->getParam('chat');
        $arrayfields = $this->arrayfieldsIsset();
        $arrayfields['user'] = array();
        $arrayfields['message'] = array();
        $arrayfields['user'][] = trim($chat['user']);
        $arrayfields['message'][] = trim($chat['message']);
        $chatId = $this->tf->chat->insertTbl($arrayfields, '');
    }
    
    public function getJSON(Request $r) {
        $chat = $r->getParam('lastId');
        if (empty ($chat)) $chat='0';
        $chatListPrew = $this->tf->chat->select()->from()->where('id>' . $chat,'')->order(' date ')->executeQuery();
        $v=new View('');
        echo $v->output($chatListPrew);
    }

}

?>
