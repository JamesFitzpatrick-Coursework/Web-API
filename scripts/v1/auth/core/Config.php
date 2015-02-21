<?php
namespace meteor\core;
check_env();

class Config
{
    private static $config;

    public static function loadConfig()
    {
        self::$config = [];
        $my_file = 'core/Config.data';
        $handle = fopen($my_file, 'r');

        while ($line = fgets($handle)) {
            if (!starts_with($line, "#") && $line != "") {
                if (preg_match("/(.*) = (.*)/", $line, $data) == 1) {
                    self::$config[trim($data[1])] = trim($data[2]);
                }
            }
        }

        fclose($handle);
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