<?php
namespace meteor\database\backend;

use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\data\profiles\GroupProfile;
use meteor\data\profiles\UserProfile;
use meteor\database\Database;
use meteor\exceptions\InvalidGroupException;

class GroupBackend {

    public static function create_group($groupname, $displayname)
    {
        $groupid = Token::generateNewToken(TOKEN_GROUP);
        $groupname = Database::format_string($groupname);
        $displayname = Database::format_string($displayname);

        $query = Database::generate_query("group_create", array($groupid->toString(), $groupname, $displayname));
        $query->execute();

        return new GroupProfile($groupid, $groupname, $displayname);
    }

    public static function delete_group(GroupProfile $profile)
    {
        $query = Database::generate_query("group_delete", array($profile->getGroupId()->toString()));
        $query->execute();
    }

    public static function update_group_profile(GroupProfile $profile)
    {
        $query = Database::generate_query("group_update", array(
                $profile->getGroupId()->toString(),
                $profile->getDisplayName(),
                $profile->getName()
            ));
        $query->execute();
    }

    public static function group_exists($lookup)
    {
        // Check if it is a user id
        if (Token::verify($lookup)) {
            $query = Database::generate_query("group_lookup_id", array($lookup));
            // Else assume a display name
        } else {
            $query = Database::generate_query("group_lookup_name", array($lookup));
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

            $query = Database::generate_query("group_lookup_id", array($groupid->toString()));
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
            $query = Database::generate_query("group_lookup_name", array($name));
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
        while ($row = $result->fetch_data()) {
            $groups[] = new GroupProfile(Token::decode($row["group_id"]), $row["group_name"], $row["group_display_name"]);
        }
        $result->close();

        return $groups;
    }

    public static function fetch_group_users(GroupProfile $group)
    {
        $query = Database::generate_query("group_user_list", array($group->getGroupId()->toString()));
        $result = $query->execute();
        $groups = array();

        while ($row = $result->fetch_data()) {
            $groups[] = new UserProfile(Token::decode($row["user_id"]), $row["user_name"], $row["user_display_name"]);
        }

        return $groups;
    }

    public static function fetch_group_settings(GroupProfile $profile)
    {
        $query = Database::generate_query("group_settings_fetch", array($profile->getGroupId()->toString()));
        $result = $query->execute();

        $settings = array();
        while ($row = $result->fetch_data()) {
            $settings[$row["setting_key"]] = $row["setting_value"];
        }

        $result->close();

        return $settings;
    }

    public static function set_group_setting(GroupProfile $profile, $setting)
    {
        if (array_key_exists($setting->{"key"}, GroupBackend::fetch_group_settings($profile))) {
            $query = Database::generate_query("group_setting_update", array(
                    $profile->getGroupId()->toString(),
                    $setting->{"key"},
                    $setting->{"value"}
                ));
        } else {
            $query = Database::generate_query("group_setting_set", array(
                    $profile->getGroupId()->toString(),
                    $setting->{"key"},
                    $setting->{"value"}
                ));
        }

        $query->execute();
    }

    public static function delete_group_setting(GroupProfile $profile, $setting)
    {
        $query = Database::generate_query("group_setting_delete", array($profile->getGroupId()->toString(), $setting));
        $query->execute();
    }

    public static function fetch_group_setting(GroupProfile $profile, $setting)
    {
        $query = Database::generate_query("group_setting_lookup", array($profile->getGroupId()->toString(), $setting));
        $result = $query->execute();
        $value = $result->fetch_data()["setting_value"];
        $result->close();

        return $value;
    }

    public static function fetch_group_permissions(GroupProfile $profile)
    {
        $query = Database::generate_query("group_permission_fetch", array($profile->getGroupId()->toString()));
        $result = $query->execute();

        $settings = array();
        while ($row = $result->fetch_data()) {
            $settings[] = $row["permission_key"];
        }

        $result->close();

        return $settings;
    }

    public static function set_group_permission(GroupProfile $profile, $permission, $value)
    {
        if ($value) {
            $query = Database::generate_query("group_permission_add", array(
                    $profile->getGroupId()->toString(),
                    $permission
                ));
        } else {
            $query = Database::generate_query("group_permission_remove", array(
                    $profile->getGroupId()->toString(),
                    $permission
                ));
        }

        $query->execute();
    }

    public static function check_group_permission(GroupProfile $profile, $permission)
    {
        $query = Database::generate_query("group_permission_check", array(
                $profile->getGroupId()->toString(),
                $permission
            ));
        $result = $query->execute();
        $count = $result->count();
        $result->close();

        return $count >= 1;
    }
}