<?php
checkEnv();

class Config {

	private static $config;

	public static function loadConfig()
	{
		self::$config = array();
		self::$config["database.host"] = "localhost";
		self::$config["database.user"] = "root";
		self::$config["database.password"] = "";
		self::$config["database.database"] = "meteor";
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