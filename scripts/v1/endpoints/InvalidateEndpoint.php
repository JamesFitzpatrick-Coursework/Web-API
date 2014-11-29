<?php

class InvalidateEndpoint extends Endpoint
{

    public function handle($body)
    {
        $data = json_decode($body);

        if (!isset($data->{"user-id"}) || !isset($data->{"client-id"}) || !isset($data->{"token"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $clientid = Token::decode($data->{"client-id"});
        $token = Token::decode($data->{"token"});

        $query = "DELETE FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $token->toString() . "' AND `client-id`='" . $clientid->toString() . "'";
        Database::query($query);

        return array("client-id" => $clientid->toString());
    }
}