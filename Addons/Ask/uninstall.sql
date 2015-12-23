DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='ask' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='ask' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_ask`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='ask_answer' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='ask_answer' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_ask_answer`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='ask_question' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='ask_question' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_ask_question`;


