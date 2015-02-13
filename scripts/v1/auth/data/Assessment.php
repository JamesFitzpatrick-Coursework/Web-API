<?php
namespace meteor\data;

use common\data\Token;

class Assessment
{
    /** @var Token */
    private $id;

    private $name;

    /** @var  array */
    private $questions;

    public function __construct(Token $id, $name, array $questions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->questions = $questions;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function toExternalForm()
    {
        $data = array (
            "id" => $this->id->toString(),
            "name" => $this->name
        );

        $questions = array();
        foreach ($this->questions as $question) {
            $questions[] = $question;
        }

        uasort($questions, array ($this, "sort_questions"));

        foreach ($questions as $question) {
            $data["questions"][] = $question;
        }

        return $data;
    }

    public function sort_questions($a, $b)
    {
        if ($a["question-number"] == $b["question-number"]) {
            return 0;
        }
        return ($a["question-number"] > $b["question-number"]) ? 1 : -1;
    }
} 