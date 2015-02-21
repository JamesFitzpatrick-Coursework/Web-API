<?php
namespace meteor\endpoints\users;

use meteor\data\profiles\GroupProfile;
use meteor\data\profiles\UserProfile;
use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        if ($this->method == "DELETE") {
            return $this->handle_delete($data);
        } elseif ($this->method == "POST") {
            return $this->handle_patch($data);
        } else {
            return $this->handle_get($data);
        }
    }

    public function handle_get($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params["id"]);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();
        $data["settings"] = UserBackend::fetch_user_settings($profile);
        $data["permissions"] = UserBackend::fetch_user_permissions($profile);

        $groups = array();
        /** @var GroupProfile $group */
        foreach (UserBackend::fetch_user_groups($profile) as $group) {
            $groups[] = $group->toExternalForm();
        }
        $data["groups"] = $groups;

        return $data;
    }

    private function handle_patch($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params['id']);

        $displayname = $profile->getDisplayName();
        $username = $profile->getUsername();

        if (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
        }

        if (isset($data->{"user-name"})) {
            $username = $data->{"user-name"};
        }

        $profile = new UserProfile($profile->getUserId(), $username, $displayname);
        UserBackend::update_user_profile($profile);
        return $this->handle_get($data);
    }

    public function handle_delete($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        UserBackend::delete_user($profile);
        return array();
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE", "POST");
    }
}