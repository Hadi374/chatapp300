<?php
session_start();

require_once 'includes/common.php';

$app = new common;

$url = explode('/', $_SERVER['REQUEST_URI']);

$action = $url[3];

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
        // use get method only for test.
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

function login($app) {
    if(isset($_GET['username']) && isset($_GET['password'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];
        $result = $app->login($username, $password);
        success("Logged In successfully", $result);
    }
}

switch($action) {
    case 'register': // register a new user to system
        register($app);
        break;
    case 'login': // login to system with username and password
        login($app);
        break;
    case 'logout': // log out from system
        $app->logout();
        success("Logged Out successfully");
        break;
    case 'sendMessage':
        sendMessage($app); // send a message to a chat(group or user)
        break;
    case 'getMessage': // get a message by id only if you have access to that message
        getMessage($app);
        break;
    case 'getSelf': // return logged in user
        $result = $app->getSelf();
        success("Get Self", $result);
        break;
    case 'getChat': // return information about a chat

    case 'uploadFile': // upload files up to 50MB.
        $result = $app->uploadFile($_FILES['file']); // this line uploads file
        $result2 = $app->getFileObject($result);     // and this line get some information about file
        success("File Uploaded", $result2);          // and this line prints that information
        break;
    case 'getFile': // get a file by 32 digit hexadecimal id returned by uploadFile.
        $app->getFile($_GET['file_id']);
        break;

    default:
        echo "Do nothing\n";
        break;
}


?>