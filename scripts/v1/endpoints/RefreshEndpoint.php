<?php

class RefreshEndpoint extends Endpoint
{

    public function handle($body)
    {
        $data = json_decode($body);

        if (!isset($data->{"user-id"}) || !isset($data->{"client-id"}) || !isset($data->{"refresh-token"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $userid = Token::decode($data->{"user-id"});
        $clientid = Token::decode($data->{"client-id"});
        $refresh = Token::decode($data->{"refresh-token"});

        $query = "SELECT count(`id`) AS count FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $refresh->toString() . "' AND `user`='" . $userid->toString() . "' AND `client-id`='" . $clientid->toString() . "'";
        $result = Database::query($query);
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);

        if ($count == 0) {
            throw new InvalidTokenException("Invalid refresh token or userid provided");
        }

        $access = Token::generateToken(TOKEN_ACCESS, $userid->getUserSecret());

        $query = "DELETE FROM " . DATABASE_TABLE_TOKENS . " WHERE `token` LIKE 'AC-%' AND `client-id`='" . $clientid->toString() . "' AND `user`='" . $userid->toString() . "';";
        Database::query($query);

        $query = "INSERT INTO " . DATABASE_TABLE_TOKENS . " (`token`, `client-id`, `user`, `expires`) VALUES ('" . $access->toString() . "','" . $clientid->toString() . "','" . $userid->toString() . "', NOW() + INTERVAL 1 HOUR);";
        Database::query($query);

        return array("client-id" => $clientid->toString(),
            "user-profile" => array("user-id" => $userid->toString(), "display-name" => ""),
            "access-token" => array("token" => $access->toString(), "expires" => 3600));
    }
}