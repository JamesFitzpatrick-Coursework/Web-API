<?php
namespace meteor\database\backend;

use common\data\Token;
use meteor\database\Database;

class AssignmentBackend {

    /** Assignment operations */
    public static function fetch_all_assignments()
    {
        $query = Database::generate_query("assignment_lookup_all");
        $result = $query->execute();

        $assignments = array();
        while ($row = $result->fetch_data()) {
            $assignments[] = [
                "assignment-id" => $row['assignment_id'],
                "assessment-id" => $row['assessment_id'],
                "deadline" => $row['assignment_deadline']
            ];
        }

        return $assignments;
    }

    public static function create_assignment(Token $id, Token $assessment, $deadline)
    {
        $query = Database::generate_query("assignment_create", array(
                $id->toString(),
                $assessment->toString(),
                $deadline
            ));
        $query->execute();
    }

    public static function delete_assignment(Token $token)
    {
        $query = Database::generate_query("assignment_delete", array($token->toString()));
        $query->execute();
    }

    public static function fetch_assignment_profile(Token $token)
    {
        $query = Database::generate_query("assignment_lookup_id", array($token->toString()));
        $result = $query->execute();
        $row = $result->fetch_data();

        return [
            "assignment-id" => $token->toString(),
            "assessment-id" => $row['assessment_id'],
            "deadline" => $row['assignment_deadline']
        ];
    }
}