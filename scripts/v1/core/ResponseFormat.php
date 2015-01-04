<?php
namespace meteor\core;
check_env();

abstract class ResponseFormat
{

    public abstract function render(array $data);

    public abstract function getContentType();

}