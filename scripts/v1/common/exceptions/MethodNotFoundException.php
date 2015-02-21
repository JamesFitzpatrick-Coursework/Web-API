<?php
namespace common\exceptions;

use common\core\HTTP;

class MethodNotFoundException extends EndpointExecutionException
{
    public function __construct($request)
    {
        parent::__construct("Method not found", ["request" => $request]);
    }

    public function get_error_code()
    {
        return HTTP::NOT_FOUND;
    }
}