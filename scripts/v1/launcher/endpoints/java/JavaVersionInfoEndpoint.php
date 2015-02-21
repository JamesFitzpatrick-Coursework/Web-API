<?php
namespace launcher\endpoints\java;

use common\core\Endpoint;
use launcher\exceptions\InvalidVersionException;

class JavaVersionInfoEndpoint extends Endpoint
{
    const BASE_DIR = "../../../assets/java/";

    public function handle($data)
    {
        if (array_key_exists("architecture", $this->params)) {
            return $this->handleArchitectureInfo($data);
        } elseif (array_key_exists("system", $this->params)) {
            return $this->handleSystemInfo($data);
        } else {
            return $this->handleVersionInfo($data);
        }
    }

    private function handleArchitectureInfo($data)
    {
        return [];
    }

    private function handleSystemInfo($data)
    {
        return [];
    }

    private function handleVersionInfo($data)
    {
        if (!is_dir(self::BASE_DIR . $this->params["version"] . "/")) {
            throw new InvalidVersionException($this->params["version"]);
        }

        $version = [];
        $versionName = $this->params["version"];

        $systemFolders = glob(self::BASE_DIR . $versionName . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

        foreach ($systemFolders as $systemFolder) {
            $system = [];
            $sysName = substr($systemFolder, strrpos($systemFolder, DIRECTORY_SEPARATOR) + 1);

            $archFolders = glob($systemFolder . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

            foreach ($archFolders as $archFolder) {
                $archName = substr($archFolder, strrpos($archFolder, DIRECTORY_SEPARATOR) + 1);
                $system[$archName] = [
                    "download" => "http://launcher.thefishlive.co.uk/java/$versionName/$sysName/$archName/download/"
                ];
            }

            $version[$sysName] = $system;
        }

        return [
            "version" => $this->params["version"],
            "systems" => $version
        ];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}