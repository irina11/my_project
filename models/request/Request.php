<?php

class Request {

    public function getParam($keyNameController, $def='null') {
        if (array_key_exists($keyNameController, $_REQUEST)) {
            return $_REQUEST[$keyNameController];
        } else {
            if (!isset($_REQUEST) || empty($_REQUEST)) {
                return $def;
            } else {
                throw new Exception('Не найден адрес');
            }
        }
    }
}
?>
