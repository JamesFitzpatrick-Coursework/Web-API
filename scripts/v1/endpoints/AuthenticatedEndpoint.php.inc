<?php

/**
 * Created by PhpStorm.
 * User: James
 * Date: 09/12/2014
 * Time: 20:33
 */
abstract class AuthenticatedEndpoint extends Endpoint
{

    public function execute($body)
    {
        if (DEBUG) {
            return parent::execute($body);
        }

        $data = $body == "" ? array() : json_decode($body);
        $this->validate_request($data, array ("client-id"));

        if (!isset($_SERVER[HEADER_AUTH_USER]) || !isset($_SERVER[HEADER_AUTH_TOKEN])) {
            throw new AuthorizationException("Must provide authentication");
        }

        $user = Backend::fetch_user_profile($_SERVER[HEADER_AUTH_USER]);
        $token = Token::decode($_SERVER[HEADER_AUTH_TOKEN]);
        $clientid = Token::decode($data->{"client-id"});

        // Validate token
        if ($token->getType() != TOKEN_ACCESS) {
            throw new AuthorizationException("Token provided is not a access token");
        }

        if (!Backend::validate_token($clientid, $user->getUserId(), $token)) {
            throw new AuthorizationException("Token provided is not a valid access token");
        }

        $payload = $this->handle($data);
        $payload["client-id"] = $data->{"client-id"};
        return $payload;
    }

} 