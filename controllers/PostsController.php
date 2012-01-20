<?php

class PostsController extends Controller {

    public function __construct($tf, $pathToTemplates) {
        parent:: __construct($tf, $pathToTemplates);
    }

    public function view(Request $r) {
        $posts = $r->getParam('post_id');
        $postsListPrew = $this->tf->blog_posts->select()->from('blog_posts')->where('id=' . $posts)->executeQuery();
        parent::formData($postsListPrew);
    }

    public function tag(Request $r) {
        $tag = $r->getParam('tag');
        if (!isset($tag)) {
            parent::redirect();
        }
        $postsListPrew = $this->tf->blog_posts->select('p.*')->from('blog_posts p')->leftJoin('blog_posts_tags pt', 'pt.post_id = p.id')->leftJoin('blog_tags t', 't.id = pt.tag_id')->where('tag=\'' . addslashes($tag) . '\'')->order(' date DESC ')->executeQuery();
        parent::formData($postsListPrew);
    }

    public function delete(Request $r) {
        $post_id = $r->getParam('post_id');
        parent::controlisLoggedUser('', 'message', 'text=authRequired');
        parent::controlParam($post_id, '', 'message', 'text=nopost');
        $this->tf->blog_posts->deleteTbl(array('id' => $post_id));
        parent::redirect();
    }

    public function edit(Request $r) {
        $post_id = $r->getParam('post_id');
        parent::controlisLoggedUser('', 'message', 'text=authRequired');
        parent::controlParam($post_id, '', 'message', 'text=nopost');

        $arrayfields = array();
        $arrayfields['id'] = array();
        $arrayfields['id'][] = $post_id;
        $post = $this->tf->blog_posts->selectTbl($arrayfields, array());
        parent::controlParam($post, '', 'message', 'text=nopost');
        $tags = $this->tf->blog_tags->select('t.tag', 'pt.post_id')->from('blog_tags t')->leftJoin('blog_posts_tags pt', 't.id = pt.tag_id')->where('post_id=' . $post_id)->executeQuery();

        $tagsList = array();
        foreach ($tags as $tag) {
            $tagsList[] = stripslashes($tag['tag']);
        }

        $post = array_pop($post);
        $this->data['post'] = array();
        $this->data['post']['id'] = $post_id;
        $this->data['post']['title'] = stripslashes($post['title']);
        $this->data['post']['content'] = stripslashes($post['content']);
        $this->data['post']['tags'] = implode(', ', $tagsList);
        $this->variableBlock['content'] = 'add_edit';
    }

    public function add(Request $r) {
        parent::controlisLoggedUser('', 'message', 'text=authRequired');
        $this->variableBlock['content'] = 'add_edit';
    }

    public function add_edit(Request $r) {
        $post = array();
        $post = $r->getParam('post');
        $post['title']=trim($post['title']);
        $post['content']=trim($post['content']);
        if (empty ($post['title'])||empty ($post['content'])) {
            parent::redirect('', 'message', 'text=noedit');
        }
        if (empty($post['post_id'])) {
            $arrayfields = parent::arrayfieldsIsset();
            $arrayfields['title'] = array();
            $arrayfields['content'] = array();
            $arrayfields['title'][] = $post['title'];
            $arrayfields['content'][] = $post['content'];
            $postId = $this->tf->blog_posts->insertTbl($arrayfields, '');
            $post['tags'] = addslashes($post['tags']);
        } else {
            $arrayfields = parent::arrayfieldsIsset();
                $arrayfields['id'] = $post['post_id'];
                $result = $this->tf->blog_posts->updateTbl(array('title' => $post['title'], 'content' => $post['content']), $arrayfields);
                $post['tags'] = addslashes($post['tags']);
                $postId = $post['post_id'];
        }
        if ($postId) {
            $arrayfields = parent::arrayfieldsIsset();
            $arrayfields['tag'] = array();
            $arrayfields['tag'] = array_map('trim', explode(',', $post['tags']));
            $this->tf->blog_tags->insertTbl($arrayfields, 'tag=tag');
            $result = $this->tf->blog_tags->selectTbl($arrayfields, array());
            if ($result) {
                $arrayfields = parent::arrayfieldsIsset();
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
        parent::redirect('posts','view','post_id='. $postId);
    }

    public function getHTML() {
        return parent::getHTML();
    }

}

?>
