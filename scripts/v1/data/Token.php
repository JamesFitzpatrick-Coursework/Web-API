<?php

$serverSecret = randomHex(8);

function randomHex($len)
{
	return substr(md5(rand()), 0, $len);
}

define("TOKEN_CLIENT", 0xAA);
define("TOKEN_REQUEST", 0xAB);
define("TOKEN_ACCESS", 0xAC);
define("TOKEN_REFRESH", 0xAD);
define("TOKEN_USER", 0xAE);

define ("SERVER_PUBLIC", "4FA851DB");

class Token
{
	private $type;
	private $user;
	private $random;
	private $server;
	
	public static function generateToken($type, $user)
	{
		global $serverSecret;
		
		if (strlen($user) != 8)
		{
			return null;
		}
	
		return new Token(dechex($type), $user, randomHex(14), SERVER_PUBLIC);
	}
	
	public static function generateNewToken($type) 
	{
		return Token::generateToken($type, randomHex(8));
	}
	
	public static function decode($token)
	{
		return new Token(substr($token, 0, 2), substr($token, 3, 8), substr($token, 12, 14), substr($token, 27, 8));
	}
	
	protected function __construct($type, $user, $random, $server)
	{
		$this->type = $type;
		$this->user = $user;
		$this->random = $random;
		$this->server = $server;
	}
	
	public function toString()
	{
		return strtoupper($this->type . "-" . $this->user . "-" . $this->random . "-" . $this->server);
	}
	
	public function getType() 
	{
		return $this->type;
	}
	
	public function getUserSecret() 
	{
		return $this->user;
	}
	
	public function getRandom() 
	{
		return $this->random;
	}
	
	public function getServerPublic() 
	{
		return $this->server;
	}

}