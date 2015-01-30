UPDATE `{table.users}`
SET `user_name` = '{2}', `user_display_name` = '{1}'
WHERE `user_id` = '{0}'
LIMIT 1;