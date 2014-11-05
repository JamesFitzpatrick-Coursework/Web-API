<?php

class LoginEndpoint extends Endpoint {

	public function handle($body) 
	{
		$data = json_decode($body);
		
		if (!isset($data->{"user-id"})
				|| !isset($data->{"client-id"})
				|| !isset($data->{"request-token"})
				|| !isset($data->{"password"})) 
		{
			throw new EndpointExecutionException("Invalid request");
		}
		
		// Check to see if request token is valid
		$request = Token::decode($data->{"request-token"});
		$userid = Token::decode($data->{"user-id"});
		
		$result = Database::query("SELECT `id` FROM " . DATABASE_TABLE_TOKENS . " WHERE `token`='" . $request->toString() . "' AND `user`='" . $userid->toString() . "' AND expires > NOW();");
		
		if (!$result) 
		{
			throw new InvalidTokenException("Request token is invalid");
		}
		
		// Check to see if username matches password
		$password = $data->{"password"};
		
		$result = Database::query("SELECT * FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $userid->toString() . "'");
		
		if (!$result)
		{
			throw new EndpointExecutionException("User does not exist", array ("user" => $userid->toString()));
		}
		
		$row = Database::fetch_data($result);
		
		if (!Crypt::checkPassword($row["password"], $password, $userid->getUserSecret()))
		{
			throw new EndpointExecutionException("Invalid password for user", array ("user" => $userid->toString()));
		}
		
		$accessToken = Token::generateToken(TOKEN_ACCESS, $userid->getUserSecret());
		$refreshToken = Token::generateToken(TOKEN_REFRESH, $userid->getUserSecret());
		
		return array("client-id" => $data->{"client-id"}, "access-token" => $accessToken->toString(), "refresh-token" => $refreshToken->toString(), "profile" => array ("user-id" => $userid->toString(), "displayname" => $row["name"]));
	}
	
}