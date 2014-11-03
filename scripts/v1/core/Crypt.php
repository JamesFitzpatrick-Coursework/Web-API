<?php
checkEnv();
define ("HASH_ALGORITHM", "sha256");

class Crypt {

	public static function hashPassword($password, $secret) 
	{
		return hash_hmac(HASH_ALGORITHM, $password, $secret);
	}
}