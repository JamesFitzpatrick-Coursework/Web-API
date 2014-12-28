<?php

/**
 * Utility class for handling backend operations.
 */
class Backend
{

    /* USER OPERATIONS */

    public static function create_user($username, $password)
    {
        $username = Database::format_string($username);
        $userid = Token::generateNewToken(TOKEN_USER);
        $query = Database::generate_query("user_create", array ($userid->toString(), $username, $username, Crypt::hashPassword($password, $userid->getUserSecret())));
        $result = $query->execute();
        $result->close();
        return new UserProfile($userid, $username);
    }

    public static function user_exists($lookup)
    {
        // Check if it is a user id
        if (Token::verify($lookup)) {
            $query = Database::generate_query("user_lookup_id", array ($lookup));
        // Else assume a display name
        } else {
            $query = Database::generate_query("user_lookup_name", array ($lookup));
        }

        $result = $query->execute();
        $count = $result->count();
        $result->close();
        return $count >= 1;
    }

    public static function fetch_user_profile($search)
    {
        if (Token::verify($search)) {
            $userid = Token::decode($search);

            if ($userid->getType() != TOKEN_USER) {
                throw new InvalidTokenException("Token provided is not a user id", array ("user-id" => $userid->toString()));
            }

            $query = Database::generate_query("user_lookup_id", array ($userid->toString()));
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidUserException("Could not find a user with that id", array("user-id" => $userid->toString()));
            }

            $displayname = $result->fetch_data()["user_name"];
            $result->close();
        } else {
            $displayname = $search;
            $query = Database::generate_query("user_lookup_name", array ($displayname));
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidUserException("Could not find a user with that name", array("display-name" => $displayname));
            }

            $userid = Token::decode($result->fetch_data()["user_id"]);
            $result->close();
        }

        return new UserProfile($userid, $displayname);
    }

    public static function fetch_user_settings(UserProfile $profile)
    {
        $query = Database::generate_query("user_settings_fetch", array ($profile->getUserId()->toString()));
        $result = $query->execute();

        $settings = array();
        while($row = $result->fetch_data()) {
            $settings[$row["setting_key"]] = $row["setting_value"];
        }

        $result->close();
        return $settings;
    }

    public static function fetch_user_permissions(UserProfile $profile)
    {
        $query = Database::generate_query("user_permissions_fetch", array ($profile->getUserId()->toString()));
        $result = $query->execute();

        $settings = array();
        while($row = $result->fetch_data()) {
            $settings[] = $row["permission_key"];
        }

        $result->close();
        return $settings;
    }

    public static function fetch_all_users()
    {
        $query = Database::generate_query("user_lookup_all");
        $result = $query->execute();

        $users = array();
        while($row = $result->fetch_data()) {
            $users[] = new UserProfile(Token::decode($row["user_id"]), $row["user_name"]);
        }

        $result->close();

        return $users;
    }

    public static function validate_user(UserProfile $user, $password)
    {
        $query = Database::generate_query("user_validate", array ($user->getUserId()->toString()));
        $result = $query->execute();
        $hash = $result->fetch_data()["password"];
        $result->close();

        return Crypt::checkPassword($hash, $password, $user->getUserId()->getUserSecret());
    }

    /* GROUP OPERATIONS */

    public static function create_group($groupname)
    {
        $groupid = Token::generateNewToken(TOKEN_GROUP);
        $groupname = Database::format_string($groupname);

        $query = Database::generate_query("group_create", array ($groupid->toString(), $groupname));
        $result = $query->execute();
        $result->close();
        return $groupid;
    }

    public static function group_exists($lookup)
    {
        // Check if it is a user id
        if (Token::verify($lookup)) {
            $query = Database::generate_query("group_lookup_id", array ($lookup));
            // Else assume a display name
        } else {
            $query = Database::generate_query("group_lookup_name", array ($lookup));
        }

        $result = $query->execute();
        $count = $result->count();
        $result->close();
        return $count >= 1;
    }

    public static function fetch_group_profile($search)
    {
        if (Token::verify($search)) {
            $groupid = Token::decode($search);

            if ($groupid->getType() != TOKEN_GROUP) {
                throw new InvalidTokenException("Token provided is not a group id");
            }

            $query = Database::generate_query("group_lookup_id", array ($groupid->toString()));
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidGroupException("Could not find a group with that id", array("group-id" => $groupid->toString()));
            }

            $displayname = $result->fetch_data()["group_name"];
            $result->close();
        } else {
            $displayname = $search;
            $query = Database::generate_query("group_lookup_id", array ($displayname));
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidGroupException("Could not find a group with that name", array("display-name" => $displayname));
            }

            $groupid = Token::decode($result->fetch_data()["group_id"]);
            $result->close();
        }

        return new GroupProfile($groupid, $displayname);
    }

    public static function fetch_all_groups()
    {
        $query = Database::generate_query("group_lookup_all");
        $result = $query->execute();

        $groups = array();
        while($row = $result->fetch_data()) {
            $groups[] = new GroupProfile(Token::decode($row["group_id"]), $row["group_name"]);
        }
        $result->close();

        return $groups;
    }

    public static function fetch_group_settings(GroupProfile $profile)
    {
        $query = Database::generate_query("group_settings_fetch", array ($profile->getGroupId()->toString()));
        $result = $query->execute();

        $settings = array();
        while($row = $result->fetch_data()) {
            $settings[$row["setting_key"]] = $row["setting_value"];
        }

        $result->close();
        return $settings;
    }

    public static function fetch_group_permissions(GroupProfile $profile)
    {
        $query = Database::generate_query("group_settings_fetch", array ($profile->getGroupId()->toString()));
        $result = $query->execute();

        $settings = array();
        while($row = $result->fetch_data()) {
            $settings[] = $row["permission_key"];
        }

        $result->close();
        return $settings;
    }


    /* TOKEN OPERATIONS */

    public static function clear_tokens(Token $clientid, Token $userid, $tokentype)
    {
        $query = Database::generate_query("token_clear", array ($tokentype, $clientid->toString(), $userid->toString()));
        $query->execute();
    }


    public static function validate_token(Token $clientid, Token $userid, Token $token)
    {
        $query = Database::generate_query("token_validate", array ($token->toString(), $clientid->toString(), $userid->toString()));
        $result = $query->execute();
        $count = $result->fetch_data()["count"];
        $result->close();

        return $count != 0;
    }

    public static function invalidate_token(Token $clientid, Token $token)
    {
        $query = Database::generate_query("token_invalidate", array ($token->toString(), $clientid->toString()));
        $query->execute();
    }

    public static function create_token(Token $clientid, Token $userid, $tokentype, $expires)
    {
        $token = Token::generateToken($tokentype, $userid->getUserSecret());

        $query = Database::generate_query("token_create", array ($token->toString(), $clientid->toString(), $userid->toString(), $expires));
        $result = $query->execute();
        $result->close();
        return $token;
    }
}