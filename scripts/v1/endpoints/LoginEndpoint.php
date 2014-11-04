<?php

class LoginEndpoint extends Endpoint {

	public function handle($body) {
		$data = json_decode($body);
		
		if (!isset($data->{"user-id"})
				|| !isset($data->{"client-id"})
				|| !isset($data->{"request-token"})
				|| !isset($data->{"password"})) {
			throw new EndpointExecutionException("Invalid request");
		}
		
		// Check to see if request token is valid
		$request = Token::decode($data->{"request-token"});
		
		// Check to see if username matches password
		$userid = Token::decode($data->{"user-id"});
		$password = $data->{"password"};
		
		
	}
	
}