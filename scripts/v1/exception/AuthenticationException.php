<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 28/12/2014
 * Time: 13:15
 */

class AuthenticationException extends EndpointExecutionException {

    public function __construct($error, array $payload)
    {
        parent::__construct($error, $payload);
    }

    public function getErrorCode()
    {
        return HTTP_UNAUTHORIZED;
    }
}