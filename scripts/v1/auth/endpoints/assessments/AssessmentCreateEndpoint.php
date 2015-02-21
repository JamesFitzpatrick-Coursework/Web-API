<?php
namespace meteor\endpoints\assessments;

use common\data\Token;
use meteor\data\profiles\AssessmentProfile;
use meteor\database\backend\AssessmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssessmentCreateEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request([
            "assessment" =>
                [
                    "profile",
                    "questions"
                ]
        ]);

        $assessment = $data->{"assessment"};
        $profileJson = $assessment->{"profile"};

        if (isset($profileJson->{"assessment-id"})) {
            $id = Token::decode($profileJson->{"assessment-id"});
        } else {
            $id = Token::generateNewToken(TOKEN_ASSESSMENT);
        }

        $name = $profileJson->{"assessment-name"};
        $displayname = isset($profileJson->{"display-name"}) ? $profileJson->{"display-name"} : $name;

        $profile = new AssessmentProfile($id, $name, $displayname);

        $questions = [];
        foreach ($assessment->{"questions"} as $questionData) {
            $questionJson = obj_to_array($questionData);

            if (isset($questionData->{"question-id"})) {
                $questionId = Token::decode($questionData->{"question-id"});
            } else {
                $questionId = Token::generateNewToken(TOKEN_QUESTION);
            }

            $question = [];
            $question["id"] = $questionId->toString();
            $question["data"] = $questionJson;
            $questions[] = $question;
        }

        return [
            "assessment" => AssessmentBackend::create_assessment($profile, $questions)->toExternalForm()
        ];
    }
}