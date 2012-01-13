<?php

session_start();
$flag = 0;
header("Content-type: text/html; charset=utf-8");
require_once 'config.php';

$db = new MySqlDb(array(DB_HOST, DB_USER, DB_PASS, DB_NAME));
$db->connectBd();
$tf = new TableFactory($db, 'models');

function isLoggedUser() {
    return isset($_SESSION['user_loggedin']);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function arrayfieldsIsset() {
    if (isset($arrayfields)) {
        unset($arrayfields);
    }
    $arrayfields = array();
    return $arrayfields;
}

function dataList($postsListParam, $tf) {
    $postsListReturn = $postsListParam;
    $strQuery='post_id IN (';
    foreach ($postsListParam as $key=>$post) {
         $strQuery.= $post['id'];
         if ($key<(count($postsListParam)-1)) {
             $strQuery.= ',';
         }
    }
    $strQuery.=')';
    
    $resultPostsTagsList = $tf->blog_tags->select('t.tag', 'pt.post_id')->from('blog_tags t')->leftJoin('blog_posts_tags pt','t.id = pt.tag_id')->where($strQuery)->executeQuery();

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

function pre($params) {
    echo '<pre>';
    print_r($params);
    echo '</pre>';
}

if (!isset($_GET['action'])) {
    $notificationsList = array();

    if (isset($_GET['message']) && $_GET['message'] == 'nopost') {
        $notificationsList[] = 'Указанный пост не найден.';
    }

    if (isset($_GET['message']) && $_GET['message'] == 'authRequired') {
        $notificationsList[] = 'Для выполнения этого действия пользователь должен быть авторизированным';
    }

    $postsListPrew = $tf->blog_posts->select()->from('blog_posts')->order(' date DESC ')->executeQuery();

    if ($postsListPrew) {
        $postsList = dataList($postsListPrew, $tf);
    }
    require_once 'templates/index_page.php';
} else {
    switch ($_GET['action']) {
        case 'tag':

            if (!isset($_GET['tag'])) {
                redirect('?');
            }

            $postsListPrew = $tf->blog_posts->select('p.*')->from('blog_posts p')->leftJoin('blog_posts_tags pt','pt.post_id = p.id')->leftJoin('blog_tags t','t.id = pt.tag_id')->where('tag=\''.addslashes($_GET['tag']).'\'')->order(' date DESC ')->executeQuery();
            if ($postsListPrew) {
                $postsList = dataList($postsListPrew, $tf);
            }

            $flag = 1;
            require_once 'templates/index_page.php';

            break;

        case 'logout':

            if (isset($_SESSION['user_loggedin'])) {
                unset($_SESSION['user_loggedin']);
            }

            redirect('?');

            break;

        case 'login':

            $authErrors = false;

            if (isset($_POST['user']) && isset($_POST['user']['login']) && isset($_POST['user']['pass'])) {
                $_POST['user']['login'] = trim($_POST['user']['login']);
                $_POST['user']['pass'] = trim($_POST['user']['pass']);
                if ($_POST['user']['login'] == BLOG_USER && $_POST['user']['pass'] == BLOG_PASS) {
                    $_SESSION['user_loggedin'] = true;
                    redirect('?');
                } else {
                    $authErrors = 'Не верно указаны логин или пароль';
                }
            }

            require_once 'templates/auth_page.php';

            break;


        case 'view':
            $post = $tf->blog_posts->select()->from('blog_posts')->where('id='.$_GET['post_id'])->executeQuery();
            if ($post) {
                $post = dataList($post, $tf);
            }
            require_once 'templates/view_page.php';

            break;

        case 'add':

            if (!isLoggedUser()) {
                redirect('?message=authRequired');
            }

            if (isset($_POST) && isset($_POST['post'])) {
                $arrayfields = arrayfieldsIsset();
                $arrayfields['title'] = array();
                $arrayfields['content'] = array();
                $arrayfields['title'][] = $_POST['post']['title'];
                $arrayfields['content'][] = $_POST['post']['content'];
                $result = $tf->blog_posts->insertTbl($arrayfields, '');

                $_POST['post']['tags'] = addslashes($_POST['post']['tags']);

                $postId = false;

                if ($result) {
                    $postId = mysql_insert_id();
                }

                if ($postId) {
                    $arrayfields = arrayfieldsIsset();
                    $arrayfields['tag'] = array();
                    $arrayfields['tag'] = array_map('trim', explode(',', $_POST['post']['tags']));
                    $tf->blog_tags->insertTbl($arrayfields, 'tag=tag');
                    $result = $tf->blog_tags->selectTbl($arrayfields, array());
                    if ($result) {
                        $arrayfields = arrayfieldsIsset();
                        $arrayfields['post_id'] = array();
                        $arrayfields['tag_id'] = array();
                        foreach ($result as $tag) {
                            $arrayfields['post_id'][] = $postId;
                            $arrayfields['tag_id'][] = $tag['id'];
                        }
                        $tf->blog_posts_tags->deleteTbl(array('post_id' => $postId));
                        $tf->blog_posts_tags->insertTbl($arrayfields, '');
                    }
                }

                if ($postId) {
                    redirect('?action=view&post_id=' . $postId);
                }
            }

            require_once 'templates/add_page.php';

            break;

        case 'delete':

            if (!isLoggedUser()) {
                redirect('?message=authRequired');
            }

            if (!isset($_POST['post_id'])) {
                redirect('?message=nopost');
            }
            $tf->blog_posts->deleteTbl(array('id' => $_POST['post_id']));
            redirect('?');

            break;

        case 'edit':

            if (!isLoggedUser()) {
                redirect('?message=authRequired');
            }

            if (isset($_POST['post'])) {
                $arrayfields = arrayfieldsIsset();
                $arrayfields['id'] = $_POST['post']['post_id'];
                $result = $tf->blog_posts->updateTbl(array('title' => $_POST['post']['title'], 'content' => $_POST['post']['content']), $arrayfields);
                $_POST['post']['tags'] = addslashes($_POST['post']['tags']);

                $postId = $_POST['post']['post_id'];
                $arrayfields = arrayfieldsIsset();
                $arrayfields['tag'] = array();
                $arrayfields['tag'] = array_map('trim', explode(',', $_POST['post']['tags']));
                $tf->blog_tags->insertTbl($arrayfields, 'tag=tag');
                $result = $tf->blog_tags->selectTbl($arrayfields, array());
                if ($result) {
                    $arrayfields = arrayfieldsIsset();
                    $arrayfields['post_id'] = array();
                    $arrayfields['tag_id'] = array();
                    foreach ($result as $tag) {
                        $arrayfields['post_id'][] = $postId;
                        $arrayfields['tag_id'][] = $tag['id'];
                    }
                    $tf->blog_posts_tags->deleteTbl(array('post_id' => $postId));
                    $tf->blog_posts_tags->insertTbl($arrayfields, '');
                }


                if ($result) {
                    redirect('?action=view&post_id=' . intval($_POST['post']['post_id']));
                }
            }

            if (!isset($_GET['post_id'])) {
                redirect('?message=nopost');
            }
            $arrayfields = arrayfieldsIsset();
            $arrayfields['id'] = array();
            $arrayfields['id'][] = $_GET['post_id'];
            $post = $tf->blog_posts->selectTbl($arrayfields, array());
            if (!$post) {
                redirect('?message=nopost');
            }
            $tags = $tf->blog_tags->select('t.tag', 'pt.post_id')->from('blog_tags t')->leftJoin('blog_posts_tags pt','t.id = pt.tag_id')->where('post_id='.$_GET['post_id'])->executeQuery();
            
            $tagsList = array();
            foreach ($tags as $tag) {
                $tagsList[] = stripslashes($tag['tag']);
            }

            $post = array_pop($post);

            $post['title'] = stripslashes($post['title']);
            $post['content'] = stripslashes($post['content']);
            $post['tags'] = implode(', ', $tagsList);

            require_once 'templates/edit_page.php';

            break;

        default:
            echo 'Неопределенное действие';
            break;
    }
}
?>