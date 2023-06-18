<?php

class Boat
{
    static string $table_name = TABLE_PREFIX."boats";

    public static function idIsValid(int $id):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT id FROM $table_name WHERE id = :id", ["id"=>$id]);

        return $res !== false;
    }

    public static function addBoat(string $name):bool{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        return $link->insert("INSERT INTO $table_name (name) VALUES (:name)", ["name"=>$name]);
    }

    public static function getBoatData(int $id_boat):array{
        $link = new DatabaseLinkHandler(HOST, CHARSET, DB, USER, PASS);
        $table_name = self::$table_name;

        $res = $link->query("SELECT name FROM $table_name WHERE id = :id", ["id"=>$id_boat]);

        if ($res === false) return [];

        return $res;
    }
}