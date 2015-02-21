<?php
namespace launcher\endpoints\assets;

use common\core\Endpoint;

class AssetDownloadEndpoint extends Endpoint
{
    const BASE_URL = "../../../../download/assets/data/";

    public function handle($data)
    {
        $hash = strtoupper($this->params["asset"]);
        $this->send_file_download(self::BASE_URL . substr($hash, 0, 1) . "/" . substr($hash, 1, 1) . "/" . $hash . "/" . $hash);
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}