<?php
namespace meteor\database;

class DatabaseQuery
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function execute()
    {
        return new DatabaseResult(Database::query($this->query));
    }
} 