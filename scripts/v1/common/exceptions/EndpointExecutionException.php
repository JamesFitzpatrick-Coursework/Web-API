<?php
namespace common\exceptions;

use common\core\HTTP;
use Exception;

class EndpointExecutionException extends Exception
{
    private $data;

    public function __construct($error, $data = [])
    {
        parent::__construct($error);
        $this->data = $data;
    }

    public function get_data()
    {
        return $this->data;
    }

    public function get_error_code()
    {
        return HTTP::BAD_REQUEST;
    }
}