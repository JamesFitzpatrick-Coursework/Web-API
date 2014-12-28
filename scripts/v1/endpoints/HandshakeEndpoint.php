<?php

class HandshakeEndpoint extends Endpoint
{

    public function handle($data)
    {
        $this->validate_request($data, array ("user"));

        $profile = Backend::fetch_user_profile($data->{"user"});

        $clientid = Token::decode($data->{"client-id"});
        $token = Backend::create_token($clientid, $profile->getUserId(), TOKEN_REQUEST, "1 HOUR");

        return array(
            "user" => $profile->toExternalForm(),
            "request-token" => $token->toExternalForm(3600)
        );
    }
}