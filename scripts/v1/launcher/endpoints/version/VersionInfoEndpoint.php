<?php
namespace launcher\endpoints\version;

use common\core\Endpoint;

class VersionInfoEndpoint extends Endpoint
{
    const BASE_URL = "http://launcher.thefishlive.co.uk/v1/versions/";

    public function handle($data)
    {
        $version = $this->params['version'];

        $data = array ();

        $data["version"] = $version;

        $data["download"] = array();
        $data["download"]["link"] = self::BASE_URL . $version . "/download/";

        $data["libraries"] = array();
        $data["libraries"]["link"] = self::BASE_URL . $version . "/libraries/";

        $data["assets"] = array();
        $data["assets"]["link"] = self::BASE_URL . $version . "/assets/";

        return $data;
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}