<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use common\data\Token;
use meteor\database\Backend;

class InvalidateEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(array("user", "token"));

        $token = Token::decode($data->{"token"});
        Backend::invalidate_token($this->clientid, $token);

        return array();
    }
}