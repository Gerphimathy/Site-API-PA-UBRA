<?php

class Skin
{
    static string $table_name = TABLE_PREFIX."skins";
    
    public static function addSkin(string $name, int $price, int $boat_id):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        return $link->insert("INSERT INTO $table_name (name, price, boat_id) VALUES (:name, :price, :boat_id)",
            ["name"=>$name, "price"=>$price, "boat_id"=>$boat_id]);
    }
}