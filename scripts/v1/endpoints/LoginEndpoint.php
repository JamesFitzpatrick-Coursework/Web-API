<?php

class LoginEndpoint extends Endpoint
{

    public function handle($body)
    {
        $data = json_decode($body);

        if (!isset($data->{"user-id"})
            || !isset($data->{"client-id"})
            || !isset($data->{"request-token"})
            || !isset($data->{"password"})
        ) {
            throw new EndpointExecutionException("Invalid request");
        }

        // Check to see if request token is valid
        $clientid = Token::decode($data->{"client-id"});
        $request = Token::decode($data->{"request-token"});
        $userid = Token::decode($data->{"user-id"});

        if ($userid->getType() != TOKEN_USER) {
            throw new InvalidTokenException("User id provided is not a user id");
        }

        if ($request->getType() != TOKEN_REQUEST) {
            throw new InvalidTokenException("Request token provided is not a valid request token");
        }

        if ($userid->getUserSecret() != $request->getUserSecret()) {
            throw new InvalidTokenException("Request token is not for this user");
        }

        $result = Database::query("SELECT `id` FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $request->toString() . "' AND `user`='" . $userid->toString() . "' AND expires > NOW();");

        if (!$result || mysqli_num_rows($result) == 0) {
            throw new InvalidTokenException("Request token is invalid");
        }

        // Check to see if username matches password
        $password = $data->{"password"};

        $result = Database::query("SELECT * FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $userid->toString() . "'");

        if (!$result) {
            throw new EndpointExecutionException("User does not exist", array("user" => $userid->toString()));
        }

        $row = Database::fetch_data($result);

        if (!Crypt::checkPassword($row["password"], $password, $userid->getUserSecret())) {
            throw new EndpointExecutionException("Invalid password for user", array("user" => $userid->toString()));
        }

        $query = "DELETE FROM " . DATABASE_TABLE_TOKENS . " WHERE `token` LIKE '" . TOKEN_ACCESS . "-%' AND `client-id`='" . $clientid->toString() . "' AND `user`='" . $userid->toString() . "';";
        Database::query($query);

        $query = "DELETE FROM " . DATABASE_TABLE_TOKENS . " WHERE `token` LIKE '" . TOKEN_REFRESH . "-%' AND `client-id`='" . $clientid->toString() . "' AND `user`='" . $userid->toString() . "';";
        Database::query($query);

        $accessToken = Token::generateToken(TOKEN_ACCESS, $userid->getUserSecret());
        $refreshToken = Token::generateToken(TOKEN_REFRESH, $userid->getUserSecret());

        Database::query("INSERT INTO " . DATABASE_TABLE_TOKENS . " (`token`, `client-id`, `user`, `expires`) VALUES ('" . $accessToken->toString() . "','" . $clientid->toString() . "', '" . $userid->toString() . "', NOW() + INTERVAL 1 HOUR);");
        Database::query("INSERT INTO " . DATABASE_TABLE_TOKENS . " (`token`, `client-id`, `user`, `expires`) VALUES ('" . $refreshToken->toString() . "','" . $clientid->toString() . "', '" . $userid->toString() . "', NOW() + INTERVAL 1 YEAR);");

        return array("client-id" => $data->{"client-id"},
            "access-token" => array("token" => $accessToken->toString(), "expires" => 3600),
            "refresh-token" => array("token" => $refreshToken->toString(), "expires" => false),
            "profile" => array("user-id" => $userid->toString(), "display-name" => $row["name"]));
    }

}