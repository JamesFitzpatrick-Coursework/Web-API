<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use common\data\Token;
use meteor\data\QuestionType;
use phpseclib\Crypt\RSA;

class TestEndpoint extends Endpoint
{
    public function handle($data)
    {
        $rsa = new RSA();
        $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_XML);
        $rsa->setPublicKeyFormat(RSA::PRIVATE_FORMAT_XML);

        return [
            "assignment"  => Token::generateNewToken(TOKEN_ASSIGNMENT)->toExternalForm(false)
        ];
    }
}