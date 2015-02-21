<?php
namespace meteor\endpoints\users;

use meteor\endpoints\AuthenticatedEndpoint;

class UserCompleteAssignment extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(
            [
                "assignment",
                "answers"
            ]);


        // Mark assessment

        // Add scores to database

        // Return score

        return [];
    }
}