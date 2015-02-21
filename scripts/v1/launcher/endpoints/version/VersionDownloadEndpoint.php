<?php
namespace launcher\endpoints\version;

use common\core\Endpoint;
use launcher\exceptions\InvalidArtifactException;

class VersionDownloadEndpoint extends Endpoint
{
    const REPO_URL = "../../../../maven/repo/uk/co/thefishlive/bs-maths/";
    const BASE_URL = "http://launcher.thefishlive.co.uk/v1/versions/";

    private $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function handle($data)
    {
        $link = $this->get_download_link($this->params['version']);

        switch ($this->type) {
            case "jar":
                $this->send_file_download($link);
                break;
            case "assets":
            case "libraries":
                return array ($this->type => json_decode(file_get_contents($link), true));
        }

        throw new InvalidArtifactException("Invalid artifact specified", array ("artifact" => $this->type));
    }

    private function get_download_link($version)
    {
        $version = strtoupper($version);
        if (\launcher\ends_with($version, "-SNAPSHOT")) { // Dev version
            $meta = simplexml_load_file(self::REPO_URL . $version . "/maven-metadata.xml");

            $versions = $meta->versioning->snapshotVersions->snapshotVersion;

            foreach ($versions as $artifact) {
                if ($this->type == "jar" && $artifact->extension == "jar" && !isset($artifact->classifier)) {
                    return self::REPO_URL . "$version/bs-maths-" . $artifact->value . "." . $artifact->extension;
                } elseif ($this->type == "assets" && $artifact->classifier == "assets-index") {
                    return self::REPO_URL . "$version/bs-maths-" . $artifact->value . "-" . $artifact->classifier . "." . $artifact->extension;
                } elseif ($this->type == "libraries" && $artifact->classifier == "libraries") {
                    return self::REPO_URL . "$version/bs-maths-" . $artifact->value . "-" . $artifact->classifier . "." . $artifact->extension;
                }
            }
        } else {
            switch ($this->type) {
                case "jar":
                    return self::REPO_URL . "$version/bs-maths-$version.jar";
                case "assets":
                    return self::REPO_URL . "$version/bs-maths-$version-assets-index.json";
                case "libraries":
                    return self::REPO_URL . "$version/bs-maths-$version-libraries.json";
            }
        }

        return "";
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}