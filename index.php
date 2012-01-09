<?php

session_start();
/*
  define('DB_HOST', 'mysql2.000webhost.com');
  define('DB_USER', 'a5633986_irina');
  define('DB_PASS', 'accept11');
  define('DB_NAME', 'a5633986_blog');


  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'blog');
 */
define('BLOG_USER', 'admin');
define('BLOG_PASS', '111');

$flag = 0;

header("Content-type: text/html; charset=utf-8");
require_once '/models/tables/TableFactory.php';

$tf = new TableFactory(array("localhost", "root", "", "blog"), '/models/');

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

function dataList($postsListParam) {
	$postsListReturn = $postsListParam;
	
	$arrayfields = arrayfieldsIsset();
	$arrayfields['id'] = array();
	foreach ($postsListParam as $post) {
		$arrayfields['id'][] = $post['id'];
	}

	$tags = $tf->blog_tags->blog_tagsSelectSpecific1($arrayfields);

	$resultPostsTagsList = doQuery($query);

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

	$postsListPrew = $tf->blog_posts->selectTbl('',array('date'=>'DESC'));
	
	if ($postsListPrew) {
		$postsList = dataList($postsListPrew);
	}
	require_once 'templates/index_page.php';
} else {
	switch ($_GET['action']) {
		case 'tag':

			if (!isset($_GET['tag'])) {
				redirect('?');
			}

			$postsListPrew = $tf->blog_posts->blog_postsSelectSpecific1(array('tag'=>$_GET['tag']),array('date'=>'DESC'));

			if ($postsListPrew) {
				$postsList = dataList($postsListPrew);
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

			$post = $tf->blog_posts->selectTbl(array('id'=>$_GET['post_id']),'');
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
				$tf->blog_posts_tags->insertTbl($arrayfields, '');

				$_POST['post']['tags'] = addslashes($_POST['post']['tags']);

				$postId = false;

				if ($result) {
					$postId = mysql_insert_id();
				}

				if ($postId) {
					$arrayfields = arrayfieldsIsset();
					$arrayfields['tag'] = array();
					$arrayfields['tag'][] = array_map('trim', explode(',', $_POST['post']['tags']));
					$tf->blog_tags->insertTbl($arrayfields, 'tag=tag');
					$result = $tf->blog_tags->selectTbl($arrayfields,'');
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
				$result = $tf->blog_posts->updateTbl(array('title' => $_POST['post']['title'], 'content' => $_POST['post']['content']), array('id' => $_POST['post']['post_id']));
				$_POST['post']['tags'] = addslashes($_POST['post']['tags']);
				$arrayfields = arrayfieldsIsset();
				$arrayfields['tag'] = array();
				$arrayfields['tag'][] = array_map('trim', explode(',', $_POST['post']['tags']));
				$tf->blog_tags->insertTbl($arrayfields, 'tag=tag');
				$result = $tf->blog_tags->selectTbl($arrayfields,'');
				if ($result) {
					$tf->blog_posts_tags->deleteTbl(array('post_id' => $_POST['post']['post_id']));
					$arrayfields = arrayfieldsIsset();
					$arrayfields['post_id'] = array();
					$arrayfields['tag_id'] = array();
					foreach ($result as $tag) {
						$arrayfields['post_id'][] = $postId;
						$arrayfields['tag_id'][] = $tag['id'];
					}
					$tf->blog_posts_tags->insertTbl($arrayfields, '');
				}

				if ($result) {
					redirect('?action=view&post_id=' . intval($_POST['post']['post_id']));
				}
			}

			if (!isset($_GET['post_id'])) {
				redirect('?message=nopost');
			}
			$post = $tf->blog_posts->selectTbl(array('id'=>$_GET['post_id']),'');
			if (!$post) {
				redirect('?message=nopost');
			}

			$tags = $tf->blog_tags->blog_tagsSelectSpecific1(array('post_id'=>$_GET['post_id']));

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
$tf->MysqlDb;
?>