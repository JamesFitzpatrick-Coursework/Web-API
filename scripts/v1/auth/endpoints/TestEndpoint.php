<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use meteor\data\QuestionType;
use phpseclib\Crypt\RSA;

class TestEndpoint extends Endpoint
{
    public function handle($data)
    {
        $rsa = new RSA();
        $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_XML);
        $rsa->setPublicKeyFormat(RSA::PRIVATE_FORMAT_XML);

        return array (
            "string" => QuestionType::convert_to_string(QuestionType::MULTI_CHOICE),
            "ordinal" => QuestionType::convert_to_ordinal("MULTI_CHOICE")
        );
    }
}