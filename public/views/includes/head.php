<?php

defined("ABSPATH") or die("Get out of here!");

session_start();

//Check token Validity
include_once __DIR__."/../../../api/models/Token.php";

$__uid = -1;
if(isset($_SESSION["token"])){
    $__uid = Token::tokenIsValid($_SESSION["token"], $_SERVER["HTTP_USER_AGENT"]) > 0;
    if ($__uid < 0) session_destroy();
    else $_SESSION["token"] = Token::refreshToken($__uid, $_SERVER["HTTP_USER_AGENT"]);
}
else session_destroy();

include_once __DIR__."/../../../api/models/Skin.php";
include_once __DIR__."/../../../api/models/Boat.php";
include_once __DIR__."/../../../api/models/User.php";

$scripts = array();

?>

<!-- the head -->
