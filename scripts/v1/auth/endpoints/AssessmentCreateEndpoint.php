<?php
namespace meteor\endpoints;

use common\data\Token;
use meteor\data\Assessment;
use meteor\data\QuestionType;
use meteor\database\Backend;
use meteor\database\backend\AssessmentBackend;

class AssessmentCreateEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $id = Token::generateNewToken(TOKEN_ASSESSMENT);

        $name = $data->{"name"};

        $questions = [];
        foreach ($data->{"questions"} as $questionJson) {
            $questionJson = obj_to_array($questionJson);

            $question = [];
            $question["id"] = Token::generateNewToken(TOKEN_QUESTION)->toString();
            $question["type"] = QuestionType::convert_to_string($questionJson["type"]); // TODO delete column in database
            $question["data"] = $questionJson;
            $questions[] = $question;
        }

        /** @var Assessment $assessment */
        $assessment = AssessmentBackend::create_assessment($id, $name, $questions);

        return [
            "assessment" => $assessment->toExternalForm()
        ];
    }
}