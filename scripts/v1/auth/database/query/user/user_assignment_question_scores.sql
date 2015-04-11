SELECT `score`
FROM `{table.users.question.scores}`
WHERE `user_id`='{0}' AND `assignment_id`='{1}' AND `score_id`= '{2}';