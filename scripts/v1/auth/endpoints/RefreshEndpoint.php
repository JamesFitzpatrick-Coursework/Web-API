<?php
namespace meteor\endpoints;

use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\database\Backend;
use meteor\exceptions\InvalidUserException;

class RefreshEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_permission("permission.login.refresh.self");
        $this->validate_request(array("user", "refresh-token"));

        $profile = Backend::fetch_user_profile($data->{"user"});
        $refresh = Token::decode($data->{"refresh-token"});

        if (!$this->user->equals($profile) && !$this->validate_permission("permission.login.refresh.others")) {
            throw new InvalidUserException("Authentication provided does not have permission to refresh this token");
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