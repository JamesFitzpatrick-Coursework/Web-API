<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 13/12/2014
 * Time: 16:56
 */

class AuthorizationException extends EndpointExecutionException
{
    public function __construct($error)
    {
        parent::__construct($error);
    }

    public function getErrorCode()
    {
        return HTTP_UNAUTHORIZED;
    }
} 