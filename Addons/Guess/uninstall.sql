DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='guess' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='guess' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_guess`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='guess_log' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='guess_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_guess_log`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='guess_option' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='guess_option' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_guess_option`;


