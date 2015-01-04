<?php
namespace meteor\exceptions;

class ClassLoadingException extends \Exception
{
    function __construct($error)
    {
        parent::__construct($error);
    }
}