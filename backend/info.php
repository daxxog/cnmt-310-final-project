<?php
require_once('./isauthn.php');


// give our correct_answers an initial value of zero if we need to
if( !isset($_SESSION['correct_answers']) ) {
    $_SESSION['correct_answers'] = 0;
}

// give our incorrect_answers an initial value of zero if we need to
if( !isset($_SESSION['incorrect_answers']) ) {
    $_SESSION['incorrect_answers'] = 0;
}

print(json_encode(array(
    "success" => true,
    "correct_answers" => $_SESSION['correct_answers'],
    "incorrect_answers" => $_SESSION['incorrect_answers']
)));
?>
