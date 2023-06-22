<?php

class Auth{

    /**
     * Get auth status as well as specified user data
     */
    public static function get():void{
        include_once __DIR__."/../models/Token.php";
        include_once __DIR__."/../models/User.php";

        if(empty($_GET["id_code"])){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "id_code", "Invalid Request - Parameter is missing");

            //Kills process
            $e->respondWithError();
        }

        try{
            $id = User::loginIdCode($_GET["id_code"]);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }


        if($id < 1) HtmlResponseHandler::formatedResponse(403);

        $data = $_GET["data"] ?? null;
        switch ($data){
            case "user":
                include_once __DIR__."/../models/User.php";
                $user = User::getUserData($id);
                HtmlResponseHandler::formatedResponse(200, [], $user);
                break;
            case "skins":
                include_once __DIR__."/../models/Skin.php";
                include_once __DIR__."/../models/Boat.php";
                $skins = Skin::ownedSkins($id);
                foreach ($skins as $skin){
                    $boat_data = Boat::getBoatData($skin->id_boat);

                    $skin->boat_name = $boat_data["name"];
                }
                HtmlResponseHandler::formatedResponse(200, [], $skins);
                break;
            default:
                $e = new InvalidParameterError(ParameterErrorCase::Format, "data", "Invalid Request - Parameter is invalid");
                $e->respondWithError();
                break;
        }
    }

    public static function post():void{
        include_once __DIR__."/../models/Token.php";

        $json = json_decode(file_get_contents("php://input"));

        if(empty($json->token)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "token", "Invalid Request - Parameter is missing");

            //Kills process
            $e->respondWithError();
        }

        try{
            $id = Token::tokenIsValid($json->token, $_SERVER['HTTP_USER_AGENT']);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }

        $agent = $_SERVER['HTTP_USER_AGENT'];
        $isvalid = !($id === -1);

        if(!$isvalid) HtmlResponseHandler::formatedResponse(403);

        $token = Token::refreshToken($id, $agent);

        HtmlResponseHandler::formatedResponse(200, [], ["token"=>$token, "agent"=>$agent, "expires"=>Token::getTokenExpiration($token, $agent)]);
    }
}