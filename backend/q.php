<?php
require_once('./isauthn.php');
require_once("./phpclasses_ws/WebServiceClient.php");

if( (!isset($_ENV['TOKEN_LOCATION'])) || empty($_ENV['TOKEN_LOCATION']) ) {
    $token_location = "/home/dvolm359/token.json";
} else {
    $token_location = $_ENV['TOKEN_LOCATION'];
}

$token_obj = json_decode(file_get_contents($token_location));
$_api_token = $token_obj->{"api_token"};
$_api_key = $token_obj->{"api_key"};


$client = new WebServiceClient("https://cnmt310.braingia.org/qws/q.php");
$data = array("apikey" => $_api_key,
             "apitoken" => $_api_token,
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
    // question already requested, but not answered
    if(isset($_SESSION['qws_result']) && isset($_SESSION['qws_result']->{'result'}) && $_SESSION['qws_result']->{'result'} === 'Success') {
//-DEBUG        header('Content-Type: text/plain; charset=utf-8');
//-DEBUG        http_response_code(400);
//-DEBUG        var_dump($_SESSION);
        $_return_result = array(
            "success" => true,
//-TESTING            "answer" => $qws_result->{'answer'},
            "question" => $_SESSION['qws_result']->{'question'}
        );
        die(json_encode($_return_result));
    }

    // get result and do some basic validation
    $qws_result = json_decode($client->send());
    $required_elements = ["result", "code", "message", "question", "answer"];
    foreach($required_elements as $element){
        // again, the is "empty" check won't work here because of the value of "code"
        if( !isset($qws_result->{$element}) ) {
            http_response_code(400);
            die('{"success": false}');
        }
    }

    if($qws_result->{"result"} === "Success") {
        $_return_result = array(
            "success" => true,
//-TESTING            "answer" => $qws_result->{'answer'},
            "question" => $qws_result->{'question'}
        );
        $_SESSION['qws_result'] = $qws_result;
        die(json_encode($_return_result));
    } else {
        http_response_code(400);
        die('{"success": false}');
    }
} catch (Exception $e) {
    http_response_code(400);
    die('{"success": false}');
}
?>
