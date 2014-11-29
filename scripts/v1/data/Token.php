<?php

define("TOKEN_CLIENT", "AA");
define("TOKEN_REQUEST", "AB");
define("TOKEN_ACCESS", "AC");
define("TOKEN_REFRESH", "AD");
define("TOKEN_USER", "AE");

class Token
{
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

    public static function decode($token)
    {
        return new Token(substr($token, 0, 2), substr($token, 3, 8), substr($token, 12, 14), substr($token, 27, 8));
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