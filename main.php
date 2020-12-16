<?php
session_start();

require_once 'includes/common.php';

$app = new common;

$url = explode('/', $_SERVER['REQUEST_URI']);

$action = $url[2];

$action = explode('?', $action)[0]; // if action contains ? just use first slice for action


function sendMessage($app) {
    // chat_id
    // text
    // reply_to

    if(isset($_GET['chat_id']) && isset($_GET['text'])) {
        $chat_id = $_GET['chat_id'];
        $text = $_GET['text'];
        $reply_to = null;
        if(isset($_GET['reply_to'])) {
            $reply_to = $_GET['reply_to'];
        }
        $result = $app->sendMessage($chat_id, $app->newTextMessage($text, $reply_to));
        success("Message Sent", $result);
    }
}

function register($app) {
    if(isset($_GET['name']) && isset($_GET['username']) && isset($_GET['email']) && isset($_GET['password'])) {

        $name = $_GET['name'];
        $username = $_GET['username'];
        $email = $_GET['email'];
        $password = $_GET['password'];
        $password_verify= $_GET['password_verify'];
        if($password != $password_verify) {
            failed("Passwords does not match");
        }

        $result =$app->register($name, $username, $email, $password);
        success("Registered Successfully", $result);
    } else {
        failed("please fill all fields");
    }
}


}


switch($action) {
    case 'register':
        // use get method only for test.
        register($app);
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
    case 'sendMessage':
        sendMessage($app);
        break;
    case 'getMessage':
        getMessage($app);
        break;
    case 'getSelf':
        $result = $app->getSelf();
        success("Get Self", $result);
        break;
    default:
        echo "Do nothing\n";
        break;
}


?>