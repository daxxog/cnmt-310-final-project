<?php
require_once('./isauthn.php');

if( (!isset($_POST['answer'])) || empty($_POST['answer']) ) {
    http_response_code(400);
    die('{"success": false}');
}

if( (!isset($_SESSION['qws_result'])) || empty($_SESSION['qws_result']) ) {
    http_response_code(400);
    die('{"success": false}');
}

$required_elements = ["result", "code", "message", "question", "answer"];
foreach($required_elements as $element){
    // again, the is "empty" check won't work here because of the value of "code"
    if( !isset($_SESSION['qws_result']->{$element}) ) {
        http_response_code(400);
        die('{"success": false}');
    }
}

if($_SESSION['qws_result']->{'answer'} !== $_POST['answer']) {
    // bump up the incorrect_answers value, setting it to one if it currently isn't set
    if( !isset($_SESSION['incorrect_answers']) ) {
        $_SESSION['incorrect_answers'] = 1;
    } else {
        $_SESSION['incorrect_answers']++;
    }

    die(json_encode(array(
        "success" => true,
        "incorrect_answers" => $_SESSION['incorrect_answers']
    )));
}

// get the hash of the question and mark it as answered $_SESSION
$_SESSION['answered_' . $_SESSION['qws_result']->{'hash'}] = true;

// remove the question web service result from the session so
// we can't just keep answering the same question over and over
// to bump up the correct_answers value
$_SESSION['qws_result'] = array();

// bump up the correct_answers value, setting it to one if it currently isn't set
if( !isset($_SESSION['correct_answers']) ) {
    $_SESSION['correct_answers'] = 1;
} else {
    $_SESSION['correct_answers']++;
}

print(json_encode(array(
    "success" => true,
    "correct_answers" => $_SESSION['correct_answers']
)));
?>
