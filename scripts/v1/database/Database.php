<?php
checkEnv();

define("DATABASE_TABLE_USERS", "meteor_users");
define("DATABASE_TABLE_GROUPS", "meteor_groups");
define("DATABASE_TABLE_USER_SETTINGS", "meteor_user_settings");
define("DATABASE_TABLE_GROUP_SETTINGS", "meteor_group_settings");
define("DATABASE_TABLE_TOKENS", "meteor_tokens");

class Database {

	private static $connected;
	private static $connection;

	public static function load()
	{
		self::$connection = mysqli_connect(Config::getDatabaseHost(), Config::getDatabaseUser(), Config::getDatabasePassword(), Config::getDatabaseName());
		self::$connected = true;
	}

	public static function query($query)
	{
		if (!self::$connected) return FALSE;
		
		$result = mysqli_query(self::$connection, $query);
		
		if ($result === FALSE)
		{
			return FALSE;
		}
		else if ($result === TRUE)
		{
			return TRUE;
		}
		
		return mysqli_fetch_assoc($result);
	}
	
	public static function close()
	{
		if (!self::$connected) return FALSE;
		return mysqli_close(self::$connection);
	}
}