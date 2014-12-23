<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 19/12/2014
 * Time: 08:59
 */

class ImageViewEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (!isset($data->{"asset"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $asset = $data->{"asset"};

        // get asset url
        $request = curl_init(IMGUR_BASE_URL . "image/" . $asset);

        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            "Authorization: Client-ID " . Imgur::imgur_api_id()
        ));
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($request);
        curl_close($request);

        // Get asset content
        $imgurData = json_decode($result);
        $result = file_get_contents($imgurData->{"data"}->{"link"});

        return array (
            "url" => $imgurData->{"data"}->{"link"},
            "content-type" => $imgurData->{"data"}->{"type"},
            "data" => base64_encode($result)
        );
    }
}