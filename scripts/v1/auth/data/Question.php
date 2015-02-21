<?php
namespace meteor\data;

use common\data\Token;

class Question
{
    private $id;

    private $data;

    private $type;

    public function __construct(Token $id, $data, $type)
    {
        $this->id = $id;
        $this->data = $data;
        $this->type = $type;
    }
} 