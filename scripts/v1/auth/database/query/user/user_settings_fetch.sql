SELECT
  `setting_key`,
  `setting_value`
FROM `{table.users.settings}`
WHERE `user_id` = '{0}';