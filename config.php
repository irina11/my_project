<?php

/*
define('DB_HOST', 'mysql2.000webhost.com');
define('DB_USER', 'a5633986_irina');
define('DB_PASS', 'accept11');
define('DB_NAME', 'a5633986_blog');
*/

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'accept11');
define('DB_NAME', 'blog');

define('BLOG_USER', 'admin');
define('BLOG_PASS', '111');




require_once 'controllers/FrontController.php';
require_once 'models/request/Request.php';
require_once 'models/tables/TableFactory.php';
require_once 'models/database/Database.php';
require_once 'models/database/MySqlDb.php';


?>
