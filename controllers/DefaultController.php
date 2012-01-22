<?php

class DefaultController extends ControllerBlog {

    public function null(Request $r) {
        $postsListPrew = $this->tf->blog_posts->select()->from()->order(' date DESC ')->executeQuery();
        echo $this->render('post_default', $this->formData($postsListPrew, 1));
    }

    public function message(Request $r) {
        $textMessage = $r->getParam('text');
        switch ($textMessage) {
            case 'authRequired':
                $this->notificationsList[] = 'Для выполнения этого действия пользователь должен быть авторизированным';
            case 'nopost':
                $this->notificationsList[] = 'Указанный пост не найден';
            case 'noedit':
                $this->notificationsList[] = 'Отсутствуют основные данные. Изменения не сохранены';    
        }
        $this->null($r);
    }
}

?>
