<?php

class DatabaseQuery
{

    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function execute()
    {
        return new DatabaseResult(Database::query($this->query));
    }

} 