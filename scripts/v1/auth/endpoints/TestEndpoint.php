<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use phpseclib\Crypt\RSA;

class TestEndpoint extends Endpoint
{
    public function handle($data)
    {
        $rsa = new RSA();
        $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_XML);
        $rsa->setPublicKeyFormat(RSA::PRIVATE_FORMAT_XML);

        return array ($rsa->createKey());
    }
}