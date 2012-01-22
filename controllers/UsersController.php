<?php

class UsersController extends ControllerBlog {

    private $blog_user = 'admin';
    private $blog_pass = '111';

    public function logout() {
        if (isset($_SESSION['user_loggedin'])) {
            unset($_SESSION['user_loggedin']);
        }
        $this->redirect();
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
                $this->redirect();
            } else {
                $data=array();
                $data['authErrors'] = 'Не верно указаны логин или пароль';
                echo $this->render('', $data);
            }
        } else $this->redirect();
    }
}

?>
