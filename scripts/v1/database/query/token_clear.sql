DELETE FROM `{table.tokens}` WHERE `token` LIKE '{0}-%' AND `client-id`='{1}' AND `user_id`='{2}';