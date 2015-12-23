DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='payment_order' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='payment_order' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_payment_order`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='payment_set' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='payment_set' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_payment_set`;


