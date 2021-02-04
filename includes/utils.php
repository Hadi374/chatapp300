<?php
function response($success, $message) {
    return array("success" => $success, 'message' => $message);
    exit(0);
}

function failed($message) {
    echo json_encode(response(false, $message));
    exit(0);
}

function success($message, $result=[]) {
    $res = response(true, $message);
    if(!empty($result)) {
        $res['result'] = $result;
    }
    echo json_encode($res);
}

?>