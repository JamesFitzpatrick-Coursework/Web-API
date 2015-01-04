<?php
namespace meteor\endpoints;

use meteor\core\Endpoint;
use meteor\database\Backend;

class HandshakeEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(array("user"));

        $profile = Backend::fetch_user_profile($data->{"user"});

        $token = Backend::create_token($this->clientid, $profile->getUserId(), TOKEN_REQUEST, "1 HOUR");

        return array(
            "user" => $profile->toExternalForm(),
            "request-token" => $token->toExternalForm(3600)
        );
    }
}