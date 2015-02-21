<?php
namespace common\core;

use common\data\Token;
use common\exceptions\EndpointExecutionException;
use common\exceptions\FileNotFoundException;
use common\exceptions\InvalidClientException;
use common\exceptions\InvalidRequestException;

abstract class Endpoint
{
    protected $clientid;
    protected $data;
    protected $method;
    protected $params;

    /**
     * Executes this endpoint.
     *
     * @param $body         string the json encoded request body
     * @param array $params parameters captured from the request url
     *
     * @throws EndpointExecutionException
     * @return array the payload response for this request
     */
    public function execute($body, array $params)
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = $params;
        $this->data = $body == "" ? [] : json_decode($body);

        if (!array_key_exists(Headers::CLIENT_ID, $_SERVER)) {
            throw new InvalidClientException();
        }

        $this->clientid = Token::decode($_SERVER[Headers::CLIENT_ID]);

        $payload = $this->handle($this->data);
        $payload["client-id"] = $this->clientid->toString();

        return $payload;
    }

    /**
     * Handles the execution of this endpoint.
     *
     * @param $data array the request data decoded
     *
     * @return array the payload response for this request
     */
    public abstract function handle($data);

    /**
     * Get the acceptable methods to be sent to this request
     *
     * @return array an array of all the acceptable methods for this request
     */
    public function get_acceptable_methods()
    {
        return ["POST"];
    }

    /**
     * Validate that the required parameters have been sent to this request.
     *
     * @param array $expected
     *
     * @throws InvalidRequestException
     * @return bool
     */
    protected function validate_request(array $expected)
    {
        $missing = [];
        $this->validate_array($this->data, $expected, $missing);

        if (count($missing) > 0) {
            throw new InvalidRequestException($missing);
        }

        return true;
    }

    private function validate_array($actual, array $expected, array &$missing)
    {
        foreach ($expected as $key => $current) {
            if (is_array($current)) {
                if (!isset($actual->{$key})) {
                    $missing[] = $current;
                    break;
                }

                $this->validate_array($actual->{$key}, $current, $missing);
                continue;
            }

            if (!isset($actual->{$current})) {
                $missing[] = $current;
            }
        }
    }

    /**
     * Redirect request to a specified url.
     *
     * @param string $destination the new destination url
     */
    protected function redirect($destination)
    {
        http_response_code(HTTP::SEE_OTHER);
        header("Location: " . $destination);
        die();
    }

    /**
     * Send a file to the client.
     *
     * @param $file
     *
     * @throws FileNotFoundException if the specified file could not be read
     */
    protected function send_file_download($file)
    {
        if (!is_readable($file)) {
            throw new FileNotFoundException($file);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        readfile($file);
        exit;
    }
}