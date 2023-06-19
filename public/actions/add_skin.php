<?php

const ABSPATH = __DIR__."/../../";

include_once __DIR__."/../../api/database/CREDENTIALS.php";
include_once __DIR__."/../../api/database/DatabaseLinkHandler.php";
include_once __DIR__."/../../api/exceptions/__includeExceptions.php";

include_once __DIR__."/../../api/models/Boat.php";
include_once __DIR__."/../../api/models/User.php";
include_once __DIR__."/../../api/models/Token.php";
include_once __DIR__."/../../api/models/Skin.php";

session_start();
$token = $_SESSION["token"] ?? null;


if($token === null){
    header("Location: /404");
    die();
}


$uid = Token::tokenIsValid($token, $_SERVER["HTTP_USER_AGENT"]);

if($uid < 0){
    header("Location: /404");
    die();
}


$is_admin = User::isAdmin($uid);
if (!$is_admin){
    header("Location: /404");
    die();
}

$name = $_POST["name"] ?? null;
$image = $_FILES["image"] ?? null;
$price = $_POST["price"] ?? null;
$id_boat = $_POST["id_boat"] ?? -1;

//Check if the boat exists
if(Boat::getBoatData($id_boat) === []){
    header("Location: /admin?error=boat_not_found");
    die();
}

if(!isset($name) || !isset($image) || !isset($price)){
    header("Location: /admin?error=missing_data");
    die();
}

//Max Size: 10MB
if($image["size"] > 10000000){
    header("Location: /admin?error=image_size");
    die();
}

Skin::addSkin($name, $price, $id_boat);
$id = Skin::getLargestId();


//Save image as png in public\uploads\boats using the boat id as name.png
$target_dir = __DIR__."/../../public/uploads/skins/";
$target_file = $target_dir . $id . ".png";
move_uploaded_file($image["tmp_name"], $target_file);


header("Location: /admin");
die();

