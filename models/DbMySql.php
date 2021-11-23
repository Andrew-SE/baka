<?php

class DbMySql
{
    public static $conn;
    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC //PDO::FETCH_OBJ
    );

    public static function connect($host, $user, $password, $db){
        if (!isset(self::$conn)){
            self::$conn= @new PDO(
                "mysql:host=$host;dbname=$db",
                $user,
                $password,
                self::$settings
            );
        }
    }

    public static function oneQuery($query, $params = array())
    {
        $response = self::$conn->prepare($query);
        $response->execute($params);
        return $response->fetch();
    }

    public static function allQuery($query, $params = array())
    {
        $response = self::$conn->prepare($query);
        $response->execute($params);
        return $response->fetchAll();
    }
    public static function firstColQuery($query, $params = array()){
        $response = self::oneQuery($query, $params);
        return $response[0];
    }

    public static function rowCount($query, $params = array())
    {
        $response = self::$conn->prepare($query);
        $response->execute($params);
        return $response->rowCount();
    }

    public static function insert($query, $params = array()){
        try {
            $response = self::$conn->prepare($query);
            $response->execute($params);
            return 0;
        }
        catch(PDOException $e)
        {
            return  "Writing into the database failed" . "<br>" . $e->getMessage();
        }
    }

}