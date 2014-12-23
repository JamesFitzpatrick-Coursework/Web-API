<?php

/**
 * Utility class for handling backend operations.
 */
class Backend
{

    /* USER OPERATIONS */

    public static function create_user($username, $password)
    {
        $userid = Token::generateNewToken(TOKEN_USER);

        Database::query("INSERT INTO " . DATABASE_TABLE_USERS . " VALUES
						('" . Database::format_string($userid->toString()) . "',
						'" . Database::format_string($username) . "',
						'" . Database::format_string($userid->getUserSecret()) . "',
						'" . Database::format_string(Crypt::hashPassword($password, $userid->getUserSecret())) . "');");

        return $userid;
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

    public static function fetch_user_profile($search)
    {
        if (Token::verify($search)) {
            $userid = Token::decode($search);

            if ($userid->getType() != TOKEN_GROUP) {
                throw new InvalidTokenException("Token provided is not a user id");
            }

            $query = "SELECT `name` FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $userid->toString() . "';";
            $result = Database::query($query);

            if (Database::count($result) == 0) {
                throw new InvalidUserException("Could not find a user with that id", array("user-id" => $userid->toString()));
            }

            $displayname = Database::fetch_data($result)["name"];
        } else {
            $displayname = $search;
            $query = "SELECT `id` FROM " . DATABASE_TABLE_USERS . " WHERE `name`='" . Database::format_string($displayname) . "';";
            $result = Database::query($query);

            if (Database::count($result) == 0) {
                throw new InvalidUserException("Could not find a user with that name", array("display-name" => $displayname));
            }

            $userid = Token::decode(Database::fetch_data($result)["id"]);
        }

        return new UserProfile($userid, $displayname);
    }

    public static function fetch_user_settings(UserProfile $profile)
    {
        $query = "SELECT `key`, `value` FROM " . DATABASE_TABLE_USER_SETTINGS . " WHERE `user_id`='" . $profile->getUserId()->toString() . "';";
        $result = Database::query($query);

        $settings = array();
        while($row = Database::fetch_data($result)) {
            $settings[$row["key"]] = $row["value"];
        }

        Database::close_query($result);
        return $settings;
    }

    public static function fetch_all_users()
    {
        $query = "SELECT * FROM " . DATABASE_TABLE_USERS;
        $result = Database::query($query);

        $users = array();
        while($row = Database::fetch_data($result)) {
            $users[] = new UserProfile(Token::decode($row["id"]), $row["name"]);
        }

        return $users;
    }

    public static function validate_user(UserProfile $user, $password)
    {
        $query = "SELECT `password` FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $user->getUserId()->toString() . "';";
        $result = Database::query($query);
        $hash = Database::fetch_data($result)["password"];
        Database::close_query($result);

        return Crypt::checkPassword($hash, $password, $user->getUserId()->getUserSecret());
    }

    /* GROUP OPERATIONS */

    public static function create_group($groupname)
    {
        $groupid = Token::generateNewToken(TOKEN_GROUP);

        Database::query("INSERT INTO " . DATABASE_TABLE_GROUPS . " VALUES
						('" . Database::format_string($groupid->toString()) . "',
						'" . Database::format_string($groupname) . "');");

        return $groupid;
    }

    public static function group_exists($lookup)
    {
        $query = "SELECT count('id') AS `count` FROM " . DATABASE_TABLE_GROUPS . " WHERE ";

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

    public static function fetch_group_profile($search)
    {
        if (Token::verify($search)) {
            $groupid = Token::decode($search);

            if ($groupid->getType() != TOKEN_GROUP) {
                throw new InvalidTokenException("Token provided is not a group id");
            }

            $query = "SELECT `name` FROM " . DATABASE_TABLE_GROUPS . " WHERE `id`='" . $groupid->toString() . "';";
            $result = Database::query($query);

            if (Database::count($result) == 0) {
                throw new InvalidGroupException("Could not find a group with that id", array("group-id" => $groupid->toString()));
            }

            $displayname = Database::fetch_data($result)["name"];
        } else {
            $displayname = $search;
            $query = "SELECT `id` FROM " . DATABASE_TABLE_GROUPS . " WHERE `name`='" . Database::format_string($displayname) . "';";
            $result = Database::query($query);

            if (Database::count($result) == 0) {
                throw new InvalidGroupException("Could not find a group with that name", array("display-name" => $displayname));
            }

            $groupid = Token::decode(Database::fetch_data($result)["id"]);
        }

        return new GroupProfile($groupid, $displayname);
    }

    public static function fetch_all_groups()
    {
        $query = "SELECT * FROM " . DATABASE_TABLE_GROUPS;
        $result = Database::query($query);

        $groups = array();
        while($row = Database::fetch_data($result)) {
            $groups[] = new GroupProfile(Token::decode($row["id"]), $row["name"]);
        }

        return $groups;
    }

    public static function fetch_group_settings(GroupProfile $profile)
    {
        $query = "SELECT `key`, `value` FROM " . DATABASE_TABLE_GROUP_SETTINGS . " WHERE `group_id`='" . $profile->getGroupId()->toString() . "';";
        $result = Database::query($query);

        $settings = array();
        while($row = Database::fetch_data($result)) {
            $settings[$row["key"]] = $row["value"];
        }

        Database::close_query($result);
        return $settings;
    }

    /* TOKEN OPERATIONS */

    public static function clear_tokens(Token $clientid, Token $userid, $tokentype)
    {
        $query = "DELETE FROM " . DATABASE_TABLE_TOKENS . " WHERE `token` LIKE '" . $tokentype . "-%' AND `client-id`='" . $clientid->toString() . "' AND `user`='" . $userid->toString() . "';";
        Database::query($query);
    }


    public static function validate_token(Token $clientid, Token $userid, Token $token)
    {
        $query = "SELECT count(`id`) AS count FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $token->toString() . "' AND `client-id`='" . $clientid->toString() . "' AND `user`='" . $userid->toString() . "' AND `expires` > NOW()";
        $result = Database::query($query);
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);

        return $count != 0;
    }

    public static function invalidate_token(Token $clientid, Token $token)
    {
        $query = "DELETE FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $token->toString() . "' AND `client-id`='" . $clientid->toString() . "'";
        Database::query($query);
    }

    public static function create_token(Token $clientid, Token $userid, $tokentype, $expires)
    {
        $token = Token::generateToken($tokentype, $userid->getUserSecret());

        $query = "INSERT INTO " . DATABASE_TABLE_TOKENS . " (`token`, `client-id`, `user`, `expires`) VALUES ('" . $token->toString() . "','" . $clientid->toString() . "','" . $userid->toString() . "', NOW() + INTERVAL $expires);";
        Database::query($query);
        return $token;
    }


}