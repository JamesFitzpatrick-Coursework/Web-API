<?php

class InvalidateEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (!isset($data->{"user-id"}) || !isset($data->{"client-id"}) || !isset($data->{"token"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $clientid = Token::decode($data->{"client-id"});
        $token = Token::decode($data->{"token"});

        Backend::invalidate_token($clientid, $token);

        return array();
    }
}