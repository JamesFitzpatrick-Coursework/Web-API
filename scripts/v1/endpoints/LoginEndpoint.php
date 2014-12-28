<?php

class LoginEndpoint extends Endpoint
{

    public function handle($data)
    {
        $this->validate_request($data, array("user", "request-token", "password"));

        // Check to see if request token is valid
        $clientid = Token::decode($data->{"client-id"});
        $request = Token::decode($data->{"request-token"});
        $profile = Backend::fetch_user_profile($data->{"user"});

        if ($request->getType() != TOKEN_REQUEST) {
            throw new InvalidTokenException("Request token provided is not a valid request token");
        }

        if (Backend::validate_token($clientid, $profile->getUserId(), $request)) {
            throw new InvalidTokenException("Request token is invalid");
        }

        // Check to see if username matches password
        $password = $data->{"password"};

        if (Backend::validate_user($profile, $password)) {
            throw new AuthenticationException("Invalid password for user", array("user" => $profile->toExternalForm()));
        }

        Backend::clear_tokens($clientid, $profile->getUserId(), TOKEN_ACCESS);
        Backend::clear_tokens($clientid, $profile->getUserId(), TOKEN_REFRESH);

        $accessToken = Backend::create_token($clientid, $profile->getUserId(), TOKEN_ACCESS, "1 HOUR");
        $refreshToken = Backend::create_token($clientid, $profile->getUserId(), TOKEN_REFRESH, "1 YEAR");

        return array(
            "access-token" => $accessToken->toExternalForm(3600),
            "refresh-token" => $refreshToken->toExternalForm(false),
            "profile" => $profile->toExternalForm()
        );
    }

}