<?php
namespace meteor\endpoints\users;

use common\data\Token;
use common\exceptions\EndpointExecutionException;
use meteor\data\profiles\AssessmentProfile;
use meteor\data\QuestionType;
use meteor\database\backend\AssessmentBackend;
use meteor\database\backend\AssignmentBackend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserAssignmentCompleteEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request([
            "assignment",
            "answers"
        ]);

        $assignmentId = Token::decode($data->{'assignment'});
        $user = UserBackend::fetch_user_profile($this->params['id']);
        $assignment = AssignmentBackend::fetch_assignment_profile($assignmentId);

        $answers = AssessmentBackend::fetch_assessment_answers(new AssessmentProfile($assignment['assessment-id']));
        $provided = $data->{'answers'};

        $scores = [];

        foreach ($answers as $answer) {
            /** @var Token $question */
            $question = $answer['question-id'];

            $score = [
                "question-id" => $question->toString(),
                "question-number" => $answer['question-number'],
                "max-score" => 1
            ];

            if ($answer['question-type'] == QuestionType::MULTI_CHOICE) {
                $score['score'] = $this->mark_multichoice_question($answer['answer-value'], $provided->{$question->toString()});
            } elseif ($answer['question-type'] == QuestionType::ANSWER) {
                $score['score'] = $this->mark_answer_question($answer['answer-value'], $provided->{$question->toString()});
            }

            $scores[] = $score;
        }

        if (count($scores) != count($answers)) {
            throw new EndpointExecutionException("An error has occurred whilst executing this endpoint");
        }

        UserBackend::add_assignment_scores($user, $assignmentId, $assignment['assessment-id'], $scores);

        return [
            "scores" => $scores
        ];
    }

    public function mark_multichoice_question($answer, $provided)
    {
        return $provided == $answer ? 1 : 0;
    }

    public function mark_answer_question($answer, $provided)
    {
        return 0;
    }
}