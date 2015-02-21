<?php
namespace common\exceptions;

use common\core\HTTP;

class MethodNotAllowedException extends EndpointExecutionException
{
    public function __construct($method)
    {
        parent::__construct("Method not allowed", ["method" => $method]);
    }

    public function get_error_code()
    {
        return HTTP::METHOD_NOT_ALLOWED;
    }
}