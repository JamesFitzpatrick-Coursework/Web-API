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
        $users = array();

        foreach (Backend::fetch_all_groups() as $user) {
            $users[] = $user->toExternalForm();
        }

        return array("count" => count($users), "groups" => $users);
    }

    private function lookup_group($lookup)
    {
        $profile = Backend::fetch_group_profile($lookup);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();

        $settings = array();
        foreach (Backend::fetch_group_settings($profile) as $key => $setting) {
            $settings[$key] = $setting;
        }
        $data["settings"] = $settings;

        return $data;
    }
}