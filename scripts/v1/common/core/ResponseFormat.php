<?php
namespace common\core;

abstract class ResponseFormat
{
    public abstract function render(array $data);

    public abstract function getContentType();

}