<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 18/12/2014
 * Time: 20:14
 */

define ("IMGUR_BASE_URL", "https://api.imgur.com/3/");

class ImageUploadEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request($data, array ("image", "name"));

        $image = $data->{"image"};
        $name = $data->{"name"};

        $request = curl_init(IMGUR_BASE_URL . "image/");

        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            "Authorization: Client-ID " . Imgur::imgur_api_id()
        ));
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, array (
           "image" => $image,
           "name" => $name
        ));

        $result = curl_exec($request);
        curl_close($request);

        return array ("Raw" => $result);
    }
}