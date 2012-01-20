<?php

class DefaultController extends Controller {

    public function __construct($tf, $pathToTemplates) {
        parent:: __construct($tf, $pathToTemplates);
    }

    public function null(Request $r) {
        $postsListPrew = $this->tf->blog_posts->select()->from('blog_posts')->order(' date DESC ')->executeQuery();
        parent::formData($postsListPrew, 1);
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

    public function getHTML() {
        return parent::getHTML();
    }

}

?>
