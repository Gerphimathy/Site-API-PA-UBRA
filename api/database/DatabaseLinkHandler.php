<?php

class DatabaseLinkHandler{
    private PDO $pdo;

    /**
     * @throws DatabaseConnectionError
     */
    function __construct(string $host, string $charset, string $dbname, string $user, string $pass){
        $host = $this->sanitizeStringQuotes($host);
        $charset = $this->sanitizeStringQuotes($charset);
        $dbname = $this->sanitizeStringQuotes($dbname);
        $user = $this->sanitizeStringQuotes($user);
        $pass = $this->sanitizeStringQuotes($pass);

        try{
            $this->pdo = new PDO('mysql:host='.$host.';charset='.$charset.';dbname='.$dbname,
                $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }catch(Exception $e){
            throw new DatabaseConnectionError($e->getMessage(), "Internal Error - Could not connect to database", "database connection");
        }
    }

    /**
     *
     * Public Functions
     *
     */

    /**
     * Select Query that returns first selected
     * @param string $query Query to execute
     * @param array $args Arguments for the query
     * @return array|bool false if empty, array of selected if select
     * @throws DatabaseConnectionError
     */
    public function query(string $query, array $args): array|bool{
        try {
            $req = $this->pdo->prepare($query);
            $req->execute($args);
            //$req->debugDumpParams();
            return $req->fetch(PDO::FETCH_ASSOC);
        }catch (Exception $e){
            throw new DatabaseConnectionError($e->getMessage(), "Internal Error - Could not query database");
        }
    }

    /**
     * Select Query that returns all selected
     * @param string $query Query to execute
     * @param array $args Arguments for the query
     * @return array|bool false if empty, array of selected if select
     * @throws DatabaseConnectionError
     */
    public function queryAll(string $query, array $args): array|bool{
        try {
            $req = $this->pdo->prepare($query);
            $req->execute($args);
            //$req->debugDumpParams();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }catch (Exception $e){
            throw new DatabaseConnectionError($e->getMessage(), "Internal Error - Could not query database");
        }
    }

    /**
     * Update/Insert query
     * @param string $query Query to execute
     * @param array $args Arguments for the query
     * @return bool false if could not insert/update, true if could insert/update
     * @throws DatabaseConnectionError
     */
    public function insert(string $query, array $args): bool{
        try {
            $req = $this->pdo->prepare($query);
            $res = $req->execute($args);
            //$req->debugDumpParams();
            return $res;
        }catch (Exception $e){
            throw new DatabaseConnectionError($e->getMessage(), "Internal Error - Could not update database");
        }
    }

    /**
     *
     * Tools
     *
     */


    /**
     * @param string $password input password
     * @return string output salted hash
     */
    public static function preparePassword(string $password) : string{
        return hash('sha512', SALT.$password);
    }

    /**
     * Generate string of random alphanumeric characters
     * (Source : https://stackoverflow.com/questions/4356289/php-random-string-generator)
     * @param int $length
     * @return string random alphanumeric string of len $length
     */
    public static function generateRandomString(int $length):string {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * Removes quotes and double quotes from a string
     * @param string $inputString String to be sanitized
     * @return string Sanitized String
     */
    public static function sanitizeStringQuotes(string $inputString) : string{
        $inputString = htmlspecialchars($inputString);

        str_replace("'", "\'", $inputString);
        str_replace('"', '\"', $inputString);

        return $inputString;
    }

    /**
     * Removes special characters from any sql query argument
     * Not necessary for prepared statements
     * @param string $inputString String to be sanitized
     * @return string Sanitized String
     */
    public static function sanitizeSqlQueryWord(string $inputString) : string{
        return preg_replace("/[A-Za-z0-9]",'',$inputString);
    }

}