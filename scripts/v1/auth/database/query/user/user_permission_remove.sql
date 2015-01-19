DELETE FROM `{table.users.permissions}`
WHERE `user_id` = '{0}' AND `permission_key` = '{1}';