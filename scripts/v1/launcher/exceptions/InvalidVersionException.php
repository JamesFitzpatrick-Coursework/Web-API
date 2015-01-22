<?php
namespace launcher\exceptions;

use common\core\HTTP;
use common\exceptions\EndpointExecutionException;

class InvalidVersionException extends EndpointExecutionException
{
    public function __construct($version)
    {
        parent::__construct("Version specified cannot be found", array ("version" => $version));
    }

    public function get_error_code()
    {
        return HTTP::NOT_FOUND;
    }
} 