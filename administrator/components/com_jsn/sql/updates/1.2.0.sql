ALTER TABLE `#__jsn_fields` ADD `edit` tinyint(1) NOT NULL DEFAULT '0';

UPDATE `#__jsn_fields` SET `edit`=1 WHERE `level`=2 AND `params` NOT LIKE '%"hideonedit":"1"%';

UPDATE `#__jsn_fields` SET `alias`='registerdate',`path`='default/registerdate',`edit`='0' WHERE `id`=10;
UPDATE `#__jsn_fields` SET `edit`='0' WHERE `id`=11;
