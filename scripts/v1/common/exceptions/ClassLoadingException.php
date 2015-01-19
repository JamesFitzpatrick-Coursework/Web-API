<?php
namespace common\exceptions;

class ClassLoadingException extends \Exception
{
    function __construct($error)
    {
        parent::__construct($error);
    }
}