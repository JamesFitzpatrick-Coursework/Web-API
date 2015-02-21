<?php
namespace launcher\endpoints\java;

use common\core\Endpoint;

class JavaVersionsEndpoint extends Endpoint
{
    const BASE_DIR = "../../../assets/java/";

    public function handle($data)
    {
        $versionFolders = glob($this::BASE_DIR . "*", GLOB_ONLYDIR);

        $versions = [];

        foreach ($versionFolders as $current) {
            $version = [];
            $versionName = substr($current, strrpos($current, DIRECTORY_SEPARATOR) + 1);

            $systemFolders = glob($current . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

            foreach ($systemFolders as $systemFolder) {
                $system = [];
                $sysName = substr($systemFolder, strrpos($systemFolder, DIRECTORY_SEPARATOR) + 1);

                $archFolders = glob($systemFolder . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

                foreach ($archFolders as $archFolder) {
                    $archName = substr($archFolder, strrpos($archFolder, DIRECTORY_SEPARATOR) + 1);
                    $system[$archName] = [
                        "download" => "http://launcher.thefishlive.co.uk/v1/java/$versionName/$sysName/$archName/download/"
                    ];
                }

                $version[$sysName] = $system;
            }

            $versions[] = [
                "version"   => $versionName,
                "downloads" => $version
            ];
        }

        return ["versions" => $versions];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}