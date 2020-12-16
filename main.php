<?php
#session_start();

require_once 'includes/common.php';

$url = explode('/', $_SERVER['REQUEST_URI']);

$action = $url[2];
echo $action . '<br>';
$action = explode('?', $action)[0]; // if action contains ? just use first slice for action

switch($action) {
    case 'register':
        echo "Registering new User!\n";
        break;
    case 'login':
        echo "Logging In User\n";
        break;
    default:
        echo "Do nothing\n";
        break;
}


?>