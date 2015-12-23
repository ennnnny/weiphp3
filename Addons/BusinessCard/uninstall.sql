DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='business_card' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='business_card' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_business_card`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='business_card_collect' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='business_card_collect' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_business_card_collect`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='business_card_column' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='business_card_column' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_business_card_column`;


