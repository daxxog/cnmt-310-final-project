<?php
// https://stackoverflow.com/a/3512570
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header('Content-Type: application/json; charset=utf-8');
print('{"logged_out": true}');
?>
