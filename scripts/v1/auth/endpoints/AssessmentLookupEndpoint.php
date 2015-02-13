<?php
namespace meteor\endpoints;

use common\data\Token;
use meteor\data\Assessment;
use meteor\database\Backend;

class AssessmentLookupEndpoint extends AuthenticatedEndpoint
{

    public function handle($data)
    {
        if ($this->method == "GET") {
            return $this->handleGet($data);
        } else if ($this->method == "DELETE") {
            return $this->handleDelete($data);
        }
    }

    public function handleDelete($data)
    {
        Backend::delete_assessment(Token::decode($this->params['id']));
        return [];
    }

    public function handleGet($data)
    {
        /** @var Assessment $assessment */
        $assessment = Backend::fetch_assessment_profile(Token::decode($this->params['id']));

        return [
            "assessment" => $assessment->toExternalForm()
        ];
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE");
    }
}