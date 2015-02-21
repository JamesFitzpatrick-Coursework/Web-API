<?php
namespace meteor\database\backend;

use common\data\Token;
use meteor\database\Database;

class TokenBackend {

    public static function clear_tokens(Token $clientid, Token $userid, $tokenType)
    {
        $query = Database::generate_query("token_clear", array($tokenType, $clientid->toString(), $userid->toString()));
        $query->execute();
    }

    public static function validate_token(Token $clientid, Token $userid, Token $token)
    {
        $query = Database::generate_query("token_validate", array(
                $token->toString(),
                $clientid->toString(),
                $userid->toString()
            ));
        $result = $query->execute();
        $count = $result->fetch_data()["count"];
        $result->close();

        return $count != 0;
    }

    public static function invalidate_token(Token $clientid, Token $token)
    {
        $query = Database::generate_query("token_invalidate", array($token->toString(), $clientid->toString()));
        $query->execute();
    }

    public static function create_token(Token $clientid, Token $userid, $tokenType, $expires)
    {
        $token = Token::generateToken($tokenType, $userid->getUserSecret());

        $query = Database::generate_query("token_create", array(
                $token->toString(),
                $userid->toString(),
                $clientid->toString(),
                $expires
            ));
        $query->execute();

        return $token;
    }
}