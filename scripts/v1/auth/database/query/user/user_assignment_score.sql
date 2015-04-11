SELECT
  `{table.users.scores}`.`score_id` AS `score_id`,
  `{table.users.scores}`.`completed` AS `date_completed`,
  SUM(`{table.users.question.scores}`.`score`) AS `score`

FROM `{table.users.assignments}`
  LEFT JOIN `{table.users.scores}`
    ON
      `{table.users.assignments}`.`assignment_id` = `{table.users.scores}`.`assignment_id` AND
      `{table.users.assignments}`.`assessment_id` = `{table.users.scores}`.`assessment_id` AND
      `{table.users.scores}`.`user_id` = '{0}'
  LEFT JOIN `{table.users.question.scores}`
    ON
      `{table.users.assignments}`.`assignment_id` = `{table.users.question.scores}`.`assignment_id` AND
      `{table.users.scores}`.`score_id` = `{table.users.question.scores}`.`score_id` AND
      `{table.users.question.scores}`.`user_id` = '{0}'

WHERE `{table.users.assignments}`.`user_id` = '{0}' AND `{table.users.assignments}`.`assignment_id` = '{1}'

      AND EXISTS(SELECT 1
                 FROM `{table.users.scores}`
                 WHERE `{table.users.scores}`.`user_id` = '{0}'
                       AND `{table.users.scores}`.`assignment_id` = `{table.users.assignments}`.`assignment_id`)