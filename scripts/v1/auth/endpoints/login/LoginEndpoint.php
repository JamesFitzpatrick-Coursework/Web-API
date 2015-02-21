<?php
namespace meteor\endpoints\login;

use common\core\Endpoint;
use common\data\Token;
use common\exceptions\InvalidTokenException;
use meteor\database\Backend;
use meteor\database\backend\TokenBackend;
use meteor\database\backend\UserBackend;
use meteor\exceptions\AuthenticationException;

class LoginEndpoint extends Endpoint
{

    public function handle($data)
    {
        $this->validate_request(array("user", "request-token", "password"));

        // Check to see if request token is valid
        $request = Token::decode($data->{"request-token"});
        $profile = UserBackend::fetch_user_profile($data->{"user"});

        if ($request->getType() != TOKEN_REQUEST) {
            throw new InvalidTokenException("Request token provided is not a valid request token");
        }

        if (!TokenBackend::validate_token($this->clientid, $profile->getUserId(), $request)) {
            throw new InvalidTokenException("Request token is invalid");
        }

        // Remove used request token
        TokenBackend::invalidate_token($this->clientid, $request);

        // Check to see if username matches password
        $password = $data->{"password"};

        if (!UserBackend::validate_user($profile, $password)) {
            throw new AuthenticationException("Invalid password for user", array("user" => $profile->toExternalForm()));
        }

        // Remove any current login sessions for this user and this client
        TokenBackend::clear_tokens($this->clientid, $profile->getUserId(), TOKEN_ACCESS);
        TokenBackend::clear_tokens($this->clientid, $profile->getUserId(), TOKEN_REFRESH);

        // create the new login session
        $accessToken = TokenBackend::create_token($this->clientid, $profile->getUserId(), TOKEN_ACCESS, "1 HOUR");
        $refreshToken = TokenBackend::create_token($this->clientid, $profile->getUserId(), TOKEN_REFRESH, "1 YEAR");

        return array(
            "access-token" => $accessToken->toExternalForm(3600),
            "refresh-token" => $refreshToken->toExternalForm(false),
            "profile" => $profile->toExternalForm()
        );
    }

}