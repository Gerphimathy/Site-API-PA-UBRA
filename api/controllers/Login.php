<?php

use JetBrains\PhpStorm\NoReturn;

class Login
{
    /**
     * Get login page
     */
    public static function get(): void
    {
        include __DIR__ . "/../../public/views/login.php";
        die();
    }

    /**
     * Creates New User
     * @throws DatabaseConnectionError
     */
    public static function put(): void
    {
        include __DIR__ . "/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if (empty($json->login)) {
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "login", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if (empty($json->password)) {
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "password", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        self::checkParams([
            "login" => $json->login,
            "password" => $json->password,
        ]);

        try {
            if (User::checkIfLoginExists($json->login))
                HtmlResponseHandler::formatedResponse(409);
        } catch (DatabaseConnectionError $e) {
            $e->setStep("Duplicate verification");
            $e->respondWithError();
        }

        try {
            if (User::createUser($json->login, $json->password) === false) {
                $e = new DatabaseConnectionError("Unknown error", "Internal Error - Could not write to database", "Database insertion");
                $e->respondWithError();
            }
        } catch (DatabaseConnectionError $err) {
            $err->setStep("Database insertion");
            $err->respondWithError();
        }

        HtmlResponseHandler::formatedResponse(200);
    }

    /**
     * Login
     * @return void
     */
    public static function post(): void
    {
        include __DIR__ . "/../models/User.php";
        include __DIR__ . "/../models/Token.php";

        $json = json_decode(file_get_contents("php://input"));

        if (empty($json->login)) {
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "login", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        if (empty($json->password)) {
            $e = new InvalidParameterError(ParameterErrorCase::Empty, "password", "Invalid Request - Parameter is missing");
            $e->respondWithError();
        }

        try {
            $id = User::attemptLogin($json->login, $json->password);
        } catch (DatabaseConnectionError $e) {
            $e->setStep("Login attempt");
            $e->respondWithError();
            die();
        }

        if ($id === -1) HtmlResponseHandler::formatedResponse(403);

        $agent = $_SERVER['HTTP_USER_AGENT'];

        try {
            if (Token::tokenExists($id, $agent))
                $token = Token::refreshToken($id, $agent);
            else
                $token = Token::generateToken($id, $agent);
        } catch (DatabaseConnectionError $e) {
            $e->setStep("Token generation");
            $e->respondWithError();
            die();
        }

        HtmlResponseHandler::formatedResponse(200, [], ["token" => $token]);
    }

    /**
     * Validate form inputs individually
     * @param mixed $param input to be validated
     * @param string $paramName name of input to be validated
     * @param int $minLength (optional) minimum length of input
     * @param int $maxLength (optional) maximum length of input
     * @throws InvalidParameterError
     */
    private static function validateParam(mixed $param, string $paramName, int $minLength = 1, int $maxLength = 20): void
    {
        if (strlen($param) > $maxLength)
            throw new InvalidParameterError(ParameterErrorCase::Long, $paramName, "Invalid Request - Parameter length exceeded");
        if (strlen($param) < $minLength)
            throw new InvalidParameterError(ParameterErrorCase::Short, $paramName, "Invalid Request - Parameter length bellow minimum");

        switch ($paramName) {
            case "login":
                if (!is_numeric($param) && !filter_var($param, FILTER_VALIDATE_EMAIL))
                    throw new InvalidParameterError(ParameterErrorCase::Format, $paramName, "Invalid Request - Parameter is of wrong format");
                break;
            case "password":
                //TODO
            default:
                break;
        }
    }

    /**
     * Checks input params array and kills+respond if any param is invalid
     * @param array $params
     * @return void
     */
    public static function checkParams(array $params): void
    {
        foreach ($params as $paramName => $param) {
            $min = 0;
            $max = 0;
            switch ($paramName) {
                case "login":
                    $min = 5;
                    $max = 64;
                    break;
                case "password":
                    $min = 8;
                    $max = 64;
                    break;
            }
            try {
                self::validateParam($param, $paramName, $min, $max);
            } catch (InvalidParameterError $e) {
                $e->respondWithError();
            }
        }
    }
}