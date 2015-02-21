<?php
namespace common\exceptions;

use common\core\HTTP;

class FileNotFoundException extends EndpointExecutionException
{
    public function __construct($file)
    {
        parent::__construct("Could not find file required", ["file" => $file]);
    }

    public function get_error_code()
    {
        return HTTP::NOT_FOUND;
    }
} 