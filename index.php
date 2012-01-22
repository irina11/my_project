<?php

header("Content-type: text/html; charset=utf-8");
try {
    require_once 'config.php';
    $db = new MySqlDb(array(DB_HOST, DB_USER, DB_PASS, DB_NAME));
    $db->connectBd();
    $tf = new TableFactory($db, 'models/');
    $r = new Request();
    $fc = new FrontController('controllers/', $tf, 'templates/');

    $fc->dispatch($r);
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit;
}
?>
