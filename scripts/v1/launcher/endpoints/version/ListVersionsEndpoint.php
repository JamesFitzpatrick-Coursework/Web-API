<?php
namespace launcher\endpoints\version;

use common\core\Endpoint;

class ListVersionsEndpoint extends Endpoint
{
    const REPO_URL = "http://maven.thefishlive.co.uk/repo/uk/co/thefishlive/bs-maths/";
    const BASE_URL = "http://launcher.thefishlive.co.uk/v1/versions/";

    public function handle($data)
    {
        $metadata = simplexml_load_file(self::REPO_URL . "maven-metadata.xml");

        $data = array ();
        $versions = $metadata->versioning->versions->version;

        foreach ($versions as $version) {
            $versionName = (string) $version;
            $data["versions"][$versionName] = array ();
            $data["versions"][$versionName]["type"] = \launcher\ends_with($versionName, "-SNAPSHOT") ? "DEVELOPMENT" : "RELEASE";
            $data["versions"][$versionName]["link"] = self::BASE_URL . $versionName . "/";
        }

        if (isset($metadata->versioning->release)) {
            $latest = (string) $metadata->versioning->release;

            $data["latest"] = array ();
            $data["latest"]["version"] = $latest;
            $data["latest"]["type"] = \launcher\ends_with((string) $latest, "-SNAPSHOT") ? "DEVELOPMENT" : "RELEASE";
            $data["latest"]["link"] = self::BASE_URL . (string) $latest . "/";
        }


        return $data;
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}