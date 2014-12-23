<?php

class UserLookupEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (isset($data->{"lookup"}))
        {
            return $this->lookup_user($data->{"lookup"});
        }
        else
        {
            return $this->list_users();
        }
    }

    private function list_users()
    {
        $users = array();

        foreach (Backend::fetch_all_users() as $user) {
            $users[] = $user->toExternalForm();
        }

        return array("count" => count($users), "users" => $users);
    }

    private function lookup_user($lookup)
    {
        $profile = Backend::fetch_user_profile($lookup);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();

        $settings = array();
        foreach (Backend::fetch_user_settings($profile) as $key => $setting) {
            $settings[$key] = $setting;
        }
        $data["settings"] = $settings;

        return $data;
    }
}