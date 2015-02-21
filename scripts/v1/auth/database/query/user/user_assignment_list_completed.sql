SELECT
  `{table.users.assignments}`.`assignment_id`,
  `{table.users.assignments}`.`assessment_id`,
  `{table.assignment}`.`assignment_deadline`,
  `{table.assessment}`.`assessment_name`,
  `{table.assessment}`.`assessment_display_name`,
  `{table.users.scores}`.`completed` AS `date_completed`,
  SUM(`{table.users.questions.scores}`.`score`) AS `score`

FROM `{table.users.assignments}`
  LEFT JOIN `{table.assignment}`
    ON
      `{table.users.assignments}`.`assignment_id` = `{table.assignment}`.`assignment_id`
  LEFT JOIN `{table.assessment}`
    ON
      `{table.users.assignments}`.`assessment_id` = `{table.assessment}`.`assessment_id`
  LEFT JOIN `{table.users.scores}`
    ON
      `{table.users.assignments}`.`assignment_id` = `{table.users.scores}`.`assignment_id` AND
      `{table.users.assignments}`.`assessment_id` = `{table.users.scores}`.`assessment_id` AND
      `{table.users.scores}`.`user_id` = '{0}'
  LEFT JOIN `{table.users.questions.scores}`
    ON
      `{table.users.assignments}`.`assignment_id` = `{table.users.questions.scores}`.`assignment_id` AND
      `{table.users.scores}`.`score_id` = `{table.users.questions.scores}`.`score_id` AND
      `{table.users.questions.scores}`.`user_id` = '{0}'

WHERE `{table.users.assignments}`.`user_id` = '{0}'

      AND EXISTS(SELECT 1
                     FROM `{table.users.scores}`
                     WHERE `{table.users.scores}`.`user_id` = '{0}'
                           AND `{table.users.scores}`.`assignment_id` = `{table.users.assignments}`.`assignment_id`)