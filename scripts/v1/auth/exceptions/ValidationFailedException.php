<?php
namespace meteor\exceptions;

use common\core\HTTP;
use common\exceptions\EndpointExecutionException;

class ValidationFailedException extends EndpointExecutionException
{
    public function __construct($error)
    {
        parent::__construct($error);
    }

    public function get_error_code()
    {
        return HTTP::OK;
    }

} 