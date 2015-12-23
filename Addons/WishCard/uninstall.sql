DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='wish_card' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='wish_card' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_wish_card`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='wish_card_content' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='wish_card_content' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_wish_card_content`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='wish_card_content_cate' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='wish_card_content_cate' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_wish_card_content_cate`;


