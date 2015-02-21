<?php
namespace meteor\endpoints\assessments;

use common\data\Token;
use meteor\data\Assessment;
use meteor\data\profiles\AssessmentProfile;
use meteor\database\backend\AssessmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssessmentLookupEndpoint extends AuthenticatedEndpoint
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
        /** @var Assessment $assessment */
        $assessment = AssessmentBackend::fetch_assessment_profile(new AssessmentProfile(Token::decode($this->params['id'])));

        return [
            "assessment" => $assessment->toExternalForm()
        ];
    }

    public function handleDelete($data)
    {
        AssessmentBackend::delete_assessment(new AssessmentProfile(Token::decode($this->params['id'])));

        return [];
    }

    private function handlePost($data)
    {
        return $this->handleGet($data);
    }

    public function get_acceptable_methods()
    {
        return ["GET", "DELETE", "POST"];
    }
}