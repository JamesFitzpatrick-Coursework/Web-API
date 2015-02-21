<?php
namespace launcher\endpoints\version;

use common\core\Endpoint;

class VersionInfoEndpoint extends Endpoint
{
    const BASE_URL = "http://launcher.thefishlive.co.uk/v1/versions/";

    public function handle($data)
    {
        $version = $this->params['version'];

        $data = [];

        $data["version"] = $version;

        $data["download"] = [];
        $data["download"]["link"] = self::BASE_URL . $version . "/download/";

        $data["libraries"] = [];
        $data["libraries"]["link"] = self::BASE_URL . $version . "/libraries/";

        $data["assets"] = [];
        $data["assets"]["link"] = self::BASE_URL . $version . "/assets/";

        return $data;
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}