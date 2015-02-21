<?php
namespace meteor\database;

class DatabaseResult
{

    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }


    public function count()
    {
        return Database::count($this->result);
    }

    public function fetch_data()
    {
        return Database::fetch_data($this->result);
    }

    public function close()
    {
        return Database::close_query($this->result);
    }
} 