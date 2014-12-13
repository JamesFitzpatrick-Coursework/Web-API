<?php
checkEnv();

abstract class Endpoint
{

    /**
     * Handles the execution of this endpoint.
     *
     * @param $body string the json encoded request body
     *
     * @return array the payload response for this request
     */
    public abstract function handle($body);


    /**
     * Executes this endpoint.
     *
     * @param $body string the json encoded request body
     *
     * @return array the payload response for this request
     */
    public function execute($body)
    {
        return $this->handle($body);
    }
}