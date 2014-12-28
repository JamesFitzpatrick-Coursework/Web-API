<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 28/12/2014
 * Time: 14:04
 */

class InvalidRequestException extends EndpointExecutionException
{
    function __construct($missing)
    {
        parent::__construct("Missing parameters in the request", array ("missing" => $missing));
    }
}