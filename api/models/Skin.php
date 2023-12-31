<?php

class Skin
{
    static string $table_name = TABLE_PREFIX."skins";

    static string $user_link_table = TABLE_PREFIX."skin_ownership";

    public static function addSkin(string $name, int $price, int $boat_id, string $identifier):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        if (self::getSkinDataByIdentifier($identifier) !== [])return false;

        return $link->insert("INSERT INTO $table_name (name, price, id_boat, identifier) VALUES (:name, :price, :id_boat, :identifier)",
            ["name"=>$name, "price"=>$price, "id_boat"=>$boat_id, "identifier"=>$identifier]);
    }

    public static function ownsSkin(int $id_user, int $id_skin):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$user_link_table;

        $res = $link->query("SELECT * FROM $table_name WHERE id_user = :id_user AND id_skin = :id_skin", ["id_user"=>$id_user, "id_skin"=>$id_skin]);

        return $res !== false;
    }

    public static function ownedSkins(int $id_user):array{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$user_link_table;

        $res = $link->queryAll("SELECT id_skin FROM $table_name WHERE id_user = :id_user", ["id_user"=>$id_user]);

        if($res === false) return [];

        $skins = [];
        foreach ($res as $skin){
            //Cast to object to avoid array to object conversion error
            $skins[] = (object)self::getSkinData($skin["id_skin"]);
        }

        return $skins;
    }

    public static function addOwnership(int $id_user, int $id_skin):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$user_link_table;

        return $link->insert("INSERT INTO $table_name (id_user, id_skin) VALUES (:id_user, :id_skin)", ["id_user"=>$id_user, "id_skin"=>$id_skin]);
    }

    public static function getSkinData(int $id_skin):array{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT id, name, id_boat, price, identifier FROM $table_name WHERE id = :id", ["id"=>$id_skin]);

        if ($res === false) return [];

        return $res;
    }

    public static function getSkinDataByIdentifier(string $identifier):array{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT id, name, id_boat, price, identifier FROM $table_name WHERE identifier = :identifier", ["identifier"=>$identifier]);

        if ($res === false) return [];

        return $res;
    }

    public static function getAllSkinsShop(int $begin = 0, int $length = 10, int $uid = -1):array{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;
        $user_link_table = self::$user_link_table;

        if($uid < 0){
            $query = "SELECT id, name, id_boat, price, identifier FROM $table_name";
            $params = [];
        }
        else{
            $query = "SELECT id, name, id_boat, price, identifier FROM $table_name WHERE id NOT IN (SELECT id_skin FROM $user_link_table WHERE id_user = :id_user)";
            $params = ["id_user"=>$uid];
        }

        try {
            $res = $link->queryAll($query, $params);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Skin::getAllSkinsShop");
            $e->respondWithError();
            die();
        }

        if ($res === false) return [];

        //Filter using begin and length
        return array_slice($res, $begin, $length);
    }

    public static function getSkinTotalShop(int $uid = -1):int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;
        $user_link_table = self::$user_link_table;

        if($uid < 0) {
            $query = "SELECT COUNT(*) FROM $table_name";
            $params = [];
        }
        else{
            $query = "SELECT COUNT(*) FROM $table_name WHERE id NOT IN (SELECT id_skin FROM $user_link_table WHERE id_user = :id_user)";
            $params = ["id_user"=>$uid];
        }

        try {
            $res = $link->query($query, $params);
        }catch (DatabaseConnectionError $e){
            $e->setStep("Skin::getAllSkinsShop");
            $e->respondWithError();
        }

        if ($res === false) return 0;

        return $res["COUNT(*)"];
    }

    public static function getLargestId():int{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT MAX(id) FROM $table_name", []);

        if ($res === false) return 0;

        return $res["MAX(id)"];
    }
}