SELECT
  `{table.assessment.answers}`.`question_id`,
  `{table.assessment.answers}`.`answer_value`,
  `{table.assessment.questions}`.`question_data`

FROM `{table.assessment.answers}`
LEFT JOIN `{table.assessment.questions}`
    ON `{table.assessment.answers}`.`question_id` = `{table.assessment.questions}`.`question_id`

WHERE `{table.assessment.questions}`.`assessment_id` = '{0}'