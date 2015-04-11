<?php
namespace meteor\database\backend;

use common\data\Token;
use meteor\data\Assessment;
use meteor\data\profiles\AssessmentProfile;
use meteor\database\Backend;
use meteor\database\Database;
use meteor\exceptions\InvalidAssessmentException;

class AssessmentBackend
{

    /** Assessment Operations */
    public static function fetch_all_assessments()
    {
        $query = Database::generate_query("assessment_lookup_all");
        $result = $query->execute();

        $assessments = [];
        while ($row = $result->fetch_data()) {
            $assessments[] = new AssessmentProfile(
                Token::decode($row["assessment_id"]),
                $row["assessment_name"],
                $row["assessment_display_name"]
            );
        }

        $result->close();

        return $assessments;
    }

    public static function create_assessment(AssessmentProfile $profile, array $questions)
    {
        $query = Database::generate_query("assessment_create", [
            $profile->getAssessmentId()->toString(),
            $profile->getName(),
            $profile->getDisplayName()
        ]);
        $query->execute();

        foreach ($questions as $question) {
            self::add_question($profile, $question);
        }

        return self::fetch_assessment($profile);
    }

    public static function add_question(AssessmentProfile $profile, $question)
    {
        $answer = (string) $question["data"]["answer"];
        unset($question['data']['answer']);

        $query = Database::generate_query("assessment_question_create", [
            $question["id"],
            $profile->getAssessmentId()->toString(),
            json_encode($question["data"])
        ]);
        $query->execute();

        $query = Database::generate_query("assessment_answer_create", [
            Token::generateNewToken(TOKEN_ANSWER)->toString(),
            $question["id"],
            $profile->getAssessmentId()->toString(),
            $answer
        ]);
        $query->execute();
    }

    public static function fetch_assessment_profile(Token $id)
    {
        $query = Database::generate_query("assessment_lookup_id", [$id->toString()]);
        $result = $query->execute();

        if ($result->count() == 0) {
            throw new InvalidAssessmentException($id);
        }

        $data = $result->fetch_data();
        return new AssessmentProfile($id, $data['assessment_name'], $data['assessment_display_name']);
    }

    public static function fetch_question(AssessmentProfile $profile, Token $id)
    {
        $query = Database::generate_query("assessment_question_lookup", [$profile->getAssessmentId()->toString(), $id->toString()]);
        $result = $query->execute();
        $row = $result->fetch_data();

        $json = json_decode($row['question_data'], true);
        $json['question-id'] = $id->toString();
        return $json;
    }

    public static function fetch_assessment(AssessmentProfile $profile)
    {
        $profile = self::fetch_assessment_profile($profile->getAssessmentId());

        $query = Database::generate_query("assessment_lookup_questions", [$profile->getAssessmentId()->toString()]);
        $result = $query->execute();

        $questions = [];
        while ($row = $result->fetch_data()) {
            $json = json_decode($row["question_data"], true);
            $json["question-id"] = $row["question_id"];
            $questions[] = $json;
        }

        return new Assessment($profile, $questions);
    }

    public static function fetch_assessment_answers(AssessmentProfile $profile)
    {
        $query = Database::generate_query("assessment_lookup_answers", [$profile->getAssessmentId()->toString()]);
        $result = $query->execute();

        $questions = [];

        while ($row = $result->fetch_data()) {
            $data = json_decode($row['question_data'], true);

            $questions[] = [
                "question-id" => Token::decode($row['question_id']),
                "question-number" => $data["question-number"],
                "question-type" => $data['type'],
                "answer-value" => $row['answer_value']
            ];
        }

        return $questions;
    }

    public static function update_question(AssessmentProfile $profile, Token $id, array $question)
    {
        unset($question["data"]["question-id"]);
        $answer = (string) $question["data"]["answer"];
        unset($question['data']['answer']);

        $query = Database::generate_query("assessment_question_update", [
            $profile->getAssessmentId()->toString(),
            $question["id"],
            json_encode($question["data"])
        ]);
        $query->execute();

        $query = Database::generate_query("assessment_answer_update", [
            $answer,
            $question["id"],
            $profile->getAssessmentId()->toString()
        ]);
        $query->execute();
    }

    public static function delete_assessment(AssessmentProfile $profile)
    {
        $query = Database::generate_query("assessment_delete", [$profile->getAssessmentId()->toString()]);
        $query->execute();
    }

    public static function delete_question(AssessmentProfile $profile, Token $id)
    {
        $query = Database::generate_query("assessment_question_delete", [$profile->getAssessmentId()->toString(), $id->toString()]);
        $query->execute();
    }
}