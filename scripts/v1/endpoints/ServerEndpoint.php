<?php
namespace meteor\endpoints;

use meteor\core\Endpoint;

class ServerEndpoint extends Endpoint
{
    public function handle($data)
    {
        return array(
            "Specification-Name" => "Meteor",
            "Specification-Version" => "1.0.0",
            "Specification-Vendor" => "James Fitzpatrick",
            "Implementation-Name" => "Meteor",
            "Implementation-Version" => "1.0.0",
            "Implementation-Vendor" => "James Fitzpatrick"
        );
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}