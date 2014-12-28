<?php

class InvalidateEndpoint extends Endpoint
{

    public function handle($data)
    {
        $this->validate_request($data, array("user-id", "token"));

        $clientid = Token::decode($data->{"client-id"});
        $token = Token::decode($data->{"token"});

        Backend::invalidate_token($clientid, $token);

        return array();
    }
}