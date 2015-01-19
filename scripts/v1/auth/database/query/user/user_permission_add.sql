INSERT INTO `{table.users.permissions}` (`user_id`, `permission_key`)
  SELECT
    '{0}',
    '{1}'
  FROM dual

  WHERE NOT EXISTS(SELECT 1
                   FROM `{table.users.permissions}`
                   WHERE `permission_key` = '{1}' AND `user_id` = '{0}');