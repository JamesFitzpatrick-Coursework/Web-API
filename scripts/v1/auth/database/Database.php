<?php
namespace meteor\database;
check_env();

use meteor\core\Config;
use meteor\exceptions\DatabaseException;

class Database
{
    private static $tables = [
        "users"                 => "meteor_users",
        "users.settings"        => "meteor_user_settings",
        "users.permissions"     => "meteor_user_permissions",
        "users.assignments"     => "meteor_users_assignments",
        "users.scores"          => "meteor_users_scores",
        "users.question.scores" => "meteor_users_question_scores",
        "groups"                => "meteor_groups",
        "groups.settings"       => "meteor_group_settings",
        "groups.permissions"    => "meteor_group_permissions",
        "groups.users"          => "meteor_group_users",
        "groups.assignments"    => "meteor_groups_assignments",
        "tokens"                => "meteor_tokens",
        "assignment"            => "meteor_assignments",
        "assessment"            => "meteor_assessments",
        "assessment.questions"  => "meteor_assessment_questions",
        "assessment.answers"    => "meteor_assessment_answers",
    ];

    private static $queryCache = [];

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

        if (mysqli_connect_errno() != 0) {
            throw new DatabaseException(mysqli_error(self::$connection));
        }

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
     * Returns the auto generated id used in the last query
     *
     * @return bool|int the auto generated id used in the last query
     */
    public static function insert_id()
    {
        if (!self::$connected) {
            return false;
        }

        return mysqli_insert_id(self::$connection);
    }

    /**
     * Generate a query from a pre defined query structure.
     *
     * @param string $name the query structure
     * @param array $data  the specific data to replace into the query
     *
     * @throws DatabaseException if there is a error generating the query
     * @return DatabaseQuery the query instance
     */
    public static function generate_query($name, $data = [])
    {
        if (array_key_exists($name, self::$queryCache)) {
            $query = self::$queryCache[$name];
        } else {
            $identifier = substr($name, 0, strpos($name, "_"));
            $path = "database/query/$identifier/$name.sql";

            if (!is_readable($path)) {
                throw new DatabaseException("Cannot find query with identifier " . $name . " (" . $path . ")");
            }

            $file = fopen($path, "r");
            $query = fread($file, filesize($path));
            self::$queryCache[$name] = $query;
        }

        // Inject execution data into the query
        for ($i = 0; $i < count($data); $i++) {
            $query = preg_replace("/\{$i\}/", preg_quote(self::format_string($data[$i])), $query);
        }

        // Inject the table name into the query
        while (preg_match("/\{(table.([^}]+))\}/", $query, $data) == 1) {
            if (!array_key_exists($data[2], self::$tables)) {
                throw new DatabaseException("Cannot find table with id " . $data[2], $name);
            }

            $query = preg_replace("/\{" . $data[1] . "\}/", self::$tables[$data[2]], $query, 1);
        }

        if (isset($file)) {
            fclose($file);
        }

        return new DatabaseQuery($query);
    }

    /**
     * Escape a string to be used in a query.
     *
     * @param $value mixed the string to escape
     *
     * @throws DatabaseException if the value provided is not in the correct
     *      format
     * @return bool|string the escaped string
     */
    public static function format_string($value)
    {
        if (!self::$connected) {
            return false;
        }

        if (!is_scalar($value)) {
            throw new DatabaseException("Value provided is not a valid sql type", ["value" => $value]);
        }

        return mysqli_escape_string(self::$connection, $value);
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