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
                    $skin->boat_identifier = $boat_data["identifier"];
                }
                HtmlResponseHandler::formatedResponse(200, [], $skins);
                break;
            case "time":
                include_once __DIR__."/../models/User.php";
                $map = $_GET["map"] ?? null;
                if($map === null){
                    $e = new InvalidParameterError(ParameterErrorCase::Empty, "map", "Invalid Request - Parameter is missing");
                    $e->respondWithError();
                }
                $time = User::getPlayerTime($id, $map);
                if($time === -1) HtmlResponseHandler::formatedResponse(404);
                HtmlResponseHandler::formatedResponse(200, [], ["time" => $time]);
                break;

            default:
                $e = new InvalidParameterError(ParameterErrorCase::Format, "data", "Invalid Request - Parameter is invalid");
                $e->respondWithError();
                break;
        }
    }

    public static function post():void{
        include_once __DIR__."/../models/Token.php";
        include_once __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if(empty($json->id_code)){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "id_code", "Invalid Request - Parameter is missing");

            //Kills process
            $e->respondWithError();
        }

        try{
            $id = User::loginIdCode($json->id_code);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }


        if($id < 1) HtmlResponseHandler::formatedResponse(403);

        $points = $json->points ?? null;
        $chrono = $json->chrono ?? null;
        if($points !== null){
            $current = User::getUserData($id)["points"];
            $new = $current + $points;
            User::setPoints($id, $new);
            HtmlResponseHandler::formatedResponse(200, [], ["points" => $new]);
        }
        elseif($chrono !== null){
            $map = $json->map ?? null;
            if ($map === null){
                $e = new InvalidParameterError(ParameterErrorCase::Empty, "map", "Invalid Request - Parameter is missing");
                $e->respondWithError();
            }

            $current = User::getPlayerTime($id, $map);
            if($current < $chrono){
                User::setPlayerTime($id, $map, $chrono);
                HtmlResponseHandler::formatedResponse(200, [], ["chrono" => $chrono]);
            }
            else{
                HtmlResponseHandler::formatedResponse(200, [], ["chrono" => $current]);
            }
        }

        $e = new InvalidParameterError(ParameterErrorCase::Empty, "data", "Invalid Request - Parameter is missing: (points or chrono)");
        $e->respondWithError();
    }
}