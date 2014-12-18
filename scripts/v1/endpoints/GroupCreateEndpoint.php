<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/12/2014
 * Time: 19:31
 */

class GroupCreateEndpoint extends Endpoint
{
    public function handle($data)
    {
        if (!isset($data->{"client-id"}) || !isset($data->{"group-name"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $clientid = Token::decode($data->{"client-id"});
        $groupname = $data->{"group-name"};

        // Check to see if user exists already
        $result = Database::query("SELECT count('id') AS `count` FROM " . DATABASE_TABLE_GROUPS . " WHERE `name`='" . Database::format_string($groupname) . "'");
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);

        if ($count >= 1) {
            throw new EndpointExecutionException("Group already exists", array("group-name" => $groupname));
        }

        // Generate a new user id for this user
        $token = Token::generateNewToken(TOKEN_GROUP);

        // Add the user to the database
        Database::query("INSERT INTO " . DATABASE_TABLE_GROUPS . " VALUES
						('" . $token->toString() . "',
						'" . Database::format_string($groupname) . "');");

        // Return the new user to the client
        return array(
            "group" => array ("group-id" => $token->toString(), "display-name" => $groupname)
        );

    }
}