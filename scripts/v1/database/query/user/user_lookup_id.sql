SELECT `user_name`, `user_display_name`
FROM `{table.users}`
WHERE `user_id` = '{0}'
LIMIT 1;