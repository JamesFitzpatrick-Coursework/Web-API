<?php
include('Crypt/RSA.php');

class HandshakeEndpoint extends Endpoint {

	private $crypt;
	
	public function __construct()
	{
		$crypt = new Crypt_RSA();
	}

	public function handle($body) {
		/*$crypt = new Crypt_RSA();
		extract($crypt->createKey());
		$client = array ("private" => $privatekey, "public" => $publickey);
		
		extract($crypt->createKey());
		$server = array ("private" => $privatekey, "public" => $publickey);*/
		
		$token = Token::generateToken(TOKEN_REQUEST, "93CE9079");
		return array("refresh" => $token->toString(), /*"crypt" => array("private" => $client["private"], "public" => $server["public"])*/);
	}
}