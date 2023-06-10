<?php

const ABSPATH = __DIR__."/";

include __DIR__."/../api/exceptions/__includeExceptions.php";
include __DIR__."/../api/tools/HtmlResponseHandler.php";
include __DIR__."/../api/database/DatabaseLinkHandler.php";
include __DIR__."/../api/database/CREDENTIALS.php";
include __DIR__."/../api/tools/const.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$route = $_REQUEST["route"] ?? "";

$method = $_SERVER["REQUEST_METHOD"];

if ($route === ""){
    include __DIR__."/views/index.php";
    die();
}

$args = explode("?",$route)[1] ?? "";
$route = explode("?",$route)[0] ?? "";

switch ($route){
    case "login":
        include __DIR__."/../api/controllers/Login.php";
        switch ($method){
            case "GET":
                Login::get();
                break;
            case "POST":
                Login::post();
                break;
            case "PUT":
                Login::put();
                break;
            default:
                HtmlResponseHandler::formatedResponse(405);
        }
        break;
    case "auth":
        include __DIR__."/../api/controllers/Auth.php";
        switch ($method){
            case "GET":
                Auth::get();
                break;
            default:
                HtmlResponseHandler::formatedResponse(405);
        }
        break;
    default:
        switch ($method){
            case "GET":
                //C'est ici qu'irait une page 404 normalement
                include __DIR__."/views/404.php";
                die();
            default:
                HtmlResponseHandler::formatedResponse(204);
        }
        break;
}