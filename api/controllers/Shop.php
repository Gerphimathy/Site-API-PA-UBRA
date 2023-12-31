<?php

class Shop
{
    public static function get(): void
    {
        include __DIR__ . "/../../public/views/shop.php";
        die();
    }

    //Purchase skin
    public static function post(): void{
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
            $id_user = Token::tokenIsValid($json->token, $_SERVER['HTTP_USER_AGENT']);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }

        if($id_user === -1) HtmlResponseHandler::formatedResponse(403);

        if(empty($json->id_skin)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "id_skin", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        $user_points = User::getUserData($id_user)["points"];
        $skin_price = Skin::getSkinData($json->id_skin)["price"] ?? null;

        if ($skin_price === null) HtmlResponseHandler::formatedResponse(404);

        if(Skin::ownsSkin($id_user, $json->id_skin)) HtmlResponseHandler::formatedResponse(409);

        if ($user_points < $skin_price) HtmlResponseHandler::formatedResponse(402);

        $r1 = Skin::addOwnership($id_user, $json->id_skin);
        $r2 = User::setPoints($id_user, $user_points - $skin_price);

        if($r1 && $r2) HtmlResponseHandler::formatedResponse(200);
        else HtmlResponseHandler::formatedResponse(500);
    }

    //DEPRECATED, use the /admin interface
    public static function put(){
        return 0;

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
        $id_boat = $json->id_boat ?? null;

        if($id_boat !== null && !Boat::idIsValid($id_boat)){
            $e = new InvalidParameterError(ParameterErrorCase::Unknown, "id_boat", "Invalid Request - Parameter is invalid");
            $e->respondWithError();
        }

        //Will also kill process if invalid
        self::filterName($name);

        switch ($json->type){
            case "skin":
                if($id_boat === null) {
                    $e = new InvalidParameterError(ParameterErrorCase::Empty, "boat_id", "Invalid Request - Parameter is missing");
                    $e->respondWithError();
                }
                if(Skin::addSkin($name, $price, $id_boat)) HtmlResponseHandler::formatedResponse(200);
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