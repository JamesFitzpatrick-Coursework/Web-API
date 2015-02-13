<?php
namespace meteor\core;

check_env();

class Crypt
{
    const HASH_ALGORITHM = PASSWORD_DEFAULT;
    const HASH_COST = 10;

    public static function hash_password($password)
    {
        $hash = password_hash($password, HASH_ALGORITHM, ["cost" => self::HASH_COST]);
        return $hash;
    }

    public static function check_password($hash, $password)
    {
        return password_verify($password, $hash);
    }
}