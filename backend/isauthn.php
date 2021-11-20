<?php
session_start();
header('Content-Type: application/json; charset=utf-8');


if( (!isset($_SESSION['authn_result'])) || empty($_SESSION['authn_result']) ) {
    http_response_code(400);
    die('{"success": false}');
}

$required_elements = ['result', 'code', 'message', 'name', 'email', 'role', 'username'];
foreach($required_elements as $element){
    // the "empty" check doesn't work here because apparently PHP thinks 0 is an "empty" value :roll_eyes:
    // code is zero in this case, which is where this would fail if we checked for empty like we normally do
    if( !isset($_SESSION['authn_result']->{$element}) ) {
        http_response_code(400);
        die('{"success": false}');
    }
}

if($_SESSION['authn_result']->{'result'} !== 'Success') {
    http_response_code(400);
    die('{"success": false}');
}
?>
