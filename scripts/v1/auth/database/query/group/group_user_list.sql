SELECT `{table.groups.users}`.user_id, `{table.users}`.user_name, `{table.users}`.user_display_name
FROM `{table.groups.users}`
  INNER JOIN `{table.users}` on `{table.groups.users}`.user_id = `{table.users}`.user_id
WHERE `{table.groups.users}`.group_id = '{0}';