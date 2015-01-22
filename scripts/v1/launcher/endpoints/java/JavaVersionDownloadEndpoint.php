<?php
namespace launcher\endpoints\java;

use common\core\Endpoint;

class JavaVersionDownloadEndpoint extends Endpoint
{
    public function handle($data)
    {
        $path = "../../../assets/java/";
        $path .= $this->params["version"] . "/";
        $path .= $this->params["system"] . "/";
        $path .= $this->params["architecture"] . "/";
        $path .= "jre-" . $this->params["version"] . "-" . $this->params["system"] . "-" . $this->params["architecture"] . ".tar.gz";

        $this->send_file_download($path);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}