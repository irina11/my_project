<?php

class Controller {

    protected $tf;
    protected $pathToTemplates;
    protected $notificationsList = array();
    protected $data = array();
    protected $variableBlock = array();

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

    public function dataList($postsListParam) {
        $postsListReturn = $postsListParam;
        $strQuery = 'post_id IN (';
        foreach ($postsListParam as $key => $post) {
            $strQuery.= $post['id'];
            if ($key < (count($postsListParam) - 1)) {
                $strQuery.= ',';
            }
        }
        $strQuery.=')';

        $resultPostsTagsList = $this->tf->blog_tags->select('t.tag', 'pt.post_id')->from('blog_tags t')->leftJoin('blog_posts_tags pt', 't.id = pt.tag_id')->where($strQuery)->executeQuery();

        $postsTagsList = array();
        foreach ($resultPostsTagsList as $postTag) {
            if (!isset($postsTagsList[$postTag['post_id']])) {
                $postsTagsList[$postTag['post_id']] = array();
            }

            if (!in_array($postTag['tag'], $postsTagsList[$postTag['post_id']])) {
                $postsTagsList[$postTag['post_id']][] = $postTag['tag'];
            }
        }

        foreach ($postsListParam as $rowNumber => $post) {
            if (isset($postsTagsList[$post['id']])) {
                $postsListReturn[$rowNumber]['tags'] = $postsTagsList[$post['id']];
            }
        }

        return $postsListReturn;
    }

    public function arrayfieldsIsset() {
        $arrayfields = array();
        return $arrayfields;
    }

    public function formData($postsListPrew, $flag) {
        if ($postsListPrew) {
            $postsList = $this->dataList($postsListPrew);
            if ($this->isLoggedUser()) {
                $this->variableBlock['header'] = 'autorized';
                $this->data['isLoggedUser'] = true;
            } else {
                $this->variableBlock['header'] = 'auth_page';
                $this->data['isLoggedUser'] = false;
            }
            $this->variableBlock['content'] = 'post_default';
            $this->data['notificationsList'] = array();
            $this->data['notificationsList'] = $this->notificationsList;
            if (isset($flag)) {
                $this->data['flag'] = 1;
            }
            $this->data['postsList'] = array();
            $this->data['postsList'] = $postsList;
        }
    }

    public static function isLoggedUser() {
        return isset($_SESSION['user_loggedin']);
    }

    public function controlisLoggedUser($controller, $action, $param) {
        if (!$this->isLoggedUser()) {
            return $this->redirect($controller, $action, $param);
        }
    }

    public function controlParam($param, $controller, $action, $param) {
        if (!isset($param) || empty($param)) {
            parent::redirect($controller, $action, $param);
        }
    }

    public function getHTML() {
        if (file_exists($this->pathToTemplates . 'View.php')) {
            require_once($this->pathToTemplates . 'View.php');
            $v = new View($this->pathToTemplates);
            return $v->render($this->variableBlock, $this->data);
        }
    }

}

?>
