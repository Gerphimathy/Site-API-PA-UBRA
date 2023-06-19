<?php

$token = $_POST["token"] ?? null;
$expires = $_POST["expires"] ?? null;

if ($token === null){
    //Redirect to 404
    header("Location: /404");
    die();
}

//Create a new session
session_start();

echo "Session started";

$_SESSION["token"] = $token;

echo "Session token set";

//$_SESSION["expires"] = $expires ?? strtotime(TOKEN_VALIDITY);

echo "Session expires set";

//Redirect to login

header("Location: /login");
die();