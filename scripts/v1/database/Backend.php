<?php

/**
 * Utility class for handling backend operations.
 */
class Backend
{

    /**
     * Create a user in the backend database.
     *
     * @param $userid Token the new user's user id
     * @param $username String the new user's display-name
     * @param $password String the new user's password
     *
     * @return mysqli_result the result of the query
     * @throws DatabaseException if there was a error handling the SQL query
     */
    public static function create_user($userid, $username, $password)
    {
        return Database::query("INSERT INTO " . DATABASE_TABLE_USERS . " VALUES
						('" . Database::format_string($userid->toString()) . "',
						'" . Database::format_string($username) . "',
						'" . Database::format_string($userid->getUserSecret()) . "',
						'" . Database::format_string($password) . "');");
    }

    public static function user_exists($lookup)
    {
        $query = "SELECT count('id') AS `count` FROM " . DATABASE_TABLE_USERS . " WHERE ";

        // Check if it is a user id
        if (Token::verify($lookup)) {
            $query .= "`id`='";
        // Else assume a display name
        } else {
            $query .= "`name`='";
        }

        $query .= Database::format_string($lookup) . "'";

        $result = Database::query($query);
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);
        return $count >= 1;
    }
}