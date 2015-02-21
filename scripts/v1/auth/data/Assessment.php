<?php
namespace meteor\data;

use meteor\data\profiles\AssessmentProfile;

class Assessment
{
    /** @var AssessmentProfile */
    private $profile;

    /** @var  array */
    private $questions;

    public function __construct(AssessmentProfile $profile, array $questions)
    {
        $this->profile = $profile;
        $this->questions = $questions;
    }

    public function getProfile()
    {
        return $this->profile;
    }


    public function getQuestions()
    {
        return $this->questions;
    }

    public function toExternalForm()
    {
        $data = [
            "profile" => $this->profile->toExternalForm()
        ];

        $questions = [];
        foreach ($this->questions as $question) {
            $questions[] = $question;
        }

        uasort($questions, [$this, "sort_questions"]);

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