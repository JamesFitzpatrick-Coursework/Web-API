<?php
namespace meteor\endpoints\login;

use common\core\Endpoint;
use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\database\Backend;
use meteor\database\backend\TokenBackend;
use meteor\database\backend\UserBackend;
use meteor\exceptions\InvalidUserException;

class RefreshEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(["user", "refresh-token"]);

        $profile = UserBackend::fetch_user_profile($data->{"user"});
        $refresh = Token::decode($data->{"refresh-token"});

        if (!$refresh->getUserSecret() == $profile->getUserId()->getUserSecret()) {
            throw new InvalidUserException("User provided and token do not match");
        }

        if (!TokenBackend::validate_token($this->clientid, $profile->getUserId(), $refresh)) {
            throw new InvalidTokenException("Invalid refresh token or userid provided");
        }

        TokenBackend::clear_tokens($this->clientid, $profile->getUserId(), TOKEN_ACCESS);
        $access = TokenBackend::create_token($this->clientid, $profile->getUserId(), TOKEN_ACCESS, "1 HOUR");

        return [
            "user-profile" => $profile->toExternalForm(),
            "access-token" => ["token" => $access->toString(), "expires" => 3600]
        ];
    }
}