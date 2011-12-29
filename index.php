<?php
session_start();

define('DB_HOST', 'mysql2.000webhost.com');
define('DB_USER', 'a5633986_irina');
define('DB_PASS', 'accept11');
define('DB_NAME', 'a5633986_blog');

define('BLOG_USER', 'admin');
define('BLOG_PASS', '111');

$flag = 0;

header("Content-type: text/html; charset=utf-8");

$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    echo "Не удается подключиться к БД: " . mysql_error();
    exit;
}

if (!mysql_select_db(DB_NAME)) {
    echo "Не удается выбрать БД: " . mysql_error();
    exit;
}

mysql_query('SET NAMES utf8');

function isLoggedUser()
{
    return isset($_SESSION['user_loggedin']);
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function doQuery($sql)
{
    $resultsList = array();
    
    mysql_query('SET NAMES utf8');
    
    $queryResult = mysql_query($sql);

    if (!$queryResult)
    {
        echo "Не удалось успешно выполнить запрос ($sql) из БД: " . mysql_error();
        exit;
    }

    $sqlParams = explode(' ', $sql);
    $sqlCommandName = strtolower($sqlParams[0]);
    
    switch ($sqlCommandName)
    {
        case 'select':

                if (mysql_num_rows($queryResult) == 0) return $resultsList;

                while ($row = mysql_fetch_assoc($queryResult))
                {
                    $resultsList[] = $row;
                }

                mysql_free_result($queryResult);
                
                return $resultsList;
            break;
        
        case 'insert':
        case 'update':
        case 'delete':

            return mysql_affected_rows();
            
            break;

        default: break;
    }
    
    return false;
}
function dataList($postsListParam)
{
    $postsListReturn = $postsListParam;
    $postsIdsList = array();
    foreach ($postsListParam as $post)
    {
        $postsIdsList[] = $post['id'];
    }
    $postsIdsList = implode(', ', $postsIdsList);
    
    $query = 'SELECT t.tag, pt.post_id FROM `blog_tags` t LEFT JOIN `blog_posts_tags` pt ON t.`id` = pt.`tag_id` WHERE pt.`post_id` IN (' . $postsIdsList . ')';
    
    $resultPostsTagsList = doQuery($query);
    
    $postsTagsList = array();
    foreach($resultPostsTagsList as $postTag)
    {
        if (!isset($postsTagsList[$postTag['post_id']]))
        {
            $postsTagsList[$postTag['post_id']] = array();
        }
        
        if (!in_array($postTag['tag'], $postsTagsList[$postTag['post_id']]))
        {
            $postsTagsList[$postTag['post_id']][] = $postTag['tag'];
        }
    }
    
    foreach($postsListParam as $rowNumber => $post)
    {
        if (isset($postsTagsList[$post['id']]))
        {
           $postsListReturn[$rowNumber]['tags'] = $postsTagsList[$post['id']];  
        }
    }
    
    return $postsListReturn;
}

function pre($params)
{
    echo '<pre>';
    print_r($params);
    echo '</pre>';
}

if (!isset($_GET['action']))
{
    $notificationsList = array();
    
    if (isset($_GET['message']) && $_GET['message'] == 'nopost')
    {
        $notificationsList[] = 'Указанный пост не найден.';
    }
    
    if (isset($_GET['message']) && $_GET['message'] == 'authRequired')
    {
        $notificationsList[] = 'Для выполнения этого действия пользователь должен быть авторизированным';
    }
    
    $postsListPrew = doQuery('SELECT * FROM blog_posts ORDER BY date DESC');
    if ($postsListPrew)
    {   
        $postsList = dataList($postsListPrew);
    }    
    require_once 'templates/index_page.php';
}
else
{
    switch ($_GET['action'])
    {
        case 'tag':
            
            if (!isset($_GET['tag']))
            {
                redirect('?');
            }
            
            $_GET['tag'] = addslashes($_GET['tag']);
            
            $postsListPrew = doQuery(
               'SELECT p.*
                FROM `blog_posts` p
                LEFT JOIN `blog_posts_tags` pt ON pt.`post_id` = p.`id`
                LEFT JOIN `blog_tags` t ON t.id = pt.`tag_id`
                WHERE t.`tag` = \'' . $_GET['tag'] . '\'
                ORDER BY p.date DESC ');

            if ($postsListPrew)
            {
                $postsList = dataList($postsListPrew);
            }
            
            $flag = 1;
            require_once 'templates/index_page.php';
            
            break;
        
        case 'logout':
            
                if (isset($_SESSION['user_loggedin']))
                {
                    unset($_SESSION['user_loggedin']);
                }
                
                redirect('?');
            
            break;
        
        case 'login':
            
            $authErrors = false;
            
            if (isset($_POST['user']) && isset($_POST['user']['login']) && isset($_POST['user']['pass']))
            {
                $_POST['user']['login'] = trim($_POST['user']['login']);
                $_POST['user']['pass']  = trim($_POST['user']['pass']);
                if ($_POST['user']['login'] == BLOG_USER && $_POST['user']['pass'] == BLOG_PASS)
                {
                    $_SESSION['user_loggedin'] = true;
                    redirect('?');
                } else {
                    $authErrors = 'Не верно указаны логин или пароль';
                }
            }

            require_once 'templates/auth_page.php';
            
            break;
        
        // view -- show one post by post Id
        case 'view':
            
            $post = doQuery('SELECT * FROM blog_posts WHERE id = ' . intval($_GET['post_id']));
            
            require_once 'templates/view_page.php';
            
            break;
        
        case 'add':
            
            if (!isLoggedUser())
            {
                redirect('?message=authRequired');
            }
            
            if (isset($_POST) && isset($_POST['post']))
            {
                $_POST['post']['title']   = addslashes($_POST['post']['title']);
                $_POST['post']['content'] = addslashes($_POST['post']['content']);
                $_POST['post']['tags']    = addslashes($_POST['post']['tags']);
                $query = 'INSERT INTO blog_posts SET title = \'' . $_POST['post']['title'] .'\', content = \'' . $_POST['post']['content'] .'\'';
                $result = doQuery($query);
                
                $postId = false;
                
                if ($result)
                {
                    $postId = mysql_insert_id();
                }
                
                if ($postId)
                {
                    $tagsList = explode(',', $_POST['post']['tags']);
                    $tagsList = array_map('trim', $tagsList);
                    
                    
                    $tagsConditions = '';
                    $tagsConditionsIn = '';
                    foreach($tagsList as $tagNum => $tag)
                    {   
                        $tagsConditions = $tagsConditions.'(\'' . $tag . '\')';
                        $tagsConditionsIn = $tagsConditionsIn.'\'' . $tag . '\''; 
                        if ($tagNum < count($tagsList) - 1)   
                        {
                          $tagsConditions = $tagsConditions.',';
                          $tagsConditionsIn = $tagsConditionsIn.',' ;
                        } 
                    }
                    
                    $query = 'INSERT INTO blog_tags (tag) VALUES ' . $tagsConditions . ' ON DUPLICATE KEY UPDATE tag=tag';
                    doQuery($query);
                    
                    $query = 'SELECT id, tag FROM blog_tags WHERE tag IN (' . $tagsConditionsIn . ')';
                    $result = doQuery($query);
                    
                    if ($result)
                    {
                        $postTags = array();
                        foreach($result as $tag)
                        {
                            $postTags[] = '(' . $postId . ', ' . $tag['id'] . ')';
                        }
                        $postTags = implode(', ', $postTags);
                        
                        $query = 'DELETE FROM blog_posts_tags WHERE post_id = ' . $postId;
                        doQuery($query);
                        
                        $query = 'INSERT INTO blog_posts_tags (post_id, tag_id) VALUES ' . $postTags;
                        doQuery($query);
                    }
                }
                
                if ($postId)
                {
                    redirect('?action=view&post_id=' . $postId);
                }
            }
            
            require_once 'templates/add_page.php';
            
            break;
        
        case 'delete':
            
            if (!isLoggedUser())
            {
                redirect('?message=authRequired');
            }
            
            if (!isset($_POST['post_id']))
            {
                redirect('?message=nopost');
            }
            
            doQuery('DELETE FROM blog_posts WHERE id = ' . intval($_POST['post_id']));
            
            redirect('?');
            
            break;
        
        case 'edit':
            
            if (!isLoggedUser())
            {
                redirect('?message=authRequired');
            }
            
            if (isset($_POST['post']))
            {
                $_POST['post']['title']   = addslashes($_POST['post']['title']);
                $_POST['post']['content'] = addslashes($_POST['post']['content']);
                $_POST['post']['tags']    = addslashes($_POST['post']['tags']);
                
                $query = 'UPDATE blog_posts SET title = \'' . $_POST['post']['title'] .'\', content = \'' . $_POST['post']['content'] .'\' WHERE id = ' . intval($_POST['post']['post_id']);
                
                $result = doQuery($query);
                
                $tagsList = explode(',', $_POST['post']['tags']);
                $tagsList = array_map('trim', $tagsList);

                $tagsConditions = '';
                $tagsConditionsIn = '';
                foreach($tagsList as $tagNum => $tag)
                {   
                    $tagsConditions = $tagsConditions.'(\'' . $tag . '\')';
                    $tagsConditionsIn = $tagsConditionsIn.'\'' . $tag . '\''; 
                    if ($tagNum < count($tagsList) - 1)   
                    {
                       $tagsConditions = $tagsConditions.',';
                       $tagsConditionsIn = $tagsConditionsIn.',' ;
                    } 
                }
                
                $query = 'INSERT INTO blog_tags (tag) VALUES ' . $tagsConditions . ' ON DUPLICATE KEY UPDATE tag=tag';
                doQuery($query);

                $query = 'SELECT id, tag FROM blog_tags WHERE tag IN (' . $tagsConditionsIn . ')';
                $result = doQuery($query);

                if ($result)
                {
                    $postTags = array();
                    foreach($result as $tag)
                    {
                        $postTags[] = '(' . intval($_POST['post']['post_id']) . ', ' . $tag['id'] . ')';
                    }
                    $postTags = implode(', ', $postTags);

                    $query = 'DELETE FROM blog_posts_tags WHERE post_id = ' . intval($_POST['post']['post_id']);
                    doQuery($query);

                    $query = 'INSERT INTO blog_posts_tags (post_id, tag_id) VALUES ' . $postTags;
                    doQuery($query);
                }

                if ($result)
                {
                    redirect('?action=view&post_id=' . intval($_POST['post']['post_id']));
                }
            }
            
            if (!isset($_GET['post_id']))
            {
                redirect('?message=nopost');
            }
            
            $post = doQuery('SELECT * FROM blog_posts WHERE id = ' . intval($_GET['post_id']));
            
            if (!$post)
            {
                redirect('?message=nopost');
            }
            
            $tags = doQuery('SELECT tag FROM `blog_tags` t LEFT JOIN `blog_posts_tags` pt ON t.`id` = pt.`tag_id` WHERE pt.`post_id` = ' . intval($_GET['post_id']));
            
            $tagsList = array();
            foreach($tags as $tag)
            {
                $tagsList[] = stripslashes($tag['tag']);
            }
            
            $post = array_pop($post);
            
            $post['title']   = stripslashes($post['title']);
            $post['content'] = stripslashes($post['content']);
            $post['tags'] = implode(', ', $tagsList);
            
            require_once 'templates/edit_page.php';
            
            break;

        default:
            echo 'Неопределенное действие';
            break;
    }
}

mysql_close($conn);

?>