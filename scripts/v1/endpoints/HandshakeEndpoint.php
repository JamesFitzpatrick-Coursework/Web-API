<?php

class HandshakeEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (!(isset($data->{"user-id"}) || isset($data->{"display-name"}))) {
            throw new EndpointExecutionException("Invalid request");
        }

        $profile = Backend::fetch_user_profile(isset($data->{"user-id"}) ? $data->{"user-id"} : $data->{"display-name"});

        $clientid = Token::decode($data->{"client-id"});
        $token = Backend::create_token($clientid, $profile->getUserId(), TOKEN_REQUEST, "1 HOUR");

        return array(
            "user" => $profile->toExternalForm(),
            "request-token" => $token->toExternalForm(3600)
        );
    }
}