<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use common\data\Token;
use meteor\database\Backend;
use meteor\exceptions\ValidationFailedException;

class ValidateEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(array("user", "token"));

        $userid = Token::decode($data->{"user"});
        $token = Token::decode($data->{"token"});

        if (!Backend::validate_token($this->clientid, $userid, $token)) {
            throw new ValidationFailedException("Specified token is not valid");
        }

        return array();
    }
}