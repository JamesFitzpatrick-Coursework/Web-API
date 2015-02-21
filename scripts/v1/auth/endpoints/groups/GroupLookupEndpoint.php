<?php
namespace meteor\endpoints\groups;

use meteor\data\profiles\GroupProfile;
use meteor\data\profiles\UserProfile;
use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

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

    public function handle_delete($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        GroupBackend::delete_group($profile);

        return [];
    }

    private function handle_post($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);

        $displayname = $profile->getDisplayName();
        $name = $profile->getName();

        if (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
        }

        if (isset($data->{"group-name"})) {
            $name = $data->{"group-name"};
        }

        $profile = new GroupProfile($profile->getGroupId(), $name, $displayname);
        GroupBackend::update_group_profile($profile);

        return $this->handle_get($data);
    }

    public function handle_get($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);

        $data = [];
        $data["profile"] = $profile->toExternalForm();
        $data["settings"] = GroupBackend::fetch_group_settings($profile);
        $data["permissions"] = GroupBackend::fetch_group_permissions($profile);

        $users = [];
        /** @var UserProfile $user */
        foreach (GroupBackend::fetch_group_users($profile) as $user) {
            $users[] = $user->toExternalForm();
        }

        $data["users"] = $users;

        return $data;
    }

    public function get_acceptable_methods()
    {
        return ["GET", "DELETE", "POST"];
    }
}