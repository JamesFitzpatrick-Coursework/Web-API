<?php
namespace meteor\endpoints\assessments;

use common\data\Token;
use meteor\database\backend\AssessmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssessmentAddQuestionEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(["question"]);

        $assessment = AssessmentBackend::fetch_assessment_profile(Token::decode($this->params['id']));

        $questionJson = obj_to_array($data->{"question"});

        if (isset($data->{"question"}->{"question-id"})) {
            $questionId = Token::decode($data->{"question"}->{"question-id"});
        } else {
            $questionId = Token::generateNewToken(TOKEN_QUESTION);
        }

        $question = [];
        $question["id"] = $questionId->toString();
        $question["data"] = $questionJson;

        AssessmentBackend::add_question($assessment, $question);

        return [];
    }
}