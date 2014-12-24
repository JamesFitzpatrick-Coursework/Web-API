<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/12/2014
 * Time: 19:52
 */

class GroupLookupEndpoint extends Endpoint
{
    public function handle($data)
    {
        if (isset($data->{"lookup"}))
        {
            return $this->lookup_group($data->{"lookup"});
        }
        else
        {
            return $this->list_groups();
        }
    }

    private function list_groups()
    {
        $groups = array();

        foreach (Backend::fetch_all_groups() as $group) {
            $groups[] = $group->toExternalForm();
        }

        return array("count" => count($groups), "groups" => $groups);
    }

    private function lookup_group($lookup)
    {
        $profile = Backend::fetch_group_profile($lookup);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();
        $data["settings"] = Backend::fetch_group_settings($profile);
        $data["permissions"] = Backend::fetch_group_permissions($profile);

        return $data;
    }
}