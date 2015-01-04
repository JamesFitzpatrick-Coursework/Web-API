<?php
namespace meteor\core;

use meteor\data\Token;
use meteor\exceptions\InvalidClientException;
use meteor\exceptions\InvalidRequestException;
use meteor\exceptions\EndpointExecutionException;

check_env();

abstract class Endpoint
{
    protected $clientid;
    protected $data;
    protected $method;
    protected $params;

    /**
     * Handles the execution of this endpoint.
     *
     * @param $data array the request data decoded
     *
     * @return array the payload response for this request
     */
    public abstract function handle($data);


    /**
     * Executes this endpoint.
     *
     * @param $body string the json encoded request body
     * @param array $params parameters captured from the request url
     *
     * @throws EndpointExecutionException
     * @return array the payload response for this request
     */
    public function execute($body, array $params)
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = $params;
        $this->data = $body == "" ? array() : json_decode($body);

        if (!array_key_exists(Headers::CLIENT_ID, $_SERVER)) {
            throw new InvalidClientException();
        }

        $this->clientid = Token::decode($_SERVER[Headers::CLIENT_ID]);

        $payload = $this->handle($this->data);
        $payload["client-id"] = $this->clientid->toString();
        return $payload;
    }

    /**
     * Get the acceptable methods to be sent to this request
     *
     * @return array an array of all the acceptable methods for this request
     */
    public function get_acceptable_methods()
    {
        return array ("POST");
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
        $missing = array();
        $this->validate_array($this->data, $expected, $missing);

        if (count($missing) > 0) {
            throw new InvalidRequestException($missing);
        }

        return true;
    }

    private function validate_array($actual, array $expected, array &$missing) {
        foreach ($expected as $key => $current) {
            if (is_array ($current)) {
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
}