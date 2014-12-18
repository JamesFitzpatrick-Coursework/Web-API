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
        $result = Database::query("SELECT * FROM " . DATABASE_TABLE_GROUPS);
        $users = array();

        while ($row = Database::fetch_data($result)) {
            $users[] = array(
                "group-id" => $row["id"],
                "display-name" => $row["name"]
            );
        }

        Database::close_query($result);

        return array("count" => count($users), "groups" => $users);
    }
}