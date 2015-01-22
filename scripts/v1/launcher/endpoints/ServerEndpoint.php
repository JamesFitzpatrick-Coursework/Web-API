<?php
namespace launcher\endpoints;

use common\core\Endpoint;

class ServerEndpoint extends Endpoint
{
    public function handle($data)
    {
        return array(
            "Specification-Name" => "meteor.launcher",
            "Specification-Version" => "1.0.0",
            "Specification-Vendor" => "James Fitzpatrick",
            "Implementation-Name" => "meteor.launcher",
            "Implementation-Version" => "1.0.0",
            "Implementation-Vendor" => "James Fitzpatrick"
        );
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}