CREATE TABLE IF NOT EXISTS `wp_youaskservice_behavior` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`fid`  int(11) NULL   COMMENT '',
`token`  varchar(60) NULL   COMMENT '',
`openid`  varchar(60) NULL   COMMENT '',
`date`  varchar(11) NULL   COMMENT '',
`enddate`  int(11) NULL   COMMENT '',
`model`  varchar(60) NULL   COMMENT '',
`num`  int(11) NULL   COMMENT '',
`keyword`  varchar(60) NULL   COMMENT '',
`type`  tinyint(1) NULL   COMMENT '',
PRIMARY KEY (`id`),
KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_behavior','youaskservice_behavior','0','','1','','1:基础','','','','','','20','','','1404033501','1404033501','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fid','','int(11) NULL ','string','','','1','','0','0','1','1404033503','1404033503','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','','varchar(60) NULL ','string','','','1','','0','0','1','1404033503','1404033503','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','','varchar(60) NULL ','string','','','1','','0','0','1','1404033503','1404033503','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('date','','varchar(11) NULL ','string','','','1','','0','0','1','1404033504','1404033504','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('enddate','','int(11) NULL ','string','','','1','','0','0','1','1404033504','1404033504','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('model','','varchar(60) NULL ','string','','','1','','0','0','1','1404033504','1404033504','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('num','','int(11) NULL ','string','','','1','','0','0','1','1404033505','1404033505','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('keyword','','varchar(60) NULL ','string','','','1','','0','0','1','1404033505','1404033505','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','','tinyint(1) NULL ','string','','','1','','0','0','1','1404033505','1404033505','','0','','','','0','');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_group` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`token`  varchar(255) NULL  COMMENT 'token',
`groupname`  varchar(255) NULL  COMMENT '分组名称',
`groupdata`  text NULL  COMMENT '分组数据源',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_group','你问我答-客服分组','0','','1','["groupname"]','1:基础','','','','','id:编号\r\ngroupname:分组名称\r\ntoken:操作:[EDIT]|编辑,[DELETE]|删除','20','groupname','','1404475456','1404491410','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','0','','0','0','1','1404485505','1404475530','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('groupname','分组名称','varchar(255) NULL','string','','','1','','0','0','1','1404475556','1404475556','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('groupdata','分组数据源','text NULL','textarea','','','0','','0','0','1','1404476127','1404476127','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_keyword` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`msgkeyword`  varchar(555) NULL  COMMENT '消息关键字',
`msgkeyword_type`  char(50) NULL  DEFAULT 3 COMMENT '关键字类型',
`msgkfaccount`  varchar(255) NULL  COMMENT '接待的客服人员',
`cTime`  int(10) NULL  COMMENT '创建时间',
`token`  varchar(255) NULL  COMMENT 'token',
`msgstate`  tinyint(2) NULL  DEFAULT 1 COMMENT '关键字状态',
`zjnum`  int(10) NULL  COMMENT '转接次数',
`zdtype`  char(10) NULL  DEFAULT 0 COMMENT '指定类型',
`kfgroupid`  int(10) NULL  DEFAULT 0 COMMENT '客服分组id',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_keyword','你问我答-关键词指配','0','','1','["msgkeyword","msgkeyword_type","zdtype","msgstate"]','1:基础','','','','','id:编号\r\nmsgkeyword:关键字\r\nmsgkeyword_type|get_name_by_status:匹配类型\r\nmsgkfaccount:指定的接待客服或分组\r\nmsgstate|get_name_by_status:状态\r\nzdtype:操作:[EDIT]|编辑,[DELETE]|删除','20','msgkeyword','','1404399143','1404493938','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('msgkeyword','消息关键字','varchar(555) NULL','string','','当用户发送的消息中含有关键字时,将自动转到分配的客服人员','1','','0','0','1','1404399336','1404399336','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('msgkeyword_type','关键字类型','char(50) NULL','select','3','选择关键字匹配的类型','1','0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配','0','0','1','1404399466','1404399466','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('msgkfaccount','接待的客服人员','varchar(255) NULL','string','','','0','','0','0','1','1404403340','1404399587','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','date','','','0','','0','0','1','1404399629','1404399629','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','0','','0','0','1','1404399656','1404399656','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('msgstate','关键字状态','tinyint(2) NULL','bool','1','停用后用户将不会触发此关键词','1','0:停用\r\n1:启用','0','0','1','1404399749','1404399749','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('zjnum','转接次数','int(10) NULL','num','','','0','','0','0','1','1404399784','1404399784','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('zdtype','指定类型','char(10) NULL','radio','0','选择关键字匹配时是按指定人员或者指定客服组','1','0:指定客服人员\r\n1:指定客服组','0','0','1','1404474672','1404474672','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('kfgroupid','客服分组id','int(10) NULL','num','0','','0','','0','0','1','1404474777','1404474777','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_logs` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`pid`  int(11) NULL   COMMENT '',
`openid`  varchar(60) NULL   COMMENT '',
`enddate`  int(11) NULL   COMMENT '',
`keyword`  varchar(200) NULL   COMMENT '',
`status`  tinyint(1) NULL   DEFAULT 2 COMMENT '',
PRIMARY KEY (`id`),
KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_logs','你问我答-聊天记录管理','0','','1','["pid","openid","enddate","keyword","status"]','1:基础','','','','','id:编号\r\nkeyword:回复内容\r\nenddate:回复时间','20','keyword','','1403947270','1404060187','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('pid','','int(11) NULL ','string','','','1','','0','0','1','1403947272','1403947272','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','','varchar(60) NULL ','string','','','1','','0','0','1','1403947273','1403947273','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('enddate','','int(11) NULL ','string','','','1','','0','0','1','1403947273','1403947273','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('keyword','','varchar(200) NULL ','string','','','1','','0','0','1','1403947274','1403947274','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','','tinyint(1) NULL ','string','2','','1','','0','0','1','1403947274','1403947274','','0','','','','0','');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`name`  varchar(60) NULL   COMMENT '客服昵称',
`token`  varchar(60) NULL   COMMENT 'token',
`userName`  varchar(60) NULL   COMMENT '客服帐号',
`userPwd`  varchar(32) NULL   COMMENT '客服密码',
`endJoinDate`  int(11) NULL   COMMENT '客服加入时间',
`status`  tinyint(1) NULL   DEFAULT 0 COMMENT '客服在线状态',
`state`  tinyint(2) NULL  DEFAULT 0 COMMENT '客服状态',
`isdelete`  tinyint(2) NULL  DEFAULT 0 COMMENT '是否删除',
`kfid`  varchar(255) NULL  COMMENT '客服编号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_user','你问我答-客服工号','0','','1','["name","userName","userPwd","state","kfid"]','1:基础','','','','','kfid:编号\r\nname:客服昵称\r\nuserName:客服帐号','20','name','userName','1403947253','1404398415','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','客服昵称','varchar(60) NULL ','string','','','1','','0','0','1','1403959775','1403947255','','0','','regex','','0','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(60) NULL ','string','','','0','','0','0','1','1403959638','1403947256','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('userName','客服帐号','varchar(60) NULL ','string','','','1','','0','0','1','1403959752','1403947256','','3','','regex','','0','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('userPwd','客服密码','varchar(32) NULL ','string','','','1','','0','0','1','1403959722','1403947257','','0','','regex','','0','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('endJoinDate','客服加入时间','int(11) NULL ','string','','','0','','0','0','1','1403959825','1403947257','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','客服在线状态','tinyint(1) NULL ','bool','0','','0','0:离线\r\n1:在线','0','0','1','1404016782','1403947258','','0','','regex','','0','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('state','客服状态','tinyint(2) NULL','bool','0','','1','0:停用\r\n1:启用','0','0','1','1404016877','1404016877','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('isdelete','是否删除','tinyint(2) NULL','bool','0','','0','0:正常\r\n1:已被删除','0','0','1','1404016931','1404016931','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('kfid','客服编号','varchar(255) NULL','string','','','1','','0','0','1','1404398387','1404398387','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_wechat_enddate` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`openid`  varchar(60) NULL   COMMENT '',
`enddate`  int(11) NULL   COMMENT '',
`joinUpDate`  int(11) NULL   DEFAULT 0 COMMENT '',
`uid`  int(11) NULL   DEFAULT 0 COMMENT '',
`token`  varchar(40) NULL   COMMENT '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_wechat_enddate','youaskservice_wechat_enddate','0','','1','','1:基础','','','','','','20','','','1404026714','1404026714','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','','varchar(60) NULL ','string','','','1','','0','0','1','1404026716','1404026716','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('enddate','','int(11) NULL ','string','','','1','','0','0','1','1404026716','1404026716','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('joinUpDate','','int(11) NULL ','string','0','','1','','0','0','1','1404026716','1404026716','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','','int(11) NULL ','string','0','','1','','0','0','1','1404026717','1404026717','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','','varchar(40) NULL ','string','','','1','','0','0','1','1404026717','1404026717','','0','','','','0','');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_wechat_grouplist` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`g_id`  varchar(20) NULL   COMMENT '',
`nickname`  varchar(60) NULL   COMMENT '',
`sex`  tinyint(1) NULL   COMMENT '',
`province`  varchar(20) NULL   COMMENT '',
`city`  varchar(30) NULL   COMMENT '',
`headimgurl`  varchar(200) NULL   COMMENT '',
`subscribe_time`  int(11) NULL   COMMENT '',
`token`  varchar(30) NULL   COMMENT '',
`openid`  varchar(60) NULL   COMMENT '',
`status`  tinyint(1) NULL   COMMENT '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_wechat_grouplist','youaskservice_wechat_grouplist','0','','1','','1:基础','','','','','','20','','','1404027300','1404027300','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('g_id','','varchar(20) NULL ','string','','','1','','0','0','1','1404027302','1404027302','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('nickname','','varchar(60) NULL ','string','','','1','','0','0','1','1404027302','1404027302','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sex','','tinyint(1) NULL ','string','','','1','','0','0','1','1404027303','1404027303','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('province','','varchar(20) NULL ','string','','','1','','0','0','1','1404027303','1404027303','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('city','','varchar(30) NULL ','string','','','1','','0','0','1','1404027303','1404027303','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('headimgurl','','varchar(200) NULL ','string','','','1','','0','0','1','1404027304','1404027304','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('subscribe_time','','int(11) NULL ','string','','','1','','0','0','1','1404027304','1404027304','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','','varchar(30) NULL ','string','','','1','','0','0','1','1404027305','1404027305','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','','varchar(60) NULL ','string','','','1','','0','0','1','1404027305','1404027305','','0','','','','0','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','','tinyint(1) NULL ','string','','','1','','0','0','1','1404027305','1404027305','','0','','','','0','');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_youaskservice_wxlogs` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`opercode`  int(10) NULL  COMMENT '会话状态',
`text`  text NULL  COMMENT '消息',
`time`  int(10) NULL  COMMENT '时间',
`openid`  varchar(255) NULL  COMMENT 'openid',
`token`  varchar(255) NULL  COMMENT 'token',
`worker`  varchar(255) NULL  COMMENT '客服名称',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('youaskservice_wxlogs','你问我答- 微信聊天记录','0','','1','','1:基础','','','','','','20','','','1406094050','1406094093','1','MyISAM','YouaskService');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('opercode','会话状态','int(10) NULL','num','','','1','','0','0','1','1406094322','1406094322','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('text','消息','text NULL','textarea','','','1','','0','0','1','1406094387','1406094387','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time','时间','int(10) NULL','datetime','','','1','','0','0','1','1406094341','1406094341','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','openid','varchar(255) NULL','string','','','1','','0','0','1','1406094276','1406094276','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','0','','0','0','1','1406094177','1406094177','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('worker','客服名称','varchar(255) NULL','string','','','1','','0','0','1','1406094257','1406094257','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


