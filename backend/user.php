<?php
require_once('./isauthn.php');

print(json_encode($_SESSION['authn_result']));
?>
