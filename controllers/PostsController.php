<?php

class PostsController extends ControllerBlog {

    public function listPosts(Request $r) {
        $postsListPrew = $this->tf->blog_posts->select()->from()->order(' date DESC ')->executeQuery();
        echo $this->render('post_default', $this->headerName(),$this->formData($postsListPrew, 1));
    }
    
    public function message(Request $r) {
        $textMessage = $r->getParam('text');
        switch ($textMessage) {
            case 'authRequired':
                $this->notificationsList[] = 'Для выполнения этого действия пользователь должен быть авторизированным';
                break;
            case 'nopost':
                $this->notificationsList[] = 'Указанный пост не найден';
                break;
            case 'noedit':
                $this->notificationsList[] = 'Отсутствуют основные данные. Изменения не сохранены';    
        }
        $this->listPosts($r);
    }
    
    public function view(Request $r) {
        $posts = $r->getParam('post_id');
        $postsListPrew = $this->tf->blog_posts->select()->from()->where('id=' . $posts,'')->executeQuery();
        echo $this->render('post_default', $this->headerName(), $this->formData($postsListPrew,''));
    }

    public function tag(Request $r) {
        $tag = $r->getParam('tag');
        if (!isset($tag)) {
            $this->redirect('posts', 'listPosts', '');
        }
        $postsListPrew = $this->tf->blog_posts->select('p.*')->from('blog_posts p')->leftJoin('blog_posts_tags pt', 'pt.post_id = p.id')->leftJoin('blog_tags t', 't.id = pt.tag_id')->where('tag=\'' . addslashes($tag) . '\'','')->order(' date DESC ')->executeQuery();
        echo $this->render('post_default', $this->headerName(), $this->formData($postsListPrew,''));
    }

    public function delete(Request $r) {
        $post_id = $r->getParam('post_id');
        $this->controlisLoggedUser('posts', 'message', 'text=authRequired');
        $this->controlParam($post_id, 'posts', 'message', 'text=nopost');
        $this->tf->blog_posts->deleteTbl(array('id' => $post_id));
        $this->redirect('posts', 'listPosts', '');
    }

    public function edit(Request $r) {
        $post_id = $r->getParam('post_id');
        $this->controlisLoggedUser('posts', 'message', 'text=authRequired');
        $this->controlParam($post_id, 'posts', 'message', 'text=nopost');

        $arrayfields = array();
        $arrayfields['id'] = array();
        $arrayfields['id'][] = $post_id;
        $post = $this->tf->blog_posts->selectTbl($arrayfields, array());
        $this->controlParam($post, 'posts', 'message', 'text=nopost');
        $tags = $this->tf->blog_tags->select('t.tag', 'pt.post_id')->from('blog_tags t')->leftJoin('blog_posts_tags pt', 't.id = pt.tag_id')->where('post_id=' . $post_id,'')->executeQuery();

        $tagsList = array();
        foreach ($tags as $tag) {
            $tagsList[] = stripslashes($tag['tag']);
        }

        $post = array_pop($post);
        $data = array();
        $data['post'] = array('id' => $post_id, 'title' => stripslashes($post['title']), 'content' => stripslashes($post['content']), 'tags' => implode(', ', $tagsList));
        echo $this->render('add_edit', $this->headerName(), $data);
    }

    public function add(Request $r) {
        $this->controlisLoggedUser('posts', 'message', 'text=authRequired');
        $data = array();
        echo $this->render('add_edit', $this->headerName(), $data);
    }

    public function add_edit(Request $r) {
        $post = array();
        $post = $r->getParam('post');
        $post['title'] = trim($post['title']);
        $post['content'] = trim($post['content']);
        if (empty($post['title']) || empty($post['content'])) {
            $this->redirect('posts', 'message', 'text=noedit');
        }
        if (empty($post['post_id'])) {
            $arrayfields = $this->arrayfieldsIsset();
            $arrayfields['title'] = array();
            $arrayfields['content'] = array();
            $arrayfields['title'][] = $post['title'];
            $arrayfields['content'][] = $post['content'];
            $postId = $this->tf->blog_posts->insertTbl($arrayfields, '');
            $post['tags'] = addslashes($post['tags']);
        } else {
            $arrayfields = $this->arrayfieldsIsset();
            $arrayfields['id'] = $post['post_id'];
            $result = $this->tf->blog_posts->updateTbl(array('title' => $post['title'], 'content' => $post['content']), $arrayfields);
            $post['tags'] = addslashes($post['tags']);
            $postId = $post['post_id'];
        }
        if ($postId) {
            $arrayfields = $this->arrayfieldsIsset();
            $arrayfields['tag'] = array();
            $arrayfields['tag'] = array_map('trim', explode(',', $post['tags']));
            $this->tf->blog_tags->insertTbl($arrayfields, 'tag=tag');
            $result = $this->tf->blog_tags->selectTbl($arrayfields, array());
            if ($result) {
                $arrayfields = $this->arrayfieldsIsset();
                $arrayfields['post_id'] = array();
                $arrayfields['tag_id'] = array();
                foreach ($result as $tag) {
                    $arrayfields['post_id'][] = $postId;
                    $arrayfields['tag_id'][] = $tag['id'];
                }
                $this->tf->blog_posts_tags->deleteTbl(array('post_id' => $postId));
                $this->tf->blog_posts_tags->insertTbl($arrayfields, '');
            }
        }
        $this->redirect('posts', 'view', 'post_id=' . $postId);
    }

}

?>
