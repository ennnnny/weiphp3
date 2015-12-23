CREATE TABLE IF NOT EXISTS `wp_comment` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`aim_table`  varchar(30) NULL  COMMENT '评论关联数据表',
`aim_id`  int(10) NULL  COMMENT '评论关联ID',
`cTime`  int(10) NULL  COMMENT '评论时间',
`follow_id`  int(10) NULL  COMMENT 'follow_id',
`content`  text NULL  COMMENT '评论内容',
`is_audit`  tinyint(2) NULL  DEFAULT 0 COMMENT '是否审核',
`uid`  int(10) NULL  COMMENT 'uid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('comment','评论互动','0','','1','["is_audit"]','1:基础','','','','','headimgurl|url_img_html:用户头像\r\nnickname|deal_emoji:用户姓名\r\ncontent:评论内容\r\ncTime|time_format:评论时间\r\nis_audit|get_name_by_status:审核状态\r\nids:操作:[DELETE]|删除','20','content:请输入评论内容','','1432602310','1435310857','1','MyISAM','Comment');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('aim_table','评论关联数据表','varchar(30) NULL','string','','','0','','0','0','1','1432602501','1432602501','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('aim_id','评论关联ID','int(10) NULL','num','','','0','','0','0','1','1432602466','1432602466','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','评论时间','int(10) NULL','datetime','','','0','','0','0','1','1432602404','1432602404','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('follow_id','follow_id','int(10) NULL','num','','','0','','0','1','1','1432602345','1432602345','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','评论内容','text NULL','textarea','','','0','','0','1','1','1432602376','1432602376','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_audit','是否审核','tinyint(2) NULL','bool','0','','1','0:未审核\r\n1:已审核','0','0','1','1435031747','1435029949','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','uid','int(10) NULL','num','','','0','','0','0','1','1435561416','1435561392','','3','','regex','get_mid','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


