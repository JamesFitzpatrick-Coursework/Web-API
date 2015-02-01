UPDATE `{table.groups}`
SET `group_name` = '{2}', `group_display_name` = '{1}'
WHERE `group_id` = '{0}'
LIMIT 1;