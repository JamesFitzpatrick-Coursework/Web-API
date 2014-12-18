<?php
checkEnv();

class Config
{
    private static $config;

    public static function loadConfig()
    {
        self::$config = array();
        self::$config["database.host"] = "localhost";
        self::$config["database.user"] = "thefish_meteor";
        self::$config["database.password"] = "VqzwaeZeXcdD";
        self::$config["database.database"] = "thefish_meteor";
    }

    public static function getDatabaseHost()
    {
        return self::$config["database.host"];
    }

    public static function getDatabaseUser()
    {
        return self::$config["database.user"];
    }

    public static function getDatabasePassword()
    {
        return self::$config["database.password"];
    }

    public static function getDatabaseName()
    {
        return self::$config["database.database"];
    }
}