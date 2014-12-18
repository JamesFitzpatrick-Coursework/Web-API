<?php

class UserLookupEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (isset($data->{"user-id"}))
        {
            return $this->lookup_user_by_id(Token::decode($data->{"user-id"}));
        }
        else if (isset($data->{"display-name"}))
        {
            return $this->lookup_user_by_name($data->{"display-name"});
        }
        else
        {
            return $this->list_users();
        }
    }

    private function list_users()
    {
        $result = Database::query("SELECT * FROM " . DATABASE_TABLE_USERS);
        $users = array();

        while ($row = Database::fetch_data($result)) {
            $users[] = array(
                "display-name" => $row["name"],
                "user-id" => $row["id"]
            );
        }

        Database::close_query($result);

        return array("count" => count($users), "users" => $users);
    }

    private function lookup_user_by_id(Token $userid)
    {
        // TODO convert to one query
        $query = "SELECT `name` FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $userid->toString() . "';";
        $result = Database::query($query);

        if (Database::count($result) == 0)
        {
            throw new InvalidUserException("User provided is not a valid user");
        }

        $displayname = Database::fetch_data($result)["name"];
        Database::close_query($result);

        $query = "SELECT `key`, `value` FROM " . DATABASE_TABLE_USER_SETTINGS . " WHERE `user_id`='" . $userid->toString() . "';";
        $result = Database::query($query);

        $data = array ();
        $data["profile"] = array (
             "display-name" => $displayname,
             "user-id" => $userid->toString()
        );

        $settings = array();
        while ($row = Database::fetch_data($result)) {
            $settings[] = array(
                "key" => $row["key"],
                "value" => $row["value"]
            );
        }
        $data["settings"] = $settings;
        Database::close_query($result);

        return $data;
    }

    private function lookup_user_by_name($name)
    {
        $query = "SELECT `name`, `id` FROM " . DATABASE_TABLE_USERS . " WHERE `name`='$name';";
        $result = Database::query($query);

        if (Database::count($result) == 0)
        {
            throw new InvalidUserException("User provided is not a valid user");
        }

        $row = Database::fetch_data($result);
        $userid = $row["id"];
        $name = $row["name"];
        Database::close_query($result);

        $query = "SELECT `key`, `value` FROM " . DATABASE_TABLE_USER_SETTINGS . " WHERE `user_id`='$userid';";
        $result = Database::query($query);

        $data = array ();
        $data["profile"] = array (
            "display-name" => $name,
            "user-id" => $userid
        );

        $settings = array();
        while ($row = Database::fetch_data($result)) {
            $settings[] = array(
                "key" => $row["key"],
                "value" => $row["value"]
            );
        }
        $data["settings"] = $settings;
        Database::close_query($result);

        return $data;
    }
}