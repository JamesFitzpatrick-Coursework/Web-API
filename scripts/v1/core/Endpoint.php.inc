<?php
checkEnv();

abstract class Endpoint
{

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
     *
     * @throws EndpointExecutionException if the endpoint could not finish execution
     * @return array the payload response for this request
     */
    public function execute($body)
    {
        $data = $body == "" ? array() : json_decode($body);

        if (!isset($data->{"client-id"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $payload = $this->handle($data);
        $payload["client-id"] = $data->{"client-id"};
        return $payload;
    }

    public function validate_request($received, array $expected)
    {
        $missing = array();
        $failed = false;

        foreach ($expected as $current)
        {
            if (!isset($received->{$current}))
            {
                $missing[] = $current;
                $failed = true;
            }
        }

        if ($failed) {
            throw new InvalidRequestException($missing);
        }

        return true;
    }
}