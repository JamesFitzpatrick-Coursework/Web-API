<?php
namespace meteor\endpoints;

use meteor\data\GroupProfile;
use meteor\data\UserProfile;
use meteor\database\Backend;

class GroupLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        if ($this->method == "DELETE") {
            return $this->handle_delete($data);
        } elseif ($this->method == "POST") {
            return $this->handle_post($data);
        } else {
            return $this->handle_get($data);
        }
    }

    public function handle_get($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();
        $data["settings"] = Backend::fetch_group_settings($profile);
        $data["permissions"] = Backend::fetch_group_permissions($profile);

        $users = array();
        /** @var UserProfile $user */
        foreach (Backend::fetch_group_users($profile) as $user) {
            $users[] = $user->toExternalForm();
        }

        $data["users"] = $users;

        return $data;
    }

    public function handle_delete($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);
        Backend::delete_group($profile);
        return array();
    }

    private function handle_post($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);

        $displayname = $profile->getDisplayName();
        $name = $profile->getName();

        if (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
        }

        if (isset($data->{"group-name"})) {
            $name = $data->{"group-name"};
        }

        $profile = new GroupProfile($profile->getGroupId(), $name, $displayname);
        Backend::update_group_profile($profile);
        return $this->handle_get($data);
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE", "POST");
    }
}