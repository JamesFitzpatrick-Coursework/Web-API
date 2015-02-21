<?php
namespace meteor\endpoints\login;

use common\core\Endpoint;
use meteor\database\Backend;
use meteor\database\backend\TokenBackend;
use meteor\database\backend\UserBackend;

class HandshakeEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(array("user"));

        $profile = UserBackend::fetch_user_profile($data->{"user"});

        $token = TokenBackend::create_token($this->clientid, $profile->getUserId(), TOKEN_REQUEST, "1 HOUR");

        return array(
            "user" => $profile->toExternalForm(),
            "request-token" => $token->toExternalForm(3600)
        );
    }
}