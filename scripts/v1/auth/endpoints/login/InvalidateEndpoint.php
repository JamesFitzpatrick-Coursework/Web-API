<?php
namespace meteor\endpoints\login;

use common\core\Endpoint;
use common\data\Token;
use meteor\database\Backend;
use meteor\database\backend\TokenBackend;

class InvalidateEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(array("user", "token"));

        $token = Token::decode($data->{"token"});
        TokenBackend::invalidate_token($this->clientid, $token);

        return array();
    }
}