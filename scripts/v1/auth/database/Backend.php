<?php
namespace meteor\database;

use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\data\UserProfile;
use meteor\data\GroupProfile;
use meteor\core\Crypt;
use meteor\exceptions\InvalidUserException;
use meteor\exceptions\InvalidGroupException;


/**
 * Utility class for handling backend operations.
 */
class Backend
{

    /* USER OPERATIONS */

    public static function create_user($username, $displayname, $password)
    {
        $username = Database::format_string($username);
        $displayname = Database::format_string($displayname);
        $userid = Token::generateNewToken(TOKEN_USER);
        $query = Database::generate_query("user_create", array ($userid->toString(), $username, $displayname, Crypt::hash_password($password)));
        $query->execute();
        return new UserProfile($userid, $username, $displayname);
    }

    public static function delete_user(UserProfile $profile)
    {
        $query = Database::generate_query("user_delete", array ($profile->getUserId()->toString()));
        $query->execute();
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

            $row = $result->fetch_data();
            $username = $row["user_name"];
            $displayname = $row["user_display_name"];
            $result->close();
        } else {
            $username = $search;
            $query = Database::generate_query("user_lookup_name", array ($username));
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidUserException("Could not find a user with that name", array("display-name" => $username));
            }

            $row = $result->fetch_data();
            $userid = Token::decode($row["user_id"]);
            $displayname = $row["user_display_name"];
            $result->close();
        }

        return new UserProfile($userid, $username, $displayname);
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

    public static function set_user_setting(UserProfile $profile, $setting)
    {
        if (array_key_exists($setting->{"key"}, Backend::fetch_user_settings($profile))) {
            $query = Database::generate_query("user_setting_update", array ($profile->getUserId()->toString(), $setting->{"key"}, $setting->{"value"}));
        } else {
            $query = Database::generate_query("user_setting_set", array ($profile->getUserId()->toString(), $setting->{"key"}, $setting->{"value"}));
        }

        $query->execute();
    }

    public static function delete_user_setting(UserProfile $profile, $setting)
    {
        $query = Database::generate_query("user_setting_delete", array ($profile->getUserId()->toString(), $setting));
        $query->execute();
    }

    public static function fetch_user_setting(UserProfile $profile, $setting)
    {
        $query = Database::generate_query("user_setting_lookup", array ($profile->getUserId()->toString(), $setting));
        $result = $query->execute();
        $value = $result->fetch_data()["setting_value"];
        $result->close();
        return $value;
    }

    public static function fetch_user_permissions(UserProfile $profile)
    {
        $query = Database::generate_query("user_permission_fetch", array ($profile->getUserId()->toString()));
        $result = $query->execute();

        $settings = array();
        while($row = $result->fetch_data()) {
            $settings[] = $row["permission_key"];
        }

        /** @var GroupProfile $group */
        foreach (Backend::fetch_user_groups($profile) as $group) {
            $query = Database::generate_query("group_permission_fetch", array ($group->getGroupId()->toString()));
            $result = $query->execute();

            while ($row = $result->fetch_data()) {
                $settings[] = $row["permission_key"];
            }
        }

        $result->close();
        return $settings;
    }

    public static function set_user_permission(UserProfile $profile, $permission, $value)
    {
        if ($value) {
            $query = Database::generate_query("user_permission_add", array ($profile->getUserId()->toString(), $permission));
        } else {
            $query = Database::generate_query("user_permission_remove", array ($profile->getUserId()->toString(), $permission));
        }

        $query->execute();
    }

    public static function check_user_permission(UserProfile $profile, $permission)
    {
        $query = Database::generate_query("user_permission_check", array ($profile->getUserId()->toString(), $permission));
        $result = $query->execute();
        $count = $result->count();
        $result->close();

        if ($count >= 1) {
            return true;
        }

        foreach (Backend::fetch_user_groups($profile) as $group) {
            if (Backend::check_group_permission($group, $permission)) {
                return true;
            }
        }

        return false;
    }

    public static function fetch_all_users()
    {
        $query = Database::generate_query("user_lookup_all");
        $result = $query->execute();

        $users = array();
        while($row = $result->fetch_data()) {
            $users[] = new UserProfile(Token::decode($row["user_id"]), $row["user_name"], $row["user_display_name"]);
        }

        $result->close();

        return $users;
    }

    public static function validate_user(UserProfile $user, $password)
    {
        $query = Database::generate_query("user_validate", array ($user->getUserId()->toString()));
        $result = $query->execute();
        $hash = $result->fetch_data()["user_password"];
        $result->close();

        return Crypt::check_password($hash, $password);
    }

    public static function fetch_user_groups(UserProfile $user)
    {
        $query = Database::generate_query("user_groups_list", array ($user->getUserId()->toString()));
        $result = $query->execute();
        $groups = array();

        while ($row = $result->fetch_data()) {
            $groups[] = new GroupProfile(Token::decode($row["group_id"]), $row["group_name"], $row["group_display_name"]);
        }

        return $groups;
    }

    /* GROUP OPERATIONS */

    public static function create_group($groupname, $displayname)
    {
        $groupid = Token::generateNewToken(TOKEN_GROUP);
        $groupname = Database::format_string($groupname);
        $displayname = Database::format_string($displayname);

        $query = Database::generate_query("group_create", array ($groupid->toString(), $groupname, $displayname));
        $query->execute();
        return new GroupProfile($groupid, $groupname, $displayname);
    }

    public static function delete_group(GroupProfile $profile)
    {
        $query = Database::generate_query("group_delete", array ($profile->getGroupId()->toString()));
        $query->execute();
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

            $row = $result->fetch_data();
            $displayname = $row["group_display_name"];
            $name = $row["group_name"];
            $result->close();
        } else {
            $name = $search;
            $query = Database::generate_query("group_lookup_name", array ($name));
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidGroupException("Could not find a group with that name", array("group-name" => $name));
            }

            $row = $result->fetch_data();
            $groupid = Token::decode($row["group_id"]);
            $displayname = $row["group_display_name"];
            $result->close();
        }

        return new GroupProfile($groupid, $name, $displayname);
    }

    public static function fetch_all_groups()
    {
        $query = Database::generate_query("group_lookup_all");
        $result = $query->execute();

        $groups = array();
        while($row = $result->fetch_data()) {
            $groups[] = new GroupProfile(Token::decode($row["group_id"]), $row["group_name"], $row["group_display_name"]);
        }
        $result->close();

        return $groups;
    }

    public static function fetch_group_users(GroupProfile $group)
    {
        $query = Database::generate_query("group_user_list", array ($group->getGroupId()->toString()));
        $result = $query->execute();
        $groups = array();

        while ($row = $result->fetch_data()) {
            $groups[] = new UserProfile(Token::decode($row["user_id"]), $row["user_name"], $row["user_display_name"]);
        }

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

    public static function set_group_setting(GroupProfile $profile, $setting)
    {
        if (array_key_exists($setting->{"key"}, Backend::fetch_group_settings($profile))) {
            $query = Database::generate_query("group_setting_update", array ($profile->getGroupId()->toString(), $setting->{"key"}, $setting->{"value"}));
        } else {
            $query = Database::generate_query("group_setting_set", array ($profile->getGroupId()->toString(), $setting->{"key"}, $setting->{"value"}));
        }

        $query->execute();
    }

    public static function delete_group_setting(GroupProfile $profile, $setting)
    {
        $query = Database::generate_query("group_setting_delete", array ($profile->getGroupId()->toString(), $setting));
        $query->execute();
    }

    public static function fetch_group_setting(GroupProfile $profile, $setting)
    {
        $query = Database::generate_query("group_setting_lookup", array ($profile->getGroupId()->toString(), $setting));
        $result = $query->execute();
        $value = $result->fetch_data()["setting_value"];
        $result->close();
        return $value;
    }

    public static function fetch_group_permissions(GroupProfile $profile)
    {
        $query = Database::generate_query("group_permission_fetch", array ($profile->getGroupId()->toString()));
        $result = $query->execute();

        $settings = array();
        while($row = $result->fetch_data()) {
            $settings[] = $row["permission_key"];
        }

        $result->close();
        return $settings;
    }

    public static function set_group_permission(GroupProfile $profile, $permission, $value)
    {
        if ($value) {
            $query = Database::generate_query("group_permission_add", array ($profile->getGroupId()->toString(), $permission));
        } else {
            $query = Database::generate_query("group_permission_remove", array ($profile->getGroupId()->toString(), $permission));
        }

        $query->execute();
    }

    public static function check_group_permission(GroupProfile $profile, $permission)
    {
        $query = Database::generate_query("group_permission_check", array ($profile->getGroupId()->toString(), $permission));
        $result = $query->execute();
        $count = $result->count();
        $result->close();
        return $count >= 1;
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

        $query = Database::generate_query("token_create", array ($token->toString(), $userid->toString(), $clientid->toString(), $expires));
        $query->execute();
        return $token;
    }
}