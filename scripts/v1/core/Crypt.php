<?php
checkEnv();
define ("HASH_ALGORITHM", "sha256");

class Crypt
{

    public static function hashPassword($password, $secret)
    {
        return hash_hmac(HASH_ALGORITHM, $password, $secret);
    }

    public static function checkPassword($hash, $password, $secret)
    {
        return $hash == self::hashPassword($password, $secret); // TODO convert to using a safer hash check
    }
}