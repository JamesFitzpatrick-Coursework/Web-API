<?php

class ServerEndpoint extends Endpoint
{

    public function handle($data)
    {
        return array("Specification-Name" => "Meteor",
            "Specification-Version" => "1.0.0",
            "Specification-Vendor" => "James Fitzpatrick",

            "Implementation-Name" => "Meteor",
            "Implementation-Version" => "1.0.0",
            "Implementation-Vendor" => "James Fitzpatrick");
    }

}