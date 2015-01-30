<?php
namespace meteor\endpoints;

use meteor\data\GroupProfile;
use meteor\data\UserProfile;
use meteor\database\Backend;

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
        $profile = Backend::fetch_user_profile($this->params["id"]);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();
        $data["settings"] = Backend::fetch_user_settings($profile);
        $data["permissions"] = Backend::fetch_user_permissions($profile);

        $groups = array();
        /** @var GroupProfile $group */
        foreach (Backend::fetch_user_groups($profile) as $group) {
            $groups[] = $group->toExternalForm();
        }
        $data["groups"] = $groups;

        return $data;
    }

    private function handle_patch($data)
    {
        $profile = Backend::fetch_user_profile($this->params['id']);

        $displayname = $profile->getDisplayName();
        $username = $profile->getUsername();

        if (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
        }

        if (isset($data->{"user-name"})) {
            $username = $data->{"user-name"};
        }

        $profile = new UserProfile($profile->getUserId(), $username, $displayname);
        Backend::update_user_profile($profile);
        return $this->handle_get($data);
    }

    public function handle_delete($data)
    {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        Backend::delete_user($profile);
        return array();
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE", "POST");
    }
}