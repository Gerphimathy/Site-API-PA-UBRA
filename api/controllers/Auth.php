<?php

class Auth{

    /**
     * Get auth status
     */
    public static function get():void{
        include_once __DIR__."/../models/Token.php";

        if(empty($_GET["token"])){
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "token", "Invalid Request - Parameter is missing");

            //Kills process
            $e->respondWithError();
        }

        try{
            $id = Token::tokenIsValid($_GET["token"], $_SERVER['HTTP_USER_AGENT']);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Token Check");
            $e->respondWithError();
            die();
        }


        $token = $_GET["token"];
        $agent= $_SERVER['HTTP_USER_AGENT'];
        $isvalid = !($id === -1);

        HtmlResponseHandler::formatedResponse(200, [], ["token"=>$token, "agent"=>$agent, "isvalid"=>$isvalid]);
    }
}