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
        if ($this->pretty) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        } else {
            echo json_encode($data);
        }
    }
}