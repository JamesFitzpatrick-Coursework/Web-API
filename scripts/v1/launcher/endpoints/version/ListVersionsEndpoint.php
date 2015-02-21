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

            $data["versions"][$versionName] = [
                "name" => $versionName,
                "type" => \launcher\ends_with($versionName, "-SNAPSHOT") ? "DEVELOPMENT" : "RELEASE",
                "link" => self::BASE_URL . $versionName . "/",
                "downloads" => [
                    "assets" => self::BASE_URL . $versionName . "/assets/",
                    "download" => self::BASE_URL . $versionName . "/download/",
                    "libraries" => self::BASE_URL . $versionName . "/libraries/",
                ]
            ];
        }


        if (isset($metadata->versioning->release)) {
            $latest = (string) $metadata->versioning->release;

            $data["latest"] = [
                "release" => $latest
            ];
        }


        return $data;
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}