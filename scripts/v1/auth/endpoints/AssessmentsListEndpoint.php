<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class AssessmentsListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        return array (
            "assessments" => Backend::fetch_all_assessments()
        );
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}