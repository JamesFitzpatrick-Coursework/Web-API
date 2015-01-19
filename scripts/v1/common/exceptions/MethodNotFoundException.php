<?php
namespace common\exceptions;

use common\core\HTTP;
use common\exceptions\EndpointExecutionException;

class MethodNotFoundException extends EndpointExecutionException
{
    public function __construct($request)
    {
        parent::__construct("Method not found", array("request" => $request));
    }

    public function get_error_code()
    {
        return HTTP::NOT_FOUND;
    }
}