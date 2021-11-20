<?php
header('Content-Type: application/json; charset=utf-8');
$required_elements = ['username', 'password'];

foreach($required_elements as $element){
    if( (!isset($_POST[$element])) || empty($_POST[$element]) ) {
        http_response_code(400);
        die('{"success": false}');
    }
}



require_once("./phpclasses_ws/WebServiceClient.php");

if( (!isset($_ENV['TOKEN_LOCATION'])) || empty($_ENV['TOKEN_LOCATION']) ) {
    $token_location = "/home/dvolm359/token.json";
} else {
    $token_location = $_ENV['TOKEN_LOCATION'];
}

$token_obj = json_decode(file_get_contents($token_location));
$_api_token = $token_obj->{"api_token"};
$_api_key = $token_obj->{"api_key"};


$client = new WebServiceClient("https://cnmt310.braingia.org/authws/auth.php");
$data = array("apikey" => $_api_key,
             "apitoken" => $_api_token,
             "username" => $_POST['username'],
             "password" => $_POST['password'],
             );
$client->setPostFields($data);


// turn PHP errors / warnings into exceptions
// https://stackoverflow.com/a/1241751
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});


try {
    $authn_result = json_decode($client->send());
    if($authn_result->{"result"} === "Success") {
        session_start();
        $_SESSION['authn_result'] = $authn_result;
        die('{"success": true}');
    } else {
        http_response_code(400);
        die('{"success": false}');
    }
} catch (Exception $e) {
    http_response_code(400);
    die('{"success": false}');
}
?>
