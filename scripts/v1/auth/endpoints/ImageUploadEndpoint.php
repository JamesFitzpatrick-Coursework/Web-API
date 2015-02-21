<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use meteor\secret\Imgur;

define ("IMGUR_BASE_URL", "https://api.imgur.com/3/");

class ImageUploadEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request(["image", "name"]);

        $image = $data->{"image"};
        $name = $data->{"name"};

        $request = curl_init(IMGUR_BASE_URL . "image/");

        curl_setopt($request, CURLOPT_HTTPHEADER, [
            "Authorization: Client-ID " . Imgur::imgur_api_id()
        ]);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, [
            "image" => $image,
            "name"  => $name
        ]);

        $result = curl_exec($request);
        curl_close($request);

        return ["Raw" => $result];
    }
}