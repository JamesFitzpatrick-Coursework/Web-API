<?php

class JsonResponseFormat extends ResponseFormat
{

    private $pretty;

    public function __construct($pretty)
    {
        $this->pretty = $pretty;
    }

    public function getContentType()
    {
        return "application/json";
    }

    public function render(array $data)
    {
        return json_encode($data, $this->pretty ? JSON_PRETTY_PRINT : 0);
    }
}