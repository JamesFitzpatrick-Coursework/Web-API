<?php
namespace meteor\endpoints\login;

use common\core\Endpoint;
use common\data\Token;
use meteor\database\Backend;
use meteor\database\backend\TokenBackend;
use meteor\exceptions\ValidationFailedException;

class ValidateEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(["user", "token"]);

        $userid = Token::decode($data->{"user"});
        $token = Token::decode($data->{"token"});

        if (!TokenBackend::validate_token($this->clientid, $userid, $token)) {
            throw new ValidationFailedException("Specified token is not valid");
        }

        return [];
    }
}