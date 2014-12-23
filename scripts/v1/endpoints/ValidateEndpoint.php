<?php

class ValidateEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (!isset($data->{"user-id"}) || !isset($data->{"user-id"}) || !isset($data->{"token"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $userid = Token::decode($data->{"user-id"});
        $clientid = Token::decode($data->{"client-id"});
        $token = Token::decode($data->{"token"});

        if (!Backend::validate_token($clientid, $userid, $token)) {
            throw new ValidationFailedException("Specified token is not valid");
        }

        return array();
    }
}