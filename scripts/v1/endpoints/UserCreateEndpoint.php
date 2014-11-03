<?php
class UserCreateEndpoint extends Endpoint {

	public function handle($body) {
		$data = json_decode($body);
		
		// Check to see if user exists already
		$lookup = Database::query("SELECT count('id') AS `count` FROM " . DATABASE_TABLE_USERS . " WHERE `name`='" . $data->{"username"} . "'");
		
		if ($lookup["count"] >= 1) {
			throw new EndpointExecutionException("User already exists", array("username" => $data->{"username"}));
		}
		
		// Generate a new user id for this user
		$token = Token::generateNewToken(TOKEN_USER);
		
		// Hash their password for storage
		$password = Crypt::hashPassword($data->{"password"}, $token->getUserSecret());
		
		// Add the user to the database
		Database::query("INSERT INTO " . DATABASE_TABLE_USERS . " VALUES 
						('" . $token->toString() . "',
						'" . $data->{"username"} . "',
						'" . $token->getUserSecret() . "',
						'" . $password . "');");
		
		// Return the new user to the client
		return array("userid" => $token->toString(),
						"username" => $data->{"username"},
						"password" => $password
					);
	}
}