<?php
require_once 'includes/db.php';

# setup database, files folder...
$query = file_get_contents('query.sql');
$db = new Database;
$db->query($query);
if($db->execute()) {
    echo 'Success';
} else {
    echo 'Fail';
}
?>