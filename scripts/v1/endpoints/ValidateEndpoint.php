<?php

class ValidateEndpoint extends Endpoint
{

    public function handle($data)
    {
        $data = json_decode($body);

        if (!isset($data->{"user-id"}) || !isset($data->{"client-id"}) || !isset($data->{"token"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $clientid = Token::decode($data->{"client-id"});
        $token = Token::decode($data->{"token"});

        $query = "SELECT count(`id`) AS count FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $token->toString() . "' AND `client-id`='" . $clientid->toString() . "' AND `expires` > NOW()";
        $result = Database::query($query);
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);

        if ($count == 0) {
            throw new ValidationFailedException("Specified token is not valid");
        }

        return array();
    }
}