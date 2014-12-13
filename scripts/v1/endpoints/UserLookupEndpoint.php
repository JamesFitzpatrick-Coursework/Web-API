<?php

class UserLookupEndpoint extends Endpoint
{

    public function handle($body)
    {
        //$data = json_decode($body);

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

}