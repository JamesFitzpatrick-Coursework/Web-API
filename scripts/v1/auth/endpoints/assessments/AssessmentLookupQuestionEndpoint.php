<?php
namespace meteor\endpoints\assessments;

use common\data\Token;
use meteor\database\backend\AssessmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssessmentLookupQuestionEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        if ($this->method == "GET") {
            return $this->handleGet($data);
        } elseif ($this->method == "DELETE") {
            return $this->handleDelete($data);
        } elseif ($this->method == "POST") {
            return $this->handlePost($data);
        }

        return [];
    }

    public function handleGet($data)
    {
        $profile = AssessmentBackend::fetch_assessment_profile(Token::decode($this->params['id']));
        return ["question" => AssessmentBackend::fetch_question($profile, Token::decode($this->params['question']))];
    }

    public function handleDelete($data)
    {
        $profile = AssessmentBackend::fetch_assessment_profile(Token::decode($this->params['id']));
        AssessmentBackend::delete_question($profile, Token::decode($this->params['question']));
        return [];
    }

    private function handlePost($data)
    {
        $this->validate_request(["question"]);

        $profile = AssessmentBackend::fetch_assessment_profile(Token::decode($this->params['id']));

        $questionId = Token::decode($this->params['question']);
        $question = [];
        $question["id"] = $questionId->toString();
        $question["data"] = obj_to_array($data->{"question"});

        AssessmentBackend::update_question($profile, $questionId, $question);
        return $this->handleGet($data);
    }

    public function get_acceptable_methods()
    {
        return ["GET", "DELETE", "POST"];
    }
}