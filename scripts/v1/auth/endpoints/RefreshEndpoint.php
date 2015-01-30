<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\database\Backend;
use meteor\exceptions\InvalidUserException;

class RefreshEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(array("user", "refresh-token"));

        $profile = Backend::fetch_user_profile($data->{"user"});
        $refresh = Token::decode($data->{"refresh-token"});

        if (!$refresh->getUserSecret() == $profile->getUserId()->getUserSecret()) {
            throw new InvalidUserException("User provided and token do not match");
        }

        if (!Backend::validate_token($this->clientid, $profile->getUserId(), $refresh)) {
            throw new InvalidTokenException("Invalid refresh token or userid provided");
        }

        Backend::clear_tokens($this->clientid, $profile->getUserId(), TOKEN_ACCESS);
        $access = Backend::create_token($this->clientid, $profile->getUserId(), TOKEN_ACCESS, "1 HOUR");

        return array(
            "user-profile" => $profile->toExternalForm(),
            "access-token" => array("token" => $access->toString(), "expires" => 3600)
        );
    }
}