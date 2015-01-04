SELECT `{table.groups.users}`.group_id, `{table.groups}`.group_name, `{table.groups}`.group_display_name
FROM `{table.groups.users}`
  INNER JOIN `{table.groups}` on `{table.groups.users}`.group_id = `{table.groups}`.group_id
WHERE `{table.groups.users}`.user_id = '{0}';