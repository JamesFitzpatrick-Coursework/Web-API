INSERT INTO `{table.groups.permissions}` (`group_id`, `permission_key`)
  SELECT
    '{0}',
    '{1}'
  FROM dual

  WHERE NOT EXISTS(SELECT 1
                   FROM `{table.groups.permissions}`
                   WHERE `permission_key` = '{1}' AND `group_id` = '{0}');