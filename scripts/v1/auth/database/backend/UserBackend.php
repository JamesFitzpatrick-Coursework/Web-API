<?php
namespace meteor\database\backend;

use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\core\Crypt;
use meteor\data\profiles\GroupProfile;
use meteor\data\profiles\UserProfile;
use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\database\Database;
use meteor\exceptions\InvalidAssignmentException;
use meteor\exceptions\InvalidUserException;

class UserBackend {

    public static function create_user($username, $displayname, $password)
    {
        $username = Database::format_string($username);
        $displayname = Database::format_string($displayname);
        $userid = Token::generateNewToken(TOKEN_USER);
        $query = Database::generate_query("user_create", [
                $userid->toString(),
                $username,
                $displayname,
                Crypt::hash_password($password)
            ]);
        $query->execute();

        return new UserProfile($userid, $username, $displayname);
    }

    public static function update_user_profile(UserProfile $profile)
    {
        $query = Database::generate_query("user_update", [
                $profile->getUserId()->toString(),
                $profile->getDisplayName(),
                $profile->getUsername()
            ]);
        $query->execute();
    }

    public static function delete_user(UserProfile $profile)
    {
        $query = Database::generate_query("user_delete", [$profile->getUserId()->toString()]);
        $query->execute();
    }

    public static function user_exists($lookup)
    {
        // Check if it is a user id
        if (Token::verify($lookup)) {
            $query = Database::generate_query("user_lookup_id", [$lookup]);
            // Else assume a display name
        } else {
            $query = Database::generate_query("user_lookup_name", [$lookup]);
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
                throw new InvalidTokenException("Token provided is not a user id", ["user-id" => $userid->toString()]);
            }

            $query = Database::generate_query("user_lookup_id", [$userid->toString()]);
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidUserException("Could not find a user with that id", ["user-id" => $userid->toString()]);
            }

            $row = $result->fetch_data();
            $username = $row["user_name"];
            $displayname = $row["user_display_name"];
            $result->close();
        } else {
            $username = $search;
            $query = Database::generate_query("user_lookup_name", [$username]);
            $result = $query->execute();

            if ($result->count() == 0) {
                throw new InvalidUserException("Could not find a user with that name", ["display-name" => $username]);
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
        $query = Database::generate_query("user_settings_fetch", [$profile->getUserId()->toString()]);
        $result = $query->execute();

        $settings = [];
        while ($row = $result->fetch_data()) {
            $settings[$row["setting_key"]] = $row["setting_value"];
        }

        $result->close();

        return $settings;
    }

    public static function set_user_setting(UserProfile $profile, $setting)
    {
        if (array_key_exists($setting->{"key"}, UserBackend::fetch_user_settings($profile))) {
            $query = Database::generate_query("user_setting_update", [
                    $profile->getUserId()->toString(),
                    $setting->{"key"},
                    $setting->{"value"}
                ]);
        } else {
            $query = Database::generate_query("user_setting_set", [
                    $profile->getUserId()->toString(),
                    $setting->{"key"},
                    $setting->{"value"}
                ]);
        }

        $query->execute();
    }

    public static function delete_user_setting(UserProfile $profile, $setting)
    {
        $query = Database::generate_query("user_setting_delete", [$profile->getUserId()->toString(), $setting]);
        $query->execute();
    }

    public static function fetch_user_setting(UserProfile $profile, $setting)
    {
        $query = Database::generate_query("user_setting_lookup", [$profile->getUserId()->toString(), $setting]);
        $result = $query->execute();
        $value = $result->fetch_data()["setting_value"];
        $result->close();

        return $value;
    }

    public static function fetch_user_permissions(UserProfile $profile)
    {
        $query = Database::generate_query("user_permission_fetch", [$profile->getUserId()->toString()]);
        $result = $query->execute();

        $settings = [];
        while ($row = $result->fetch_data()) {
            $settings[] = $row["permission_key"];
        }

        /** @var GroupProfile $group */
        foreach (UserBackend::fetch_user_groups($profile) as $group) {
            $query = Database::generate_query("group_permission_fetch", [$group->getGroupId()->toString()]);
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
            $query = Database::generate_query("user_permission_add", [
                    $profile->getUserId()->toString(),
                    $permission
                ]);
        } else {
            $query = Database::generate_query("user_permission_remove", [
                    $profile->getUserId()->toString(),
                    $permission
                ]);
        }

        $query->execute();
    }

    public static function check_user_permission(UserProfile $profile, $permission)
    {
        $query = Database::generate_query("user_permission_check", [
                $profile->getUserId()->toString(),
                $permission
            ]);
        $result = $query->execute();
        $count = $result->count();
        $result->close();

        if ($count >= 1) {
            return true;
        }

        foreach (UserBackend::fetch_user_groups($profile) as $group) {
            if (GroupBackend::check_group_permission($group, $permission)) {
                return true;
            }
        }

        return false;
    }

    public static function fetch_all_users()
    {
        $query = Database::generate_query("user_lookup_all");
        $result = $query->execute();

        $users = [];
        while ($row = $result->fetch_data()) {
            $users[] = new UserProfile(Token::decode($row["user_id"]), $row["user_name"], $row["user_display_name"]);
        }

        $result->close();

        return $users;
    }

    public static function validate_user(UserProfile $user, $password)
    {
        $query = Database::generate_query("user_validate", [$user->getUserId()->toString()]);
        $result = $query->execute();
        $hash = $result->fetch_data()["user_password"];
        $result->close();

        return Crypt::check_password($hash, $password);
    }

    public static function fetch_user_groups(UserProfile $user)
    {
        $query = Database::generate_query("user_groups_list", [$user->getUserId()->toString()]);
        $result = $query->execute();
        $groups = [];

        while ($row = $result->fetch_data()) {
            $groups[] = new GroupProfile(Token::decode($row["group_id"]), $row["group_name"], $row["group_display_name"]);
        }

        return $groups;
    }

    public static function add_user_group(UserProfile $user, GroupProfile $group)
    {
        $query = Database::generate_query("user_groups_add", [
                $user->getUserId()->toString(),
                $group->getGroupId()->toString()
            ]);
        $query->execute();
    }

    public static function add_user_assignment(UserProfile $profile, Token $id)
    {
        if (!$id->getType() != TOKEN_ASSIGNMENT)
        {
            throw new InvalidAssignmentException("Assignment id provided is not a valid assignment id");
        }

        $assignment = AssignmentBackend::fetch_assignment_profile($id);
        $query = Database::generate_query("user_assignment_add", [$profile->getUserId()->toString(), $assignment["assignment-id"], $assignment["assessment-id"]]);
        $query->execute();

        return $assignment;
    }

    public static function fetch_user_assignments_all(UserProfile $user)
    {
        $query = Database::generate_query("user_assignment_list_all", [$user->getUserId()->toString()]);
        $result = $query->execute();

        $assignments = [];
        while ($row = $result->fetch_data()) {
            $assignments[] =
                [
                    "assignment" =>
                        [
                            "assignment-id" => $row['assignment_id'],
                            "assignment-deadline" => $row['assignment_deadline'],
                            "assessment" =>
                            [
                                "assessment-id" => $row['assessment_id'],
                                "assessment-name" => $row['assessment_name'],
                                "assessment-display-name" => $row['assessment_display_name']
                            ]
                        ],
                    "completed" => ($row['completed'] == 1 ? true : false),
                    "date-completed" => $row['date_completed']
                ];
        }

        return $assignments;
    }

    public static function fetch_user_assignments_complete(UserProfile $user)
    {
        $query = Database::generate_query("user_assignment_list_completed", [$user->getUserId()->toString()]);
        $result = $query->execute();

        $assignments = [];
        while ($row = $result->fetch_data()) {
            $assignments[] =
                [
                    "assignment" =>
                        [
                            "assignment-id" => $row['assignment_id'],
                            "assignment-deadline" => $row['assignment_deadline'],
                            "assessment" =>
                                [
                                    "assessment-id" => $row['assessment_id'],
                                    "assessment-name" => $row['assessment_name'],
                                    "assessment-display-name" => $row['assessment_display_name']
                                ]
                        ],
                    "date-completed" => $row['date_completed'],
                    "score" => $row['score']
                ];
        }

        return $assignments;
    }

    public static function fetch_user_assignments_outstanding(UserProfile $user)
    {
        $query = Database::generate_query("user_assignment_list_outstanding", [$user->getUserId()->toString()]);
        $result = $query->execute();

        $assignments = [];
        while ($row = $result->fetch_data()) {
            $assignments[] =
                [
                    "assignment" =>
                        [
                            "assignment-id" => $row['assignment_id'],
                            "assignment-deadline" => $row['assignment_deadline'],
                            "assessment" =>
                                [
                                    "assessment-id" => $row['assessment_id'],
                                    "assessment-name" => $row['assessment_name'],
                                    "assessment-display-name" => $row['assessment_display_name']
                                ]
                        ]
                ];
        }

        return $assignments;
    }

}