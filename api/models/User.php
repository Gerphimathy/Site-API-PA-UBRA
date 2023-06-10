<?php

class User{

    static string $table_name = TABLE_PREFIX."users";

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

        return $link->insert("INSERT INTO $table_name (login, password) VALUES (:login, :password)",
            [
                "login"=>$login,
                "password"=>$link::preparePassword($password),
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