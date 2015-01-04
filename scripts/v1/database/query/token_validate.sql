SELECT count(`token`) AS `count`
FROM `{table.tokens}`
WHERE `token` = '{0}' AND `client_id` = '{1}' AND `user_id` = '{2}' AND `expires` > NOW();