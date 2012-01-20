<?php

class UsersController extends Controller {

    private $blog_user = 'admin';
    private $blog_pass = '111';

    public function __construct($tf, $pathToTemplates) {
        parent:: __construct($tf, $pathToTemplates);
    }

    public function logout() {
        if (isset($_SESSION['user_loggedin'])) {
            unset($_SESSION['user_loggedin']);
        }
        parent::redirect();
    }

    public function login($r) {
        $authErrors = false;
        $user = array();
        $user = $r->getParam('user');

        if (isset($user) && isset($user['login']) && isset($user['pass'])) {
            $user['login'] = trim($user['login']);
            $user['pass'] = trim($user['pass']);
            if ($user['login'] == $this->blog_user && $user['pass'] == $this->blog_pass) {
                $_SESSION['user_loggedin'] = true;
                parent::redirect();
            } else {
                $this->variableBlock['header'] = 'auth_page';
                $this->data['isLoggedUser'] = false;
                $this->variableBlock['content'] = '';
                $this->data['authErrors'] = 'Не верно указаны логин или пароль';
            }
        }
        //parent::redirect();
    }

    public function getHTML() {
        return parent::getHTML();
    }

}

?>
