<?php
namespace meteor\endpoints\assignments;

use common\data\Token;
use meteor\data\Assessment;
use meteor\data\profiles\AssessmentProfile;
use meteor\data\QuestionType;
use meteor\database\Backend;
use meteor\database\backend\AssignmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssignmentCreateEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request([
            "assessment",
            "deadline"
        ]);

        $id = Token::generateNewToken(TOKEN_ASSIGNMENT);
        AssignmentBackend::create_assignment($id, Token::decode($data->{"assessment"}), $data->{"deadline"});

        return [
            "assignment-id" => $id->toString(),
            "assessment-id" => $data->{"assessment"},
            "deadline" => $data->{"deadline"}
        ];
    }
}