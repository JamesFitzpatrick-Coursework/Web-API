<?php
namespace meteor\database;
check_env();

use meteor\core\Config;
use meteor\exceptions\DatabaseException;

class Database
{
    private static $tables = array (
        "users" =>              "meteor_users",
        "groups" =>             "meteor_groups",
        "users.settings" =>     "meteor_user_settings",
        "groups.settings" =>    "meteor_group_settings",
        "users.permissions" =>  "meteor_user_permissions",
        "groups.permissions" => "meteor_group_permissions",
        "groups.users" =>       "meteor_group_users",
        "tokens" =>             "meteor_tokens",
    );

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
     * @return \mysqli_result the result of the sql query
     * @throws DatabaseException if there was a error executing the query
     */
    public static function query($query)
    {
        if (!self::$connected) {
            return false;
        }

        $result = mysqli_query(self::$connection, $query);

        if ($result === false || mysqli_errno(self::$connection) != 0) {
            throw new DatabaseException(mysqli_error(self::$connection), $query);
        }

        return $result;
    }

    /**
     * Fetch the data for a row from a query result.
     *
     * @param $result \mysqli_result the result of a query
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
     * Return the amount of rows in a given query.
     *
     * @param $result \mysqli_result the query to count the rows in
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

    /**
     * Escape a string to be used in a query.
     *
     * @param $value mixed the string to escape
     *
     * @return bool|string the escaped string
     */
    public static function format_string($value)
    {
        if (!self::$connected) {
            return false;
        }

        return mysqli_escape_string(self::$connection, $value);
    }

    public static function generate_query($name, $data = array())
    {
        $path = "database/query/$name.sql";
        $file = fopen($path, "r");
        $query = fread($file, filesize($path));

        // Inject execution data into the query
        for ($i = 0; $i < count($data); $i++) {
            $query = preg_replace("/\{$i\}/", self::format_string($data[$i]), $query);
        }

        // Inject the table name into the query
        while (preg_match("/\{(table.([^}]+))\}/", $query, $data) == 1) {
            $query = preg_replace("/\{" . $data[1] . "\}/", self::$tables[$data[2]], $query, 1);
        }

        fclose($file);
        return new DatabaseQuery($query);
    }

    /**
     * Frees the memory associated with a result
     *
     * @param $query \mysqli_result the query result to close
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

        self::$connected = false;
        return mysqli_close(self::$connection);
    }
}