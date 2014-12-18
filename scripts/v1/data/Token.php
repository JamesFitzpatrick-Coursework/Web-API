<?php

define("TOKEN_CLIENT", "AA");
define("TOKEN_REQUEST", "AB");
define("TOKEN_ACCESS", "AC");
define("TOKEN_REFRESH", "AD");
define("TOKEN_USER", "AE");
define("TOKEN_GROUP", "AF");

class Token
{
    const TOKEN_REGEX = "/^([A-F0-9]{2})-([A-F0-9]{8})-([A-F0-9]{14})-([A-F0-9]{8})$/";

    private $type;
    private $user;
    private $random;
    private $server;

    public static function generateToken($type, $user)
    {
        if (strlen($user) != 8) {
            return null;
        }

        return new Token($type, $user, randomHex(14), randomHex(8));
    }

    public static function generateNewToken($type)
    {
        return Token::generateToken($type, randomHex(8));
    }

    public static function verify($token)
    {
        return preg_match(self::TOKEN_REGEX, $token) == 1;
    }

    public static function decode($token)
    {
        if (preg_match(self::TOKEN_REGEX, $token, $result) != 1) {
            throw new InvalidTokenException("Token (" . $token . ") is not a valid token");
        }

        return new Token($result[1], $result[2], $result[3], $result[4]);
    }

    protected function __construct($type, $user, $random, $server)
    {
        $this->type = strtoupper($type);
        $this->user = strtoupper($user);
        $this->random = strtoupper($random);
        $this->server = strtoupper($server);
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