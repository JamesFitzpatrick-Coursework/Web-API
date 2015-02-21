<?php
namespace launcher\endpoints\java;

use common\core\Endpoint;

class JavaVersionDownloadEndpoint extends Endpoint
{
    const FILE_FORMAT = ".tar.lzma";

    public function handle($data)
    {
        $path = "../../../assets/java/";
        $path .= $this->params["version"] . "/";
        $path .= $this->params["system"] . "/";
        $path .= $this->params["architecture"] . "/";
        $path .= "jre-" . $this->params["version"] . "-" . $this->params["system"] . "-" . $this->params["architecture"] . self::FILE_FORMAT;

        $this->send_file_download($path);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}