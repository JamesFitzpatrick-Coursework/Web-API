<?php
checkEnv();

define("DATABASE_PREFIX", "meteor_");
define("DATABASE_TABLE_USERS", DATABASE_PREFIX . "users");
define("DATABASE_TABLE_GROUPS", DATABASE_PREFIX . "groups");
define("DATABASE_TABLE_USER_SETTINGS", DATABASE_PREFIX . "user_settings");
define("DATABASE_TABLE_GROUP_SETTINGS", DATABASE_PREFIX . "group_settings");
define("DATABASE_TABLE_TOKENS", DATABASE_PREFIX . "tokens");

class Database
{

    private static $connected;
    private static $connection;

    /**
     * Initialise the database from the config settings.
     */
    public static function init()
    {
        if (self::$connected) {
            return;
        }

        self::$connection = mysqli_connect(Config::getDatabaseHost(), Config::getDatabaseUser(), Config::getDatabasePassword(), Config::getDatabaseName());
        self::$connected = true;
    }

    /**
     * Execute a SQL query to this database.
     *
     * @param $query string the sql query to execute
     *
     * @return mysqli_result the result of the sql query
     * @throws DatabaseException if there was a error executing the query
     */
    public static function query($query)
    {
        if (!self::$connected) {
            return false;
        }

        $result = mysqli_query(self::$connection, $query);

        if ($result === false || mysqli_errno(self::$connection) != 0) {
            throw new DatabaseException(mysqli_error(self::$connection));
        }

        return $result;
    }

    /**
     * Fetch the data for a row from a query result.
     *
     * @param $result mysqli_result the result of a query
     *
     * @return array|bool|null the data fetched from the result
     */
    public static function fetch_data($result)
    {
        if (!self::$connected) {
            return false;
        }

        return mysqli_fetch_assoc($result);
    }

    /**
     * @param $result
     *
     * @return bool|int the amount of rows contained in the specified query
     */
    public static function count($result)
    {
        if (!self::$connected) {
            return false;
        }

        return mysqli_num_rows($result);
    }

    public static function formatString($value)
    {
        if (!self::$connected) {
            return false;
        }

        return mysqli_escape_string(self::$connection, $value);
    }

    /**
     * Frees the memory associated with a result
     *
     * @param $query mysqli_result the query result to close
     *
     * @return bool if it was successful
     */
    public static function close_query($query)
    {
        if (!self::$connected) {
            return false;
        }

        mysqli_free_result($query);

        return true;
    }

    /**
     * Close the database connection
     *
     * @return bool if it was successful
     */
    public static function close()
    {
        if (!self::$connected) {
            return false;
        }

        return mysqli_close(self::$connection);
    }
}