SELECT `group_name`, `group_display_name`
FROM `{table.groups}`
WHERE `group_id` = '{0}'
LIMIT 1;