ALTER TABLE `#__jsn_fields` ADD `accessview` int(10) unsigned NOT NULL DEFAULT '0';

UPDATE `#__jsn_fields` SET `accessview`=1;
