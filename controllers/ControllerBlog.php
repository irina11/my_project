<?php

class ControllerBlog extends Controller {

    protected $notificationsList = array();

    public function render($content,$headerName,$p) {
        $pMain = array();
        $pHeader = array();
        $p['isLoggedUser'] = $this->isLoggedUser();
        if (empty($content)) {
            $pHeader = $p;
        } else {
            $v=new View($this->getTplName($content));
            $pMain['content'] = $v->render($p);
        }
        $v=new View($this->getTplName($headerName));
        $pMain['header'] = $v->render($pHeader);
        if (isset($p['flag'])) {$pMain['flag'] = $p['flag'];}
        $pMain['notificationsList'] = array();
        $pMain['notificationsList'] = $this->notificationsList;
        $v=new View($this->getTplName('main_template'));
        return $v->render($pMain);
    }
    
    public function headerName() {
        if ($this->isLoggedUser()) {
            return $headerName = 'autorized';
        } else {
            return $headerName = 'auth_page';
        }
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

        $resultPostsTagsList = $this->tf->blog_tags->select('t.tag', 'pt.post_id')->from('blog_tags t')->leftJoin('blog_posts_tags pt', 't.id = pt.tag_id')->where($strQuery,'')->executeQuery();

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

    public function formData($postsListPrew, $flag) {
        if (isset($postsListPrew) && !empty($postsListPrew)) {
            $postsListPrew = $this->dataList($postsListPrew);
            $data = array();
            $data['postsList'] = $postsListPrew;
            if (isset($flag)&&!empty ($flag)) {
                $data['flag'] = 1;
            }
            return $data;
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

}

?>
