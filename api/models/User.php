<?php

class User{

    static string $table_name = TABLE_PREFIX."users";
    static string $time_table_name = TABLE_PREFIX."times";

    /**
     * Inserts new use into the database
     * @param string $login
     * @param string $password
     * @return bool result of the insertion
     * @throws DatabaseConnectionError
     */
    public static function createUser(string $login, string $password):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        do{
            $id_code = DatabaseLinkHandler::generateRandomString(10);
            $res = $link->query("SELECT id FROM $table_name WHERE id_code = :id_code", ["id_code"=>$id_code]);
        }while($res !== false);

        return $link->insert("INSERT INTO $table_name (login, password, username, id_code) VALUES (:login, :password, :username, :id_code)",
            [
                "login"=>$login,
                "password"=>$link::preparePassword($password),
                "username"=>$login,
                "id_code"=>$id_code
            ]
        );
    }

    public static function getPlayerTime(int $id_user, int $id_map):int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$time_table_name;

        $res = $link->query("SELECT time FROM $table_name WHERE id_user = :id_user AND id_map = :id_map", ["id_user"=>$id_user, "id_map"=>$id_map]);

        if($res === false) return -1;

        return $res["time"];
    }

    public static function setPlayerTime(int $id_user, int $id_map, int $time):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$time_table_name;

        $res = $link->query("SELECT time FROM $table_name WHERE id_user = :id_user AND id_map = :id_map", ["id_user"=>$id_user, "id_map"=>$id_map]);

        if($res === false) return $link->insert("INSERT INTO $table_name (id_user, id_map, time) VALUES (:id_user, :id_map, :time)", ["id_user"=>$id_user, "id_map"=>$id_map, "time"=>$time]);

        return $link->insert("UPDATE $table_name SET time = :time WHERE id_user = :id_user AND id_map = :id_map", ["id_user"=>$id_user, "id_map"=>$id_map, "time"=>$time]);
    }

    public static function isAdmin(string $id):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT is_admin FROM $table_name WHERE id = :id", ["id"=>$id]);

        if($res === false) return false;

        return $res["is_admin"] == 1 || $res["is_admin"] == "1";
    }

    /**
     * @param int $id
     * @param string $newUsername
     * @return bool
     * @throws DatabaseConnectionError
     */
    public static function updateUserName(int $id, string $newUsername):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $newUsername = htmlspecialchars($newUsername);
        $newUsername = DatabaseLinkHandler::sanitizeStringQuotes($newUsername);

        return $link->insert("UPDATE $table_name SET username = :username WHERE id = :id",
            [
                "username"=>$newUsername,
                "id"=>$id
            ]
        );
    }

    /**
     * @param int $id
     * @param string $newPassword
     * @return bool
     * @throws DatabaseConnectionError
     */
    public static function updatePassWord(int $id, string $newPassword):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        return $link->insert("UPDATE $table_name SET password = :password WHERE id = :id",
            [
                "password"=>$link::preparePassword($newPassword),
                "id"=>$id
            ]
        );
    }

    public static function getUserData(int $id):array{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT login, username, id_code, points, is_admin FROM $table_name WHERE id = :id", ["id"=>$id]);

        if($res === false) return [];

        return $res;
    }

    public static function setPoints(int $id_user, int $newPoints):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        return $link->insert("UPDATE $table_name SET points = :points WHERE id = :id",
            [
                "points"=>$newPoints,
                "id"=>$id_user
            ]
        );
    }

    /**
     * @param int $id
     * @return bool
     * @throws DatabaseConnectionError
     */
    public static function updateIdCode(int $id):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        do{
            $id_code = DatabaseLinkHandler::generateRandomString(10);
            $res = $link->query("SELECT id FROM $table_name WHERE id_code = :id_code", ["id_code"=>$id_code]);
        }while($res !== false);

        return $link->insert("UPDATE $table_name SET id_code = :id_code WHERE id = :id",
            [
                "id_code"=>$id_code,
                "id"=>$id
            ]
        );
    }

    /**
     * Returns ID of the user with correct login-password combination, -1 if user does not exist
     * @param string $login
     * @param string $password
     * @return int ID of the user with correct login-password combination, -1 if user does not exist
     * @throws DatabaseConnectionError
     */
    public static function attemptLogin(string $login, string $password):int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT id FROM $table_name WHERE login = :login AND password = :password",
            [
                "login"=>$login,
                "password"=>$link::preparePassword($password)
            ]
        );

        if ($res === false) return -1;
        else return $res["id"];
    }

    public static function loginIdCode(string $id_code):int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT id FROM $table_name WHERE id_code = :id_code", ["id_code"=>$id_code]);

        if ($res === false) return false;
        else return $res["id"];
    }

    /**
     * Checks to if login is already in use
     * @throws DatabaseConnectionError
     * @return bool true if login already in use, false if it is free to use
     */
    public static function checkIfLoginExists(string $login):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT id FROM $table_name WHERE login = :login", ["login" => $login]);

        if ($res !== false) return true;
        else return false;
    }
}