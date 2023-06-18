<?php

class Shop
{
    public static function put(){
        include_once __DIR__."/../models/Token.php";
        include_once __DIR__."/../models/User.php";
        include_once __DIR__."/../models/Boat.php";
        include_once __DIR__."/../models/Skin.php";

        $json = json_decode(file_get_contents("php://input"));

        if(empty($json->token)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "token", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        try{
            $id = Token::tokenIsValid($json->token, $_SERVER['HTTP_USER_AGENT']);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }

        if (!User::isAdmin($id)) HtmlResponseHandler::formatedResponse(403);

        //params: boat or skin objects

        if(empty($json->type)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "type", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        $name = $json->name ?? "";
        $price = $json->price ?? 0;
        $boat_id = $json->boat_id ?? null;

        if($boat_id !== null && !Boat::idIsValid($boat_id)){
            $e = new InvalidParameterError(ParameterErrorCase::Unknown, "boat_id", "Invalid Request - Parameter is invalid");
            $e->respondWithError();
        }

        //Will also kill process if invalid
        self::filterName($name);

        switch ($json->type){
            case "skin":
                if($boat_id === null) {
                    $e = new InvalidParameterError(ParameterErrorCase::Empty, "boat_id", "Invalid Request - Parameter is missing");
                    $e->respondWithError();
                }
                if(Skin::addSkin($name, $price, $boat_id)) HtmlResponseHandler::formatedResponse(200);
                else HtmlResponseHandler::formatedResponse(500);
                break;
            case "boat":
                if(Boat::addBoat($name)) HtmlResponseHandler::formatedResponse(200);
                else HtmlResponseHandler::formatedResponse(500);
                break;
            default:
                $e = new InvalidParameterError(ParameterErrorCase::Unknown, "type", "Invalid Request - Parameter is invalid");
                $e->respondWithError();
                break;
        }

    }

    public static function filterName(string $name): void{
        //Max: 255
        //Min: 3

        if(strlen($name) > 255) $e = new InvalidParameterError(ParameterErrorCase::Long, "name", "Invalid Request - Parameter is too long");
        if(strlen($name) < 3) $e = new InvalidParameterError(ParameterErrorCase::Short, "name", "Invalid Request - Parameter is too short");

        if(isset($e))$e->respondWithError();

        //Only alphanumeric characters and spaces
        if(!preg_match("/^[a-zA-Z0-9 ]*$/", $name)) $e = new InvalidParameterError(ParameterErrorCase::Format, "name", "Invalid Request - Parameter is invalid");
    }
}