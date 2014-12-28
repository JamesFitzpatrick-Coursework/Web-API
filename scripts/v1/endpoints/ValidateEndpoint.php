<?php

class ValidateEndpoint extends Endpoint
{

    public function handle($data)
    {
        $this->validate_request($data, array("user", "token"));

        $userid = Token::decode($data->{"user"});
        $clientid = Token::decode($data->{"client-id"});
        $token = Token::decode($data->{"token"});

        if (!Backend::validate_token($clientid, $userid, $token)) {
            throw new ValidationFailedException("Specified token is not valid");
        }

        return array();
    }
}