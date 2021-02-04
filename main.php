<?php
#session_start();

require_once 'includes/common.php';

$app = new common;

$url = explode('/', $_SERVER['REQUEST_URI']);

$action = $url[2];
echo $action . '<br>';
$action = explode('?', $action)[0]; // if action contains ? just use first slice for action



switch($action) {
    case 'register':
        $app->register('name', 'username', 'email', 'password');
        break;
    case 'login':
        $app->login('name', 'pass');
        break;
    default:
        echo "Do nothing\n";
        break;
}


?>