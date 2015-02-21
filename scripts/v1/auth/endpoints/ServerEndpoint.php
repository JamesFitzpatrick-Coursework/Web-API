<?php
namespace meteor\endpoints;

use common\core\Endpoint;

class ServerEndpoint extends Endpoint
{
    public function handle($data)
    {
        return [
            "Specification-Name"     => "meteor.auth",
            "Specification-Version"  => "1.0.0",
            "Specification-Vendor"   => "James Fitzpatrick",
            "Implementation-Name"    => "meteor.auth",
            "Implementation-Version" => "1.0.0",
            "Implementation-Vendor"  => "James Fitzpatrick"
        ];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}