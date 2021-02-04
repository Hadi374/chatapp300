<?php
session_start();

require_once 'includes/common.php';

$app = new common;

$url = explode('/', $_SERVER['REQUEST_URI']);

$action = $url[2];

$action = explode('?', $action)[0]; // if action contains ? just use first slice for action



switch($action) {
    case 'register':
        // use get method only for test.
        if(isset($_GET['name']) && isset($_GET['username']) && isset($_GET['email']) && isset($_GET['password'])) {
            
            $result =$app->register($_GET['name'], $_GET['username'], $_GET['email'], $_GET['password']);
            success("Registered Successfully", $result);
        } else {
            failed("please fill all fields");
        }
        break;
    case 'login':
        if(isset($_GET['username']) && isset($_GET['password'])) {
            $result = $app->login($_GET['username'], $_GET['password']);
            success("Logged In successfully", $result);
        }
        break;
    case 'logout':
        $app->logout();
        success("Logged Out successfully");
        break;
    default:
        echo "Do nothing\n";
        break;
}


?>