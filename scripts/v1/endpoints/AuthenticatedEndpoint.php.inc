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

        if (!isset($data->{"client-id"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        if (!isset($_SERVER[HEADER_AUTH_USER]) || !isset($_SERVER[HEADER_AUTH_TOKEN])) {
            throw new AuthorizationException("Must provide authentication");
        }

        $user = Token::decode($_SERVER[HEADER_AUTH_USER]);
        $token = Token::decode($_SERVER[HEADER_AUTH_TOKEN]);
        $clientid = Token::decode($data->{"client-id"});

        // Validate user
        if ($user->getType() != TOKEN_USER) {
            throw new AuthorizationException("User id provided is not a user id");
        }

        $query = "SELECT `name` FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $user->toString() . "';";
        $result = Database::query($query);

        if (Database::count($result) == 0) {
            throw new AuthorizationException("User id provided is not valid");
        }

        // Validate token
        if ($token->getType() != TOKEN_ACCESS) {
            throw new AuthorizationException("Token provided is not a access token");
        }

        $query = "SELECT count(`id`) AS count FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $token->toString() . "' AND `user`='" . $user->toString() . "' AND `client-id`='" . $clientid->toString() . "' AND `expires` > NOW()";
        $result = Database::query($query);
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);

        if ($count == 0) {
            throw new AuthorizationException("Token provided is not a valid access token");
        }

        $payload = $this->handle($data);
        $payload["client-id"] = $data->{"client-id"};
        return $payload;
    }

} 