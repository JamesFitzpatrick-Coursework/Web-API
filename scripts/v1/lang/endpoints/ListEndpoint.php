<?php
namespace lang\endpoints;

use common\core\Endpoint;

class ListEndpoint extends Endpoint
{
    const BASE_DIR = "../../../assets/lang/";

    public function handle($data)
    {
        $files = glob(self::BASE_DIR . "*", GLOB_ONLYDIR);
        $langs = array();

        foreach ($files as $file) {
            $langs[] = substr($file, strrpos($file, "/") + 1);
        }

        return array ("languages" => $langs);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}