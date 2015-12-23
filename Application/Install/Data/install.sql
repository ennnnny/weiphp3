SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wp_action`
-- ----------------------------
DROP TABLE IF EXISTS `wp_action`;
CREATE TABLE `wp_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text COMMENT '行为规则',
  `log` text COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='系统行为表';

-- ----------------------------
-- Records of wp_action
-- ----------------------------
INSERT INTO `wp_action` VALUES ('1', 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了管理中心', '1', '0', '1393685660');
INSERT INTO `wp_action` VALUES ('2', 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', '2', '0', '1380173180');
INSERT INTO `wp_action` VALUES ('3', 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', '2', '0', '1383285646');
INSERT INTO `wp_action` VALUES ('4', 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\r\n表[model]，记录编号[record]。', '2', '0', '1386139726');
INSERT INTO `wp_action` VALUES ('5', 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', '2', '0', '1383285551');
INSERT INTO `wp_action` VALUES ('6', 'update_config', '更新配置', '新增或修改或删除配置', '', '', '1', '1', '1383294988');
INSERT INTO `wp_action` VALUES ('7', 'update_model', '更新模型', '新增或修改模型', '', '', '1', '1', '1383295057');
INSERT INTO `wp_action` VALUES ('8', 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', '1', '1', '1383295963');
INSERT INTO `wp_action` VALUES ('9', 'update_channel', '更新导航', '新增或修改或删除导航', '', '', '1', '1', '1383296301');
INSERT INTO `wp_action` VALUES ('10', 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', '1', '1', '1383296392');
INSERT INTO `wp_action` VALUES ('11', 'update_category', '更新分类', '新增或修改或删除分类', '', '', '1', '1', '1383296765');
INSERT INTO `wp_action` VALUES ('12', 'admin_login', '登录后台', '管理员登录后台', '', '[user|get_nickname]在[time|time_format]登录了后台', '2', '1', '1393685618');

-- ----------------------------
-- Table structure for `wp_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_action_log`;
CREATE TABLE `wp_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='行为日志表';

-- ----------------------------
-- Records of wp_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_addon_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_addon_category`;
CREATE TABLE `wp_addon_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `title` varchar(255) DEFAULT NULL COMMENT '分类名',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='插件分类表';

-- ----------------------------
-- Records of wp_addon_category
-- ----------------------------
INSERT INTO `wp_addon_category` VALUES ('1', null, '奖励功能', '4');
INSERT INTO `wp_addon_category` VALUES ('2', null, '互动功能', '3');
INSERT INTO `wp_addon_category` VALUES ('7', '0', '高级功能', '10');
INSERT INTO `wp_addon_category` VALUES ('4', null, '公众号管理', '20');
INSERT INTO `wp_addon_category` VALUES ('8', '0', '用户管理', '1');

-- ----------------------------
-- Table structure for `wp_addons`
-- ----------------------------
DROP TABLE IF EXISTS `wp_addons`;
CREATE TABLE `wp_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `type` tinyint(1) DEFAULT '0' COMMENT '插件类型 0 普通插件 1 微信插件 2 易信插件',
  `cate_id` int(11) DEFAULT NULL,
  `is_show` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sti` (`status`,`is_show`)
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=utf8 COMMENT='微信插件表';

-- ----------------------------
-- Records of wp_addons
-- ----------------------------
INSERT INTO `wp_addons` VALUES ('160', 'CustomReply', '自定义回复', '支持图文回复、多图文回复、文本回复功能', '1', 'null', '凡星', '0.1', '1448265263', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('161', 'AutoReply', '自动回复', 'WeiPHP基础功能，能实现配置关键词，用户回复此关键词后自动回复对应的文件，图文，图片信息', '1', 'null', '凡星', '0.1', '1448265611', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('162', 'WeiSite', '微官网', '微3G网站、支持分类管理，文章管理、底部导航管理、微信引导信息配置，微网站统计代码部署。同时支持首页多模板切换、信息列表多模板切换、信息详情模板切换、底部导航多模板切换。并配置有详细的模板二次开发教程', '1', '{\"title\":\"\\u70b9\\u51fb\\u8fdb\\u5165\\u9996\\u9875\",\"cover\":\"\",\"info\":\"\",\"background\":\"\",\"code\":\"\",\"template_index\":\"ColorV1\",\"template_footer\":\"V1\",\"template_lists\":\"V1\",\"template_detail\":\"V1\"}', '凡星', '0.1', '1448265667', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('163', 'UserCenter', '微信用户中心', '实现3G首页、微信登录，微信用户绑定，微信用户信息初始化等基本功能', '1', '{\"score\":\"100\",\"experience\":\"100\",\"need_bind\":\"1\",\"bind_start\":\"0\",\"jumpurl\":\"\"}', '凡星', '0.1', '1448265671', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('164', 'Exam', '微考试', '主要功能有试卷管理，题目录入管理，考生信息和考分汇总管理。', '1', 'null', '凡星', '0.1', '1448265686', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('165', 'Draw', '比赛抽奖', '功能主要有奖品设置，抽奖配置和抽奖统计', '1', 'null', '凡星', '0.1', '1448265689', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('166', 'Extensions', '融合第三方', '第三方功能扩展', '1', 'null', '凡星', '0.1', '1448265693', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('167', 'Forms', '通用表单', '管理员可以轻松地增加一个表单用于收集用户的信息，如活动报名、调查反馈、预约填单等', '1', 'null', '凡星', '0.1', '1448265695', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('168', 'DeveloperTool', '开发者工具箱', '开发者可以用来调试，监控运营系统的参数', '1', 'null', '凡星', '0.1', '1448265698', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('190', 'YouaskService', '你问我答客服系统', '一个支持你问我答,关键词制定客服的客服系统', '1', '{\"state\":\"0\",\"zrg\":\"\\u4eba\\u5de5\\u5ba2\\u670d\",\"model\":\"1\",\"tcrg\":\"\\u9000\\u51fa\\u4eba\\u5de5\\u5ba2\\u670d\"}', '陌路生人', '0.1', '1448265818', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('191', 'RealPrize', '实物奖励', '实物奖励设置', '1', 'null', 'aManx', '0.1', '1448265822', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('192', 'Xydzp', '幸运大转盘', '网络上最热门的抽奖活动 支持作弊等各种详细配置', '1', '{\"need_trueljinfo\":\"1\"}', '南方卫视', '0.1', '1448265825', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('193', 'Reserve', '微预约', '微预约是商家利用微营销平台实现在线预约的一种服务，可以运用于汽车、房产、酒店、医疗、餐饮等一系列行业，给用户的出行办事、购物、消费带来了极大的便利！且操作简单， 响应速度非常快，受到业界的一致好评！', '1', 'null', '凡星', '0.1', '1448265828', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('188', 'Scratch', '刮刮卡', '刮刮卡', '1', 'null', '凡星', '0.1', '1448265811', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('189', 'Robot', '机器人聊天', '实现的效果如下\r\n用户输入：“机器人学习时间”\r\n微信回复：“你的问题是？”\r\n用户输入：“这个世界上谁最美？”\r\n微信回复： “你的答案是？”\r\n用户回复： “当然是你啦！”\r\n微信回复：“我明白啊，不信你可以问问我”\r\n用户回复：“这个世界上谁最美？”\r\n微信回复：“当然是你啦！”', '1', 'null', '凡星', '0.1', '1448265814', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('169', 'Coupon', '优惠券', '配合粉丝圈子，打造粉丝互动的运营激励基础', '1', 'null', '凡星', '0.1', '1448265702', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('170', 'Guess', '竞猜', '节目竞猜 有奖竞猜 竞猜项目配置', '1', 'null', '无名', '0.1', '1448265704', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('171', 'Comment', '评论互动', '可放到手机界面里进行评论，显示支持弹屏方式', '1', '{\"min_time\":\"30\",\"limit\":\"15\"}', '凡星', '0.1', '1448265708', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('172', 'Game', '互动游戏', '这是一个临时描述', '1', 'null', '凡星', '0.1', '1448265711', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('173', 'ConfigureAccount', '帐号配置', '配置共众帐号的信息', '0', '{\"title\":\"WeiPHP\\u6447\\u7535\\u89c6\",\"id\":\"gh_dd85ac50d2dd\",\"account\":\"weiphp-tv\",\"type\":\"3\",\"logo\":\"\",\"articleurl\":\"\"}', 'manx', '0.1', '1448265714', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('176', 'Ask', '抢答', '用于电视互动答题', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1448265769', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('177', 'BusinessCard', '微名片', '', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1448265772', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('178', 'HelloWorld', '微信入门案例', '这是一个简单的入门案例', '1', 'null', '凡星', '0.1', '1448265779', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('182', 'Vote', '投票', '支持文本和图片两类的投票功能', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1448265793', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('183', 'Sms', '短信服务', '短信服务，短信验证，短信发送', '1', '{\"random\":\"1\"}', 'jacy', '0.1', '1448265796', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('184', 'Survey', '微调研', '实现通用的调研功能，支持单选、多选和简答三种题目的录入', '1', 'null', '凡星', '0.1', '1448265799', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('185', 'Shop', '商城', '支持后台发布商品 banner管理 前端多模板选择 订单管理等', '1', 'null', '凡星', '0.1', '1448265801', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('187', 'WishCard', '微贺卡', 'Diy贺卡 自定贺卡内容 发给好友 后台编辑', '1', 'null', '凡星', '0.1', '1448265808', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('186', 'Wecome', '欢迎语', '用户关注公众号时发送的欢迎信息，支持文本，图片，图文的信息', '1', '{\"type\":\"1\",\"keyword\":\"\",\"title\":\"\",\"description\":\"\",\"pic_url\":\"\",\"url\":\"\",\"appmsg_id\":\"\"}', '凡星', '0.1', '1448265805', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('179', 'Invite', '微邀约', '微邀约适合各行各业，可用于会议邀约、活动邀约，同时实现微信报名人数自动统计等功能。', '1', 'null', '无名', '0.1', '1448265783', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('180', 'Tongji', '统计监控', '统计实时参与摇电视的次数', '1', 'null', '凡星', '0.1', '1448265787', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('181', 'Test', '微测试', '主要功能有问卷管理，题目录入管理，用户信息和得分汇总管理。', '1', 'null', '凡星', '0.1', '1448265790', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('175', 'CardVouchers', '微信卡券', '在微信平台创建卡券后，可配置到这里生成素材提供用户领取，它既支持电视台自己公众号发布的卡券，也支持由商家公众号发布的卡券', '1', 'null', '凡星', '0.1', '1448265766', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('174', 'Chat', '智能聊天', '通过网络上支持的智能API，实现：天气、翻译、藏头诗、笑话、歌词、计算、域名信息/备案/收录查询、IP查询、手机号码归属、人工智能聊天等功能', '1', '{\"tuling_key\":\"d812d695a5e0df258df952698faca6cc\",\"tuling_url\":\"http:\\/\\/www.tuling123.com\\/openapi\\/api\",\"simsim_key\":\"41250a68-3cb5-43c8-9aa2-d7b3caf519b1\",\"simsim_url\":\"http:\\/\\/sandbox.api.simsimi.com\\/request.p\",\"rand_reply\":\"\\r\\n\\u6211\\u4eca\\u5929\\u7d2f\\u4e86\\uff0c\\u660e\\u5929\\u518d\\u966a\\u4f60\\u804a\\u5929\\u5427\\r\\n\\u54c8\\u54c8~~\\r\\n\\u4f60\\u8bdd\\u597d\\u591a\\u554a\\uff0c\\u4e0d\\u8ddf\\u4f60\\u804a\\u4e86\\r\\n\\u867d\\u7136\\u4e0d\\u61c2\\uff0c\\u4f46\\u89c9\\u5f97\\u4f60\\u8bf4\\u5f97\\u5f88\\u5bf9\"}', '凡星', '0.1', '1448265717', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('159', 'CustomMenu', '自定义菜单', '自定义菜单能够帮助公众号丰富界面，让用户更好更快地理解公众号的功能', '1', 'null', '凡星', '0.1', '1448265257', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('194', 'PublicBind', '一键绑定公众号', '', '1', '{\"random\":\"1\",\"ComponentVerifyTicket\":\"ticket@@@y2ENapQDybpEgf6sORyqZyYMNHmwoA_LJnTHU9EmcZKwjaZeyz4UVaS2GNawZ4ODnESWXut3NPExOZJ0MlgQlg\"}', '凡星', '0.1', '1448265831', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('195', 'Payment', '支付通', '微信支付,财富通,支付宝', '1', '{\"isopen\":\"1\",\"isopenwx\":\"1\",\"isopenzfb\":\"0\",\"isopencftwap\":\"0\",\"isopencft\":\"0\",\"isopenyl\":\"0\",\"isopenload\":\"1\"}', '拉帮姐派(陌路生人)', '0.1', '1448265835', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('196', 'Leaflets', '微信宣传页', '微信公众号二维码推广页面，用作推广或者制作广告易拉宝，可以发布到QQ群微博博客论坛等等...', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1448265839', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('197', 'NoAnswer', '没回答的回复', '当用户提供的内容或者关键词系统无关识别回复时，自动把当前配置的内容回复给用户', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1448265842', '0', '0', null, '1');

-- ----------------------------
-- Table structure for `wp_analysis`
-- ----------------------------
DROP TABLE IF EXISTS `wp_analysis`;
CREATE TABLE `wp_analysis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sports_id` int(10) DEFAULT NULL COMMENT 'sports_id',
  `type` varchar(30) DEFAULT NULL COMMENT 'type',
  `time` varchar(50) DEFAULT NULL COMMENT 'time',
  `total_count` int(10) DEFAULT '0' COMMENT 'total_count',
  `follow_count` int(10) DEFAULT '0' COMMENT 'follow_count',
  `aver_count` int(10) DEFAULT '0' COMMENT 'aver_count',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_analysis
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_article_style`
-- ----------------------------
DROP TABLE IF EXISTS `wp_article_style`;
CREATE TABLE `wp_article_style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_id` int(10) DEFAULT '0' COMMENT '分组样式',
  `style` text COMMENT '样式内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_article_style
-- ----------------------------
INSERT INTO `wp_article_style` VALUES ('1', '1', '<section style=\"border: 0px none; padding: 0px; box-sizing: border-box; margin: 0px; font-family: 微软雅黑;\"><section class=\"main\" style=\"border: none rgb(0,187,236); margin: 0.8em 5% 0.3em; box-sizing: border-box; padding: 0px;\"><section class=\"main2 wxqq-color wxqq-bordertopcolor wxqq-borderleftcolor wxqq-borderrightcolor wxqq-borderbottomcolor\" data-brushtype=\"text\" style=\"color: rgb(0,187,236); font-size: 20px; letter-spacing: 3px; padding: 9px 4px 14px; text-align: center; margin: 0px auto; border: 4px solid rgb(0,187,236); border-top-left-radius: 8px; border-top-right-radius: 8px; border-bottom-right-radius: 8px; border-bottom-left-radius: 8px; box-sizing: border-box;\">理念<span class=\"main3 wxqq-color\" data-brushtype=\"text\" style=\"display: block; font-size: 10px; line-height: 12px; border-color: rgb(0,187,236); color: inherit; box-sizing: border-box; padding: 0px; margin: 0px;\">PHILOSOPHY</span></section><section class=\"main4 wxqq-bordertopcolor wxqq-borderbottomcolor\" style=\"width: 0px; margin-right: auto; margin-left: auto; border-top-width: 0.6em; border-top-style: solid; border-bottom-color: rgb(0,187,236); border-top-color: rgb(0,187,236); height: 10px; color: inherit; border-left-width: 0.7em !important; border-left-style: solid !important; border-left-color: transparent !important; border-right-width: 0.7em !important; border-right-style: solid !important; border-right-color: transparent !important; box-sizing: border-box; padding: 0px;\" data-width=\"0px\"></section></section></section>');
INSERT INTO `wp_article_style` VALUES ('2', '3', '<section label=\"Copyright © 2015 playhudong All Rights Reserved.\" style=\"\r\nmargin:1em auto;\r\npadding: 1em 2em;\r\nborder-style: none;\" id=\"shifu_c_001\"><span style=\"\r\nfloat: left;\r\nmargin-left: 19px;\r\nmargin-top: -9px;\r\noverflow: hidden;\r\ndisplay:block;\"><img style=\"\r\nvertical-align: top;\r\ndisplay:inline-block;\" src=\"http://1251001145.cdn.myqcloud.com/1251001145/style/images/card-3.gif\"><section class=\"color\" style=\"\r\nmin-height: 30px;\r\ncolor: #fff;\r\ndisplay: inline-block;\r\ntext-align: center;\r\nbackground: #999999;\r\nfont-size: 15px;\r\npadding: 7px 5px;\r\nmin-width: 30px;\"><span style=\"font-size:15px;\"> 01 </span></section></span><section style=\"\r\npadding: 16px;\r\npadding-top: 28px;\r\nborder: 2px solid #999999;\r\nwidth: 100%;\r\nfont-size: 14px;\r\nline-height: 1.4;\"><span>星期一天气晴我离开你／不带任何行李／除了一本陪我放逐的日记／今天天晴／心情很低／突然决定离开你</span></section></section>');
INSERT INTO `wp_article_style` VALUES ('3', '1', '<section><section class=\"wxqq-borderleftcolor wxqq-borderRightcolor wxqq-bordertopcolor wxqq-borderbottomcolor\" style=\"border:5px solid #A50003;padding:5px;width:100%;\"><section class=\"wxqq-borderleftcolor wxqq-borderRightcolor wxqq-bordertopcolor wxqq-borderbottomcolor\" style=\"border:1px solid #A50003;padding:15px 20px;\"><p style=\"color:#A50003;text-align:center;border-bottom:1px solid #A50003\"><span class=\"wxqq-color\" data-brushtype=\"text\" style=\"font-size:48px\">情人节快乐</span></p><section data-style=\"color:#A50003;text-align:center;font-size:18px\" style=\"color:#A50003;text-align:center;width:96%;margin-left:5px;\"><p class=\"wxqq-color\" style=\"color:#A50003;text-align:center;font-size:18px\">happy valentine\'s day<span style=\"color:inherit; font-size:24px; line-height:1.6em; text-align:right; text-indent:2em\"></span><span style=\"color:rgb(227, 108, 9); font-size:24px; line-height:1.6em; text-align:right; text-indent:2em\"></span></p><section style=\"width:100%;\"><section><section><p style=\"color:#000;text-align:left;\">我们没有秘密，整天花前月下，别人以为我们不懂爱情，我们乐呵呵地笑大人们都太傻。</p></section></section></section></section></section></section></section>');
INSERT INTO `wp_article_style` VALUES ('4', '4', '<p><img src=\"http://www.wxbj.cn//ys/gz/gx2.gif\"></p>');
INSERT INTO `wp_article_style` VALUES ('5', '5', '<section class=\"tn-Powered-by-XIUMI\" style=\"margin-top: 0.5em; margin-bottom: 0.5em; border: none rgb(142, 201, 101); font-size: 14px; font-family: inherit; font-weight: inherit; text-decoration: inherit; color: rgb(142, 201, 101);\"><img data-src=\"http://mmbiz.qpic.cn/mmbiz/4HiaqFGEibVwaxcmNMU5abRHm7bkZ9icUxC3DrlItWpOnXSjEpZXIeIr2K0923xw43aKw8oibucqm8wkMYZvmibqDkg/0?wx_fmt=png\" class=\"tn-Powered-by-XIUMI\" data-type=\"png\" data-ratio=\"0.8055555555555556\" data-w=\"36\" _width=\"2.6em\" src=\"https://mmbiz.qlogo.cn/mmbiz/4HiaqFGEibVwaxcmNMU5abRHm7bkZ9icUxC3DrlItWpOnXSjEpZXIeIr2K0923xw43aKw8oibucqm8wkMYZvmibqDkg/640?wx_fmt=png\" style=\"float: right; width: 2.6em !important; visibility: visible !important; background-color: rgb(142, 201, 101);\"><section class=\"tn-Powered-by-XIUMI\" style=\"clear: both;\"></section><section class=\"tn-Powered-by-XIUMI\" style=\"padding-right: 10px; padding-left: 10px; text-align: center;\"><section class=\"tn-Powered-by-XIUMI\" style=\"text-align: left;\">炎热的夏季，应该吃点什么好呢！我们为您打造7月盛夏美食狂欢季，清暑解渴的热带水果之王【芒果下午茶】，海鲜盛宴上的【生蚝狂欢】，肉食者的天堂【澳洲之夜】，呼朋唤友，户外聚餐的最佳攻略【夏季BBQ】，消暑瘦身利器【迷你冬瓜盅】，清淡亦或重口味，总有一款是你所爱！</section></section><img data-src=\"http://mmbiz.qpic.cn/mmbiz/4HiaqFGEibVwaxcmNMU5abRHm7bkZ9icUxCkEmrfLmAXYYOXO0q4RGYsQqfzhO6SOdoFCTqYqwlS87ovGrQjCYmWw/0?wx_fmt=png\" class=\"tn-Powered-by-XIUMI\" data-type=\"png\" data-ratio=\"0.8055555555555556\" data-w=\"36\" _width=\"2.6em\" src=\"https://mmbiz.qlogo.cn/mmbiz/4HiaqFGEibVwaxcmNMU5abRHm7bkZ9icUxCkEmrfLmAXYYOXO0q4RGYsQqfzhO6SOdoFCTqYqwlS87ovGrQjCYmWw/640?wx_fmt=png\" style=\"width: 2.6em !important; visibility: visible !important; background-color: rgb(142, 201, 101);\"><p><br></p></section>');
INSERT INTO `wp_article_style` VALUES ('8', '6', '<blockquote class=\"wxqq-borderTopColor wxqq-borderRightColor wxqq-borderBottomColor wxqq-borderLeftColor\" style=\"border: 3px dotted rgb(230, 37, 191); padding: 10px; margin: 10px 0px; font-weight: normal; border-top-left-radius: 5px !important; border-top-right-radius: 5px !important; border-bottom-right-radius: 5px !important; border-bottom-left-radius: 5px !important;\"><h3 style=\"color:rgb(89,89,89);font-size:14px;margin:0;\"><span class=\"wxqq-bg\" style=\"background-color: rgb(230, 37, 191); color: rgb(255, 255, 255); padding: 2px 5px; font-size: 14px; margin-right: 15px; border-top-left-radius: 5px !important; border-top-right-radius: 5px !important; border-bottom-right-radius: 5px !important; border-bottom-left-radius: 5px !important;\">微信编辑器</span>微信号：<span class=\"wxqq-bg\" style=\"background-color: rgb(230, 37, 191); color: rgb(255, 255, 255); padding: 2px 5px; font-size: 14px; border-top-left-radius: 5px !important; border-top-right-radius: 5px !important; border-bottom-right-radius: 5px !important; border-bottom-left-radius: 5px !important;\">wxbj.cn</span></h3><p style=\"margin:10px 0 5px 0;\">微信公众号简介，欢迎使用微信在线图文排版编辑器助手！</p></blockquote>');
INSERT INTO `wp_article_style` VALUES ('9', '8', '<p><img src=\"http://www.wxbj.cn/ys/gz/yw1.gif\"></p>');
INSERT INTO `wp_article_style` VALUES ('7', '7', '<p><img src=\"https://mmbiz.qlogo.cn/mmbiz/cZV2hRpuAPhuxibIOsThcH7HF1lpQ0Yvkvh88U3ia9AbTPJSmriawnJ7W7S5iblSlSianbHLGO6IvD0N4g2y2JEFRoA/0/mmbizgif\"></p>');

-- ----------------------------
-- Table structure for `wp_article_style_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_article_style_group`;
CREATE TABLE `wp_article_style_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_name` varchar(255) DEFAULT NULL COMMENT '分组名称',
  `desc` text COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_article_style_group
-- ----------------------------
INSERT INTO `wp_article_style_group` VALUES ('1', '标题', '标题样式');
INSERT INTO `wp_article_style_group` VALUES ('3', '卡片', '类卡片样式');
INSERT INTO `wp_article_style_group` VALUES ('4', '关注', '引导关注公众号的样式');
INSERT INTO `wp_article_style_group` VALUES ('5', '内容', '内容样式');
INSERT INTO `wp_article_style_group` VALUES ('6', '互推', '互推公众号的样式');
INSERT INTO `wp_article_style_group` VALUES ('7', '分割', '分割样式');
INSERT INTO `wp_article_style_group` VALUES ('8', '原文引导', '原文引导样式');

-- ----------------------------
-- Table structure for `wp_ask`
-- ----------------------------
DROP TABLE IF EXISTS `wp_ask`;
CREATE TABLE `wp_ask` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `finish_tip` text COMMENT '结束语',
  `content` text COMMENT '活动介绍',
  `shop_address` text COMMENT '商家地址',
  `appids` text COMMENT '提示关注的公众号',
  `finish_button` text COMMENT '成功抢答完后显示的按钮',
  `card_id` varchar(255) DEFAULT NULL COMMENT '卡券ID',
  `appsecre` varchar(255) DEFAULT NULL COMMENT '卡券对应的appsecre',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_ask
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_ask_answer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_ask_answer`;
CREATE TABLE `wp_ask_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `answer` text COMMENT '回答内容',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenId',
  `uid` int(10) DEFAULT NULL COMMENT '用户UID',
  `question_id` int(10) unsigned NOT NULL COMMENT 'question_id',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `ask_id` int(10) unsigned NOT NULL COMMENT 'ask_id',
  `is_correct` tinyint(2) DEFAULT '1' COMMENT '是否回答正确',
  `times` int(4) DEFAULT '0' COMMENT '次数',
  PRIMARY KEY (`id`),
  KEY `ask_id_uid` (`ask_id`,`uid`),
  KEY `question_uid` (`uid`,`question_id`,`times`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_ask_answer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_ask_question`
-- ----------------------------
DROP TABLE IF EXISTS `wp_ask_question`;
CREATE TABLE `wp_ask_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '问题描述',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `is_must` tinyint(2) DEFAULT '1' COMMENT '是否必填',
  `extra` text NOT NULL COMMENT '参数',
  `type` char(50) NOT NULL DEFAULT 'radio' COMMENT '问题类型',
  `ask_id` int(10) unsigned NOT NULL COMMENT 'ask_id',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `answer` varchar(255) NOT NULL COMMENT '正确答案',
  `is_last` tinyint(2) DEFAULT '0' COMMENT '是否最后一题',
  `wait_time` int(10) DEFAULT '0' COMMENT '等待时间',
  `percent` int(10) DEFAULT '100' COMMENT '抢中概率',
  `answer_time` int(10) DEFAULT NULL COMMENT '答题时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_ask_question
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_attachment`
-- ----------------------------
DROP TABLE IF EXISTS `wp_attachment`;
CREATE TABLE `wp_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT '0' COMMENT '用户ID',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '附件显示名',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小',
  `dir` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级目录ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of wp_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `wp_attribute`;
CREATE TABLE `wp_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `extra` text NOT NULL COMMENT '参数',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `is_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `validate_rule` varchar(255) NOT NULL DEFAULT '',
  `validate_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `error_info` varchar(100) NOT NULL DEFAULT '',
  `validate_type` varchar(25) NOT NULL DEFAULT '',
  `auto_rule` varchar(100) NOT NULL DEFAULT '',
  `auto_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_type` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12088 DEFAULT CHARSET=utf8 COMMENT='模型属性表';

-- ----------------------------
-- Records of wp_attribute
-- ----------------------------
INSERT INTO `wp_attribute` VALUES ('5', 'nickname', '用户名', 'text NULL', 'string', '', '', '0', '', '1', '1', '1', '1447302832', '1436929161', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('6', 'password', '登录密码', 'varchar(100) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302859', '1436929210', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('7', 'truename', '真实姓名', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302886', '1436929252', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('8', 'mobile', '联系电话', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302825', '1436929280', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('9', 'email', '邮箱地址', 'varchar(100) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302817', '1436929305', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('10', 'sex', '性别', 'tinyint(2) NULL', 'radio', '', '', '0', '0:保密\r\n1:男\r\n2:女', '1', '0', '1', '1447302800', '1436929397', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11', 'headimgurl', '头像地址', 'varchar(255) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302811', '1436929482', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12', 'city', '城市', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302793', '1436929506', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('13', 'province', '省份', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302787', '1436929524', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('14', 'country', '国家', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302781', '1436929541', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('15', 'language', '语言', 'varchar(20) NULL', 'string', 'zh-cn', '', '0', '', '1', '0', '1', '1447302725', '1436929571', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('16', 'score', '金币值', 'int(10) NULL', 'num', '0', '', '0', '', '1', '0', '1', '1447302731', '1436929597', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('17', 'experience', '经验值', 'int(10) NULL', 'num', '0', '', '0', '', '1', '0', '1', '1447302738', '1436929619', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('18', 'unionid', '微信第三方ID', 'varchar(50) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302717', '1436929681', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('19', 'login_count', '登录次数', 'int(10) NULL', 'num', '0', '', '0', '', '1', '0', '1', '1447302710', '1436930011', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('20', 'reg_ip', '注册IP', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302746', '1436930035', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('21', 'reg_time', '注册时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1', '0', '1', '1447302754', '1436930051', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('22', 'last_login_ip', '最近登录IP', 'varchar(30) NULL', 'string', '', '', '0', '', '1', '0', '1', '1447302761', '1436930072', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('23', 'last_login_time', '最近登录时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1', '0', '1', '1447302770', '1436930087', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('24', 'status', '状态', 'tinyint(2) NULL', 'bool', '1', '', '0', '0:禁用\r\n1:启用', '1', '0', '1', '1447302703', '1436930138', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('25', 'is_init', '初始化状态', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:未初始化\r\n1:已初始化', '1', '0', '1', '1447302696', '1436930184', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('26', 'is_audit', '审核状态', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:未审核\r\n1:已审核', '1', '0', '1', '1447302688', '1436930216', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('27', 'subscribe_time', '用户关注公众号时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1', '0', '1', '1437720655', '1437720655', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('28', 'remark', '微信用户备注', 'varchar(100) NULL', 'string', '', '', '0', '', '1', '0', '1', '1437720686', '1437720686', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('29', 'groupid', '微信端的分组ID', 'int(10) NULL', 'num', '', '', '0', '', '1', '0', '1', '1437720714', '1437720714', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('4', 'come_from', '来源', 'tinyint(1) NULL', 'select', '0', '', '0', '0:PC注册用户\r\n1:微信同步用户\r\n2:手机注册用户', '1', '0', '1', '1447302852', '1438331357', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('31', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '1', '', '2', '1', '1', '1436932588', '1436932588', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('32', 'has_public', '是否配置公众号', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:否\r\n1:是', '2', '0', '1', '1436933464', '1436933464', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('33', 'headface_url', '管理员头像', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '2', '0', '1', '1436933503', '1436933503', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('34', 'GammaAppId', '摇电视的AppId', 'varchar(30) NULL', 'string', '', '', '1', '', '2', '0', '1', '1436933562', '1436933562', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('35', 'GammaSecret', '摇电视的Secret', 'varchar(100) NULL', 'string', '', '', '1', '', '2', '0', '1', '1436933602', '1436933602', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('36', 'copy_right', '授权信息', 'varchar(255) NULL', 'string', '', '', '1', '', '2', '0', '1', '1436933690', '1436933690', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('37', 'tongji_code', '统计代码', 'text NULL', 'textarea', '', '', '1', '', '2', '0', '1', '1436933778', '1436933778', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('38', 'website_logo', '网站LOGO', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '2', '0', '1', '1436934006', '1436934006', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('39', 'menu_type', '菜单类型', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:顶级菜单|pid@hide\r\n1:侧栏菜单|pid@show', '3', '0', '1', '1435218508', '1435216049', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('40', 'pid', '上级菜单', 'varchar(50) NULL', 'cascade', '0', '', '1', 'type=db&table=manager_menu&menu_type=0&uid=[manager_id]', '3', '0', '1', '1438858450', '1435216147', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('41', 'title', '菜单名', 'varchar(50) NULL', 'string', '', '', '1', '', '3', '1', '1', '1435216185', '1435216185', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('42', 'url_type', '链接类型', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:插件|addon_name@show,url@hide\r\n1:外链|addon_name@hide,url@show', '3', '0', '1', '1435218596', '1435216291', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('43', 'addon_name', '插件名', 'varchar(30) NULL', 'dynamic_select', '', '', '1', 'table=addons&type=0&value_field=name&title_field=title&order=id asc', '3', '0', '1', '1439433250', '1435216373', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('44', 'url', '外链', 'varchar(255) NULL', 'string', '', '', '1', '', '3', '0', '1', '1435216436', '1435216436', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('45', 'target', '打开方式', 'char(50) NULL', 'select', '_self', '', '1', '_self:当前窗口打开\r\n_blank:在新窗口打开', '3', '0', '1', '1435216626', '1435216626', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('46', 'is_hide', '是否隐藏', 'tinyint(2) NULL', 'radio', '0', '', '1', '0:否\r\n1:是', '3', '0', '1', '1435216697', '1435216697', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('47', 'sort', '排序号', 'int(10) NULL', 'num', '0', '值越小越靠前', '1', '', '3', '0', '1', '1435217270', '1435217270', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('48', 'uid', '管理员ID', 'int(10) NULL', 'num', '', '', '4', '', '3', '0', '1', '1435224916', '1435223957', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('49', 'keyword', '关键词', 'varchar(100) NOT NULL ', 'string', '', '', '1', '', '4', '1', '1', '1388815953', '1388815953', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('50', 'addon', '关键词所属插件', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '4', '1', '1', '1388816207', '1388816207', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('51', 'aim_id', '插件表里的ID值', 'int(10) unsigned NOT NULL ', 'num', '', '', '1', '', '4', '1', '1', '1388816287', '1388816287', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('52', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '4', '0', '1', '1407251221', '1388816392', '', '1', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('53', 'token', 'Token', 'varchar(100) NULL ', 'string', '', '', '0', '', '4', '0', '1', '1408945788', '1391399528', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('54', 'keyword_length', '关键词长度', 'int(10) unsigned NULL ', 'num', '0', '', '1', '', '4', '0', '1', '1407251147', '1393918566', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('55', 'keyword_type', '匹配类型', 'tinyint(2) NULL ', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '4', '0', '1', '1417745067', '1393919686', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('56', 'extra_text', '文本扩展', 'text NULL ', 'textarea', '', '', '0', '', '4', '0', '1', '1407251248', '1393919736', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('57', 'extra_int', '数字扩展', 'int(10) NULL ', 'num', '', '', '0', '', '4', '0', '1', '1407251240', '1393919798', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('58', 'request_count', '请求数', 'int(10) NULL', 'num', '0', '用户回复的次数', '0', '', '4', '0', '1', '1401938983', '1401938983', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('59', 'qr_code', '二维码', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '5', '1', '1', '1406127577', '1388815953', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('60', 'addon', '二维码所属插件', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '5', '1', '1', '1406127594', '1388816207', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('61', 'aim_id', '插件表里的ID值', 'int(10) unsigned NOT NULL ', 'num', '', '', '1', '', '5', '1', '1', '1388816287', '1388816287', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('62', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '1', '', '5', '0', '1', '1388816392', '1388816392', '', '1', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('63', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '5', '0', '1', '1391399528', '1391399528', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('64', 'action_name', '二维码类型', 'char(30) NULL', 'select', 'QR_SCENE', 'QR_SCENE为临时,QR_LIMIT_SCENE为永久 ', '1', 'QR_SCENE:临时二维码\r\nQR_LIMIT_SCENE:永久二维码', '5', '0', '1', '1406130162', '1393919686', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('65', 'extra_text', '文本扩展', 'text NULL ', 'textarea', '', '', '1', '', '5', '0', '1', '1393919736', '1393919736', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('66', 'extra_int', '数字扩展', 'int(10) NULL ', 'num', '', '', '1', '', '5', '0', '1', '1393919798', '1393919798', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('67', 'request_count', '请求数', 'int(10) NULL', 'num', '0', '用户回复的次数', '0', '', '5', '0', '1', '1402547625', '1401938983', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('68', 'scene_id', '场景ID', 'int(10) NULL', 'num', '0', '', '1', '', '5', '0', '1', '1406127542', '1406127542', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('69', 'is_use', '是否为当前公众号', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:否\r\n1:是', '6', '0', '1', '1391682184', '1391682184', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('70', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '6', '0', '1', '1402453598', '1391597344', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('71', 'uid', '用户ID', 'int(10) NULL ', 'num', '', '', '0', '', '6', '1', '1', '1391575873', '1391575210', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('72', 'public_name', '公众号名称', 'varchar(50) NOT NULL', 'string', '', '', '1', '', '6', '1', '1', '1391576452', '1391575955', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('73', 'public_id', '公众号原始id', 'varchar(100) NOT NULL', 'string', '', '请正确填写，保存后不能再修改，且无法接收到微信的信息', '1', '', '6', '1', '1', '1402453976', '1391576015', '', '1', '公众号原始ID已经存在，请不要重复增加', 'unique', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('74', 'wechat', '微信号', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '6', '1', '1', '1391576484', '1391576144', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('75', 'interface_url', '接口地址', 'varchar(255) NULL', 'string', '', '', '0', '', '6', '0', '1', '1392946881', '1391576234', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('76', 'headface_url', '公众号头像', 'varchar(255) NULL', 'picture', '', '', '1', '', '6', '0', '1', '1429847363', '1391576300', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('77', 'area', '地区', 'varchar(50) NULL', 'string', '', '', '0', '', '6', '0', '1', '1392946934', '1391576435', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('78', 'addon_config', '插件配置', 'text NULL', 'textarea', '', '', '0', '', '6', '0', '1', '1391576537', '1391576537', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('79', 'addon_status', '插件状态', 'text NULL', 'textarea', '', '', '0', '160:自定义回复\r\n161:自动回复\r\n162:微官网\r\n163:微信用户中心\r\n164:微考试\r\n165:比赛抽奖\r\n166:融合第三方\r\n167:通用表单\r\n168:开发者工具箱\r\n139:促销活动\r\n190:你问我答客服系统\r\n191:实物奖励\r\n192:幸运大转盘\r\n193:微预约\r\n106:微信红包\r\n188:刮刮卡\r\n189:机器人聊天\r\n124:预约挂号\r\n169:优惠券\r\n170:竞猜\r\n171:评论互动\r\n172:互动游戏\r\n135:自媒体中心\r\n138:现金优惠券\r\n176:抢答\r\n177:微名片\r\n178:微信入门案例\r\n141:工作授权\r\n182:投票\r\n143:帮拆礼包\r\n144:会员卡\r\n145:签到\r\n183:短信服务\r\n184:微调研\r\n185:商城\r\n149:秒杀\r\n187:微贺卡\r\n186:欢迎语\r\n179:微邀约\r\n180:统计监控\r\n181:微测试\r\n175:微信卡券\r\n174:智能聊天\r\n159:自定义菜单\r\n194:一键绑定公众号\r\n195:支付通\r\n196:微信宣传页\r\n197:没回答的回复\r\n', '6', '0', '1', '1391576571', '1391576571', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11269', 'keyword', '关键词', 'varchar(255) NULL', 'string', '', '', '1', '', '1143', '0', '1', '1396602514', '1396602514', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11270', 'keyword_type', '关键词类型', 'tinyint(2) NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1143', '0', '1', '1396602706', '1396602548', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11271', 'mult_ids', '多图文ID', 'varchar(255) NULL', 'string', '', '', '0', '', '1143', '0', '1', '1396602601', '1396602578', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11272', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1143', '0', '1', '1396602821', '1396602821', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11273', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1144', '1', '1', '1396061575', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('80', 'type', '公众号类型', 'char(10) NULL', 'radio', '0', '', '1', '0:普通订阅号\r\n1:认证订阅号/普通服务号\r\n2:认证服务号', '6', '0', '1', '1416904702', '1393718575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('81', 'appid', 'AppID', 'varchar(255) NULL', 'string', '', '应用ID', '1', '', '6', '0', '1', '1416904750', '1393718735', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('82', 'secret', 'AppSecret', 'varchar(255) NULL', 'string', '', '应用密钥', '1', '', '6', '0', '1', '1416904771', '1393718806', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('83', 'group_id', '等级', 'int(10) unsigned NULL ', 'select', '0', '', '0', '', '6', '0', '1', '1393753499', '1393724468', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('84', 'is_audit', '是否审核', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:否\r\n1:是', '6', '1', '1', '1430879018', '1430879007', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('85', 'is_init', '是否初始化', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:否\r\n1:是', '6', '1', '1', '1430888244', '1430878899', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('86', 'encodingaeskey', 'EncodingAESKey', 'varchar(255) NULL', 'string', '', '安全模式下必填', '1', '', '6', '0', '1', '1419775850', '1419775850', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('87', 'tips_url', '提示关注公众号的文章地址', 'varchar(255) NULL', 'string', '', '', '1', '', '6', '0', '1', '1420789769', '1420789769', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('88', 'GammaAppId', 'GammaAppId', 'varchar(255) NULL', 'string', '', '', '1', '', '6', '0', '1', '1424529968', '1424529968', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('89', 'GammaSecret', 'GammaSecret', 'varchar(255) NULL', 'string', '', '', '1', '', '6', '0', '1', '1424529990', '1424529990', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('90', 'public_copy_right', '版权信息', 'varchar(255) NULL', 'string', '', '', '1', '', '6', '0', '1', '1431141576', '1431141576', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('91', 'domain', '自定义域名', 'varchar(30) NULL', 'string', '', '', '0', '', '6', '0', '1', '1439698931', '1439698931', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('92', 'title', '等级名', 'varchar(50) NULL', 'string', '', '', '1', '', '7', '0', '1', '1393724854', '1393724854', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('93', 'addon_status', '插件权限', 'text NULL', 'checkbox', '', '', '1', '160:自定义回复\r\n161:自动回复\r\n162:微官网\r\n163:微信用户中心\r\n164:微考试\r\n165:比赛抽奖\r\n166:融合第三方\r\n167:通用表单\r\n168:开发者工具箱\r\n139:促销活动\r\n190:你问我答客服系统\r\n191:实物奖励\r\n192:幸运大转盘\r\n193:微预约\r\n106:微信红包\r\n188:刮刮卡\r\n189:机器人聊天\r\n124:预约挂号\r\n169:优惠券\r\n170:竞猜\r\n171:评论互动\r\n172:互动游戏\r\n135:自媒体中心\r\n138:现金优惠券\r\n176:抢答\r\n177:微名片\r\n178:微信入门案例\r\n141:工作授权\r\n182:投票\r\n143:帮拆礼包\r\n144:会员卡\r\n145:签到\r\n183:短信服务\r\n184:微调研\r\n185:商城\r\n149:秒杀\r\n187:微贺卡\r\n186:欢迎语\r\n179:微邀约\r\n180:统计监控\r\n181:微测试\r\n175:微信卡券\r\n174:智能聊天\r\n159:自定义菜单\r\n194:一键绑定公众号\r\n195:支付通\r\n196:微信宣传页\r\n197:没回答的回复\r\n', '7', '0', '1', '1393731903', '1393725072', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11314', 'pid', '一级目录', 'int(10) NULL', 'cascade', '0', '', '1', 'type=db&table=weisite_category&pid=id', '1148', '0', '1', '1439522271', '1439469294', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11315', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1149', '1', '1', '1396061575', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11316', 'keyword_type', '关键词类型', 'tinyint(2) NULL', 'select', '', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1149', '0', '1', '1396061814', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('94', 'uid', '管理员UID', 'int(10) NULL ', 'admin', '', '', '1', '', '8', '1', '1', '1447215599', '1398933236', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('95', 'mp_id', '公众号ID', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '8', '1', '1', '1398933300', '1398933300', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('96', 'is_creator', '是否为创建者', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:不是\r\n1:是', '8', '0', '1', '1398933380', '1398933380', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('97', 'addon_status', '插件权限', 'text NULL', 'checkbox', '', '', '1', '160:自定义回复\r\n161:自动回复\r\n162:微官网\r\n163:微信用户中心\r\n164:微考试\r\n165:比赛抽奖\r\n166:融合第三方\r\n167:通用表单\r\n168:开发者工具箱\r\n139:促销活动\r\n190:你问我答客服系统\r\n191:实物奖励\r\n192:幸运大转盘\r\n193:微预约\r\n106:微信红包\r\n188:刮刮卡\r\n189:机器人聊天\r\n124:预约挂号\r\n169:优惠券\r\n170:竞猜\r\n171:评论互动\r\n172:互动游戏\r\n135:自媒体中心\r\n138:现金优惠券\r\n176:抢答\r\n177:微名片\r\n178:微信入门案例\r\n141:工作授权\r\n182:投票\r\n143:帮拆礼包\r\n144:会员卡\r\n145:签到\r\n183:短信服务\r\n184:微调研\r\n185:商城\r\n149:秒杀\r\n187:微贺卡\r\n186:欢迎语\r\n179:微邀约\r\n180:统计监控\r\n181:微测试\r\n175:微信卡券\r\n174:智能聊天\r\n159:自定义菜单\r\n194:一键绑定公众号\r\n195:支付通\r\n196:微信宣传页\r\n197:没回答的回复\r\n', '8', '0', '1', '1398933475', '1398933475', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11309', 'icon', '分类图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1148', '0', '1', '1395988966', '1395988966', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11310', 'url', '外链', 'varchar(255) NULL', 'string', '', '为空时默认跳转到该分类的文章列表页面', '1', '', '1148', '0', '1', '1401408363', '1395989660', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11311', 'is_show', '显示', 'tinyint(2) NULL', 'bool', '1', '', '1', '0: 不显示\r\n1: 显示', '1148', '0', '1', '1395989709', '1395989709', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11312', 'token', 'Token', 'varchar(100) NULL ', 'string', '', '', '0', '', '1148', '0', '1', '1395989760', '1395989760', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('98', 'is_use', '是否为当前管理的公众号', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:不是\r\n1:是', '8', '0', '1', '1398996982', '1398996975', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('99', 'attach', '上传文件', 'int(10) unsigned NOT NULL ', 'file', '', '支持xls,xlsx两种格式', '1', '', '9', '1', '1', '1407554177', '1407554177', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('100', 'icon', '分类图标', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '10', '0', '1', '1400047745', '1400047745', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('101', 'title', '分类名', 'varchar(255) NULL', 'string', '', '', '1', '', '10', '0', '1', '1400047764', '1400047764', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('102', 'sort', '排序号', 'int(10) NULL', 'num', '0', '值越小越靠前', '1', '', '10', '0', '1', '1400050453', '1400047786', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12064', 'uid', '用户uid', 'int(10) NULL', 'num', '', '', '0', '', '1230', '0', '1', '1445255505', '1445255505', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12065', 'aim_id', 'aim_id', 'int(10) NULL', 'num', '', '', '0', '', '1230', '0', '1', '1445253482', '1445253482', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12063', 'status', '支付状态', 'tinyint(2) NOT NULL', 'bool', '0', '', '1', '0:未支付\r\n1:已支付\r\n2:支付失败', '1230', '0', '1', '1420597026', '1420597026', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12062', 'showwxpaytitle', '是否显示标题', 'tinyint(2) NOT NULL', 'bool', '0', '', '1', '0:不显示\r\n1:显示', '1230', '0', '1', '1420596980', '1420596980', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12061', 'paytype', '支付方式', 'varchar(30) NOT NULL', 'string', '', '', '1', '', '1230', '0', '1', '1420596929', '1420596929', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12060', 'wecha_id', 'OpenID', 'varchar(200) NOT NULL', 'string', '', '', '1', '', '1230', '0', '1', '1420596530', '1420596530', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12059', 'token', 'Token', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1230', '0', '1', '1420596492', '1420596492', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('110', 'name', '分类标识', 'varchar(255) NULL', 'string', '', '只能使用英文', '0', '', '12', '0', '1', '1403711345', '1397529355', '', '3', '只能输入由数字、26个英文字母或者下划线组成的标识名', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('111', 'title', '分类标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '12', '1', '1', '1397529407', '1397529407', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('112', 'icon', '分类图标', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '12', '0', '1', '1397529461', '1397529461', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('113', 'pid', '上一级分类', 'int(10) unsigned NULL ', 'select', '0', '如果你要增加一级分类，这里选择“无”即可', '1', '0:无', '12', '0', '1', '1398266132', '1397529555', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('114', 'path', '分类路径', 'varchar(255) NULL', 'string', '', '', '0', '', '12', '0', '1', '1397529604', '1397529604', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('115', 'module', '分类所属功能', 'varchar(255) NULL', 'string', '', '', '0', '', '12', '0', '1', '1397529671', '1397529671', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('116', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '数值越小越靠前', '1', '', '12', '0', '1', '1397529705', '1397529705', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('117', 'is_show', '是否显示', 'tinyint(2) NULL', 'bool', '1', '', '1', '0:不显示\r\n1:显示', '12', '0', '1', '1397532496', '1397529809', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('118', 'intro', '分类描述', 'varchar(255) NULL', 'string', '', '', '1', '', '12', '0', '1', '1398414247', '1398414247', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('119', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '12', '0', '1', '1398593086', '1398523502', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('120', 'code', '分类扩展编号', 'varchar(255) NULL', 'string', '', '原分类或者导入分类的扩展编号', '0', '', '12', '0', '1', '1404182741', '1404182630', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('121', 'cTime', '发布时间', 'int(10) UNSIGNED NULL', 'datetime', '', '', '0', '', '13', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('122', 'name', '分组标识', 'varchar(100) NOT NULL', 'string', '', '英文字母或者下划线，长度不超过30', '1', '', '13', '1', '1', '1403624543', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('123', 'title', '分组标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '13', '1', '1', '1403624556', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('124', 'level', '最多级数', 'tinyint(1) unsigned NULL', 'select', '3', '', '1', '1:1级\r\n2:2级\r\n3:3级\r\n4:4级\r\n5:5级\r\n6:6级\r\n7:7级', '13', '0', '1', '1404193097', '1404192897', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('125', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '13', '1', '1', '1408947244', '1396602859', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('126', 'title', '积分描述', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '14', '1', '1', '1438589622', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('127', 'name', '积分标识', 'varchar(50) NULL', 'string', '', '', '1', '', '14', '0', '1', '1438589601', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('128', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '14', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('129', 'experience', '经验值', 'int(10) NULL', 'num', '0', '可以是正数，也可以是负数，如 -10 表示减10个经验值', '1', '', '14', '0', '1', '1398564024', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('130', 'score', '金币值', 'int(10) NULL', 'num', '0', '可以是正数，也可以是负数，如 -10 表示减10个金币值', '1', '', '14', '0', '1', '1398564097', '1396062146', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('131', 'token', 'Token', 'varchar(255) NULL', 'string', '0', '', '0', '', '14', '0', '1', '1398564146', '1396602859', '', '3', '', 'regex', '', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('132', 'credit_name', '积分标识', 'varchar(50) NULL', 'string', '', '', '1', '', '15', '0', '1', '1398564405', '1398564405', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('133', 'uid', '用户ID', 'int(10) NULL', 'num', '0', '', '1', '', '15', '0', '1', '1398564351', '1398564351', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('134', 'experience', '经验值', 'int(10) NULL', 'num', '0', '', '1', '', '15', '0', '1', '1398564448', '1398564448', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('135', 'score', '金币值', 'int(10) NULL', 'num', '0', '', '1', '', '15', '0', '1', '1398564486', '1398564486', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('136', 'cTime', '记录时间', 'int(10) NULL', 'datetime', '', '', '0', '', '15', '0', '1', '1398564567', '1398564567', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('137', 'admin_uid', '操作者UID', 'int(10) NULL', 'num', '0', '', '0', '', '15', '0', '1', '1398564629', '1398564629', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('138', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '15', '0', '1', '1400603451', '1400603451', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('139', 'cover_id', '图片在本地的ID', 'int(10) NULL', 'num', '', '', '0', '', '16', '0', '1', '1438684652', '1438684652', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('140', 'cover_url', '本地URL', 'varchar(255) NULL', 'string', '', '', '0', '', '16', '0', '1', '1438684692', '1438684692', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('141', 'media_id', '微信端图文消息素材的media_id', 'varchar(100) NULL', 'string', '0', '', '0', '', '16', '0', '1', '1438744962', '1438684776', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('142', 'wechat_url', '微信端的图片地址', 'varchar(255) NULL', 'string', '', '', '0', '', '16', '0', '1', '1439973558', '1438684807', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('143', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '16', '0', '1', '1438684829', '1438684829', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('144', 'manager_id', '管理员ID', 'int(10) NULL', 'num', '', '', '0', '', '16', '0', '1', '1438684847', '1438684847', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('145', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '16', '0', '1', '1438684865', '1438684865', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('146', 'title', '标题', 'varchar(100) NULL', 'string', '', '', '1', '', '17', '1', '1', '1438670933', '1438670933', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('147', 'author', '作者', 'varchar(30) NULL', 'string', '', '', '1', '', '17', '0', '1', '1438670961', '1438670961', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('148', 'cover_id', '封面', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '17', '0', '1', '1438674438', '1438670980', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('149', 'intro', '摘要', 'varchar(255) NULL', 'textarea', '', '', '1', '', '17', '0', '1', '1438671024', '1438671024', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('150', 'content', '内容', 'longtext  NULL', 'editor', '', '', '1', '', '17', '0', '1', '1440473839', '1438671049', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('151', 'link', '外链', 'varchar(255) NULL', 'string', '', '', '1', '', '17', '0', '1', '1438671066', '1438671066', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('152', 'group_id', '多图文组的ID', 'int(10) NULL', 'num', '0', '0 表示单图文，多于0 表示多图文中的第一个图文的ID值', '0', '', '17', '0', '1', '1438671163', '1438671163', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('153', 'thumb_media_id', '图文消息的封面图片素材id（必须是永久mediaID）', 'varchar(100) NULL', 'string', '', '', '0', '', '17', '0', '1', '1438671302', '1438671285', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('154', 'media_id', '微信端图文消息素材的media_id', 'varchar(100) NULL', 'string', '0', '', '1', '', '17', '0', '1', '1438744941', '1438671373', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('155', 'manager_id', '管理员ID', 'int(10) NULL', 'num', '', '', '0', '', '17', '0', '1', '1438683172', '1438683172', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('156', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '17', '0', '1', '1438683194', '1438683194', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('157', 'cTime', '发布时间', 'int(10) NULL', 'datetime', '', '', '0', '', '17', '0', '1', '1438683499', '1438683499', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('158', 'bind_keyword', '关联关键词', 'varchar(50) NULL', 'string', '', '先在自定义回复里增加图文，多图文或者文本内容，再把它的关键词填写到这里', '1', '', '18', '0', '1', '1437984209', '1437984184', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('159', 'preview_openids', '预览人OPENID', 'text NULL', 'textarea', '', '选填，多个可用逗号或者换行分开，OpenID值可在微信用户的列表中找到', '1', '', '18', '0', '1', '1438049470', '1437985038', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('160', 'group_id', '群发对象', 'int(10) NULL', 'dynamic_select', '0', '全部用户或者某分组用户', '1', 'table=auth_group&manager_id=[manager_id]&token=[token]&value_field=id&title_field=title&first_option=全部用户', '18', '0', '1', '1438049058', '1437985498', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('161', 'type', '素材来源', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:站内关键词|bind_keyword@show,media_id@hide\r\n1:微信永久素材ID|bind_keyword@hide,media_id@show', '18', '0', '1', '1437988869', '1437988869', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('162', 'media_id', '微信素材ID', 'varchar(100) NULL', 'string', '', '微信后台的素材管理里永久素材的media_id值', '1', '', '18', '0', '1', '1437988973', '1437988973', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('163', 'send_type', '发送方式', 'tinyint(1) NULL', 'bool', '0', '', '1', '0:按用户组发送|group_id@show,send_openids@hide\r\n1:指定OpenID发送|group_id@hide,send_openids@show', '18', '0', '1', '1438049241', '1438049241', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('164', 'send_openids', '要发送的OpenID', 'text NULL', 'textarea', '', '多个可用逗号或者换行分开，OpenID值可在微信用户的列表中找到', '1', '', '18', '0', '1', '1438049362', '1438049362', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('165', 'msg_id', 'msg_id', 'varchar(255) NULL', 'string', '', '', '0', '', '18', '0', '1', '1439980539', '1438054616', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('166', 'publicid', '公众号ID', 'int(10) NULL', 'num', '0', '', '0', '', '19', '0', '1', '1439448400', '1439448400', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('167', 'module_name', '类型名', 'varchar(30) NULL', 'string', '', '', '0', '', '19', '0', '1', '1439448516', '1439448516', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('168', 'controller_name', '控制器名', 'varchar(30) NULL', 'string', '', '', '0', '', '19', '0', '1', '1439448567', '1439448567', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('169', 'action_name', '方法名', 'varchar(30) NULL', 'string', '', '', '0', '', '19', '0', '1', '1439448616', '1439448616', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('170', 'uid', '访问者ID', 'varchar(255) NULL', 'string', '0', '', '0', '', '19', '0', '1', '1439448654', '1439448654', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('171', 'ip', 'ip地址', 'varchar(30) NULL', 'string', '', '', '0', '', '19', '0', '1', '1439448742', '1439448742', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('172', 'brower', '浏览器', 'varchar(30) NULL', 'string', '', '', '0', '', '19', '0', '1', '1439448792', '1439448792', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('173', 'param', '其它GET参数', 'text NULL', 'textarea', '', '', '0', '', '19', '0', '1', '1439448834', '1439448834', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('174', 'referer', '访问的URL', 'varchar(255) NULL', 'string', '', '', '0', '', '19', '0', '1', '1439448886', '1439448874', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('175', 'cTime', '时间', 'int(10) NULL', 'datetime', '', '', '0', '', '19', '0', '1', '1439450668', '1439450668', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('176', 'wechat_group_name', '微信端的分组名', 'varchar(100) NULL', 'string', '', '', '0', '', '20', '0', '1', '1437635205', '1437635205', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('177', 'wechat_group_id', '微信端的分组ID', 'int(10) NULL', 'num', '-1', '', '0', '', '20', '0', '1', '1447659224', '1437635149', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('178', 'qr_code', '微信二维码', 'varchar(255) NULL', 'string', '', '', '0', '', '20', '0', '1', '1437635117', '1437635117', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('179', 'is_default', '是否默认自动加入', 'tinyint(1) NULL', 'radio', '0', '只有设置一个默认组，设置当前为默认组后之前的默认组将取消', '0', '0:否\r\n1:是', '20', '0', '1', '1437642358', '1437635042', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('180', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '20', '0', '1', '1437634089', '1437634089', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('181', 'manager_id', '管理员ID', 'int(10) NULL', 'num', '0', '为0时表示系统用户组', '0', '', '20', '0', '1', '1437634309', '1437634062', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('182', 'rules', '权限', 'text NULL', 'textarea', '', '', '0', '', '20', '0', '1', '1437634022', '1437634022', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('183', 'type', '类型', 'tinyint(2) NULL', 'bool', '1', '', '0', '0:普通用户组\r\n1:微信用户组\r\n2:等级用户组\r\n3:认证用户组', '20', '0', '1', '1437633981', '1437633981', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('184', 'status', '状态', 'tinyint(2) NULL', 'bool', '1', '', '0', '1:正常\r\n0:禁用\r\n-1:删除', '20', '0', '1', '1437633826', '1437633826', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('185', 'description', '描述信息', 'text NULL', 'textarea', '', '', '1', '', '20', '0', '1', '1437633751', '1437633751', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('186', 'icon', '图标', 'int(10) UNSIGNED NULL', 'picture', '', '', '0', '', '20', '0', '1', '1437633711', '1437633711', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('187', 'title', '分组名称', 'varchar(30) NULL', 'string', '', '', '1', '', '20', '1', '1', '1437641907', '1437633598', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('188', 'wechat_group_count', '微信端用户数', 'int(10) NULL', 'num', '', '', '0', '', '20', '0', '1', '1437644061', '1437644061', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('189', 'is_del', '是否已删除', 'tinyint(1) NULL', 'bool', '0', '', '0', '0:否\r\n1:是', '20', '0', '1', '1437650054', '1437650044', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('190', 'sports_id', 'sports_id', 'int(10) NULL', 'num', '', '', '0', '', '21', '0', '1', '1432806979', '1432806979', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('191', 'type', 'type', 'varchar(30) NULL', 'string', '', '', '0', '', '21', '0', '1', '1432807001', '1432807001', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('192', 'time', 'time', 'varchar(50) NULL', 'string', '', '', '0', '', '21', '0', '1', '1432807028', '1432807028', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('193', 'total_count', 'total_count', 'int(10) NULL', 'num', '0', '', '0', '', '21', '0', '1', '1432807049', '1432807049', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('194', 'follow_count', 'follow_count', 'int(10) NULL', 'num', '0', '', '0', '', '21', '0', '1', '1432807063', '1432807063', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('195', 'aver_count', 'aver_count', 'int(10) NULL', 'num', '0', '', '0', '', '21', '0', '1', '1432807079', '1432807079', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('196', 'group_id', '分组样式', 'int(10) NULL', 'num', '0', '', '1', '', '22', '0', '1', '1436845570', '1436845570', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('197', 'style', '样式内容', 'text NULL', 'textarea', '', '请填写html', '1', '', '22', '1', '1', '1436846111', '1436846111', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('198', 'group_name', '分组名称', 'varchar(255) NULL', 'string', '', '', '1', '', '23', '1', '1', '1436845332', '1436845332', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('199', 'desc', '说明', 'text NULL', 'textarea', '', '', '1', '', '23', '0', '1', '1436845361', '1436845361', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12057', 'single_orderid', '订单号', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1230', '0', '1', '1420596415', '1420596415', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12058', 'price', '价格', 'decimal(10,2) NULL', 'num', '', '', '1', '', '1230', '0', '1', '1439812508', '1420596472', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12056', 'orderName', '订单名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1230', '0', '1', '1439976366', '1420596373', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12055', 'from', '回调地址', 'varchar(50) NOT NULL', 'string', '', '', '1', '', '1230', '0', '1', '1420596347', '1420596347', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12054', 'join_count', '参加人数', 'int(10) NULL', 'num', '0', '', '0', '', '1229', '0', '1', '1444962764', '1444962764', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12053', 'init_count', '初始化预约数', 'int(10) NULL', 'num', '0', '', '0', '', '1229', '0', '1', '1444962246', '1444962246', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12052', 'max_limit', '最大预约数', 'int(10) NULL', 'num', '0', '为空时表示不限制', '0', '', '1229', '0', '1', '1444962264', '1444962198', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12051', 'money', '报名费用', 'decimal(11,2) NULL', 'num', '0', '', '0', '', '1229', '0', '1', '1444962160', '1444962160', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12049', 'reserve_id', '预约活动ID', 'int(10) NULL', 'num', '', '', '0', '', '1229', '0', '1', '1444962084', '1444962084', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12050', 'name', '名称', 'varchar(100) NULL', 'string', '', '', '0', '', '1229', '0', '1', '1444962123', '1444962123', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12048', 'is_pay', '是否支付', 'int(10) NULL', 'num', '0', '', '0', '', '1228', '0', '1', '1445258123', '1445258123', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12047', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1228', '0', '1', '1396690911', '1396690911', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12046', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1228', '0', '1', '1396688042', '1396688042', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12045', 'openid', 'OpenId', 'varchar(255) NULL', 'string', '', '', '0', '', '1228', '0', '1', '1396688187', '1396688187', '', '3', '', 'regex', 'get_openid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12044', 'cTime', '增加时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1228', '0', '1', '1396688434', '1396688434', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12043', 'value', '微预约值', 'text NULL', 'textarea', '', '', '0', '', '1228', '0', '1', '1396688355', '1396688355', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12042', 'reserve_id', '微预约ID', 'int(10) UNSIGNED NULL', 'num', '', '', '4', '', '1228', '0', '1', '1396710064', '1396688308', '', '3', '', 'regex', '', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12040', 'type', '字段类型', 'char(50) NOT NULL', 'select', 'string', '用于微预约中的展示方式', '1', 'string:单行输入\r\ntextarea:多行输入\r\nradio:单选\r\ncheckbox:多选\r\nselect:下拉选择\r\ndatetime:时间\r\npicture:上传图片', '1227', '1', '1', '1396871262', '1396683600', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12041', 'is_check', '验证是否成功', 'int(10) NULL', 'num', '0', '', '0', '', '1228', '0', '1', '1445246146', '1445246146', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12020', 'intro', '封面简介', 'text NULL', 'textarea', '', '', '1', '', '1226', '1', '1', '1439371986', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12021', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1226', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12022', 'cover', '封面图片', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1226', '1', '1', '1439372018', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12023', 'template', '模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1226', '0', '1', '1431661124', '1431661124', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12024', 'status', '状态', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:已禁用\r\n1:已开启', '1226', '0', '1', '1444917938', '1444917938', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12025', 'start_time', '报名开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1226', '0', '1', '1444959115', '1444959115', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12026', 'end_time', '报名结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1226', '0', '1', '1444959142', '1444959142', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12027', 'pay_online', '是否支持在线支付', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:否\r\n1:是', '1226', '0', '1', '1444959225', '1444959225', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12028', 'is_show', '是否显示', 'tinyint(2) NULL', 'select', '1', '是否显示在微预约中', '1', '1:显示\r\n0:不显示', '1227', '0', '1', '1396848437', '1396848437', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12029', 'reserve_id', '微预约ID', 'int(10) UNSIGNED NULL', 'num', '', '', '4', '', '1227', '0', '1', '1396710040', '1396690613', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12030', 'error_info', '出错提示', 'varchar(255) NULL', 'string', '', '验证不通过时的提示语', '1', '', '1227', '0', '1', '1396685920', '1396685920', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12031', 'sort', '排序号', 'int(10) UNSIGNED NULL', 'num', '0', '值越小越靠前', '1', '', '1227', '0', '1', '1396685825', '1396685825', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12032', 'validate_rule', '正则验证', 'varchar(255) NULL', 'string', '', '为空表示不作验证', '1', '', '1227', '0', '1', '1396685776', '1396685776', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12033', 'is_must', '是否必填', 'tinyint(2) NULL', 'bool', '', '用于自动验证', '1', '0:否\r\n1:是', '1227', '0', '1', '1396685579', '1396685579', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12034', 'remark', '字段备注', 'varchar(255) NULL', 'string', '', '用于微预约中的提示', '1', '', '1227', '0', '1', '1396685482', '1396685482', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12035', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1227', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12036', 'value', '默认值', 'varchar(255) NULL', 'string', '', '字段的默认值', '1', '', '1227', '0', '1', '1396685291', '1396685291', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12037', 'title', '字段标题', 'varchar(255) NOT NULL', 'string', '', '请输入字段标题，用于微预约显示', '1', '', '1227', '1', '1', '1396676830', '1396676830', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12038', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1227', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12039', 'extra', '参数', 'text NULL', 'textarea', '', '字段类型为单选、多选、下拉选择和级联选择时的定义数据，其它字段类型为空', '1', '', '1227', '0', '1', '1396835020', '1396685105', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12066', 'wxmchid', '微信支付商户号', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '1', '1', '1439364696', '1436437067', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12067', 'shop_id', '商店ID', 'int(10) NULL', 'num', '0', '', '0', '', '1231', '0', '1', '1436437020', '1436437003', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12068', 'quick_merid', '银联在线merid', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436949', '1436436949', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12069', 'quick_merabbr', '商户名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436970', '1436436970', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12070', 'wxpartnerid', '微信partnerid', 'varchar(255) NULL', 'string', '', '', '0', '', '1231', '0', '1', '1436437196', '1436436910', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12071', 'wxpartnerkey', '微信partnerkey', 'varchar(255) NULL', 'string', '', '', '0', '', '1231', '0', '1', '1436437236', '1436436888', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12072', 'partnerid', '财付通标识', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436798', '1436436798', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12073', 'key', 'KEY', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436771', '1436436771', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12074', 'ctime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1231', '0', '1', '1436436498', '1436436498', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12075', 'quick_security_key', '银联在线Key', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436931', '1436436931', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12076', 'wappartnerkey', 'WAP财付通Key', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436863', '1436436863', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12077', 'wappartnerid', '财付通标识WAP', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436834', '1436436834', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12078', 'partnerkey', '财付通Key', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436816', '1436436816', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12079', 'pid', 'PID', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436707', '1436436707', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12080', 'zfbname', '帐号', 'varchar(255) NULL', 'string', '', '', '1', '', '1231', '0', '1', '1436436653', '1436436653', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12081', 'wxappsecret', 'AppSecret', 'varchar(255) NULL', 'string', '', '微信支付中的公众号应用密钥', '1', '', '1231', '1', '1', '1439364612', '1436436618', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12082', 'wxpaysignkey', '支付密钥', 'varchar(255) NULL', 'string', '', 'PartnerKey', '1', '', '1231', '1', '1', '1439364810', '1436436569', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12083', 'wxappid', 'AppID', 'varchar(255) NULL', 'string', '', '微信支付中的公众号应用ID', '1', '', '1231', '1', '1', '1439364573', '1436436534', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12084', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1231', '0', '1', '1436436415', '1436436415', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12085', 'wx_cert_pem', '上传证书', 'int(10) UNSIGNED NULL', 'file', '', 'apiclient_cert.pem', '1', '', '1231', '0', '1', '1439804529', '1439550487', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12086', 'wx_key_pem', '上传密匙', 'int(10) UNSIGNED NULL', 'file', '', 'apiclient_key.pem', '1', '', '1231', '0', '1', '1439804544', '1439804014', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12087', 'shop_pay_score', '支付返积分', 'int(10) NULL', 'num', '0', '不设置则默认为采用该支付方式不送积分', '1', '', '1231', '0', '1', '1443065789', '1443064056', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11518', 'cTime', '增加时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1171', '0', '1', '1396688434', '1396688434', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11516', 'forms_id', '表单ID', 'int(10) UNSIGNED NULL', 'num', '', '', '4', '', '1171', '0', '1', '1396710064', '1396688308', '', '3', '', 'regex', '', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11517', 'value', '表单值', 'text NULL', 'textarea', '', '', '0', '', '1171', '0', '1', '1396688355', '1396688355', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11515', 'type', '字段类型', 'char(50) NOT NULL', 'select', 'string', '用于表单中的展示方式', '1', 'string:单行输入\r\ntextarea:多行输入\r\nradio:单选\r\ncheckbox:多选\r\nselect:下拉选择\r\ndatetime:时间\r\npicture:上传图片', '1170', '1', '1', '1396871262', '1396683600', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11514', 'extra', '参数', 'text NULL', 'textarea', '', '字段类型为单选、多选、下拉选择和级联选择时的定义数据，其它字段类型为空', '1', '', '1170', '0', '1', '1396835020', '1396685105', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11513', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1170', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11512', 'title', '字段标题', 'varchar(255) NOT NULL', 'string', '', '请输入字段标题，用于表单显示', '1', '', '1170', '1', '1', '1396676830', '1396676830', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11511', 'value', '默认值', 'varchar(255) NULL', 'string', '', '字段的默认值', '1', '', '1170', '0', '1', '1396685291', '1396685291', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11510', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1170', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11509', 'name', '字段名', 'varchar(100) NULL', 'string', '', '请输入字段名 英文字母开头，长度不超过30', '1', '', '1170', '1', '1', '1447638080', '1396676792', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11508', 'remark', '字段备注', 'varchar(255) NULL', 'string', '', '用于表单中的提示', '1', '', '1170', '0', '1', '1396685482', '1396685482', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11507', 'is_must', '是否必填', 'tinyint(2) NULL', 'bool', '', '用于自动验证', '1', '0:否\r\n1:是', '1170', '0', '1', '1396685579', '1396685579', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11506', 'validate_rule', '正则验证', 'varchar(255) NULL', 'string', '', '为空表示不作验证', '1', '', '1170', '0', '1', '1396685776', '1396685776', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11504', 'error_info', '出错提示', 'varchar(255) NULL', 'string', '', '验证不通过时的提示语', '1', '', '1170', '0', '1', '1396685920', '1396685920', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11505', 'sort', '排序号', 'int(10) UNSIGNED NULL', 'num', '0', '值越小越靠前', '1', '', '1170', '0', '1', '1396685825', '1396685825', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11503', 'forms_id', '表单ID', 'int(10) UNSIGNED NULL', 'num', '', '', '4', '', '1170', '0', '1', '1396710040', '1396690613', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11502', 'is_show', '是否显示', 'tinyint(2) NULL', 'select', '1', '是否显示在表单中', '1', '1:显示\r\n0:不显示', '1170', '0', '1', '1396848437', '1396848437', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11501', 'template', '模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1169', '0', '1', '1431661124', '1431661124', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11500', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1169', '1', '1', '1396866048', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11499', 'cover', '封面图片', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1169', '1', '1', '1439372018', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11498', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1169', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11497', 'intro', '封面简介', 'text NULL', 'textarea', '', '', '1', '', '1169', '1', '1', '1439371986', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11496', 'can_edit', '是否允许编辑', 'tinyint(2) NULL', 'bool', '0', '用户提交表单是否可以再编辑', '1', '0:不允许\r\n1:允许', '1169', '0', '1', '1396688624', '1396688624', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11495', 'finish_tip', '用户提交后提示内容', 'text NULL', 'string', '', '为空默认为：提交成功，谢谢参与', '1', '', '1169', '0', '1', '1447497102', '1396673689', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11494', 'content', '详细介绍', 'text NULL', 'editor', '', '可不填', '1', '', '1169', '0', '1', '1396865295', '1396865295', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11493', 'jump_url', '提交后跳转的地址', 'varchar(255) NULL', 'string', '', '要以http://开头的完整地址，为空时不跳转', '1', '', '1169', '0', '1', '1402458121', '1399800276', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11492', 'keyword_type', '关键词类型', 'tinyint(2) NOT NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配', '1169', '1', '1', '1396624426', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11330', 'icon', '图标', 'int(10) unsigned NULL ', 'picture', '', '根据选择的底部模板决定是否需要上传图标', '1', '', '1150', '0', '1', '1396506297', '1396506297', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11329', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1150', '0', '1', '1394526820', '1394526820', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11331', 'title', '标题', 'varchar(255) NULL', 'string', '', '可为空', '1', '', '1151', '0', '1', '1396098316', '1396098316', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11327', 'pid', '一级菜单', 'tinyint(2) NULL', 'select', '0', '如果是一级菜单，选择“无”即可', '1', '0:无', '1150', '0', '1', '1409045931', '1394518930', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11328', 'sort', '排序号', 'tinyint(4) NULL ', 'num', '0', '数值越小越靠前', '1', '', '1150', '0', '1', '1394523288', '1394519175', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11300', 'keyword', '关键词', 'varchar(255) NULL', 'string', '', '多个关键词可以用空格分开，如“高富帅 白富美”', '1', '', '1147', '1', '1', '1439194858', '1439194849', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11301', 'msg_type', '消息类型', 'char(50) NULL', 'select', 'text', '', '0', 'text:文本|content@show,group_id@hide,image_id@hide\r\nnews:图文|content@hide,group_id@show,image_id@hide\r\nimage:图片|content@hide,group_id@hide,image_id@show', '1147', '1', '1', '1439204529', '1439194979', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11302', 'content', '文本内容', 'text NULL', 'textarea', '', '', '1', '', '1147', '0', '1', '1439195826', '1439195091', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11303', 'group_id', '图文', 'int(10) NULL', 'news', '', '', '1', '', '1147', '0', '1', '1439204192', '1439195901', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11304', 'image_id', '上传图片', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1147', '0', '1', '1439195945', '1439195945', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11305', 'manager_id', '管理员ID', 'int(10) NULL', 'num', '', '', '0', '', '1147', '0', '1', '1439203621', '1439203575', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11306', 'token', 'Token', 'varchar(50) NULL', 'string', '', '', '0', '', '1147', '0', '1', '1439203612', '1439203612', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11307', 'image_material', '素材图片id', 'int(10) NULL', 'num', '', '', '0', '', '1147', '0', '1', '1447738833', '1447738833', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11317', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1149', '1', '1', '1396061877', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11318', 'intro', '简介', 'text NULL', 'textarea', '', '', '1', '', '1149', '0', '1', '1396061947', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11319', 'cate_id', '所属类别', 'int(10) unsigned NULL ', 'select', '0', '要先在微官网分类里配置好分类才可选择', '1', '0:请选择分类', '1149', '0', '1', '1396078914', '1396062003', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11320', 'cover', '封面图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1149', '0', '1', '1396062093', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11321', 'content', '内容', 'text NULL', 'editor', '', '', '1', '', '1149', '0', '1', '1396062146', '1396062146', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11322', 'cTime', '发布时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1149', '0', '1', '1396075102', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11323', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '数值越小越靠前', '1', '', '1149', '0', '1', '1396510508', '1396510508', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11324', 'view_count', '浏览数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1149', '0', '1', '1396510630', '1396510630', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11325', 'url', '关联URL', 'varchar(255) NULL ', 'string', '', '', '1', '', '1150', '0', '1', '1394519090', '1394519090', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11326', 'title', '菜单名', 'varchar(50) NOT NULL', 'string', '', '可创建最多 3 个一级菜单，每个一级菜单下可创建最多 5 个二级菜单。编辑中的菜单不会马上被用户看到，请放心调试。', '1', '', '1150', '1', '1', '1408950832', '1394518988', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11332', 'img', '图片', 'int(10) unsigned NOT NULL ', 'picture', '', '', '1', '', '1151', '1', '1', '1396098349', '1396098349', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11333', 'url', '链接地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1151', '0', '1', '1396098380', '1396098380', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11334', 'is_show', '是否显示', 'tinyint(2) NULL', 'bool', '1', '', '1', '0:不显示\r\n1:显示', '1151', '0', '1', '1396098464', '1396098464', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11335', 'sort', '排序', 'int(10) unsigned NULL ', 'num', '0', '值越小越靠前', '1', '', '1151', '0', '1', '1396098682', '1396098682', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11336', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '1151', '0', '1', '1396098747', '1396098747', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11337', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1152', '1', '1', '1396624337', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11338', 'keyword_type', '关键词匹配类型', 'tinyint(2) NOT NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配', '1152', '1', '1', '1396624426', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11339', 'title', '试卷标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1152', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11340', 'intro', '封面简介', 'text NOT NULL', 'textarea', '', '', '1', '', '1152', '0', '1', '1396624505', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11341', 'mTime', '修改时间', 'int(10) NOT NULL', 'datetime', '', '', '0', '', '1152', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11342', 'cover', '封面图片', 'int(10) UNSIGNED NOT NULL', 'picture', '', '', '1', '', '1152', '0', '1', '1396624534', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11343', 'cTime', '发布时间', 'int(10) UNSIGNED NOT NULL', 'datetime', '', '', '0', '', '1152', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11344', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1152', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11345', 'finish_tip', '结束语', 'text NOT NULL', 'string', '', '为空默认为：考试完成，谢谢参与', '1', '', '1152', '0', '1', '1447646362', '1396953940', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11346', 'start_time', '考试开始时间', 'int(10) NULL', 'datetime', '', '为空表示什么时候开始都可以', '2', '', '1152', '0', '1', '1447752638', '1397036762', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11347', 'end_time', '考试结束时间', 'int(10) NULL', 'datetime', '', '为空表示不限制结束时间', '2', '', '1152', '0', '1', '1447753072', '1397036831', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11348', 'title', '题目标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1153', '1', '1', '1397037377', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11349', 'intro', '题目描述', 'text NOT NULL', 'textarea', '', '', '1', '', '1153', '0', '1', '1396954176', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11350', 'cTime', '发布时间', 'int(10) UNSIGNED NOT NULL', 'datetime', '', '', '0', '', '1153', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11351', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1153', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11352', 'is_must', '是否必填', 'tinyint(2) NOT NULL', 'bool', '1', '', '0', '0:否\r\n1:是', '1153', '0', '1', '1397035513', '1396954649', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11353', 'extra', '参数', 'text NOT NULL', 'textarea', '', '每个选项换一行，每项输入格式如：A:男人', '1', '', '1153', '0', '1', '1397036210', '1396954558', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11354', 'type', '题目类型', 'char(50) NOT NULL', 'radio', 'radio', '', '1', 'radio:单选题\r\ncheckbox:多选题', '1153', '1', '1', '1397036281', '1396954463', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11355', 'exam_id', 'exam_id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '4', '', '1153', '1', '1', '1396954240', '1396954240', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11356', 'sort', '排序号', 'int(10) UNSIGNED NOT NULL', 'num', '0', '值越小越靠前', '1', '', '1153', '0', '1', '1396955010', '1396955010', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11357', 'score', '分值', 'int(10) UNSIGNED NOT NULL', 'num', '0', '考生答对此题的得分数', '1', '', '1153', '0', '1', '1397035609', '1397035609', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11358', 'answer', '标准答案', 'varchar(255) NOT NULL', 'string', '', '多个答案用空格分开，如： A B C', '1', '', '1153', '0', '1', '1397035889', '1397035889', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11359', 'answer', '回答内容', 'text NOT NULL', 'textarea', '', '', '0', '', '1154', '0', '1', '1396955766', '1396955766', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11360', 'openid', 'OpenId', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1154', '0', '1', '1396955581', '1396955581', '', '3', '', 'regex', 'get_openid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11361', 'uid', '用户UID', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '0', '', '1154', '0', '1', '1396955530', '1396955530', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11362', 'question_id', 'question_id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '4', '', '1154', '1', '1', '1396955412', '1396955392', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11363', 'cTime', '发布时间', 'int(10) UNSIGNED NOT NULL', 'datetime', '', '', '0', '', '1154', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11364', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1154', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11365', 'exam_id', 'exam_id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '4', '', '1154', '1', '1', '1396955403', '1396955369', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11366', 'score', '得分', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '0', '', '1154', '0', '1', '1397040133', '1397040133', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11367', 'follow_id', '粉丝id', 'int(10) NULL', 'num', '', '', '1', '', '1155', '0', '1', '1432619233', '1432619233', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11368', 'sports_id', '场次id', 'int(10) NULL', 'num', '', '', '1', '', '1155', '0', '1', '1432690316', '1432619261', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11369', 'count', '抽奖次数', 'int(10) NULL', 'num', '0', '', '1', '', '1155', '0', '1', '1432619288', '1432619288', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11370', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1155', '0', '1', '1435313298', '1435313298', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11371', 'cTime', '支持时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1155', '0', '1', '1432690461', '1432690461', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11372', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1155', '0', '1', '1444986759', '1444986759', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11373', 'sports_id', '活动编号', 'int(10) NULL', 'num', '', '', '1', '', '1156', '0', '1', '1432690590', '1432613794', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11374', 'award_id', '奖品编号', 'varchar(255) NULL', 'cascade', '', '', '1', '', '1156', '0', '1', '1432710935', '1432613820', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11375', 'award_num', '奖品数量', 'int(10) NULL', 'num', '', '', '1', '', '1156', '0', '1', '1432624743', '1432624743', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11376', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1156', '0', '1', '1435313078', '1435313078', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11377', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1157', '0', '1', '1435313219', '1435313219', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11378', 'draw_id', '活动编号', 'int(10) NULL', 'num', '', '', '1', '', '1157', '0', '1', '1432619092', '1432618270', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11379', 'sport_id', '场次编号', 'int(10) NULL', 'num', '', '', '1', '', '1157', '0', '1', '1432618305', '1432618305', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11380', 'award_id', '奖品编号', 'int(10) NULL', 'num', '', '', '1', '', '1157', '0', '1', '1432618336', '1432618336', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11381', 'follow_id', '粉丝id', 'int(10) NULL', 'num', '', '', '1', '', '1157', '0', '1', '1432618392', '1432618392', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11382', 'address', '地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1157', '0', '1', '1432618543', '1432618543', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11383', 'num', '获奖数', 'int(10) NULL', 'num', '0', '', '1', '', '1157', '0', '1', '1432618584', '1432618584', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11384', 'state', '兑奖状态', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:未兑奖\r\n1:已兑奖', '1157', '0', '1', '1432644421', '1432618716', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11385', 'zjtime', '中奖时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1157', '0', '1', '1432716949', '1432618837', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11386', 'djtime', '兑奖时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1157', '0', '1', '1432618922', '1432618922', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11387', 'remark', '备注', 'text NULL', 'textarea', '', '', '1', '', '1157', '0', '1', '1445056786', '1445056786', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11388', 'aim_table', '活动标识', 'varchar(255) NULL', 'string', '', '', '0', '', '1157', '0', '1', '1444966689', '1444966689', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11389', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1157', '0', '1', '1444966581', '1444966581', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11390', 'scan_code', '核销码', 'varchar(255) NULL', 'string', '', '', '1', '', '1157', '0', '1', '1446202559', '1446202559', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11391', 'title', '活动名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1158', '1', '1', '1435306559', '1435306559', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11392', 'remark', '活动描述', 'text NULL', 'textarea', '', '', '1', '', '1158', '1', '1', '1435307454', '1435307126', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11393', 'logo_img', '活动LOGO', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1158', '1', '1', '1435307446', '1435307174', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11394', 'start_time', '开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1158', '1', '1', '1435310820', '1435307277', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11395', 'end_time', '结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1158', '1', '1', '1435310830', '1435307296', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11396', 'get_prize_tip', '中奖提示信息', 'varchar(255) NULL', 'string', '', '', '1', '', '1158', '1', '1', '1435307421', '1435307411', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11397', 'no_prize_tip', '未中奖提示信息', 'varchar(255) NULL', 'string', '', '', '1', '', '1158', '1', '1', '1435307517', '1435307517', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11398', 'ctime', '活动创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1158', '0', '1', '1435307577', '1435307577', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11399', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1158', '0', '1', '1435307671', '1435307671', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11400', 'lottery_number', '抽奖次数', 'int(10) NULL', 'num', '1', '每日允许用户抽奖的机会数，小于0 为无限次', '1', '', '1158', '0', '1', '1436233580', '1435585561', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11401', 'comment_status', '评论是否需要审核', 'char(10) NULL', 'radio', '0', '', '1', '0:不审核\r\n1:审核', '1158', '0', '1', '1436155693', '1435665821', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11402', 'get_prize_count', '中奖次数', 'int(10) NULL', 'num', '1', '每用户是否允许多次中奖', '1', '', '1158', '0', '1', '1436181974', '1436181850', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11403', 'lzwg_id', '活动编号', 'int(10) NULL', 'num', '', '', '1', '', '1159', '0', '1', '1435734910', '1435734886', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11404', 'lzwg_type', '活动类型', 'char(10) NULL', 'radio', '0', '', '1', '0:投票\r\n1:调查', '1159', '0', '1', '1435734977', '1435734977', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11405', 'vote_id', '题目编号', 'int(10) NULL', 'num', '', '', '1', '', '1159', '0', '1', '1435735047', '1435735047', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11406', 'vote_type', '问题类型', 'char(10) NULL', 'radio', '1', '', '1', '0:单选\r\n1:多选', '1159', '0', '1', '1435735092', '1435735092', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11407', 'vote_limit', '最多选择几项', 'int(10) NULL', 'num', '', '', '1', '', '1159', '0', '1', '1435735172', '1435735172', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11408', 'img', '奖品图片', 'int(10) NOT NULL', 'picture', '', '', '1', '', '1160', '1', '1', '1432609211', '1432607410', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11409', 'name', '奖项名称', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1160', '1', '1', '1432621511', '1432607270', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11410', 'score', '积分数', 'int(10) NULL', 'num', '0', '虚拟奖品积分奖励', '1', '', '1160', '1', '1', '1433312545', '1433304974', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11411', 'award_type', '奖品类型', 'varchar(30) NULL', 'bool', '1', '选择奖品类别', '1', '1:实物奖品|price@show,score@hide\r\n0:虚拟奖品|price@hide,score@show', '1160', '1', '1', '1433312276', '1433303130', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11412', 'price', '商品价格', 'FLOAT(10) NULL', 'num', '0', '价格默认为0，表示未报价', '1', '', '1160', '0', '1', '1433312127', '1432607574', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11413', 'explain', '奖品说明', 'text NULL', 'textarea', '', '', '1', '', '1160', '0', '1', '1432621815', '1432607605', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11414', 'count', '奖品数量', 'int(10) NOT NULL', 'num', '', '', '1', '', '1160', '1', '1', '1447833730', '1432607983', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11415', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1160', '0', '1', '1432809831', '1432608522', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11416', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1160', '0', '1', '1435308540', '1435308540', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11417', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1160', '0', '1', '1444879923', '1444879923', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11418', 'coupon_id', '选择赠送券', 'char(50) NULL', 'select', '', '', '1', '', '1160', '0', '1', '1444893831', '1444881398', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11419', 'money', '返现金额', 'float(10) NULL', 'num', '', '', '1', '', '1160', '0', '1', '1444882709', '1444881428', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11420', 'aim_table', '活动标识', 'varchar(255) NULL', 'string', '', '', '0', '', '1160', '0', '1', '1444883071', '1444883071', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11421', 'score', '比分', 'varchar(30) NULL', 'string', '', '输入格式：4:1', '1', '', '1161', '0', '1', '1432781750', '1432556644', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11422', 'content', '说明', 'text NULL', 'textarea', '', '请输入说明', '1', '', '1161', '0', '1', '1432556696', '1432556696', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11423', 'start_time', '时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1161', '1', '1', '1432556499', '1432556499', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11424', 'visit_team', '客场球队', 'varchar(255) NULL', 'cascade', '', '', '1', 'type=db&table=sports_team', '1161', '1', '1', '1432558295', '1432556450', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11425', 'home_team', '主场球队', 'varchar(255) NULL', 'cascade', '', '', '1', 'type=db&table=sports_team', '1161', '1', '1', '1432558269', '1432556380', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11426', 'countdown', '擂鼓时长', 'int(10) NULL', 'num', '60', '擂鼓倒计的时长,单位为秒,取值范围: 10~99', '1', '', '1161', '0', '1', '1432645901', '1432642097', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11427', 'drum_count', '擂鼓次数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432642664', '1432642664', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11428', 'drum_follow_count', '擂鼓人数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432642718', '1432642718', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11429', 'home_team_support_count', '主场球队支持数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432642808', '1432642808', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11430', 'visit_team_support_count', '客场球队支持人数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432642849', '1432642849', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11431', 'home_team_drum_count', '主场球队擂鼓数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432642978', '1432642978', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11432', 'visit_team_drum_count', '客场球队擂鼓数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432644311', '1432643015', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11433', 'yaotv_count', '摇一摇总次', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432884957', '1432784354', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11434', 'draw_count', '抽奖总次数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432887571', '1432784416', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11435', 'is_finish', '是否已结束', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:未结束\r\n1:已结束', '1161', '0', '1', '1432868975', '1432868975', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11436', 'yaotv_follow_count', '摇电视总人数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432884721', '1432884721', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11437', 'draw_follow_count', '抽奖总人数', 'int(10) NULL', 'num', '0', '', '0', '', '1161', '0', '1', '1432887553', '1432887553', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11438', 'comment_status', '评论是否需要审核', 'tinyint(2) NULL', 'radio', '0', '', '1', '0:不审核\r\n1:审核', '1161', '0', '1', '1435109668', '1435030411', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11439', 'sports_id', '场次ID', 'int(10) NULL', 'num', '', '', '0', '', '1162', '0', '1', '1432642290', '1432642290', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11440', 'team_id', '球队ID', 'int(10) NULL', 'num', '', '', '0', '', '1162', '0', '1', '1432642312', '1432642312', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11441', 'follow_id', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1162', '0', '1', '1432642354', '1432642354', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11442', 'drum_count', '擂鼓次数', 'int(10) NULL', 'num', '', '', '0', '', '1162', '0', '1', '1432642384', '1432642384', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11443', 'cTime', '时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1162', '0', '1', '1432642409', '1432642409', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11444', 'sports_id', '场次ID', 'int(10) NULL', 'num', '', '', '0', '', '1163', '0', '1', '1432635120', '1432635120', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11445', 'team_id', '球队ID', 'int(10) NULL', 'num', '', '', '0', '', '1163', '0', '1', '1432635147', '1432635147', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11446', 'follow_id', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1163', '0', '1', '1432635168', '1432635168', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11447', 'cTime', '支持时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1163', '0', '1', '1432635202', '1432635202', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11448', 'sort', '排序号', 'int(10) NULL', 'num', '0', '', '0', '', '1164', '0', '1', '1432559360', '1432559360', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11449', 'intro', '球队说明', 'text  NULL', 'textarea', '', '', '1', '', '1164', '0', '1', '1432557159', '1432556960', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11450', 'pid', 'pid', 'int(10) NULL', 'num', '0', '', '0', '', '1164', '0', '1', '1432557085', '1432557085', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11451', 'logo', '球队图标', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1164', '1', '1', '1432556913', '1432556913', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11452', 'title', '球队名称', 'varchar(100) NULL', 'string', '', '请输入球队名称', '1', '', '1164', '1', '1', '1432958716', '1432556869', 'unique', '3', '球队名称不能重复', 'unique', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11453', 'title', '活动名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1165', '1', '1', '1444877324', '1444877324', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11454', 'game_type', '游戏类型', 'char(10) NULL', 'radio', '1', '', '1', '1:刮刮乐\r\n2:大转盘\r\n3:砸金蛋\r\n4:九宫格', '1165', '1', '1', '1444877425', '1444877425', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11455', 'status', '状态', 'char(10) NULL', 'radio', '1', '', '1', '1:开启\r\n0:禁用', '1165', '0', '1', '1444877482', '1444877468', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11456', 'start_time', '开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1165', '1', '1', '1444877509', '1444877509', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11457', 'end_time', '结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1165', '1', '1', '1444877530', '1444877530', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11458', 'day_attend_limit', '每人每天抽奖次数', 'int(10) NULL', 'num', '0', '0，则不限制，超过此限制点击抽奖，系统会提示“您今天的抽奖次数已经用完!”', '1', '', '1165', '0', '1', '1444879540', '1444878111', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11459', 'attend_limit', '每人总共抽奖次数', 'int(10) NULL', 'num', '0', '0，则不限制；否则必须>=每人每天抽奖次数，超过此限制点击抽奖，系统会提示“您的所有抽奖次数已用完!”', '1', '', '1165', '0', '1', '1444879552', '1444878167', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11460', 'day_win_limit', '每人每天中奖次数', 'int(10) NULL', 'num', '0', '0，则不限制，超过此限制点击抽奖，抽奖者将无概率中奖', '1', '', '1165', '0', '1', '1444879608', '1444878254', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11461', 'win_limit', '每人总共中奖次数', 'int(10) NULL', 'num', '0', '0，则不限制；否则必须>=每人每天中奖次数，超过此限制点击抽奖，抽奖者将无概率中奖', '1', '', '1165', '0', '1', '1444879656', '1444878336', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11462', 'day_winners_count', '每天最多中奖人数', 'int(10) NULL', 'num', '0', '0，则不限制，超过此限制时，系统会提示“今天奖品已抽完，明天再来吧!”', '1', '', '1165', '0', '1', '1444879673', '1444878419', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11463', 'url', '关注链接', 'varchar(300) NULL', 'string', '', '', '0', '', '1165', '0', '1', '1445068488', '1444878621', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11464', 'remark', '活动说明', 'text NULL', 'textarea', '', '', '1', '', '1165', '0', '1', '1444878676', '1444878676', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11465', 'keyword', '微信关键词', 'varchar(255) NULL', 'string', '', '', '1', '', '1165', '1', '1', '1444878722', '1444878722', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11466', 'attend_num', '参与总人数', 'int(10) NULL', 'num', '0', '', '0', '', '1165', '0', '1', '1444878774', '1444878774', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11467', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1165', '0', '1', '1444878837', '1444878837', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11468', 'manager_id', '管理员id', 'int(10) NULL', 'num', '', '', '0', '', '1165', '0', '1', '1444878900', '1444878900', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11469', 'award_id', '奖品id', 'int(10) NULL', 'num', '', '', '1', '', '1166', '1', '1', '1444901378', '1444900999', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11470', 'games_id', '抽奖游戏id', 'int(10) NULL', 'num', '', '', '1', '', '1166', '1', '1', '1444901386', '1444901037', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11471', 'grade', '中奖等级', 'varchar(255) NULL', 'string', '', '', '1', '', '1166', '1', '1', '1444901399', '1444901079', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11472', 'num', '奖品数量', 'int(10) NULL', 'num', '', '', '1', '', '1166', '1', '1', '1444901364', '1444901364', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11473', 'max_count', '最多抽奖', 'int(10) NULL', 'num', '', 'n次,把奖品发放完, 不能小于奖品数量', '1', '', '1166', '0', '1', '1444901486', '1444901486', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11474', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1166', '0', '1', '1444901512', '1444901512', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11475', 'follow_id', '粉丝id', 'int(10) NULL', 'num', '', '', '1', '', '1167', '0', '1', '1432619233', '1432619233', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11476', 'sports_id', '场次id', 'int(10) NULL', 'num', '', '', '1', '', '1167', '0', '1', '1432690316', '1432619261', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11477', 'count', '抽奖次数', 'int(10) NULL', 'num', '0', '', '1', '', '1167', '0', '1', '1432619288', '1432619288', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11478', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1167', '0', '1', '1435313298', '1435313298', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11479', 'cTime', '支持时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1167', '0', '1', '1432690461', '1432690461', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11480', 'keyword_type', '关键词匹配类型', 'tinyint(2) NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配', '1168', '0', '1', '1394268247', '1393921586', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11481', 'api_token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1168', '0', '1', '1393922455', '1393912408', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11482', 'cTime', '创建时间', 'int(10) NOT NULL', 'datetime', '', '', '0', '', '1168', '0', '1', '1393913608', '1393913608', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11483', 'api_url', '第三方URL', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1168', '0', '1', '1393912354', '1393912354', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11484', 'output_format', '数据输出格式', 'tinyint(1) NULL', 'select', '0', '', '1', '0:标准微信xml\r\n1:json格式', '1168', '0', '1', '1394268422', '1393912288', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11485', 'keyword_filter', '关键词过滤', 'tinyint(2) NOT NULL', 'bool', '0', '如设置电影为触发词,用户输入 电影 美国派 时，如果启用过滤只将美国派这个词发送到的你的接口，如果不过滤 就是整个 电影 美国派全部发送到的接口', '1', '0:不过滤\r\n1:过滤', '1168', '0', '1', '1394268410', '1393912057', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11486', 'keyword', '关键词', 'varchar(255) NOT NULL', 'string', '', '多个关键词请用空格格开', '1', '', '1168', '1', '1', '1393912492', '1393911842', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11487', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1168', '0', '1', '1402454223', '1402454223', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11488', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1169', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11489', 'cTime', '发布时间', 'int(10) UNSIGNED NULL', 'datetime', '', '', '0', '', '1169', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11490', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1169', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11491', 'password', '表单密码', 'varchar(255) NULL', 'string', '', '如要用户输入密码才能进入表单，则填写此项。否则留空，用户可直接进入表单', '0', '', '1169', '0', '1', '1396871497', '1396672643', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11313', 'sort', '排序号', 'int(10) NULL ', 'num', '0', '数值越小越靠前', '1', '', '1148', '0', '1', '1396340334', '1396340334', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11308', 'title', '分类标题', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1148', '1', '1', '1408950771', '1395988016', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11291', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1145', '0', '1', '1396603007', '1396603007', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11290', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '', '1', '', '1145', '0', '1', '1396580674', '1396580674', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11289', 'view_count', '浏览数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1145', '0', '1', '1396580643', '1396580643', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11288', 'content', '回复内容', 'text NULL', 'textarea', '', '请不要多于1000字否则无法发送。支持加超链接，但URL必须带http://', '1', '', '1145', '0', '1', '1396607362', '1396578597', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('723', 'sn', 'SN码', 'varchar(255) NULL', 'string', '', '', '0', '', '81', '0', '1', '1399272236', '1399272228', '', '3', '', 'regex', 'uniqid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('724', 'uid', '粉丝UID', 'int(10) NULL', 'num', '', '', '0', '', '81', '0', '1', '1399772738', '1399272401', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('725', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '81', '0', '1', '1399272456', '1399272456', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('726', 'is_use', '是否已使用', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:未使用\r\n1:已使用', '81', '0', '1', '1400601159', '1399272514', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('727', 'use_time', '使用时间', 'int(10) NULL', 'datetime', '', '', '0', '', '81', '0', '1', '1399272560', '1399272537', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('728', 'addon', '来自的插件', 'varchar(255) NULL', 'string', 'Coupon', '', '4', '', '81', '0', '1', '1399272651', '1399272651', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('729', 'target_id', '来源ID', 'int(10) unsigned NULL ', 'num', '', '', '4', '', '81', '0', '1', '1399272705', '1399272705', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('730', 'prize_id', '奖项ID', 'int(10) unsigned NULL ', 'num', '', '', '0', '', '81', '0', '1', '1399686317', '1399686317', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('731', 'prize_title', '奖项', 'varchar(255) NULL', 'string', '', '', '1', '', '81', '0', '1', '1399790367', '1399790367', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('732', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '81', '0', '1', '1404525481', '1404525481', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('733', 'can_use', '是否可用', 'tinyint(2) NULL', 'bool', '1', '', '0', '0:不可用\r\n1:可用', '81', '0', '1', '1418890020', '1418890020', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('734', 'server_addr', '服务器IP', 'varchar(50) NULL', 'string', '', '', '1', '', '81', '0', '1', '1425807865', '1425807865', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('776', 'title', '应用标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '87', '1', '1', '1402758132', '1394033402', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('777', 'uid', '用户ID', 'int(10) NULL ', 'num', '0', '', '0', '', '87', '0', '1', '1394087733', '1394033447', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('778', 'content', '应用详细介绍', 'text NULL ', 'editor', '', '', '1', '', '87', '1', '1', '1402758118', '1394033484', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('779', 'cTime', '发布时间', 'int(10) NULL ', 'datetime', '', '', '0', '', '87', '0', '1', '1394033571', '1394033571', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('780', 'attach', '应用压缩包', 'varchar(255) NULL ', 'file', '', '需要上传zip文件', '1', '', '87', '0', '1', '1402758100', '1394033674', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('781', 'is_top', '置顶', 'int(10) NULL ', 'bool', '0', '0表示不置顶，否则其它值表示置顶且值是置顶的时间', '1', '0:不置顶\r\n1:置顶', '87', '0', '1', '1402800009', '1394068787', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('782', 'cid', '分类', 'tinyint(4) NULL ', 'select', '', '', '0', '1:基础模块\r\n2:行业模块\r\n3:会议活动\r\n4:娱乐模块\r\n5:其它模块', '87', '0', '1', '1402758069', '1394069964', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('783', 'view_count', '浏览数', 'int(11) unsigned NULL ', 'num', '0', '', '0', '', '87', '0', '1', '1394072168', '1394072168', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('784', 'download_count', '下载数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '87', '0', '1', '1394085763', '1394085763', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('785', 'img_2', '应用截图2', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '87', '0', '1', '1402758035', '1394084714', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('786', 'img_1', '应用截图1', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '87', '0', '1', '1402758046', '1394084635', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('787', 'img_3', '应用截图3', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '87', '0', '1', '1402758021', '1394084757', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('788', 'img_4', '应用截图4', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '87', '0', '1', '1402758011', '1394084797', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('789', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '1', '', '88', '0', '1', '1430880974', '1430880974', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('790', 'name', '素材名称', 'varchar(100) NULL', 'string', '', '', '1', '', '88', '0', '1', '1424612322', '1424611929', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('791', 'status', '状态', 'char(10) NULL', 'radio', 'UnSubmit', '', '1', 'UnSubmit:未提交\r\nWaiting:入库中\r\nSuccess:入库成功\r\nFailure:入库失败', '88', '0', '1', '1424612039', '1424612039', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('792', 'cTime', '提交时间', 'int(10) NULL', 'datetime', '', '', '1', '', '88', '0', '1', '1424612114', '1424612114', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('793', 'url', '实际摇一摇所使用的页面URL', 'varchar(255) NULL', 'string', '', '', '1', '', '88', '0', '1', '1424612483', '1424612154', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('794', 'type', '素材类型', 'varchar(255) NULL', 'string', '', '', '1', '', '88', '0', '1', '1424612421', '1424612421', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('795', 'detail', '素材内容', 'text NULL', 'textarea', '', '', '1', '', '88', '0', '1', '1424612456', '1424612456', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('796', 'reason', '入库失败的原因', 'text NULL', 'textarea', '', '', '1', '', '88', '0', '1', '1424612509', '1424612509', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('797', 'create_time', '申请时间', 'int(10) NULL', 'datetime', '', '', '1', '', '88', '0', '1', '1424612542', '1424612542', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('798', 'checked_time', '入库时间', 'int(10) NULL', 'datetime', '', '', '1', '', '88', '0', '1', '1424612571', '1424612571', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('799', 'source', '来源', 'varchar(50) NULL', 'string', '', '', '1', '', '88', '0', '1', '1424836818', '1424836818', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('800', 'source_id', '来源ID', 'int(10) NULL', 'num', '', '', '1', '', '88', '0', '1', '1424836842', '1424836842', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('801', 'wechat_id', '微信端的素材ID', 'int(10) NULL', 'num', '', '', '0', '', '88', '0', '1', '1425370605', '1425370605', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('802', 'uid', '管理员id', 'int(10) NULL', 'num', '', '', '1', '', '89', '0', '1', '1431575588', '1431575588', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('803', 'token', '用户token', 'varchar(255) NULL', 'string', '', '', '1', '', '89', '0', '1', '1431575617', '1431575617', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('804', 'addons', '插件名称', 'varchar(255) NULL', 'string', '', '', '1', '', '89', '0', '1', '1431590322', '1431575667', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('805', 'template', '模版名称', 'varchar(255) NULL', 'string', '', '', '1', '', '89', '0', '1', '1431575691', '1431575691', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('832', 'title', '公告标题', 'varchar(255) NULL', 'string', '', '', '1', '', '93', '1', '1', '1431143985', '1431143985', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('833', 'content', '公告内容', 'text  NULL', 'editor', '', '', '1', '', '93', '1', '1', '1431144020', '1431144020', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('834', 'create_time', '发布时间', 'int(10) NULL', 'datetime', '', '', '4', '', '93', '0', '1', '1431146373', '1431144069', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('835', 'version', '版本号', 'int(10) unsigned NOT NULL ', 'num', '', '', '1', '', '94', '1', '1', '1393770457', '1393770457', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('836', 'title', '升级包名', 'varchar(50) NOT NULL', 'string', '', '', '1', '', '94', '1', '1', '1393770499', '1393770499', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('837', 'description', '描述', 'text NULL', 'textarea', '', '', '1', '', '94', '0', '1', '1393770546', '1393770546', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('838', 'create_date', '创建时间', 'int(10) NULL', 'datetime', '', '', '1', '', '94', '0', '1', '1393770591', '1393770591', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('839', 'download_count', '下载统计', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '94', '0', '1', '1393770659', '1393770659', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('840', 'package', '升级包地址', 'varchar(255) NOT NULL', 'textarea', '', '', '1', '', '94', '1', '1', '1393812247', '1393770727', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11980', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1221', '0', '1', '1431659474', '1431659474', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11979', 'background', '背景图', 'int(10) UNSIGNED NULL', 'picture', '', '图片尺寸建议是760X421 并且主要内容要居中并留出大转盘位置', '1', '', '1221', '0', '1', '1419997464', '1419997464', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11977', 'start_date', '开始时间', 'int(10) NULL ', 'datetime', '', '', '1', '', '1221', '0', '1', '1395395676', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11978', 'experience', '消耗经验值', 'int(10) NULL', 'num', '0', '', '1', '', '1221', '0', '1', '1419299966', '1419299966', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11976', 'keyword', '关键词', 'varchar(255) NULL ', 'string', '', '用户发送 “关键词” 触发', '1', '', '1221', '0', '1', '1395395713', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11975', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1221', '0', '1', '1395396571', '1395396571', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11974', 'des_jj', '活动介绍', 'text NULL ', 'textarea', '', '活动介绍简介 用于给用户发送消息时候的图文描述', '1', '', '1221', '0', '1', '1431068323', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11973', 'des', '活动介绍', 'text NULL ', 'textarea', '', '', '0', '', '1221', '0', '1', '1431068356', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11972', 'choujnum', '每日抽奖次数', 'int(10) unsigned NULL ', 'num', '0', '', '1', '', '1221', '0', '1', '1395395485', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11971', 'guiz', '活动规则', 'text NULL ', 'textarea', '', '', '1', '', '1221', '0', '1', '1418369751', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11970', 'title', '活动标题', 'varchar(255) NULL ', 'string', '', '', '1', '', '1221', '0', '1', '1395395535', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11969', 'picurl', '封面图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1221', '1', '1', '1439370422', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11968', 'states', '活动状态', 'char(10) NULL ', 'radio', '0', '', '0', '0:未开始\r\n1:已结束', '1221', '0', '1', '1395395602', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11967', 'cTime', '活动创建时间', 'int(10) NULL ', 'datetime', '', '', '0', '', '1221', '0', '1', '1395395963', '1395395179', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11966', 'end_date', '结束日期', 'int(10) NULL ', 'datetime', '', '', '1', '', '1221', '0', '1', '1395395670', '1395395179', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11965', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1220', '0', '1', '1430132994', '1430132994', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11964', 'prize_title', '活动标题', 'varchar(255) NULL', 'string', '', '', '1', '', '1220', '1', '1', '1429855569', '1429855569', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11963', 'use_content', '使用说明', 'text NULL', 'textarea', '', '用户领取成功后才会看到', '1', '', '1220', '1', '1', '1429757185', '1429757185', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11960', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1220', '0', '1', '1429521039', '1429521039', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11961', 'fail_content', '领取失败提示', 'text NULL', 'textarea', '', '用户领取失败，或者没有领取到时看到的提示', '1', '', '1220', '1', '1', '1429860149', '1429860149', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11962', 'prize_type', '奖品类型', 'tinyint(2) NULL', 'radio', '1', '选择奖品类型', '1', '1:实物\r\n0:虚拟', '1220', '1', '1', '1429756998', '1429756539', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('896', 'ToUserName', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143065', '1438143065', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('897', 'FromUserName', 'OpenID', 'varchar(100) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143098', '1438143098', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('898', 'CreateTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '103', '0', '1', '1438143120', '1438143120', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('899', 'MsgType', '消息类型', 'varchar(30) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143139', '1438143139', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('900', 'MsgId', '消息ID', 'varchar(100) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143182', '1438143182', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('901', 'Content', '文本消息内容', 'text NULL', 'textarea', '', '', '0', '', '103', '0', '1', '1438143218', '1438143218', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('902', 'PicUrl', '图片链接', 'varchar(255) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143273', '1438143273', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('903', 'MediaId', '多媒体文件ID', 'varchar(100) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143357', '1438143357', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('904', 'Format', '语音格式', 'varchar(30) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143397', '1438143397', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('905', 'ThumbMediaId', '缩略图的媒体id', 'varchar(30) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143445', '1438143426', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('906', 'Title', '消息标题', 'varchar(100) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143471', '1438143471', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('907', 'Description', '消息描述', 'text NULL', 'textarea', '', '', '0', '', '103', '0', '1', '1438143535', '1438143535', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('908', 'Url', 'Url', 'varchar(255) NULL', 'string', '', '', '0', '', '103', '0', '1', '1438143558', '1438143558', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('909', 'collect', '收藏状态', 'tinyint(1) NULL', 'bool', '0', '', '0', '0:未收藏\r\n1:已收藏', '103', '0', '1', '1438153936', '1438153936', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('910', 'deal', '处理状态', 'tinyint(1) NULL', 'bool', '0', '', '0', '0:未处理\r\n1:已处理', '103', '0', '1', '1438165005', '1438153991', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('911', 'is_read', '是否已读', 'tinyint(1) NULL', 'bool', '0', '', '1', '0:未读\r\n1:已读', '103', '0', '1', '1438165062', '1438165062', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('912', 'type', '消息分类', 'tinyint(1) NULL', 'bool', '0', '', '1', '0:用户消息\r\n1:管理员回复消息', '103', '0', '1', '1438168301', '1438168301', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12002', 'num', '库存数量', 'int(10) unsigned NULL ', 'num', '0', '', '1', '', '1224', '0', '1', '1396667941', '1395395190', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('12001', 'miaoshu', '奖品描述', 'text NULL ', 'textarea', '', '', '1', '', '1224', '0', '1', '1418628021', '1395395190', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('12000', 'pic', '奖品图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1224', '0', '1', '1395495279', '1395395190', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('12008', 'uid', '用户id', 'varchar(255) NULL', 'string', '', '用户id', '0', '', '1225', '0', '1', '1395567404', '1395567404', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11998', 'duijma', '兑奖码', 'text NULL', 'textarea', '', '请输入兑奖码，一行一个', '0', '', '1224', '0', '1', '1419300292', '1396253842', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11999', 'title', '奖品名称', 'varchar(255) NULL ', 'string', '', '', '1', '', '1224', '0', '1', '1395495283', '1395395190', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('1062', 'login_name', 'login_name', 'varchar(100) NULL', 'string', '', '', '1', '', '1', '0', '1', '1447302647', '1439978705', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1063', 'content', '文本消息内容', 'text NULL', 'textarea', '', '', '0', '', '18', '0', '1', '1439980070', '1439980070', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11986', 'gailv_maxnum', '单日发放上限', 'int(10) UNSIGNED NULL', 'num', '0', '每天最多发放奖品的数量', '1', '', '1222', '0', '1', '1395559281', '1395559281', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11987', 'xydzp_option_id', '幸运大转盘关联的全局奖品id', 'int(10) UNSIGNED NULL', 'num', '', '幸运大转盘关联的全局奖品id', '0', '', '1222', '0', '1', '1395555085', '1395555085', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11988', 'uid', '用户openid', 'varchar(255) NULL', 'string', '', '', '0', '', '1223', '0', '1', '1396686415', '1396686415', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11989', 'message', '留言', 'text NULL ', 'string', '', '', '1', '', '1223', '0', '1', '1395395200', '1395395200', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11990', 'address', '收件地址', 'text NULL ', 'string', '', '', '1', '', '1223', '0', '1', '1395395200', '1395395200', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('1071', 'is_bind', '是否为微信开放平台绑定账号', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:否\r\n1:是', '6', '0', '1', '1440746890', '1440746890', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11984', 'jlnum', '奖励数量', 'int(10) UNSIGNED NULL', 'num', '1', '中奖后，获得该奖品的数量', '0', '', '1222', '0', '1', '1419303776', '1395559386', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11985', 'type', '奖品中奖方式', 'char(50) NULL', 'select', '0', '选择奖品中奖的方式', '0', '0:按概率中奖\r\n1:按时间中奖(未启用)\r\n2:按顺序中奖(未启用)\r\n3:按指定用户id中奖(未启用)', '1222', '0', '1', '1419303723', '1395559102', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11983', 'xydzp_id', '幸运大转盘关联的活动id', 'int(10) UNSIGNED NULL', 'num', '0', '幸运大转盘关联的活动id', '0', '', '1222', '0', '1', '1395555019', '1395555019', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11878', 'cover', '封面图片', 'int(10) unsigned NULL ', 'picture', '', '可为空', '1', '', '1210', '0', '1', '1399710705', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11877', 'end_time', '结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1210', '0', '1', '1399259433', '1399259433', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11876', 'intro', '封面简介', 'text NULL', 'editor', '', '', '1', '', '1210', '0', '1', '1420983308', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11875', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1210', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11869', 'num', '名额数量', 'int(10) unsigned NULL ', 'num', '', '', '1', '', '1209', '1', '1', '1439370137', '1399348843', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11870', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '值越小越靠前', '1', '', '1209', '0', '1', '1399557716', '1399557716', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11871', 'img', '奖品图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1209', '0', '1', '1399557997', '1399557997', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11872', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1209', '0', '1', '1404525428', '1404525428', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11873', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1210', '1', '1', '1396624337', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11874', 'use_tips', '使用说明', 'varchar(255) NOT NULL', 'textarea', '', '用户获取刮刮卡后显示的提示信息', '1', '', '1210', '1', '1', '1420989679', '1399259489', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1101', 'url', '图文页url', 'varchar(255) NULL', 'string', '', '', '0', '', '17', '0', '1', '1441077355', '1441077355', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11991', 'iphone', '电话', 'varchar(255) NULL ', 'string', '', '', '1', '', '1223', '0', '1', '1395395200', '1395395200', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11992', 'zip', '邮编', 'int(10) unsigned NULL ', 'string', '', '', '1', '', '1223', '0', '1', '1395395200', '1395395200', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11993', 'state', '领奖状态', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:未领取\r\n1:已领取', '1223', '0', '1', '1396705093', '1395395200', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11994', 'xydzp_option_id', '奖品id', 'int(10) unsigned NULL ', 'string', '0', '', '1', '', '1223', '0', '1', '1395395200', '1395395200', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11995', 'xydzp_id', '活动id', 'int(10) unsigned NULL ', 'string', '0', '', '1', '', '1223', '0', '1', '1395395200', '1395395200', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11996', 'zjdate', '中奖时间', 'int(10) UNSIGNED NULL', 'num', '', '', '0', '', '1223', '0', '1', '1396191999', '1396191999', '', '3', '', 'regex', 'time()', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11997', 'jptype', '奖品类型', 'char(10) NULL ', 'select', '0', '奖品的类型', '1', '0:经验值|coupon_id@hide,experience@show,num@show,card_url@hide\r\n1:优惠券|coupon_id@show,experience@hide,num@show,card_url@hide\r\n2:谢谢参与|coupon_id@hide,experience@hide,num@hide,card_url@hide\r\n3:微信卡券|coupon_id@hide,experience@hide,num@show,card_url@show', '1224', '0', '1', '1420207419', '1395395190', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1825', 'ToUserName', 'token', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447241964', '1447241964', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1826', 'FromUserName', 'openid', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447242006', '1447242006', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1827', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '1', '', '201', '0', '1', '1447242030', '1447242030', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1828', 'msgType', '消息类型', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447242059', '1447242059', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1829', 'manager_id', '管理员id', 'int(10) NULL', 'num', '', '', '1', '', '201', '0', '1', '1447242090', '1447242090', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1830', 'content', '内容', 'text NULL', 'textarea', '', '', '1', '', '201', '0', '1', '1447242120', '1447242120', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1831', 'media_id', '多媒体文件id', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447242146', '1447242146', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1832', 'is_send', '是否已经发送', 'int(10) NULL', 'num', '', '', '1', '0:未发\r\n1:已发', '201', '0', '1', '1447242181', '1447242181', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1833', 'uid', '粉丝uid', 'int(10) NULL', 'num', '', '', '1', '', '201', '0', '1', '1447242202', '1447242202', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1834', 'news_group_id', '图文组id', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447242229', '1447242229', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1835', 'video_title', '视频标题', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447242267', '1447242267', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1836', 'video_description', '视频描述', 'text NULL', 'textarea', '', '', '1', '', '201', '0', '1', '1447242291', '1447242291', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1837', 'video_thumb', '视频缩略图', 'varchar(255) NULL', 'string', '', '', '1', '', '201', '0', '1', '1447242366', '1447242366', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1844', 'voice_id', '语音id', 'int(10) NULL', 'num', '', '', '1', '', '201', '0', '1', '1447242400', '1447242400', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1845', 'image_id', '图片id', 'int(10) NULL', 'num', '', '', '1', '', '201', '0', '1', '1447242440', '1447242440', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1846', 'video_id', '视频id', 'int(10) NULL', 'num', '', '', '1', '', '201', '0', '1', '1447242464', '1447242464', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1847', 'send_type', '发送方式', 'int(10) NULL', 'num', '', '', '1', '0:分组\r\n1:指定用户', '201', '0', '1', '1447242498', '1447242498', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1848', 'send_opends', '指定用户', 'text NULL', 'textarea', '', '', '1', '', '201', '0', '1', '1447242529', '1447242529', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1849', 'group_id', '分组id', 'int(10) NULL', 'num', '', '', '1', '', '201', '0', '1', '1447242553', '1447242553', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1850', 'diff', '区分消息标识', 'int(10) NULL', 'num', '0', '', '1', '', '201', '0', '1', '1447242584', '1447242584', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1326', 'content', '文本内容', 'text NULL', 'textarea', '', '', '1', '', '148', '1', '1', '1442976151', '1442976151', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1327', 'token', 'Token', 'varchar(50) NULL', 'string', '', '', '0', '', '148', '0', '1', '1442978004', '1442978004', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1328', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '148', '0', '1', '1442978041', '1442978041', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1820', 'is_use', '可否使用', 'int(10) NULL', 'num', '1', '', '0', '0:不可用\r\n1:可用', '148', '0', '1', '1445496947', '1445496947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1821', 'aim_id', '添加来源标识id', 'int(10) NULL', 'num', '', '', '0', '', '148', '0', '1', '1445497010', '1445497010', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1822', 'aim_table', '来源表名', 'varchar(255) NULL', 'string', '', '', '0', '', '148', '0', '1', '1445497218', '1445497218', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1329', 'file_id', '上传文件', 'int(10) NULL', 'file', '', '', '1', '', '149', '0', '1', '1442982169', '1438684652', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1330', 'cover_url', '本地URL', 'varchar(255) NULL', 'string', '', '', '0', '', '149', '0', '1', '1438684692', '1438684692', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1331', 'media_id', '微信端图文消息素材的media_id', 'varchar(100) NULL', 'string', '0', '', '0', '', '149', '0', '1', '1438744962', '1438684776', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1332', 'wechat_url', '微信端的文件地址', 'varchar(255) NULL', 'string', '', '', '0', '', '149', '0', '1', '1439973558', '1438684807', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1333', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '149', '0', '1', '1443004484', '1438684829', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1334', 'manager_id', '管理员ID', 'int(10) NULL', 'num', '', '', '0', '', '149', '0', '1', '1442982446', '1438684847', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1335', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '149', '0', '1', '1442982460', '1438684865', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1336', 'title', '素材名称', 'varchar(100) NULL', 'string', '', '', '1', '', '149', '0', '1', '1442981165', '1442981165', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1341', 'type', '类型', 'int(10) NULL', 'num', '', '', '0', '1:语音素材\r\n2:视频素材', '149', '0', '1', '1445599238', '1443006101', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1838', 'introduction', '描述', 'text NULL', 'textarea', '', '', '0', '', '149', '0', '1', '1447299133', '1445684769', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1121', 'level', '管理等级', 'tinyint(2) NULL', 'num', '0', '', '0', '', '1', '0', '1', '1441522953', '1441522953', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12007', 'card_url', '领取卡券的地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1224', '0', '1', '1420207297', '1420207297', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12009', 'xydzp_id', '幸运大转盘关联的活动id', 'int(10) UNSIGNED NULL', 'num', '', '幸运大转盘关联的活动id', '0', '', '1225', '0', '1', '1395567452', '1395567452', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12010', 'num', '已经抽奖的次数', 'int(10) UNSIGNED NULL', 'num', '0', '', '1', '', '1225', '0', '1', '1395567486', '1395567486', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12011', 'cjdate', '抽奖日期', 'int(10) NULL', 'datetime', '', '', '1', '', '1225', '0', '1', '1395567537', '1395567537', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12012', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1226', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12013', 'cTime', '发布时间', 'int(10) UNSIGNED NULL', 'datetime', '', '', '0', '', '1226', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12014', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1226', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('12015', 'password', '微预约密码', 'varchar(255) NULL', 'string', '', '如要用户输入密码才能进入微预约，则填写此项。否则留空，用户可直接进入微预约', '0', '', '1226', '0', '1', '1396871497', '1396672643', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12016', 'jump_url', '提交后跳转的地址', 'varchar(255) NULL', 'string', '', '要以http://开头的完整地址，为空时不跳转', '1', '', '1226', '0', '1', '1402458121', '1399800276', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12017', 'content', '详细介绍', 'text NULL', 'editor', '', '可不填', '1', '', '1226', '0', '1', '1396865295', '1396865295', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12018', 'finish_tip', '用户提交后提示内容', 'text NULL', 'textarea', '', '为空默认为：提交成功，谢谢参与', '1', '', '1226', '0', '1', '1396676366', '1396673689', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12019', 'can_edit', '是否允许编辑', 'tinyint(2) NULL', 'bool', '0', '用户提交预约是否可以再编辑', '1', '0:不允许\r\n1:允许', '1226', '0', '1', '1396688624', '1396688624', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11744', 'cover', '封面图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1193', '1', '1', '1439368240', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11743', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1193', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11742', 'intro', '封面简介', 'text NULL', 'textarea', '', '', '1', '', '1193', '0', '1', '1396624505', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11741', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1193', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11739', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1193', '1', '1', '1396624337', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11740', 'keyword_type', '关键词类型', 'tinyint(2) NOT NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1193', '1', '1', '1396624426', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11982', 'gailv_str', '参数', 'varchar(255) NULL', 'string', '', '请输入对应中奖方式的相应值 多个以英文状态下的 逗号(,)分隔', '0', '', '1222', '0', '1', '1419303819', '1395559219', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11981', 'gailv', '中奖概率', 'int(10) UNSIGNED NULL', 'num', '0', '请输入0-100之间的整数', '1', '', '1222', '0', '1', '1419303857', '1395559151', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1112', 'login_password', '登录密码', 'varchar(255) NULL', 'string', '', '', '1', '', '1', '0', '1', '1441187439', '1441187439', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1114', 'manager_id', '公众号管理员ID', 'int(10) NULL', 'num', '0', '', '0', '', '1', '0', '1', '1441512815', '1441512815', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12005', 'coupon_id', '优惠券编号', 'int(10) NULL', 'num', '', '', '1', '', '1224', '0', '1', '1419300336', '1419300336', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12006', 'experience', '奖励经验值', 'int(10) NULL', 'num', '0', '', '1', '', '1224', '0', '1', '1419300396', '1419300396', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12004', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1224', '0', '1', '1395554191', '1395554191', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('12003', 'isdf', '是否为谢谢惠顾类', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:中奖品\r\n1:该奖为谢谢惠顾类', '1224', '0', '1', '1419392345', '1396191564', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11867', 'title', '奖项标题', 'varchar(255) NULL', 'string', '', '如特等奖、一等奖。。。', '1', '', '1209', '1', '1', '1439370111', '1399348734', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11868', 'name', '奖项', 'varchar(255) NULL', 'string', '', '如iphone、吹风机等', '1', '', '1209', '1', '1', '1439370125', '1399348785', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11757', 'survey_id', 'survey_id', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '1194', '1', '1', '1396955403', '1396955369', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11753', 'uid', '用户UID', 'int(10) NULL ', 'num', '', '', '0', '', '1194', '0', '1', '1396955530', '1396955530', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11754', 'question_id', 'question_id', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '1194', '1', '1', '1396955412', '1396955392', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11755', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1194', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11756', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1194', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11745', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1193', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11746', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1193', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11747', 'finish_tip', '结束语', 'text NULL', 'string', '', '为空默认为：调研完成，谢谢参与', '1', '', '1193', '0', '1', '1447640072', '1396953940', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11748', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1193', '0', '1', '1430193696', '1430193696', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11749', 'start_time', '开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1193', '1', '1', '1440408604', '1440407931', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11750', 'end_time', '结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1193', '1', '1', '1440408598', '1440407951', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11751', 'answer', '回答内容', 'text NULL', 'textarea', '', '', '0', '', '1194', '0', '1', '1396955766', '1396955766', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11752', 'openid', 'OpenId', 'varchar(255) NULL', 'string', '', '', '0', '', '1194', '0', '1', '1396955581', '1396955581', '', '3', '', 'regex', 'get_openid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1155', 'membership', '会员等级', 'char(50) NULL', 'select', '0', '请在会员等级 添加会员级别名称', '0', '', '1', '0', '1', '1447302405', '1441795509', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11758', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1195', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11759', 'intro', '问题描述', 'text NULL', 'textarea', '', '', '1', '', '1195', '0', '1', '1396954176', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11760', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1195', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11761', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1195', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11762', 'is_must', '是否必填', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:否\r\n1:是', '1195', '0', '1', '1396954649', '1396954649', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11763', 'extra', '参数', 'text NULL', 'textarea', '', '类型为单选、多选时的定义数据，格式见上面的提示', '1', '', '1195', '0', '1', '1396954558', '1396954558', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11764', 'type', '问题类型', 'char(50) NOT NULL', 'radio', 'radio', '', '1', 'radio:单选题\r\ncheckbox:多选题\r\ntextarea:简答题', '1195', '1', '1', '1396962517', '1396954463', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11765', 'survey_id', 'survey_id', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '1195', '1', '1', '1396954240', '1396954240', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11766', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '值越小越靠前', '1', '', '1195', '0', '1', '1396955010', '1396955010', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11767', 'title', '商店名称', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1196', '1', '1', '1422671603', '1422671261', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11768', 'logo', '商店LOGO', 'int(10) NULL', 'picture', '', '', '1', '', '1196', '0', '1', '1422950521', '1422671295', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11769', 'intro', '店铺简介', 'text NULL', 'textarea', '', '', '1', '', '1196', '0', '1', '1422671570', '1422671345', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11770', 'mobile', '联系电话', 'varchar(30) NULL', 'string', '', '', '1', '', '1196', '0', '1', '1422671410', '1422671410', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11771', 'qq', 'QQ', 'int(10) NULL', 'num', '', '', '1', '', '1196', '0', '1', '1422671498', '1422671498', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11772', 'wechat', '微信', 'varchar(50) NULL', 'string', '', '', '1', '', '1196', '0', '1', '1422671544', '1422671544', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11773', 'template', '模板', 'varchar(30) NULL', 'string', '', '', '0', '', '1196', '0', '1', '1422950165', '1422950165', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11774', 'content', '店铺介绍', 'text  NULL', 'editor', '', '', '1', '', '1196', '0', '1', '1423108654', '1423108654', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11775', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '1196', '0', '1', '1439456512', '1439455806', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11776', 'manager_id', '管理员ID', 'int(10) NULL', 'num', '', '', '0', '', '1196', '0', '1', '1439456496', '1439455828', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11777', 'cover', '商品封面图', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1197', '0', '1', '1431071756', '1422672306', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11778', 'content', '商品介绍', 'text NOT NULL', 'editor', '', '', '1', '', '1197', '0', '1', '1422672255', '1422672255', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11779', 'title', '商品名称', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1197', '1', '1', '1422672113', '1422672113', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11780', 'price', '价格', 'decimal(10,2) NULL', 'num', '0', '', '1', '', '1197', '0', '1', '1439468076', '1422672186', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11781', 'imgs', '商品图片', 'varchar(255) NOT NULL', 'mult_picture', '', '可以上传多个图片', '1', '', '1197', '0', '1', '1438331467', '1422672449', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11782', 'inventory', '库存数量', 'int(10) NULL', 'num', '0', '', '1', '', '1197', '0', '1', '1422935578', '1422672560', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11783', 'shop_id', '商店ID', 'int(10) NULL', 'num', '0', '', '4', '', '1197', '0', '1', '1422934861', '1422931951', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11784', 'is_show', '是否上架', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:否\r\n1:是', '1197', '0', '1', '1422935533', '1422935533', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11785', 'sale_count', '销售量', 'int(10) NULL', 'num', '0', '', '0', '', '1197', '0', '1', '1422935712', '1422935600', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11786', 'is_recommend', '是否推荐', 'tinyint(2) NULL', 'bool', '', '推荐后首页的推荐商品里显示', '1', '0:否\r\n1:是', '1197', '0', '1', '1423107236', '1423107213', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11787', 'rank', '热销度', 'int(10) NULL', 'num', '0', '热销度由发布时间、推荐状态、销量三个维度进行计算得到', '0', '', '1197', '0', '1', '1423474955', '1423126715', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11788', 'show_time', '上架时间', 'int(10) NULL', 'datetime', '0', '', '0', '', '1197', '0', '1', '1423127849', '1423127833', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11789', 'old_price', '原价', 'int(10) NULL', 'num', '', '', '1', '', '1197', '0', '1', '1423132272', '1423132272', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11790', 'type', '商品类型', 'tinyint(2) NULL', 'bool', '0', '注：虚拟商品不支持货到付款', '0', '0:实物商品（需要快递）\r\n1:虚拟商品（不需要快递）', '1197', '0', '1', '1439549244', '1439458735', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11791', 'category_id', '商品分类', 'char(50) NULL', 'select', '', '', '1', '', '1197', '0', '1', '1440126604', '1440066901', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11792', 'uid', '使用UID', 'int(10) NULL', 'num', '', '', '0', '', '1198', '0', '1', '1423471296', '1423471296', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11793', 'goods_id', '商品ID', 'int(10) NULL', 'num', '', '', '0', '', '1198', '0', '1', '1423471321', '1423471321', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11794', 'cTime', '收藏时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1198', '0', '1', '1423471348', '1423471348', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11795', 'uid', '用户ID', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '0', '', '1199', '0', '1', '1419577913', '1419577913', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11796', 'shop_id', '商店id', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1199', '0', '1', '1419578098', '1419577949', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11797', 'goods_id', '商品id', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1199', '0', '1', '1419578025', '1419578025', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11798', 'num', '数量', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '1', '', '1199', '0', '1', '1419578075', '1419578075', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11799', 'price', '单价', 'varchar(30) NOT NULL', 'num', '', '', '0', '', '1199', '0', '1', '1419578162', '1419578154', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11800', 'goods_type', '商品类型', 'tinyint(2) NOT NULL', 'bool', '0', '', '1', '', '1199', '0', '1', '1420551825', '1420551825', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11801', 'openid', 'openid', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1199', '0', '1', '1420195356', '1420195356', '', '3', '', 'regex', 'get_openid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11802', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1200', '1', '1', '1429522999', '1423477509', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11803', 'truename', '收货人姓名', 'varchar(100) NULL', 'string', '', '', '1', '', '1200', '1', '1', '1423477690', '1423477548', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11804', 'mobile', '手机号码', 'varchar(50) NULL', 'string', '', '', '1', '', '1200', '1', '1', '1423477580', '1423477580', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11805', 'city', '城市', 'varchar(255) NULL', 'cascade', '', '', '1', 'module=city', '1200', '1', '1', '1423477660', '1423477660', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11806', 'address', '具体地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1200', '1', '1', '1423477681', '1423477681', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11807', 'is_use', '是否设置为默认', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:否\r\n1:是', '1200', '0', '1', '1423536697', '1423477729', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11808', 'title', '标题', 'varchar(255) NULL', 'string', '', '可为空', '1', '', '1201', '0', '1', '1396098316', '1396098316', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11809', 'img', '图片', 'int(10) unsigned NOT NULL ', 'picture', '', '', '1', '', '1201', '1', '1', '1396098349', '1396098349', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11810', 'url', '链接地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1201', '0', '1', '1396098380', '1396098380', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11811', 'is_show', '是否显示', 'tinyint(2) NULL', 'bool', '1', '', '1', '0:不显示\r\n1:显示', '1201', '0', '1', '1396098464', '1396098464', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11812', 'sort', '排序', 'int(10) unsigned NULL ', 'num', '0', '值越小越靠前', '1', '', '1201', '0', '1', '1396098682', '1396098682', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11813', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '1201', '0', '1', '1396098747', '1396098747', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11814', 'shop_id', '商店ID', 'int(10) NULL', 'num', '0', '', '4', '', '1201', '0', '1', '1422934490', '1422932093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11815', 'order_id', '订单ID', 'int(10) NULL', 'num', '', '', '0', '', '1202', '0', '1', '1439525588', '1439525588', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11816', 'status_code', '状态码', 'char(50) NULL', 'select', '0', '', '0', '0:待支付\r\n1:待商家确认\r\n2:待发货\r\n3:配送中\r\n4:确认已收货\r\n5:确认已收款\r\n6:待评价\r\n7:已评价', '1202', '0', '1', '1439536678', '1439525934', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11817', 'remark', '备注内容', 'varchar(255) NULL', 'string', '', '', '0', '', '1202', '0', '1', '1439525979', '1439525979', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11818', 'cTime', '时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1202', '0', '1', '1439526002', '1439526002', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11819', 'extend', '扩展信息', 'varchar(255) NULL', 'string', '', '', '0', '', '1202', '0', '1', '1439526038', '1439526038', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11820', 'goods_datas', '商品序列化数据', 'text NOT NULL', 'textarea', '', '', '1', '', '1203', '0', '1', '1423534050', '1420269321', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11821', 'uid', '用户id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '1', '', '1203', '0', '1', '1420269348', '1420269348', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11822', 'remark', '备注', 'text NOT NULL', 'textarea', '', '', '1', '', '1203', '0', '1', '1423534071', '1420269399', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11823', 'order_number', '订单编号', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1203', '0', '1', '1423534179', '1420269451', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11824', 'cTime', '订单时间', 'int(10) NOT NULL', 'datetime', '', '', '1', '', '1203', '0', '1', '1423534102', '1420269666', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11825', 'total_price', '总价', 'decimal(10,2) NULL', 'num', '', '', '1', '', '1203', '0', '1', '1439812371', '1420272711', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11826', 'openid', 'OpenID', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1203', '0', '1', '1420526437', '1420526437', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11827', 'pay_status', '支付状态', 'int(10)  NULL', 'num', '', '', '0', '', '1203', '0', '1', '1423537847', '1420596969', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11828', 'pay_type', '支付类型', 'int(10) NULL', 'num', '', '', '0', '', '1203', '0', '1', '1423537868', '1420596998', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11829', 'address_id', '配送信息', 'int(10) NULL', 'num', '', '', '1', '', '1203', '0', '1', '1423534264', '1423534264', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11830', 'is_send', '是否发货', 'int(10) NULL', 'num', '0', '', '1', '', '1203', '0', '1', '1438336095', '1438336095', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11831', 'send_code', '快递公司编号', 'varchar(255) NULL', 'string', '', '', '1', '', '1203', '0', '1', '1438336511', '1438336511', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11832', 'send_number', '快递单号', 'varchar(255) NULL', 'string', '', '', '1', '', '1203', '0', '1', '1438336556', '1438336556', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11833', 'send_type', '发货类型', 'char(10) NULL', 'radio', '', '', '1', '0|线上发货\r\n1|物流公司发货', '1203', '0', '1', '1438336756', '1438336756', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11834', 'is_new', '是否为新订单', 'tinyint(2) NULL', 'bool', '1', '', '0', '0:否\r\n1:是', '1203', '0', '1', '1439435979', '1439435969', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11835', 'shop_id', '商店编号', 'int(10) NULL', 'num', '0', '', '1', '', '1203', '0', '1', '1439455026', '1439455026', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11836', 'status_code', '订单跟踪状态码', 'char(50) NULL', 'select', '0', '', '0', '0:待支付\r\n1:待商家确认\r\n2:待发货\r\n3:配送中\r\n4:确认已收货\r\n5:确认已收款\r\n6:待评价\r\n7:已评价', '1203', '0', '1', '1439536746', '1439526095', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11837', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1204', '0', '1', '1422931055', '1422930936', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11838', 'goods_id', '商品ID', 'int(10) NULL', 'num', '', '', '0', '', '1204', '0', '1', '1422930970', '1422930970', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11839', 'score', '得分', 'int(10) NULL', 'num', '0', '', '0', '', '1204', '0', '1', '1422931004', '1422931004', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11840', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1204', '0', '1', '1422931044', '1422931044', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11841', 'title', '分类标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1205', '1', '1', '1397529407', '1397529407', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11842', 'icon', '分类图标', 'int(10) unsigned NULL ', 'picture', '', '建议上传100X100的正方形图片', '1', '', '1205', '0', '1', '1431072029', '1397529461', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11843', 'pid', '上一级分类', 'int(10) unsigned NULL ', 'select', '0', '如果你要增加一级分类，这里选择“无”即可', '0', '0:无', '1205', '0', '1', '1422934148', '1397529555', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11844', 'path', '分类路径', 'varchar(255) NULL', 'string', '', '', '0', '', '1205', '0', '1', '1397529604', '1397529604', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11845', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '数值越小越靠前', '1', '', '1205', '0', '1', '1397529705', '1397529705', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11846', 'is_show', '是否显示', 'tinyint(2) NULL', 'bool', '1', '', '1', '0:不显示\r\n1:显示', '1205', '0', '1', '1397532496', '1397529809', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11847', 'shop_id', '商店ID', 'int(10) NOT NULL', 'num', '0', '', '4', '', '1205', '0', '1', '1422934193', '1422672025', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11848', 'is_recommend', '是否推荐', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:否\r\n1:是', '1205', '0', '1', '1423106432', '1423106432', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11849', 'send_name', '发送人', 'varchar(255) NULL', 'string', '', '', '1', '', '1206', '1', '1', '1429346507', '1429346507', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11850', 'receive_name', '接收人', 'varchar(255) NULL', 'string', '', '', '1', '', '1206', '1', '1', '1429346556', '1429346556', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11851', 'content', '祝福语', 'text NULL', 'textarea', '', '', '1', '', '1206', '1', '1', '1429346679', '1429346679', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11852', 'create_time', ' 创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1206', '0', '1', '1429604045', '1429346729', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11853', 'template', '模板', 'char(50) NULL', 'string', '', '模板的文件夹名称，不能为中文', '1', '', '1206', '1', '1', '1429348371', '1429346979', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11854', 'template_cate', '模板分类', 'varchar(255) NULL', 'string', '', '', '4', '', '1206', '1', '1', '1429348355', '1429347540', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11855', 'read_count', '浏览次数', 'int(10) NULL', 'num', '0', '', '0', '', '1206', '0', '1', '1429348951', '1429348951', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11856', 'mid', '用户Id', 'varchar(255) NULL', 'num', '', '', '0', '', '1206', '0', '1', '1429673299', '1429512603', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11857', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1206', '0', '1', '1429764969', '1429764969', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11858', 'content_cate_id', '祝福语类别Id', 'int(10) NULL', 'num', '0', '', '4', '', '1207', '1', '1', '1429349347', '1429349074', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11859', 'content', '祝福语', 'text NULL', 'textarea', '', '', '1', '', '1207', '1', '1', '1429349162', '1429349162', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11860', 'content_cate', '类别', 'varchar(255) NULL', 'select', '', '', '1', '', '1207', '0', '1', '1429522282', '1429350568', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11861', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1207', '0', '1', '1429523422', '1429512730', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11862', 'content_cate_name', '祝福语类别', 'varchar(255) NULL', 'string', '', '', '1', '', '1208', '1', '1', '1429349396', '1429349396', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11863', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1208', '0', '1', '1429520955', '1429512697', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11864', 'content_cate_icon', '类别图标', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1208', '0', '1', '1429597855', '1429597855', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11865', 'addon', '来源插件', 'varchar(255) NULL', 'string', 'Scratch', '', '0', '', '1209', '0', '1', '1399348676', '1399348676', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11866', 'target_id', '来源ID', 'int(10) unsigned NULL ', '', '', '', '1', '', '1209', '0', '1', '1420980352', '1399348699', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11738', 'plat_type', '平台标识', 'int(10) NULL', 'num', '', '', '1', '', '1192', '0', '1', '1446110716', '1446110716', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11737', 'status', '使用状态', 'int(10) NULL', 'num', '', '', '1', '', '1192', '0', '1', '1446110690', '1446110690', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11715', 'title', '题目标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1190', '1', '1', '1397037377', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11716', 'intro', '题目描述', 'text NOT NULL', 'textarea', '', '', '1', '', '1190', '0', '1', '1396954176', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11717', 'cTime', '发布时间', 'int(10) UNSIGNED NOT NULL', 'datetime', '', '', '0', '', '1190', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11718', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1190', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11719', 'is_must', '是否必填', 'tinyint(2) NOT NULL', 'bool', '1', '', '0', '0:否\r\n1:是', '1190', '0', '1', '1397035513', '1396954649', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11720', 'extra', '参数', 'text NOT NULL', 'textarea', '', '输入格式见上面的提示', '1', '', '1190', '0', '1', '1397142592', '1396954558', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11721', 'type', '题目类型', 'char(50) NOT NULL', 'radio', 'radio', '', '0', 'radio:单选题', '1190', '1', '1', '1397142548', '1396954463', '', '3', '', 'regex', 'radio', '1', 'string');
INSERT INTO `wp_attribute` VALUES ('11722', 'test_id', 'test_id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '4', '', '1190', '1', '1', '1396954240', '1396954240', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11723', 'sort', '排序号', 'int(10) UNSIGNED NOT NULL', 'num', '0', '值越小越靠前', '1', '', '1190', '0', '1', '1396955010', '1396955010', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11724', 'answer', '回答内容', 'text NOT NULL', 'textarea', '', '', '0', '', '1191', '0', '1', '1396955766', '1396955766', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11725', 'openid', 'OpenId', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1191', '0', '1', '1396955581', '1396955581', '', '3', '', 'regex', 'get_openid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11726', 'uid', '用户UID', 'int(10) NOT NULL', 'num', '', '', '0', '', '1191', '0', '1', '1396955530', '1396955530', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11727', 'question_id', 'question_id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '4', '', '1191', '1', '1', '1396955412', '1396955392', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11728', 'cTime', '发布时间', 'int(10) UNSIGNED NOT NULL', 'datetime', '', '', '0', '', '1191', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11729', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1191', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11730', 'test_id', 'test_id', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '4', '', '1191', '1', '1', '1396955403', '1396955369', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11731', 'score', '得分', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '0', '', '1191', '0', '1', '1397040133', '1397040133', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11732', 'from_type', '用途', 'varchar(255) NULL', 'string', '', '', '1', '', '1192', '0', '1', '1446107717', '1446107717', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11733', 'code', '验证码', 'varchar(255) NULL', 'string', '', '', '1', '', '1192', '0', '1', '1446110095', '1446110095', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11734', 'smsId', '短信唯一标识', 'varchar(255) NULL', 'string', '', '', '1', '', '1192', '0', '1', '1446110244', '1446110244', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11735', 'phone', '手机号', 'varchar(255) NULL', 'string', '', '', '1', '', '1192', '0', '1', '1446110276', '1446110276', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11736', 'cTime', '创建时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1192', '0', '1', '1446110405', '1446110405', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11287', 'keyword_type', '关键词类型', 'tinyint(2) NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1145', '0', '1', '1396623302', '1396578249', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11286', 'keyword', '关键词', 'varchar(255) NULL', 'string', '', '多个关键词请用空格分开：例如: 高 富 帅', '1', '', '1145', '0', '1', '1396578460', '1396578212', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11285', 'author', '作者', 'varchar(50) NULL', 'string', '', '为空时取当前用户名', '1', '', '1144', '0', '1', '1437988055', '1437988055', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11284', 'jump_url', '外链', 'varchar(255) NULL', 'string', '', '如需跳转填写网址(记住必须有http://)如果填写了图文详细内容，这里请留空，不要设置！', '1', '', '1144', '0', '1', '1402482073', '1402482073', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11283', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1144', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11282', 'view_count', '浏览数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1144', '0', '1', '1396510630', '1396510630', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11281', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '数值越小越靠前', '1', '', '1144', '0', '1', '1396510508', '1396510508', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11280', 'cTime', '发布时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1144', '0', '1', '1396075102', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11279', 'content', '内容', 'text NULL', 'editor', '', '', '1', '', '1144', '0', '1', '1396062146', '1396062146', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11278', 'cover', '封面图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1144', '0', '1', '1396062093', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11277', 'cate_id', '所属类别', 'int(10) unsigned NULL ', 'select', '0', '要先在微官网分类里配置好分类才可选择', '1', '0:请选择分类', '1144', '0', '1', '1396078914', '1396062003', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11268', 'jump_type', '推送类型', 'char(10) NULL', 'radio', '0', '', '1', '1:URL|keyword@hide,url@show\r\n0:关键词|keyword@show,url@hide', '1142', '0', '1', '1447208981', '1447208981', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11267', 'sucai_type', '素材类型', 'char(50) NULL', 'select', '0', '', '1', '0:请选择\r\n1:图文\r\n2:文本\r\n3:图片\r\n4:语音\r\n5:视频', '1142', '0', '1', '1447208890', '1447208890', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11266', 'target_id', '选择内容', 'int(10) NULL', 'num', '', '', '4', '0:请选择', '1142', '0', '1', '1447208825', '1447208825', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11265', 'addon', '选择插件', 'char(50) NULL', 'select', '0', '', '1', '0:请选择', '1142', '0', '1', '1447208750', '1447208750', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11264', 'from_type', '配置动作', 'char(50) NULL', 'select', '-1', '', '1', '0:站内信息|keyword@hide,url@hide,type@hide,sucai_type@hide,addon@show,jump_type@show\r\n1:站内素材|keyword@hide,url@hide,type@hide,sucai_type@show,addon@hide,jump_type@hide\r\n9:自定义|keyword@show,url@hide,type@show,addon@hide,sucai_type@hide,jump_type@hide\r\n-1:请选择|keyword@hide,url@hide,type@hide,addon@hide,sucai_type@hide,jump_type@hide', '1142', '0', '1', '1447318552', '1447208677', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11275', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1144', '1', '1', '1396061877', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11276', 'intro', '简介', 'text NULL', 'textarea', '', '', '1', '', '1144', '0', '1', '1396061947', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11959', 'prize_image', '奖品图片', 'varchar(255) NULL', 'picture', '上传奖品图片', '', '1', '', '1220', '1', '1', '1429756675', '1429516329', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11958', 'prize_count', '奖品个数', 'int(10) NULL', 'num', '', '', '1', '', '1220', '1', '1', '1429779465', '1429516109', '/^[0-9]*$/', '3', '奖品个数不能小于0', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11957', 'prize_conditions', '活动说明', 'text NULL', 'textarea', '', '奖品说明', '1', '', '1220', '1', '1', '1429756762', '1429516052', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11955', 'prizeid', '奖品编号', 'int(10) NULL', 'num', '', '', '4', '', '1219', '0', '1', '1447832021', '1429607543', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11956', 'prize_name', '奖品名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1220', '1', '1', '1429515512', '1429515512', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11953', 'uid', '用户id', 'int(10) NULL', 'num', '', '', '0', '', '1219', '0', '1', '1429673948', '1429522086', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11954', 'remark', '备注', 'varchar(255) NULL', 'string', '', '', '1', '', '1219', '0', '1', '1429598446', '1429598446', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11951', 'mobile', '手机', 'varchar(50) NULL', 'string', '', '', '1', '', '1219', '1', '1', '1429521877', '1429521877', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11952', 'turename', '收货人姓名', 'varchar(255) NULL', 'string', '', '', '1', '', '1219', '1', '1', '1429672245', '1429521930', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11950', 'address', '奖品收货地址', 'varchar(255) NULL', 'textarea', '', '', '1', '', '1219', '1', '1', '1429857152', '1429521685', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11885', 'max_num', '每人最多允许抽奖次数', 'int(10) unsigned NULL ', 'num', '1', '0表示不限制数量', '1', '', '1210', '0', '1', '1399260079', '1399260079', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11884', 'predict_num', '预计参与人数', 'int(10) unsigned NOT NULL ', 'num', '', '预计人数直接影响抽奖概率：中奖概率 = 奖品总数/(预估活动人数*每人抽奖次数) 要确保100%中奖可设置为1', '1', '', '1210', '1', '1', '1399710446', '1399259992', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11883', 'end_img', '过期提示图片', 'int(10) unsigned NULL ', 'picture', '', '可为空', '1', '', '1210', '0', '1', '1399712676', '1399711987', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11882', 'end_tips', '过期说明', 'text NULL', 'textarea', '', '活动过期或者结束说明', '1', '', '1210', '0', '1', '1399259570', '1399259570', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11881', 'start_time', '开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1210', '0', '1', '1399259416', '1399259416', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11880', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1210', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11879', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1210', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11523', 'keyword', '关键词', 'varchar(100) NULL', 'string', '', '', '0', '', '1172', '0', '1', '1422330526', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11522', 'background', '素材背景图', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1172', '0', '1', '1422000992', '1422000992', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11521', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1171', '0', '1', '1396690911', '1396690911', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11260', 'keyword', '关联关键词', 'varchar(100) NULL', 'string', '', '', '1', '', '1142', '0', '1', '1416812109', '1394519054', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11261', 'url', '关联URL', 'varchar(255) NULL ', 'string', '', '', '1', '', '1142', '0', '1', '1394519090', '1394519090', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11262', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1142', '0', '1', '1394526820', '1394526820', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11263', 'type', '类型', 'varchar(30) NULL', 'bool', 'click', '', '1', 'click:点击推事件|keyword@show,url@hide\r\nview:跳转URL|keyword@hide,url@show\r\nscancode_push:扫码推事件|keyword@show,url@hide\r\nscancode_waitmsg:扫码带提示|keyword@show,url@hide\r\npic_sysphoto:弹出系统拍照发图|keyword@show,url@hide\r\npic_photo_or_album:弹出拍照或者相册发图|keyword@show,url@hide\r\npic_weixin:弹出微信相册发图器|keyword@show,url@hide\r\nlocation_select:弹出地理位置选择器|keyword@show,url@hide\r\nnone:无事件的一级菜单|keyword@hide,url@hide', '1142', '0', '1', '1416812039', '1416810588', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11519', 'openid', 'OpenId', 'varchar(255) NULL', 'string', '', '', '0', '', '1171', '0', '1', '1396688187', '1396688187', '', '3', '', 'regex', 'get_openid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11520', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1171', '0', '1', '1396688042', '1396688042', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11274', 'keyword_type', '关键词类型', 'tinyint(2) NULL', 'select', '', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1144', '0', '1', '1396061814', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11257', 'sort', '排序号', 'tinyint(4) NULL ', 'num', '0', '数值越小越靠前', '1', '', '1142', '0', '1', '1394523288', '1394519175', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11258', 'pid', '一级菜单', 'int(10) NULL', 'select', '0', '如果是一级菜单，选择“无”即可', '1', '0:无', '1142', '0', '1', '1416810279', '1394518930', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11259', 'title', '菜单名', 'varchar(50) NOT NULL', 'string', '', '可创建最多 3 个一级菜单，每个一级菜单下可创建最多 5 个二级菜单。编辑中的菜单不会马上被用户看到，请放心调试。', '1', '', '1142', '1', '1', '1408951570', '1394518988', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11539', 'addon_condition', '插件场景限制', 'varchar(255) NULL', 'string', '', '格式：[插件名:id值]，如[投票:10]表示对ID为10的投票投完才能领取，更多的说明见表单上的提示', '0', '', '1172', '0', '1', '1418885827', '1399261026', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11538', 'credit_bug', '积分消费', 'int(10) unsigned NULL ', 'num', '0', '用积分中的财富兑换、兑换后扣除相应的积分财富', '0', '', '1172', '0', '1', '1418885794', '1399260764', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11537', 'credit_conditon', '积分限制', 'int(10) unsigned NULL ', 'num', '0', '粉丝达到多少积分后才能领取，领取后不扣积分', '0', '', '1172', '0', '1', '1418885804', '1399260618', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11551', 'shop_name', '商家名称', 'varchar(255) NULL', 'string', '优惠商家', '', '1', '', '1172', '0', '1', '1427280255', '1427280255', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11550', 'use_start_time', '使用开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1172', '1', '1', '1427280116', '1427280008', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11593', 'is_audit', '是否审核', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:未审核\r\n1:已审核', '1178', '0', '1', '1435031747', '1435029949', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11549', 'over_time', '使用的截止时间', 'int(10) NULL', 'datetime', '', '券的使用截止时间，为空时表示不限制', '1', '', '1172', '0', '1', '1427161334', '1427161118', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11547', 'start_tips', '活动还没开始时的提示语', 'varchar(255) NULL', 'string', '', '', '1', '', '1172', '0', '1', '1423134546', '1423134546', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11548', 'more_button', '其它按钮', 'text NULL', 'textarea', '', '格式：按钮名称|按钮跳转地址，每行一个。如：查看官网|http://weiphp.cn', '1', '', '1172', '0', '1', '1423193853', '1423193853', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11601', 'code', '卡券code码', 'text NULL', 'textarea', '', '指定的卡券code 码，只能被领一次。use_custom_code 字段为true 的卡券必须填写，非自定义code 不必填写', '1', '', '1179', '0', '1', '1421980773', '1421980773', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11600', 'appsecre', '开通卡券的商家公众号密钥', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1179', '1', '1', '1421980516', '1421980516', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11595', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '1', '', '1179', '0', '1', '1430998977', '1430998977', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11596', 'cover', '素材封面', 'int(10) UNSIGNED NULL', 'picture', '', '', '0', '', '1179', '0', '1', '1427435373', '1422000629', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11597', 'balance', '红包余额', 'varchar(30) NULL', 'string', '', '红包余额，以分为单位。红包类型必填 （LUCKY_MONEY），其他卡券类型不填', '0', '', '1179', '0', '1', '1427435295', '1421982394', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11598', 'card_id', '卡券ID', 'varchar(100) NOT NULL', 'string', '', '', '0', '', '1179', '1', '1', '1427435272', '1421980436', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11599', 'openid', 'OpenID', 'text NULL', 'textarea', '', '指定领取者的openid，只有该用户能领取。bind_openid字段为true的卡券必须填写，非自定义openid 不必填写', '0', '', '1179', '0', '1', '1427435344', '1421980851', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1542', 'score', '修改积分', 'int(10) NULL', 'num', '', '', '1', '', '176', '1', '1', '1444302622', '1444302410', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1543', 'branch_id', '修改门店', 'int(10) NULL', 'num', '', '', '1', '', '176', '0', '1', '1444302450', '1444302450', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1544', 'operator', '操作员', 'varchar(255) NULL', 'string', '', '', '1', '', '176', '0', '1', '1444302474', '1444302474', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1545', 'cTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '176', '0', '1', '1444302508', '1444302508', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1546', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '1', '', '176', '0', '1', '1444302539', '1444302539', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1547', 'member_id', '会员卡id', 'int(10) NULL', 'num', '', '', '4', '', '176', '0', '1', '1444302566', '1444302566', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1548', 'manager_id', '管理员id', 'int(10) NULL', 'num', '', '', '1', '', '176', '0', '1', '1444302595', '1444302595', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11610', 'more_button', '添加更多按钮', 'text NULL', 'textarea', '', '', '1', '', '1179', '0', '1', '1427512791', '1427512791', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11609', 'shop_logo', '商家LOGO', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1179', '0', '1', '1427437781', '1427437781', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11608', 'shop_name', '商家名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1179', '0', '1', '1427438002', '1427438002', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11607', 'head_bg_color', '头部背景颜色', 'varchar(255) NULL', 'string', '#35a2dd', '', '1', '', '1179', '0', '1', '1427435535', '1427435535', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11606', 'button_color', '领取按钮颜色', 'varchar(255) NULL', 'string', '#0dbd02', '', '1', '', '1179', '0', '1', '1427435492', '1427435492', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11602', 'content', '活动介绍', 'text NULL', 'editor', '', '', '1', '', '1179', '0', '1', '1421981078', '1421981078', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11603', 'background', '背景图', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1179', '0', '1', '1422000931', '1422000931', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11604', 'token', 'token', 'varchar(50) NULL', 'string', '', '', '1', '', '1179', '0', '1', '1430999013', '1430999013', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11605', 'title', '卡券标题', 'varchar(255) NULL', 'string', '卡券', '', '1', '', '1179', '0', '1', '1427435445', '1427435445', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11592', 'content', '评论内容', 'text NULL', 'textarea', '', '', '0', '', '1178', '1', '1', '1432602376', '1432602376', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11530', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1172', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11531', 'start_time', '开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1172', '0', '1', '1422330558', '1399259416', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11532', 'end_tips', '领取结束说明', 'text NULL', 'textarea', '', '活动过期或者结束说明', '1', '', '1172', '0', '1', '1427161168', '1399259570', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11533', 'end_img', '领取结束提示图片', 'int(10) unsigned NULL ', 'picture', '', '可为空', '1', '', '1172', '0', '1', '1427161296', '1400989793', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11534', 'num', '优惠券数量', 'int(10) unsigned NULL ', 'num', '0', '0表示不限制数量', '1', '', '1172', '0', '1', '1399259838', '1399259808', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11535', 'max_num', '每人最多允许获取次数', 'int(10) unsigned NULL ', 'num', '1', '0表示不限制数量', '0', '', '1172', '0', '1', '1447758805', '1399260079', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11536', 'follower_condtion', '粉丝状态', 'char(50) NULL', 'select', '1', '粉丝达到设置的状态才能获取', '0', '0:不限制\r\n1:已关注\r\n2:已绑定信息\r\n3:会员卡成员', '1172', '0', '1', '1418885814', '1399260479', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11625', 'card_id', '卡券ID', 'varchar(255) NULL', 'string', '', '可为空', '1', '', '1180', '0', '1', '1421406387', '1421406387', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11624', 'finish_button', '成功抢答完后显示的按钮', 'text NULL', 'textarea', '', '格式：按钮名|跳转链接，如：百度|www.baidu.com 多个时换行分割', '1', '', '1180', '0', '1', '1420857847', '1420857847', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11611', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1179', '0', '1', '1430129779', '1430129779', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11612', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1180', '1', '1', '1396624337', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11613', 'keyword_type', '关键词类型', 'tinyint(2) NOT NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1180', '1', '1', '1396624426', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11614', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1180', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11615', 'intro', '封面简介', 'text NULL', 'textarea', '', '', '1', '', '1180', '1', '1', '1439367292', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11616', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1180', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11617', 'cover', '封面图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1180', '0', '1', '1396624534', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11618', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1180', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11619', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1180', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11620', 'finish_tip', '结束语', 'text NULL', 'textarea', '', '为空默认为：抢答完成，谢谢参与', '1', '', '1180', '1', '1', '1439367319', '1396953940', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11621', 'content', '活动介绍', 'text NULL', 'editor', '', '显示在用户进入的开始界面', '1', '', '1180', '0', '1', '1420791982', '1420791908', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11622', 'shop_address', '商家地址', 'text NULL', 'textarea', '', '显示在马上开始的下面，多个地址用英文逗号或者换行分割', '1', '', '1180', '0', '1', '1420798853', '1420794534', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11623', 'appids', '提示关注的公众号', 'text NULL', 'textarea', '', '格式：广东南方卫视|wx2d7ce60bbfc928ef 多个公众号用换行分割', '1', '', '1180', '0', '1', '1420798902', '1420796356', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11558', 'name', '店名', 'varchar(100) NULL', 'string', '', '', '1', '', '1173', '1', '1', '1427164635', '1427164635', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11541', 'view_count', '浏览人数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1172', '0', '1', '1399270926', '1399270926', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11542', 'addon', '插件', 'char(50) NULL', 'select', 'public', '', '0', 'public:通用\r\ninvite:微邀约', '1172', '0', '1', '1418885638', '1418885638', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11543', 'shop_uid', '商家管理员ID', 'varchar(255) NULL', 'string', '', '', '0', '', '1172', '0', '1', '1421750246', '1418900122', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11544', 'use_count', '已使用数', 'int(10) NULL', 'num', '0', '', '0', '', '1172', '0', '1', '1418910237', '1418910237', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11545', 'pay_password', '核销密码', 'varchar(255) NULL', 'string', '', '', '1', '', '1172', '0', '1', '1420875229', '1420875229', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11546', 'empty_prize_tips', '奖品抽完后的提示', 'varchar(255) NULL', 'string', '', '不填写时默认显示：您来晚了，优惠券已经领取完', '1', '', '1172', '0', '1', '1421394437', '1421394267', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11559', 'address', '详细地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1173', '1', '1', '1427164668', '1427164668', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11560', 'phone', '联系电话', 'varchar(30) NULL', 'string', '', '', '1', '', '1173', '0', '1', '1427166529', '1427164707', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11561', 'gps', 'GPS经纬度', 'varchar(50) NULL', 'string', '', '格式：经度,纬度', '1', '', '1173', '0', '1', '1427371523', '1427164833', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11562', 'coupon_id', '所属优惠券编号', 'int(10) NULL', 'num', '', '', '4', '', '1173', '0', '1', '1427165806', '1427165806', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11563', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1173', '0', '1', '1440071867', '1440071805', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11564', 'manager_id', '管理员id', 'int(10) NULL', 'num', '', '', '0', '', '1173', '0', '1', '1440071927', '1440071917', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11565', 'open_time', '营业时间', 'varchar(50) NULL', 'string', '', '', '1', '', '1173', '0', '1', '1443106576', '1443106576', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11566', 'img', '门店展示图', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1173', '0', '1', '1447060275', '1447060275', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11567', 'coupon_id', 'coupon_id', 'int(10) NULL', 'num', '', '', '1', '', '1174', '0', '1', '1427356371', '1427356371', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11568', 'shop_id', 'shop_id', 'int(10) NULL', 'num', '', '', '1', '', '1174', '0', '1', '1427356387', '1427356387', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11569', 'title', '竞猜标题', 'varchar(255) NULL', 'string', '', '', '1', '', '1175', '1', '1', '1428655010', '1428655010', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11570', 'desc', '活动说明', 'text NULL', 'textarea', '', '', '1', '', '1175', '0', '1', '1428657017', '1428657017', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11571', 'start_time', '开始时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1175', '1', '1', '1428657086', '1428657086', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11572', 'end_time', '结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1175', '1', '1', '1428657122', '1428657122', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11573', 'create_time', '创建时间', 'int(10) NULL', 'datetime', '', '', '4', '', '1175', '0', '1', '1428664508', '1428664122', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11574', 'guess_count', '', 'int(10) unsigned NULL ', 'num', '0', '', '4', '', '1175', '0', '1', '1428718033', '1428717991', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11575', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1175', '0', '1', '1429521291', '1429512366', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11576', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1175', '0', '1', '1430115411', '1430103969', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11577', 'cover', '主题图片', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1175', '0', '1', '1430384839', '1430384839', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11578', 'user_id', '用户ID', 'int(10) unsigned NULL', 'num', '0', '', '0', '', '1176', '0', '1', '1428738317', '1428738317', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11579', 'guess_id', '竞猜Id', 'int(10) unsigned NULL', 'num', '0', '', '0', '', '1176', '0', '1', '1428738379', '1428738379', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11580', 'token', '用户token', 'varchar(255) NULL', 'string', '', '', '1', '', '1176', '0', '1', '1428738405', '1428738405', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11581', 'optionIds', '用户猜的选项IDs', 'varchar(255) NULL', 'string', '', '', '0', '', '1176', '0', '1', '1428738522', '1428738522', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11582', 'cTime', '创时间', 'int(10) NULL', 'date', '', '', '0', '', '1176', '0', '1', '1428738552', '1428738552', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11583', 'guess_id', '竞猜活动的Id', 'int(10) NULL', 'num', '0', '', '4', '', '1177', '0', '1', '1428659228', '1428659228', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11584', 'name', '选项名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1177', '1', '1', '1428659270', '1428659270', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11585', 'image', '选项图片', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1177', '0', '1', '1428659313', '1428659313', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11586', 'order', '选项顺序', 'int(10) NULL', 'num', '0', '', '1', '', '1177', '0', '1', '1428659354', '1428659354', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11587', 'guess_count', '竞猜人数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1177', '0', '1', '1430302786', '1428659432', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11588', 'aim_table', '评论关联数据表', 'varchar(30) NULL', 'string', '', '', '0', '', '1178', '0', '1', '1432602501', '1432602501', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11589', 'aim_id', '评论关联ID', 'int(10) NULL', 'num', '', '', '0', '', '1178', '0', '1', '1432602466', '1432602466', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11590', 'cTime', '评论时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1178', '0', '1', '1432602404', '1432602404', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11591', 'follow_id', 'follow_id', 'int(10) NULL', 'num', '', '', '0', '', '1178', '1', '1', '1432602345', '1432602345', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11540', 'collect_count', '已领取数', 'int(10) unsigned NULL ', 'num', '0', '', '0', '', '1172', '0', '1', '1400992246', '1399270900', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11524', 'use_tips', '使用说明', 'text NULL', 'editor', '', '用户获取优惠券后显示的提示信息', '1', '', '1172', '1', '1', '1420868972', '1399259489', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11525', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1172', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11526', 'intro', '封面简介', 'text NULL', 'textarea', '', '', '0', '', '1172', '0', '1', '1418885972', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11527', 'end_time', '领取结束时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1172', '0', '1', '1427161023', '1399259433', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11528', 'cover', '优惠券图片', 'int(10) unsigned NULL ', 'picture', '', '', '1', '', '1172', '0', '1', '1418886050', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11529', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1172', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('1663', 'credit_title', '积分标题', 'varchar(50) NULL', 'string', '', '', '0', '', '15', '0', '1', '1444731976', '1444731976', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11714', 'finish_tip', '评论语', 'text NOT NULL', 'textarea', '', '详细说明见上面的提示，配置格式：[0-59]不合格', '1', '', '1189', '0', '1', '1397142371', '1396953940', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11713', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1189', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11712', 'cover', '封面图片', 'int(10) UNSIGNED NOT NULL', 'picture', '', '', '1', '', '1189', '0', '1', '1396624534', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11626', 'appsecre', '卡券对应的appsecre', 'varchar(255) NULL', 'string', '', '', '1', '', '1180', '0', '1', '1421406470', '1421406470', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11627', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1180', '0', '1', '1430210161', '1430210161', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11628', 'answer', '回答内容', 'text NULL', 'textarea', '', '', '0', '', '1181', '0', '1', '1396955766', '1396955766', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11629', 'openid', 'OpenId', 'varchar(255) NULL', 'string', '', '', '0', '', '1181', '0', '1', '1430286439', '1396955581', '', '3', '', 'regex', 'get_openid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11630', 'uid', '用户UID', 'int(10) NULL ', 'num', '', '', '0', '', '1181', '0', '1', '1396955530', '1396955530', '', '3', '', 'regex', 'get_mid', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11631', 'question_id', 'question_id', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '1181', '1', '1', '1396955412', '1396955392', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11632', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1181', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11633', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1181', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11634', 'ask_id', 'ask_id', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '1181', '1', '1', '1396955403', '1396955369', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11635', 'is_correct', '是否回答正确', 'tinyint(2) NULL', 'bool', '1', '', '0', '0:不正确\r\n1:正确', '1181', '0', '1', '1420685956', '1420685956', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11636', 'times', '次数', 'int(4) NULL', 'num', '0', '', '0', '', '1181', '0', '1', '1420965038', '1420965038', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11637', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1182', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11638', 'intro', '问题描述', 'text NULL', 'textarea', '', '', '1', '', '1182', '0', '1', '1396954176', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11639', 'cTime', '发布时间', 'int(10) unsigned NULL ', 'datetime', '', '', '0', '', '1182', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11640', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1182', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11641', 'is_must', '是否必填', 'tinyint(2) NULL', 'bool', '1', '', '0', '0:否\r\n1:是', '1182', '0', '1', '1420686586', '1396954649', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11642', 'extra', '参数', 'text NOT NULL', 'textarea', '', '类型为单选、多选时的定义数据，格式见上面的提示', '1', '', '1182', '1', '1', '1421749164', '1396954558', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11643', 'type', '问题类型', 'char(50) NOT NULL', 'radio', 'radio', '', '1', 'radio:单选题\r\ncheckbox:多选题', '1182', '1', '1', '1420689062', '1396954463', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11644', 'ask_id', 'ask_id', 'int(10) unsigned NOT NULL ', 'num', '', '', '4', '', '1182', '1', '1', '1396954240', '1396954240', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11645', 'sort', '排序号', 'int(10) unsigned NULL ', 'num', '0', '值越小越靠前', '1', '', '1182', '0', '1', '1420689390', '1396955010', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11646', 'answer', '正确答案', 'varchar(255) NOT NULL', 'string', '', '多个答案用空格分开，如： A B C', '1', '', '1182', '1', '1', '1421749143', '1420685041', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11647', 'is_last', '是否最后一题', 'tinyint(2) NULL', 'bool', '0', '如设置为最后一题，用户回答后进入完成页面，否则进入等待下一题提示页面', '0', '0:否\r\n1:是', '1182', '0', '1', '1421749096', '1420686448', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11648', 'wait_time', '等待时间', 'int(10) NULL', 'num', '0', '单位为秒，表示答题后进入下一题的间隔时间', '1', '', '1182', '0', '1', '1420688805', '1420688703', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11649', 'percent', '抢中概率', 'int(10) NULL', 'num', '100', '抢到题目的百分比，请填写0~100之间的整数，如填写50表示概率为50%', '1', '', '1182', '0', '1', '1420855970', '1420855970', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11650', 'answer_time', '答题时间', 'int(10) NULL', 'num', '', '不填则为无答题时间限制', '1', '', '1182', '0', '1', '1427541360', '1427541360', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11651', 'permission', '权限设置', 'char(50) NULL', 'select', '1', '', '1', '1:完全公开(公众人物)\r\n2:群友可见(商务交往)\r\n3:交换名片可见(私人来往)\r\n4:仅自己可见(绝密保存)', '1183', '0', '1', '1438945015', '1438945015', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11652', 'intro', '个人介绍', 'text NULL', 'textarea', '', '', '1', '', '1183', '0', '1', '1438944828', '1438944828', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11653', 'wishing', '希望结交', 'varchar(100) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942908', '1438942908', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11654', 'wechat', '微信号', 'varchar(50) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942392', '1438942392', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11655', 'Email', '邮箱', 'varchar(100) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942359', '1438942359', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11656', 'telephone', '座机', 'varchar(30) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942330', '1438942330', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11657', 'address', '地址', 'varchar(255) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438933681', '1438933681', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11658', 'company_url', '公司网址', 'varchar(255) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438933650', '1438933650', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11659', 'department', '所属部门', 'varchar(50) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438933471', '1438933471', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11660', 'company', '公司名称', 'varchar(100) NULL', 'string', '', '', '1', '', '1183', '1', '1', '1438933418', '1438933418', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11661', 'mobile', '手机', 'varchar(30) NULL', 'string', '', '', '1', '', '1183', '1', '1', '1438933381', '1438933381', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11662', 'position', '职位头衔', 'varchar(50) NULL', 'string', '', '', '1', '如：XX创始人、xx级教师、xx投资顾问、XX爸爸、XX达人', '1183', '0', '1', '1438933330', '1438933330', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11663', 'headface', '头像', 'int(10) UNSIGNED NULL', 'picture', '', '', '0', '', '1183', '0', '1', '1439175454', '1438944864', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11664', 'personal_url', '个人主页', 'varchar(255) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438944804', '1438944804', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11665', 'interest', '兴趣', 'varchar(255) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942945', '1438942945', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11666', 'tag', '人物标签', 'varchar(255) NULL', 'string', '', '多个用逗号分开', '1', '', '1183', '0', '1', '1438942491', '1438942491', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11667', 'weibo', '微博', 'varchar(255) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942443', '1438942443', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11668', 'qq', 'QQ号', 'varchar(30) NULL', 'string', '', '', '1', '', '1183', '0', '1', '1438942418', '1438942418', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11669', 'service', '产品服务', 'text NULL', 'textarea', '', '', '1', '', '1183', '1', '1', '1438933623', '1438933623', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11670', 'truename', '真实姓名', 'varchar(50) NULL', 'string', '', '', '1', '', '1183', '1', '1', '1438931443', '1438931443', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11671', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1183', '0', '1', '1438931293', '1438931293', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11672', 'template', '选择的模板', 'varchar(50) NULL', 'string', '', '', '0', '', '1183', '0', '1', '1438947089', '1438947089', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11673', 'token', 'Token', 'varchar(100) NULL', 'string', '', '', '0', '', '1183', '0', '1', '1439869080', '1439290478', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11674', 'from_uid', '收藏人ID', 'int(10) NULL', 'num', '', '', '0', '', '1184', '0', '1', '1439188549', '1439188462', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11675', 'to_uid', '被收藏人的ID', 'int(10) NULL', 'num', '', '', '0', '', '1184', '0', '1', '1439188512', '1439188512', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11676', 'cTime', '收藏时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1184', '0', '1', '1439188537', '1439188537', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11677', 'business_card_id', '名片id', 'int(10) NULL', 'num', '', '', '4', '', '1185', '0', '1', '1441779829', '1441522726', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11678', 'type', '栏目类型', 'char(10) NULL', 'select', '0', '', '1', '0:自定义|cate_id@hide,title@show,url@show\r\n1:文章分类|cate_id@show,title@hide,url@hide', '1185', '0', '1', '1441525619', '1441512922', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11679', 'cate_id', '分类', 'varchar(100) NULL', 'dynamic_select', '0', '', '1', 'table=we_media_category&value_field=id', '1185', '0', '1', '1441525628', '1441513085', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11680', 'title', '栏目名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1185', '0', '1', '1441525667', '1441513114', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11681', 'url', '跳转url', 'varchar(255) NULL', 'string', '', '', '1', '', '1185', '0', '1', '1441525683', '1441520141', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11682', 'sort', '排序', 'int(10) NULL', 'num', '0', '', '1', '', '1185', '0', '1', '1441520666', '1441520666', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11683', 'uid', '用户id', 'int(10) NULL', 'num', '', '', '0', '', '1185', '0', '1', '1441781769', '1441528808', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11684', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1186', '1', '1', '1396624337', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11685', 'keyword_type', '关键词类型', 'tinyint(2) NOT NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配', '1186', '1', '1', '1396624426', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11686', 'title', '标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1186', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11687', 'intro', '封面简介', 'text NULL', 'textarea', '', '', '1', '', '1186', '1', '1', '1447826199', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11688', 'mTime', '修改时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1186', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11689', 'cover', '封面图片', 'int(10) UNSIGNED NOT NULL', 'picture', '', '', '1', '', '1186', '1', '1', '1418266006', '1396062093', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11690', 'experience', '消耗经验值', 'int(10) NULL', 'num', '0', '', '1', '', '1186', '0', '1', '1418180506', '1418180506', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11691', 'cTime', '发布时间', 'int(10) UNSIGNED NULL', 'datetime', '', '', '0', '', '1186', '0', '1', '1396624612', '1396075102', '', '3', '', 'regex', 'time', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11692', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1186', '0', '1', '1396602871', '1396602859', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11693', 'num', '邀请人数', 'int(10) NULL', 'num', '0', '邀请多少人后才能用优惠券', '1', '', '1186', '1', '1', '1447826376', '1418180590', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11694', 'coupon_id', '优惠券编号', 'int(10) NULL', 'num', '', '可以在优惠券列表中找到对应的编号', '1', '', '1186', '1', '1', '1447826355', '1418180739', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11695', 'coupon_num', '优惠券数', 'int(10) NULL', 'num', '0', '赠送多少张优惠券', '0', '', '1186', '0', '1', '1418959022', '1418180812', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11696', 'receive_num', '已领取优惠券数', 'int(10) NULL', 'num', '0', '', '0', '', '1186', '0', '1', '1418181528', '1418181528', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11697', 'content', '邀约介绍', 'text NULL', 'editor', '', '', '1', '', '1186', '1', '1', '1447826165', '1418265599', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11698', 'template', '模板名称', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1186', '0', '1', '1430122784', '1430122766', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11699', 'token', 'Token', 'varchar(255) NULL', 'string', '', '', '0', '', '1187', '0', '1', '1418192408', '1418192408', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11700', 'uid', '用户ID', 'int(10) NULL', 'num', '', '', '0', '', '1187', '0', '1', '1418192629', '1418192629', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11701', 'invite_id', '邀约ID', 'int(10) NULL', 'num', '', '', '1', '', '1187', '0', '1', '1418192878', '1418192878', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11702', 'invite_num', '已邀请人数', 'int(10) NULL', 'num', '', '', '0', '', '1187', '0', '1', '1418192971', '1418192971', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11703', 'token', 'Token', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '1188', '0', '1', '1401371165', '1401371165', '', '3', '', 'regex', 'get_token', '1', 'function');
INSERT INTO `wp_attribute` VALUES ('11704', 'month', '月份', 'int(10) NOT NULL', 'num', '', '', '1', '', '1188', '0', '1', '1401371192', '1401371192', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11705', 'day', '日期', 'int(10) NOT NULL', 'num', '', '', '1', '', '1188', '0', '1', '1401371209', '1401371209', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11706', 'content', '统计数据', 'text NOT NULL', 'textarea', '', '', '1', '', '1188', '0', '1', '1401371292', '1401371292', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11707', 'keyword', '关键词', 'varchar(100) NOT NULL', 'string', '', '', '1', '', '1189', '1', '1', '1396624337', '1396061575', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11708', 'keyword_type', '关键词匹配类型', 'tinyint(2) NOT NULL', 'select', '0', '', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配', '1189', '1', '1', '1396624426', '1396061765', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11709', 'title', '问卷标题', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '1189', '1', '1', '1396624461', '1396061859', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11710', 'intro', '封面简介', 'text NOT NULL', 'textarea', '', '', '1', '', '1189', '0', '1', '1396624505', '1396061947', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11711', 'mTime', '修改时间', 'int(10) NOT NULL', 'datetime', '', '', '0', '', '1189', '0', '1', '1396624664', '1396624664', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11552', 'shop_logo', '商家LOGO', 'int(10) UNSIGNED NULL', 'picture', '', '', '1', '', '1172', '0', '1', '1427280293', '1427280293', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11553', 'head_bg_color', '头部背景颜色', 'varchar(255) NULL', 'string', '#35a2dd', '', '1', '', '1172', '0', '1', '1427282839', '1427282785', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11554', 'button_color', '按钮颜色', 'varchar(255) NULL', 'string', '#0dbd02', '', '1', '', '1172', '0', '1', '1427282886', '1427282886', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11555', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1172', '0', '1', '1430127336', '1430127336', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11556', 'member', '选择人群', 'varchar(100) NULL', 'checkbox', '0', '', '1', '0:所有用户\r\n-1:所有会员', '1172', '0', '1', '1444821380', '1444821380', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11557', 'is_del', '是否删除', 'int(10) NULL', 'num', '0', '', '0', '', '1172', '0', '1', '1446119564', '1446119564', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11594', 'uid', 'uid', 'int(10) NULL', 'num', '', '', '0', '', '1178', '0', '1', '1435561416', '1435561392', '', '3', '', 'regex', 'get_mid', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1823', 'is_material', '设置为文本素材', 'int(10) NULL', 'num', '0', '', '0', '0:不设置\r\n1:设置', '103', '0', '1', '1445497359', '1445497359', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1824', 'admin_uid', '核销管理员ID', 'int(10) NULL', 'num', '', '', '0', '', '81', '0', '1', '1445504807', '1445504807', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1839', 'msgtype', '消息类型', 'varchar(255) NULL', 'string', '', '', '1', '', '18', '0', '1', '1445833955', '1445833955', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1840', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '1', '', '18', '0', '1', '1445834006', '1445834006', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1841', 'appmsg_id', '图文id', 'int(10) NULL', 'num', '', '', '1', '', '18', '0', '1', '1445840292', '1445834101', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1842', 'voice_id', '语音id', 'int(10) NULL', 'num', '', '', '1', '', '18', '0', '1', '1445834144', '1445834144', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1843', 'video_id', '视频id', 'int(10) NULL', 'num', '', '', '1', '', '18', '0', '1', '1445834174', '1445834174', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('1851', 'cTime', '群发时间', 'int(10) NULL', 'datetime', '', '', '1', '', '18', '0', '1', '1445856491', '1445856442', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11886', 'follower_condtion', '粉丝状态', 'char(50) NULL', 'select', '1', '粉丝达到设置的状态才能获取', '1', '0:不限制\r\n1:已关注\r\n2:已绑定信息\r\n3:会员卡成员', '1210', '0', '1', '1399260479', '1399260479', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11887', 'credit_conditon', '积分限制', 'int(10) unsigned NULL ', 'num', '0', '粉丝达到多少积分后才能领取，领取后不扣积分', '1', '', '1210', '0', '1', '1399260618', '1399260618', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11888', 'credit_bug', '积分消费', 'int(10) unsigned NULL ', 'num', '0', '用积分中的财富兑换、兑换后扣除相应的积分财富', '1', '', '1210', '0', '1', '1399260764', '1399260764', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11889', 'addon_condition', '插件场景限制', 'varchar(255) NULL', 'string', '', '格式：[插件名:id值]，如[投票:10]表示对ID为10的投票投完才能领取，更多的说明见表单上的提示', '1', '', '1210', '0', '1', '1399274022', '1399261026', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11890', 'collect_count', '已领取人数', 'int(10) unsigned NULL ', 'num', '0', '', '1', '', '1210', '0', '1', '1420980201', '1399270900', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11891', 'view_count', '浏览人数', 'int(10) unsigned NULL ', 'num', '0', '', '1', '', '1210', '0', '1', '1420980183', '1399270926', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11892', 'update_time', '更新时间', 'int(10) NULL', 'datetime', '', '', '0', '', '1210', '0', '1', '1399562468', '1399359204', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11893', 'template', '素材模板', 'varchar(255) NULL', 'string', 'default', '', '1', '', '1210', '0', '1', '1430201266', '1430201266', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11894', 'fid', '', 'int(11) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033503', '1404033503', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11895', 'token', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033503', '1404033503', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11896', 'openid', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033503', '1404033503', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11897', 'date', '', 'varchar(11) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033504', '1404033504', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11898', 'enddate', '', 'int(11) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033504', '1404033504', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11899', 'model', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033504', '1404033504', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11900', 'num', '', 'int(11) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033505', '1404033505', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11901', 'keyword', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033505', '1404033505', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11902', 'type', '', 'tinyint(1) NULL ', 'string', '', '', '1', '', '1211', '0', '1', '1404033505', '1404033505', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11903', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1212', '0', '1', '1404485505', '1404475530', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11904', 'groupname', '分组名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1212', '0', '1', '1404475556', '1404475556', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11905', 'groupdata', '分组数据源', 'text NULL', 'textarea', '', '', '0', '', '1212', '0', '1', '1404476127', '1404476127', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11906', 'msgkeyword', '消息关键字', 'varchar(555) NULL', 'string', '', '当用户发送的消息中含有关键字时,将自动转到分配的客服人员', '1', '', '1213', '0', '1', '1404399336', '1404399336', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11907', 'msgkeyword_type', '关键字类型', 'char(50) NULL', 'select', '3', '选择关键字匹配的类型', '1', '0:完全匹配\r\n1:左边匹配\r\n2:右边匹配\r\n3:模糊匹配\r\n4:正则匹配\r\n5:随机匹配', '1213', '0', '1', '1404399466', '1404399466', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11908', 'msgkfaccount', '接待的客服人员', 'varchar(255) NULL', 'string', '', '', '0', '', '1213', '0', '1', '1404403340', '1404399587', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11909', 'cTime', '创建时间', 'int(10) NULL', 'date', '', '', '0', '', '1213', '0', '1', '1404399629', '1404399629', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11910', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1213', '0', '1', '1404399656', '1404399656', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11911', 'msgstate', '关键字状态', 'tinyint(2) NULL', 'bool', '1', '停用后用户将不会触发此关键词', '1', '0:停用\r\n1:启用', '1213', '0', '1', '1404399749', '1404399749', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11912', 'zjnum', '转接次数', 'int(10) NULL', 'num', '', '', '0', '', '1213', '0', '1', '1404399784', '1404399784', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11913', 'zdtype', '指定类型', 'char(10) NULL', 'radio', '0', '选择关键字匹配时是按指定人员或者指定客服组', '1', '0:指定客服人员\r\n1:指定客服组', '1213', '0', '1', '1404474672', '1404474672', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11914', 'kfgroupid', '客服分组id', 'int(10) NULL', 'num', '0', '', '0', '', '1213', '0', '1', '1404474777', '1404474777', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11915', 'pid', '', 'int(11) NULL ', 'string', '', '', '1', '', '1214', '0', '1', '1403947272', '1403947272', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11916', 'openid', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1214', '0', '1', '1403947273', '1403947273', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11917', 'enddate', '', 'int(11) NULL ', 'string', '', '', '1', '', '1214', '0', '1', '1403947273', '1403947273', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11918', 'keyword', '', 'varchar(200) NULL ', 'string', '', '', '1', '', '1214', '0', '1', '1403947274', '1403947274', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11919', 'status', '', 'tinyint(1) NULL ', 'string', '2', '', '1', '', '1214', '0', '1', '1403947274', '1403947274', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11920', 'name', '客服昵称', 'varchar(60) NULL ', 'string', '', '', '1', '', '1215', '0', '1', '1403959775', '1403947255', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11921', 'token', 'token', 'varchar(60) NULL ', 'string', '', '', '0', '', '1215', '0', '1', '1403959638', '1403947256', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11922', 'userName', '客服帐号', 'varchar(60) NULL ', 'string', '', '', '1', '', '1215', '0', '1', '1403959752', '1403947256', '', '3', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11923', 'userPwd', '客服密码', 'varchar(32) NULL ', 'string', '', '', '1', '', '1215', '0', '1', '1403959722', '1403947257', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11924', 'endJoinDate', '客服加入时间', 'int(11) NULL ', 'string', '', '', '0', '', '1215', '0', '1', '1403959825', '1403947257', '', '3', '', 'regex', 'time', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11925', 'status', '客服在线状态', 'tinyint(1) NULL ', 'bool', '0', '', '0', '0:离线\r\n1:在线', '1215', '0', '1', '1404016782', '1403947258', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `wp_attribute` VALUES ('11926', 'state', '客服状态', 'tinyint(2) NULL', 'bool', '0', '', '1', '0:停用\r\n1:启用', '1215', '0', '1', '1404016877', '1404016877', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11927', 'isdelete', '是否删除', 'tinyint(2) NULL', 'bool', '0', '', '0', '0:正常\r\n1:已被删除', '1215', '0', '1', '1404016931', '1404016931', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11928', 'kfid', '客服编号', 'varchar(255) NULL', 'string', '', '', '1', '', '1215', '0', '1', '1404398387', '1404398387', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11929', 'openid', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1216', '0', '1', '1404026716', '1404026716', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11930', 'enddate', '', 'int(11) NULL ', 'string', '', '', '1', '', '1216', '0', '1', '1404026716', '1404026716', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11931', 'joinUpDate', '', 'int(11) NULL ', 'string', '0', '', '1', '', '1216', '0', '1', '1404026716', '1404026716', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11932', 'uid', '', 'int(11) NULL ', 'string', '0', '', '1', '', '1216', '0', '1', '1404026717', '1404026717', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11933', 'token', '', 'varchar(40) NULL ', 'string', '', '', '1', '', '1216', '0', '1', '1404026717', '1404026717', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11934', 'g_id', '', 'varchar(20) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027302', '1404027302', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11935', 'nickname', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027302', '1404027302', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11936', 'sex', '', 'tinyint(1) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027303', '1404027303', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11937', 'province', '', 'varchar(20) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027303', '1404027303', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11938', 'city', '', 'varchar(30) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027303', '1404027303', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11939', 'headimgurl', '', 'varchar(200) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027304', '1404027304', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11940', 'subscribe_time', '', 'int(11) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027304', '1404027304', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11941', 'token', '', 'varchar(30) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027305', '1404027305', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11942', 'openid', '', 'varchar(60) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027305', '1404027305', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11943', 'status', '', 'tinyint(1) NULL ', 'string', '', '', '1', '', '1217', '0', '1', '1404027305', '1404027305', '', '0', '', '', '', '0', '');
INSERT INTO `wp_attribute` VALUES ('11944', 'opercode', '会话状态', 'int(10) NULL', 'num', '', '', '1', '', '1218', '0', '1', '1406094322', '1406094322', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11945', 'text', '消息', 'text NULL', 'textarea', '', '', '1', '', '1218', '0', '1', '1406094387', '1406094387', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11946', 'time', '时间', 'int(10) NULL', 'datetime', '', '', '1', '', '1218', '0', '1', '1406094341', '1406094341', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11947', 'openid', 'openid', 'varchar(255) NULL', 'string', '', '', '1', '', '1218', '0', '1', '1406094276', '1406094276', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11948', 'token', 'token', 'varchar(255) NULL', 'string', '', '', '0', '', '1218', '0', '1', '1406094177', '1406094177', '', '3', '', 'regex', 'get_token', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11949', 'worker', '客服名称', 'varchar(255) NULL', 'string', '', '', '1', '', '1218', '0', '1', '1406094257', '1406094257', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11242', 'is_use', '可否使用', 'int(10) NULL', 'num', '1', '', '0', '0:不可用\r\n1:可用', '149', '0', '1', '1447405173', '1447403730', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11243', 'aim_id', '添加来源标识id', 'int(10) NULL', 'num', '', '', '0', '', '149', '0', '1', '1447404930', '1447404930', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11244', 'aim_table', '来源表名', 'varchar(255) NULL', 'string', '', '', '1', '', '149', '0', '1', '1447405156', '1447405156', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11245', 'is_use', '可否使用', 'int(10) NULL', 'num', '1', '', '0', '0:不可用\r\n1:可用', '16', '0', '1', '1447405234', '1447405234', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11246', 'aim_id', '添加来源标识id', 'int(10) NULL', 'num', '', '', '0', '', '16', '0', '1', '1447405283', '1447405283', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11247', 'aim_table', '来源表名', 'varchar(255) NULL', 'string', '', '', '1', '', '16', '0', '1', '1447405301', '1447405301', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11248', 'is_use', '可否使用', 'int(10) NULL', 'num', '1', '', '0', '0:不可用\r\n1:可用', '17', '0', '1', '1447405553', '1447405510', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11249', 'aim_id', '添加来源标识id', 'int(10) NULL', 'num', '', '', '0', '', '17', '0', '1', '1447405545', '1447405545', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` VALUES ('11250', 'aim_table', '来源表名', 'varchar(255) NULL', 'string', '', '', '0', '', '17', '0', '1', '1447405577', '1447405577', '', '3', '', 'regex', '', '3', 'function');

-- ----------------------------
-- Table structure for `wp_auth_extend`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_extend`;
CREATE TABLE `wp_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';

-- ----------------------------
-- Records of wp_auth_extend
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_group`;
CREATE TABLE `wp_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(30) DEFAULT NULL COMMENT '分组名称',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '图标',
  `description` text COMMENT '描述信息',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态',
  `type` tinyint(2) DEFAULT '1' COMMENT '类型',
  `rules` text COMMENT '权限',
  `manager_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认自动加入',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '微信二维码',
  `wechat_group_id` int(10) DEFAULT '-1' COMMENT '微信端的分组ID',
  `wechat_group_name` varchar(100) DEFAULT NULL COMMENT '微信端的分组名',
  `wechat_group_count` int(10) DEFAULT NULL COMMENT '微信端用户数',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auth_group
-- ----------------------------
INSERT INTO `wp_auth_group` VALUES ('1', '默认用户组', null, '通用的用户组', '1', '0', '1,2,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,96,97,100,102,103,105,106', '0', '', '0', '', null, '', null, '0');
INSERT INTO `wp_auth_group` VALUES ('2', '公众号粉丝组', null, '所有从公众号自动注册的粉丝用户都会自动加入这个用户组', '1', '0', '1,2,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,82,83,84,88,89,90,91,92,93,96,97,100,102,103,195', '0', '', '0', '', null, '', null, '0');
INSERT INTO `wp_auth_group` VALUES ('3', '公众号管理组', null, '公众号管理员注册时会自动加入这个用户组', '1', '0', '', '0', '', '0', '', null, '', null, '0');

-- ----------------------------
-- Table structure for `wp_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_group_access`;
CREATE TABLE `wp_auth_group_access` (
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auth_group_access
-- ----------------------------
INSERT INTO `wp_auth_group_access` VALUES ('1', '3');

-- ----------------------------
-- Table structure for `wp_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_rule`;
CREATE TABLE `wp_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  `group` char(30) DEFAULT '默认分组',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=280 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auth_rule
-- ----------------------------
INSERT INTO `wp_auth_rule` VALUES ('241', 'Admin/Rule/createRule', '权限节点管理', '1', '', '默认分组');
INSERT INTO `wp_auth_rule` VALUES ('242', 'Admin/AuthManager/index', '用户组管理', '1', '', '默认分组');
INSERT INTO `wp_auth_rule` VALUES ('243', 'Admin/User/index', '用户信息', '1', '', '用户管理');

-- ----------------------------
-- Table structure for `wp_auto_reply`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auto_reply`;
CREATE TABLE `wp_auto_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `msg_type` char(50) DEFAULT 'text' COMMENT '消息类型',
  `content` text COMMENT '文本内容',
  `group_id` int(10) DEFAULT NULL COMMENT '图文',
  `image_id` int(10) unsigned DEFAULT NULL COMMENT '上传图片',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `token` varchar(50) DEFAULT NULL COMMENT 'Token',
  `image_material` int(10) DEFAULT NULL COMMENT '素材图片id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auto_reply
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_business_card`
-- ----------------------------
DROP TABLE IF EXISTS `wp_business_card`;
CREATE TABLE `wp_business_card` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `permission` char(50) DEFAULT '1' COMMENT '权限设置',
  `intro` text COMMENT '个人介绍',
  `wishing` varchar(100) DEFAULT NULL COMMENT '希望结交',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信号',
  `Email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `telephone` varchar(30) DEFAULT NULL COMMENT '座机',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `company_url` varchar(255) DEFAULT NULL COMMENT '公司网址',
  `department` varchar(50) DEFAULT NULL COMMENT '所属部门',
  `company` varchar(100) DEFAULT NULL COMMENT '公司名称',
  `mobile` varchar(30) DEFAULT NULL COMMENT '手机',
  `position` varchar(50) DEFAULT NULL COMMENT '职位头衔',
  `headface` int(10) unsigned DEFAULT NULL COMMENT '头像',
  `personal_url` varchar(255) DEFAULT NULL COMMENT '个人主页',
  `interest` varchar(255) DEFAULT NULL COMMENT '兴趣',
  `tag` varchar(255) DEFAULT NULL COMMENT '人物标签',
  `weibo` varchar(255) DEFAULT NULL COMMENT '微博',
  `qq` varchar(30) DEFAULT NULL COMMENT 'QQ号',
  `service` text COMMENT '产品服务',
  `truename` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `template` varchar(50) DEFAULT NULL COMMENT '选择的模板',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_business_card
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_business_card_collect`
-- ----------------------------
DROP TABLE IF EXISTS `wp_business_card_collect`;
CREATE TABLE `wp_business_card_collect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `from_uid` int(10) DEFAULT NULL COMMENT '收藏人ID',
  `to_uid` int(10) DEFAULT NULL COMMENT '被收藏人的ID',
  `cTime` int(10) DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_business_card_collect
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_business_card_column`
-- ----------------------------
DROP TABLE IF EXISTS `wp_business_card_column`;
CREATE TABLE `wp_business_card_column` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `business_card_id` int(10) DEFAULT NULL COMMENT '名片id',
  `type` char(10) DEFAULT '0' COMMENT '栏目类型',
  `cate_id` varchar(100) DEFAULT '0' COMMENT '分类',
  `title` varchar(255) DEFAULT NULL COMMENT '栏目名称',
  `url` varchar(255) DEFAULT NULL COMMENT '跳转url',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_business_card_column
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_card_vouchers`
-- ----------------------------
DROP TABLE IF EXISTS `wp_card_vouchers`;
CREATE TABLE `wp_card_vouchers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '素材封面',
  `balance` varchar(30) DEFAULT NULL COMMENT '红包余额',
  `card_id` varchar(100) NOT NULL COMMENT '卡券ID',
  `openid` text COMMENT 'OpenID',
  `appsecre` varchar(255) NOT NULL COMMENT '开通卡券的商家公众号密钥',
  `code` text COMMENT '卡券code码',
  `content` text COMMENT '活动介绍',
  `background` int(10) unsigned DEFAULT NULL COMMENT '背景图',
  `token` varchar(50) DEFAULT NULL COMMENT 'token',
  `title` varchar(255) DEFAULT '卡券' COMMENT '卡券标题',
  `button_color` varchar(255) DEFAULT '#0dbd02' COMMENT '领取按钮颜色',
  `head_bg_color` varchar(255) DEFAULT '#35a2dd' COMMENT '头部背景颜色',
  `shop_name` varchar(255) DEFAULT NULL COMMENT '商家名称',
  `shop_logo` int(10) unsigned DEFAULT NULL COMMENT '商家LOGO',
  `more_button` text COMMENT '添加更多按钮',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_card_vouchers
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_channel`
-- ----------------------------
DROP TABLE IF EXISTS `wp_channel`;
CREATE TABLE `wp_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级频道ID',
  `title` char(30) NOT NULL COMMENT '频道标题',
  `url` char(100) NOT NULL COMMENT '频道连接',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `target` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_channel
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_comment`
-- ----------------------------
DROP TABLE IF EXISTS `wp_comment`;
CREATE TABLE `wp_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `aim_table` varchar(30) DEFAULT NULL COMMENT '评论关联数据表',
  `aim_id` int(10) DEFAULT NULL COMMENT '评论关联ID',
  `cTime` int(10) DEFAULT NULL COMMENT '评论时间',
  `follow_id` int(10) DEFAULT NULL COMMENT 'follow_id',
  `content` text COMMENT '评论内容',
  `is_audit` tinyint(2) DEFAULT '0' COMMENT '是否审核',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_common_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_common_category`;
CREATE TABLE `wp_common_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) DEFAULT NULL COMMENT '分类标识',
  `title` varchar(255) NOT NULL COMMENT '分类标题',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '上一级分类',
  `path` varchar(255) DEFAULT NULL COMMENT '分类路径',
  `module` varchar(255) DEFAULT NULL COMMENT '分类所属功能',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `intro` varchar(255) DEFAULT NULL COMMENT '分类描述',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `code` varchar(255) DEFAULT NULL COMMENT '分类扩展编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_common_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_common_category_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_common_category_group`;
CREATE TABLE `wp_common_category_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) NOT NULL COMMENT '分组标识',
  `title` varchar(255) NOT NULL COMMENT '分组标题',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `level` tinyint(1) unsigned DEFAULT '3' COMMENT '最多级数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_common_category_group
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_config`;
CREATE TABLE `wp_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of wp_config
-- ----------------------------
INSERT INTO `wp_config` VALUES ('1', 'WEB_SITE_TITLE', '1', '网站标题', '1', '', '网站标题前台显示标题', '1378898976', '1430825115', '1', 'weiphp3.0', '0');
INSERT INTO `wp_config` VALUES ('2', 'WEB_SITE_DESCRIPTION', '2', '网站描述', '1', '', '网站搜索引擎描述', '1378898976', '1379235841', '1', 'weiphp是互联网+的IT综合解决方案', '1');
INSERT INTO `wp_config` VALUES ('3', 'WEB_SITE_KEYWORD', '2', '网站关键字', '1', '', '网站搜索引擎关键字', '1378898976', '1381390100', '1', 'weiphp,互联网+,微信开源开发框架', '8');
INSERT INTO `wp_config` VALUES ('4', 'WEB_SITE_CLOSE', '4', '关闭站点', '1', '0:关闭\r\n1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', '1378898976', '1406859591', '1', '1', '1');
INSERT INTO `wp_config` VALUES ('9', 'CONFIG_TYPE_LIST', '3', '配置类型列表', '4', '', '主要用于数据解析和页面表单的生成', '1378898976', '1379235348', '1', '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', '2');
INSERT INTO `wp_config` VALUES ('10', 'WEB_SITE_ICP', '1', '网站备案号', '1', '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', '1378900335', '1379235859', '1', '', '9');
INSERT INTO `wp_config` VALUES ('11', 'DOCUMENT_POSITION', '3', '文档推荐位', '2', '', '文档推荐位，推荐到多个位置KEY值相加即可', '1379053380', '1379235329', '1', '1:列表页推荐\r\n2:频道页推荐\r\n4:网站首页推荐', '3');
INSERT INTO `wp_config` VALUES ('12', 'DOCUMENT_DISPLAY', '3', '文档可见性', '2', '', '文章可见性仅影响前台显示，后台不收影响', '1379056370', '1379235322', '1', '0:所有人可见\r\n1:仅注册会员可见\r\n2:仅管理员可见', '4');
INSERT INTO `wp_config` VALUES ('13', 'COLOR_STYLE', '4', '后台色系', '1', 'default_color:默认\r\nblue_color:紫罗兰', '后台颜色风格', '1379122533', '1379235904', '1', 'default_color', '10');
INSERT INTO `wp_config` VALUES ('20', 'CONFIG_GROUP_LIST', '3', '配置分组', '4', '', '配置分组', '1379228036', '1384418383', '1', '1:基本\r\n3:用户\r\n4:系统\r\n5:站点', '4');
INSERT INTO `wp_config` VALUES ('21', 'HOOKS_TYPE', '3', '钩子的类型', '4', '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', '1379313397', '1379313407', '1', '1:视图\r\n2:控制器', '6');
INSERT INTO `wp_config` VALUES ('22', 'AUTH_CONFIG', '3', 'Auth配置', '4', '', '自定义Auth.class.php类配置', '1379409310', '1379409564', '1', 'AUTH_ON:1\r\nAUTH_TYPE:2', '8');
INSERT INTO `wp_config` VALUES ('23', 'OPEN_DRAFTBOX', '4', '是否开启草稿功能', '2', '0:关闭草稿功能\r\n1:开启草稿功能\r\n', '新增文章时的草稿功能配置', '1379484332', '1379484591', '1', '1', '1');
INSERT INTO `wp_config` VALUES ('24', 'DRAFT_AOTOSAVE_INTERVAL', '0', '自动保存草稿时间', '2', '', '自动保存草稿的时间间隔，单位：秒', '1379484574', '1386143323', '1', '60', '2');
INSERT INTO `wp_config` VALUES ('25', 'LIST_ROWS', '0', '后台每页记录数', '4', '', '后台数据每页显示记录数', '1379503896', '1391938052', '1', '20', '10');
INSERT INTO `wp_config` VALUES ('26', 'USER_ALLOW_REGISTER', '4', '是否允许用户注册', '3', '0:关闭注册\r\n1:允许注册', '是否开放用户注册', '1379504487', '1379504580', '1', '1', '0');
INSERT INTO `wp_config` VALUES ('27', 'CODEMIRROR_THEME', '4', '预览插件的CodeMirror主题', '4', '3024-day:3024 day\r\n3024-night:3024 night\r\nambiance:ambiance\r\nbase16-dark:base16 dark\r\nbase16-light:base16 light\r\nblackboard:blackboard\r\ncobalt:cobalt\r\neclipse:eclipse\r\nelegant:elegant\r\nerlang-dark:erlang-dark\r\nlesser-dark:lesser-dark\r\nmidnight:midnight', '详情见CodeMirror官网', '1379814385', '1384740813', '1', 'ambiance', '3');
INSERT INTO `wp_config` VALUES ('28', 'DATA_BACKUP_PATH', '1', '数据库备份根路径', '4', '', '路径必须以 / 结尾', '1381482411', '1381482411', '1', './Data/', '5');
INSERT INTO `wp_config` VALUES ('29', 'DATA_BACKUP_PART_SIZE', '0', '数据库备份卷大小', '4', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '1381482488', '1381729564', '1', '20971520', '7');
INSERT INTO `wp_config` VALUES ('30', 'DATA_BACKUP_COMPRESS', '4', '数据库备份文件是否启用压缩', '4', '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1381713345', '1381729544', '1', '1', '9');
INSERT INTO `wp_config` VALUES ('31', 'DATA_BACKUP_COMPRESS_LEVEL', '4', '数据库备份文件压缩级别', '4', '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '1381713408', '1381713408', '1', '9', '10');
INSERT INTO `wp_config` VALUES ('32', 'DEVELOP_MODE', '4', '开启开发者模式', '4', '0:关闭\r\n1:开启', '是否开启开发者模式', '1383105995', '1440555973', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('33', 'ALLOW_VISIT', '3', '不受限控制器方法', '0', '', '', '1386644047', '1386644741', '1', '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname', '0');
INSERT INTO `wp_config` VALUES ('34', 'DENY_VISIT', '3', '超管专限控制器方法', '0', '', '仅超级管理员可访问的控制器方法', '1386644141', '1386644659', '1', '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', '0');
INSERT INTO `wp_config` VALUES ('35', 'REPLY_LIST_ROWS', '0', '回复列表每页条数', '2', '', '', '1386645376', '1387178083', '1', '20', '0');
INSERT INTO `wp_config` VALUES ('36', 'ADMIN_ALLOW_IP', '2', '后台允许访问IP', '4', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1387165454', '1387165553', '1', '', '12');
INSERT INTO `wp_config` VALUES ('37', 'SHOW_PAGE_TRACE', '4', '是否显示页面Trace', '4', '0:关闭\r\n1:开启', '是否显示页面Trace信息', '1387165685', '1387165685', '1', '0', '1');
INSERT INTO `wp_config` VALUES ('38', 'WEB_SITE_VERIFY', '4', '登录验证码', '1', '0:关闭\r\n1:开启', '登录时是否需要验证码', '1378898976', '1406859544', '1', '1', '2');
INSERT INTO `wp_config` VALUES ('42', 'ACCESS', '2', '未登录时可访问的页面', '4', '', '不区分大小写', '1390656601', '1390664079', '1', 'Home/User/*\r\nHome/Index/*\r\nhome/weixin/*\r\nadmin/File/*\r\nhome/File/*\r\nhome/Forum/*\r\nHome/Material/detail', '0');
INSERT INTO `wp_config` VALUES ('44', 'DEFAULT_PUBLIC_GROUP_ID', '0', '公众号默认等级ID', '3', '', '前台新增加的公众号的默认等级，值为0表示不做权限控制，公众号拥有全部插件的权限', '1393759885', '1393759981', '1', '0', '2');
INSERT INTO `wp_config` VALUES ('45', 'SYSTEM_UPDATE_REMIND', '4', '系统升级提醒', '4', '0:关闭\r\n1:开启', '开启后官方有新升级信息会及时在后台的网站设置页面头部显示升级提醒', '1393764263', '1393764263', '1', '0', '5');
INSERT INTO `wp_config` VALUES ('46', 'SYSTEM_UPDATRE_VERSION', '0', '系统升级最新版本号', '4', '', '记录当前系统的版本号，这是与官方比较是否有升级包的唯一标识，不熟悉者只勿改变其数值', '1393764702', '1394337646', '1', '20150826', '0');
INSERT INTO `wp_config` VALUES ('47', 'FOLLOW_YOUKE_UID', '0', '粉丝游客ID', '0', '', '', '1398927704', '1398927704', '1', '-11876', '0');
INSERT INTO `wp_config` VALUES ('48', 'DEFAULT_PUBLIC', '0', '注册后默认可管理的公众号ID', '3', '', '可为空。配置用户注册后即可管理的公众号ID，多个时用英文逗号分割', '1398928794', '1398929088', '1', '', '3');
INSERT INTO `wp_config` VALUES ('49', 'DEFAULT_PUBLIC_CREATE_MAX_NUMB', '0', '默认用户最多可创建的公众号数', '3', '', '注册用户最多的创建数，也可以在用户管理里对每个用户设置不同的值', '1398949652', '1398950115', '1', '5', '4');
INSERT INTO `wp_config` VALUES ('50', 'COPYRIGHT', '1', '版权信息', '1', '', '', '1401018910', '1401018910', '1', '版本由圆梦云科技有限公司所有', '3');
INSERT INTO `wp_config` VALUES ('51', 'WEIPHP_STORE_LICENSE', '1', '应用商店授权许可证', '1', '', '要与 应用商店》网站信息 里的授权许可证保持一致', '1402972720', '1402977473', '1', '', '0');
INSERT INTO `wp_config` VALUES ('52', 'SYSTEM_LOGO', '1', '网站LOGO', '5', '', '填写LOGO的网址，为空时默认显示weiphp的logo', '1403566699', '1403566746', '1', '', '0');
INSERT INTO `wp_config` VALUES ('53', 'SYSTEM_CLOSE_REGISTER', '4', '前台注册开关', '5', '0:不关闭\r\n1:关闭', '关闭后在登录页面不再显示注册链接', '1403568006', '1403568006', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('54', 'SYSTEM_CLOSE_ADMIN', '4', '后台管理开关', '5', '0:不关闭\r\n1:关闭', '关闭后在登录页面不再显示后台登录链接', '1403568006', '1403568006', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('55', 'SYSTEM_CLOSE_WIKI', '4', '二次开发开关', '5', '0:不关闭\r\n1:关闭', '关闭后在登录页面不再显示二次开发链接', '1403568006', '1403568006', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('56', 'SYSTEM_CLOSE_BBS', '4', '官方论坛开关', '5', '0:不关闭\r\n1:关闭', '关闭后在登录页面不再显示官方论坛链接', '1403568006', '1403568006', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('57', 'LOGIN_BACKGROUP', '1', '登录界面背景图', '5', '', '请输入图片网址，为空时默认使用自带的背景图', '1403568006', '1403570059', '1', '', '0');
INSERT INTO `wp_config` VALUES ('60', 'TONGJI_CODE', '2', '第三方统计JS代码', '5', '', '', '1428634717', '1428634717', '1', '', '0');
INSERT INTO `wp_config` VALUES ('61', 'SENSITIVE_WORDS', '1', '敏感词', '0', '', '当出现有敏感词的地方，会用*号代替, (多个敏感词用 , 隔开 )', '1433125977', '1435062628', '1', 'bitch,shit', '0');
INSERT INTO `wp_config` VALUES ('63', 'PUBLIC_BIND', '4', '公众号第三方平台', '5', '0:关闭\r\n1:开启', '申请审核通过微信开放平台里的公众号第三方平台账号后，就可以开启体验了', '1434542818', '1434542818', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('64', 'COMPONENT_APPID', '1', '公众号开放平台的AppID', '5', '', '公众号第三方平台开启后必填的参数', '1434542891', '1434542975', '1', '', '0');
INSERT INTO `wp_config` VALUES ('65', 'COMPONENT_APPSECRET', '1', '公众号开放平台的AppSecret', '5', '', '公众号第三方平台开启后必填的参数', '1434542936', '1434542984', '1', '', '0');
INSERT INTO `wp_config` VALUES ('62', 'REG_AUDIT', '4', '注册审核', '3', '0:需要审核\r\n1:不需要审核', '', '1439811099', '1439811099', '1', '1', '1');

-- ----------------------------
-- Table structure for `wp_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `wp_coupon`;
CREATE TABLE `wp_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `background` int(10) unsigned DEFAULT NULL COMMENT '素材背景图',
  `keyword` varchar(100) DEFAULT NULL COMMENT '关键词',
  `use_tips` text COMMENT '使用说明',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `end_time` int(10) DEFAULT NULL COMMENT '领取结束时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '优惠券图片',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_tips` text COMMENT '领取结束说明',
  `end_img` int(10) unsigned DEFAULT NULL COMMENT '领取结束提示图片',
  `num` int(10) unsigned DEFAULT '0' COMMENT '优惠券数量',
  `max_num` int(10) unsigned DEFAULT '1' COMMENT '每人最多允许获取次数',
  `follower_condtion` char(50) DEFAULT '1' COMMENT '粉丝状态',
  `credit_conditon` int(10) unsigned DEFAULT '0' COMMENT '积分限制',
  `credit_bug` int(10) unsigned DEFAULT '0' COMMENT '积分消费',
  `addon_condition` varchar(255) DEFAULT NULL COMMENT '插件场景限制',
  `collect_count` int(10) unsigned DEFAULT '0' COMMENT '已领取数',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览人数',
  `addon` char(50) DEFAULT 'public' COMMENT '插件',
  `shop_uid` varchar(255) DEFAULT NULL COMMENT '商家管理员ID',
  `use_count` int(10) DEFAULT '0' COMMENT '已使用数',
  `pay_password` varchar(255) DEFAULT NULL COMMENT '核销密码',
  `empty_prize_tips` varchar(255) DEFAULT NULL COMMENT '奖品抽完后的提示',
  `start_tips` varchar(255) DEFAULT NULL COMMENT '活动还没开始时的提示语',
  `more_button` text COMMENT '其它按钮',
  `over_time` int(10) DEFAULT NULL COMMENT '使用的截止时间',
  `use_start_time` int(10) DEFAULT NULL COMMENT '使用开始时间',
  `shop_name` varchar(255) DEFAULT '优惠商家' COMMENT '商家名称',
  `shop_logo` int(10) unsigned DEFAULT NULL COMMENT '商家LOGO',
  `head_bg_color` varchar(255) DEFAULT '#35a2dd' COMMENT '头部背景颜色',
  `button_color` varchar(255) DEFAULT '#0dbd02' COMMENT '按钮颜色',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  `member` varchar(100) DEFAULT '0' COMMENT '选择人群',
  `is_del` int(10) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_coupon_shop`
-- ----------------------------
DROP TABLE IF EXISTS `wp_coupon_shop`;
CREATE TABLE `wp_coupon_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) DEFAULT NULL COMMENT '店名',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `phone` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `gps` varchar(50) DEFAULT NULL COMMENT 'GPS经纬度',
  `coupon_id` int(10) DEFAULT NULL COMMENT '所属优惠券编号',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `open_time` varchar(50) DEFAULT NULL COMMENT '营业时间',
  `img` int(10) unsigned DEFAULT NULL COMMENT '门店展示图',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_coupon_shop
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_coupon_shop_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_coupon_shop_link`;
CREATE TABLE `wp_coupon_shop_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `coupon_id` int(10) DEFAULT NULL COMMENT 'coupon_id',
  `shop_id` int(10) DEFAULT NULL COMMENT 'shop_id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_coupon_shop_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_credit_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_credit_config`;
CREATE TABLE `wp_credit_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '积分描述',
  `name` varchar(50) DEFAULT NULL COMMENT '积分标识',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `experience` varchar(30) DEFAULT '0' COMMENT '经验值',
  `score` varchar(30) DEFAULT '0' COMMENT '金币值',
  `token` varchar(255) DEFAULT '0' COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_credit_config
-- ----------------------------
INSERT INTO `wp_credit_config` VALUES ('1', '关注公众号', 'subscribe', '1438587911', '100', '100', '0');
INSERT INTO `wp_credit_config` VALUES ('2', '取消关注公众号', 'unsubscribe', '1438596459', '-100', '-100', '0');
INSERT INTO `wp_credit_config` VALUES ('3', '参与投票', 'vote', '1398565597', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('4', '参与调研', 'survey', '1398565640', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('5', '参与考试', 'exam', '1398565659', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('6', '参与测试', 'test', '1398565681', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('7', '微信聊天', 'chat', '1398565740', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('8', '建议意见反馈', 'suggestions', '1398565798', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('9', '会员卡绑定', 'card_bind', '1438596438', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('10', '获取优惠卷', 'coupons', '1398565926', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('11', '访问微网站', 'weisite', '1398565973', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('12', '查看自定义回复内容', 'custom_reply', '1398566068', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('13', '填写通用表单', 'forms', '1398566118', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('14', '访问微商店', 'shop', '1398566206', '0', '0', '0');
INSERT INTO `wp_credit_config` VALUES ('32', '程序自由增加', 'auto_add', '1442659667', '￥', '￥', '0');

-- ----------------------------
-- Table structure for `wp_credit_data`
-- ----------------------------
DROP TABLE IF EXISTS `wp_credit_data`;
CREATE TABLE `wp_credit_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT '0' COMMENT '用户ID',
  `credit_name` varchar(50) DEFAULT NULL COMMENT '积分标识',
  `experience` int(10) DEFAULT '0' COMMENT '体力值',
  `score` int(10) DEFAULT '0' COMMENT '积分值',
  `cTime` int(10) DEFAULT NULL COMMENT '记录时间',
  `admin_uid` int(10) DEFAULT '0' COMMENT '操作者UID',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `credit_title` varchar(50) DEFAULT NULL COMMENT '积分标题',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_credit_data
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_menu`;
CREATE TABLE `wp_custom_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `pid` int(10) DEFAULT '0' COMMENT '一级菜单',
  `title` varchar(50) NOT NULL COMMENT '菜单名',
  `keyword` varchar(100) DEFAULT NULL COMMENT '关联关键词',
  `url` varchar(255) DEFAULT NULL COMMENT '关联URL',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `type` varchar(30) DEFAULT 'click' COMMENT '类型',
  `from_type` char(50) DEFAULT '-1' COMMENT '配置动作',
  `addon` char(50) DEFAULT '0' COMMENT '选择插件',
  `target_id` int(10) DEFAULT NULL COMMENT '选择内容',
  `sucai_type` char(50) DEFAULT '0' COMMENT '素材类型',
  `jump_type` char(10) DEFAULT '0' COMMENT '推送类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_menu
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_reply_mult`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_reply_mult`;
CREATE TABLE `wp_custom_reply_mult` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '关键词类型',
  `mult_ids` varchar(255) DEFAULT NULL COMMENT '多图文ID',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_reply_mult
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_reply_news`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_reply_news`;
CREATE TABLE `wp_custom_reply_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT NULL COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '简介',
  `cate_id` int(10) unsigned DEFAULT '0' COMMENT '所属类别',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `content` text COMMENT '内容',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览数',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `jump_url` varchar(255) DEFAULT NULL COMMENT '外链',
  `author` varchar(50) DEFAULT NULL COMMENT '作者',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_reply_news
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_reply_text`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_reply_text`;
CREATE TABLE `wp_custom_reply_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '关键词类型',
  `content` text COMMENT '回复内容',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览数',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_reply_text
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_sendall`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_sendall`;
CREATE TABLE `wp_custom_sendall` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ToUserName` varchar(255) DEFAULT NULL COMMENT 'token',
  `FromUserName` varchar(255) DEFAULT NULL COMMENT 'openid',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `msgType` varchar(255) DEFAULT NULL COMMENT '消息类型',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `content` text COMMENT '内容',
  `media_id` varchar(255) DEFAULT NULL COMMENT '多媒体文件id',
  `is_send` int(10) DEFAULT NULL COMMENT '是否已经发送',
  `uid` int(10) DEFAULT NULL COMMENT '粉丝uid',
  `news_group_id` varchar(10) DEFAULT NULL COMMENT '图文组id',
  `video_title` varchar(255) DEFAULT NULL COMMENT '视频标题',
  `video_description` text COMMENT '视频描述',
  `video_thumb` varchar(255) DEFAULT NULL COMMENT '视频缩略图',
  `voice_id` int(10) DEFAULT NULL COMMENT '语音id',
  `image_id` int(10) DEFAULT NULL COMMENT '图片id',
  `video_id` int(10) DEFAULT NULL COMMENT '视频id',
  `send_type` int(10) DEFAULT NULL COMMENT '发送方式',
  `send_opends` text COMMENT '指定用户',
  `group_id` int(10) DEFAULT NULL COMMENT '分组id',
  `diff` int(10) DEFAULT '0' COMMENT '区分消息标识',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_sendall
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_customer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_customer`;
CREATE TABLE `wp_customer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `sex` varchar(4) DEFAULT '',
  `mobile` varchar(200) DEFAULT '',
  `tel` varchar(200) DEFAULT '',
  `email` varchar(200) DEFAULT '',
  `company` varchar(100) DEFAULT '',
  `job` varchar(20) DEFAULT '',
  `address` varchar(120) DEFAULT '',
  `website` varchar(200) DEFAULT '',
  `qq` varchar(16) DEFAULT '',
  `weixin` varchar(50) DEFAULT '',
  `yixin` varchar(50) DEFAULT '',
  `weibo` varchar(50) DEFAULT '',
  `laiwang` varchar(50) DEFAULT '',
  `remark` varchar(255) DEFAULT '',
  `origin` bigint(20) unsigned NOT NULL DEFAULT '0',
  `originName` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `createUser` varchar(32) NOT NULL DEFAULT '',
  `createTime` int(10) unsigned NOT NULL DEFAULT '0',
  `groupId` varchar(20) NOT NULL DEFAULT '',
  `groupName` varchar(200) DEFAULT '',
  `group` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_draw_follow_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_draw_follow_log`;
CREATE TABLE `wp_draw_follow_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `follow_id` int(10) DEFAULT NULL COMMENT '粉丝id',
  `sports_id` int(10) DEFAULT NULL COMMENT '场次id',
  `count` int(10) DEFAULT '0' COMMENT '抽奖次数',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `cTime` int(10) DEFAULT NULL COMMENT '支持时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  PRIMARY KEY (`id`),
  KEY `sports_id` (`sports_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_draw_follow_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_exam`
-- ----------------------------
DROP TABLE IF EXISTS `wp_exam`;
CREATE TABLE `wp_exam` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词匹配类型',
  `title` varchar(255) NOT NULL COMMENT '试卷标题',
  `intro` text NOT NULL COMMENT '封面简介',
  `mTime` int(10) NOT NULL COMMENT '修改时间',
  `cover` int(10) unsigned NOT NULL COMMENT '封面图片',
  `cTime` int(10) unsigned NOT NULL COMMENT '发布时间',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `finish_tip` text NOT NULL COMMENT '结束语',
  `start_time` int(10) DEFAULT NULL COMMENT '考试开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '考试结束时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_exam
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_exam_answer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_exam_answer`;
CREATE TABLE `wp_exam_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `answer` text NOT NULL COMMENT '回答内容',
  `openid` varchar(255) NOT NULL COMMENT 'OpenId',
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `question_id` int(10) unsigned NOT NULL COMMENT 'question_id',
  `cTime` int(10) unsigned NOT NULL COMMENT '发布时间',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `exam_id` int(10) unsigned NOT NULL COMMENT 'exam_id',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '得分',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_exam_answer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_exam_question`
-- ----------------------------
DROP TABLE IF EXISTS `wp_exam_question`;
CREATE TABLE `wp_exam_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '题目标题',
  `intro` text NOT NULL COMMENT '题目描述',
  `cTime` int(10) unsigned NOT NULL COMMENT '发布时间',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `is_must` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否必填',
  `extra` text NOT NULL COMMENT '参数',
  `type` char(50) NOT NULL DEFAULT 'radio' COMMENT '题目类型',
  `exam_id` int(10) unsigned NOT NULL COMMENT 'exam_id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分值',
  `answer` varchar(255) NOT NULL COMMENT '标准答案',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_exam_question
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_extensions`
-- ----------------------------
DROP TABLE IF EXISTS `wp_extensions`;
CREATE TABLE `wp_extensions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '关键词匹配类型',
  `api_token` varchar(255) NOT NULL COMMENT 'Token',
  `cTime` int(10) NOT NULL COMMENT '创建时间',
  `api_url` varchar(255) NOT NULL COMMENT '第三方URL',
  `output_format` tinyint(1) DEFAULT '0' COMMENT '数据输出格式',
  `keyword_filter` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词过滤',
  `keyword` varchar(255) NOT NULL COMMENT '关键词',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_extensions
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_file`
-- ----------------------------
DROP TABLE IF EXISTS `wp_file`;
CREATE TABLE `wp_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` char(20) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` char(30) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `create_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Records of wp_file
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_forms`
-- ----------------------------
DROP TABLE IF EXISTS `wp_forms`;
CREATE TABLE `wp_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `password` varchar(255) DEFAULT NULL COMMENT '表单密码',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词类型',
  `jump_url` varchar(255) DEFAULT NULL COMMENT '提交后跳转的地址',
  `content` text COMMENT '详细介绍',
  `finish_tip` text COMMENT '用户提交后提示内容',
  `can_edit` tinyint(2) DEFAULT '0' COMMENT '是否允许编辑',
  `intro` text COMMENT '封面简介',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `template` varchar(255) DEFAULT 'default' COMMENT '模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_forms
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_forms_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `wp_forms_attribute`;
CREATE TABLE `wp_forms_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `forms_id` int(10) unsigned DEFAULT NULL COMMENT '表单ID',
  `error_info` varchar(255) DEFAULT NULL COMMENT '出错提示',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `validate_rule` varchar(255) DEFAULT NULL COMMENT '正则验证',
  `is_must` tinyint(2) DEFAULT NULL COMMENT '是否必填',
  `remark` varchar(255) DEFAULT NULL COMMENT '字段备注',
  `name` varchar(100) DEFAULT NULL COMMENT '字段名',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `value` varchar(255) DEFAULT NULL COMMENT '默认值',
  `title` varchar(255) NOT NULL COMMENT '字段标题',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `extra` text COMMENT '参数',
  `type` char(50) NOT NULL DEFAULT 'string' COMMENT '字段类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_forms_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_forms_value`
-- ----------------------------
DROP TABLE IF EXISTS `wp_forms_value`;
CREATE TABLE `wp_forms_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `forms_id` int(10) unsigned DEFAULT NULL COMMENT '表单ID',
  `value` text COMMENT '表单值',
  `cTime` int(10) DEFAULT NULL COMMENT '增加时间',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenId',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_forms_value
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_forum`
-- ----------------------------
DROP TABLE IF EXISTS `wp_forum`;
CREATE TABLE `wp_forum` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `uid` int(10) DEFAULT '0' COMMENT '用户ID',
  `content` text COMMENT '内容',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `attach` varchar(255) DEFAULT NULL COMMENT '附件',
  `is_top` int(10) DEFAULT '0' COMMENT '置顶',
  `cid` tinyint(4) DEFAULT NULL COMMENT '分类',
  `view_count` int(11) unsigned DEFAULT '0' COMMENT '浏览数',
  `reply_count` int(11) unsigned DEFAULT '0' COMMENT '回复数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_forum
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_guess`
-- ----------------------------
DROP TABLE IF EXISTS `wp_guess`;
CREATE TABLE `wp_guess` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '竞猜标题',
  `desc` text COMMENT '活动说明',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `guess_count` int(10) unsigned DEFAULT '0',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '主题图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_guess
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_guess_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_guess_log`;
CREATE TABLE `wp_guess_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '用户ID',
  `guess_id` int(10) unsigned DEFAULT '0' COMMENT '竞猜Id',
  `token` varchar(255) DEFAULT NULL COMMENT '用户token',
  `optionIds` varchar(255) DEFAULT NULL COMMENT '用户猜的选项IDs',
  `cTime` int(10) DEFAULT NULL COMMENT '创时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_guess_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_guess_option`
-- ----------------------------
DROP TABLE IF EXISTS `wp_guess_option`;
CREATE TABLE `wp_guess_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `guess_id` int(10) DEFAULT '0' COMMENT '竞猜活动的Id',
  `name` varchar(255) DEFAULT NULL COMMENT '选项名称',
  `image` int(10) unsigned DEFAULT NULL COMMENT '选项图片',
  `order` int(10) DEFAULT '0' COMMENT '选项顺序',
  `guess_count` int(10) unsigned DEFAULT '0' COMMENT '竞猜人数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_guess_option
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_hooks`
-- ----------------------------
DROP TABLE IF EXISTS `wp_hooks`;
CREATE TABLE `wp_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` text NOT NULL COMMENT '钩子挂载的插件 ''，''分割',
  PRIMARY KEY (`id`),
  UNIQUE KEY `搜索索引` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='插件钩子表';

-- ----------------------------
-- Records of wp_hooks
-- ----------------------------
INSERT INTO `wp_hooks` VALUES ('1', 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', '1', '0', '');
INSERT INTO `wp_hooks` VALUES ('2', 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', '1', '0', 'ReturnTop');
INSERT INTO `wp_hooks` VALUES ('3', 'documentEditForm', '添加编辑表单的 扩展内容钩子', '1', '0', '');
INSERT INTO `wp_hooks` VALUES ('4', 'documentDetailAfter', '文档末尾显示', '1', '0', 'SocialComment');
INSERT INTO `wp_hooks` VALUES ('5', 'documentDetailBefore', '页面内容前显示用钩子', '1', '0', '');
INSERT INTO `wp_hooks` VALUES ('6', 'documentSaveComplete', '保存文档数据后的扩展钩子', '2', '0', '');
INSERT INTO `wp_hooks` VALUES ('7', 'documentEditFormContent', '添加编辑表单的内容显示钩子', '1', '0', 'Editor');
INSERT INTO `wp_hooks` VALUES ('8', 'adminArticleEdit', '后台内容编辑页编辑器', '1', '1378982734', 'EditorForAdmin');
INSERT INTO `wp_hooks` VALUES ('13', 'AdminIndex', '首页小格子个性化显示', '1', '1382596073', 'SiteStat,SystemInfo,DevTeam');
INSERT INTO `wp_hooks` VALUES ('14', 'topicComment', '评论提交方式扩展钩子。', '1', '1380163518', 'Editor');
INSERT INTO `wp_hooks` VALUES ('16', 'app_begin', '应用开始', '2', '1384481614', '');
INSERT INTO `wp_hooks` VALUES ('17', 'weixin', '微信插件必须加载的钩子', '1', '1388810858', 'Hitegg,Diy,RedBag,WeMedia,ShopCoupon,Card,SingIn,Seckill,CustomMenu,CustomReply,AutoReply,WeiSite,UserCenter,Exam,Draw,Extensions,Forms,Coupon,Guess,Comment,Game,ConfigureAccount,Chat,CardVouchers,Ask,BusinessCard,HelloWorld,Invite,Tongji,Test,Vote,Sms,Survey,Shop,Wecome,WishCard,Scratch,Robot,YouaskService,RealPrize,Xydzp,Reserve,PublicBind,Payment,Leaflets,NoAnswer');
INSERT INTO `wp_hooks` VALUES ('18', 'cascade', '级联菜单', '1', '1398694587', 'Cascade');
INSERT INTO `wp_hooks` VALUES ('19', 'page_diy', '万能页面的钩子', '1', '1399040364', 'Diy');
INSERT INTO `wp_hooks` VALUES ('20', 'dynamic_select', '动态下拉菜单', '1', '1435223189', 'DynamicSelect');
INSERT INTO `wp_hooks` VALUES ('21', 'news', '图文素材选择', '1', '1439196828', 'News');

-- ----------------------------
-- Table structure for `wp_import`
-- ----------------------------
DROP TABLE IF EXISTS `wp_import`;
CREATE TABLE `wp_import` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `attach` int(10) unsigned NOT NULL COMMENT '上传文件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_import
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_invite`
-- ----------------------------
DROP TABLE IF EXISTS `wp_invite`;
CREATE TABLE `wp_invite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `cover` int(10) unsigned NOT NULL COMMENT '封面图片',
  `experience` int(10) DEFAULT '0' COMMENT '消耗经验值',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `num` int(10) DEFAULT '0' COMMENT '邀请人数',
  `coupon_id` int(10) DEFAULT NULL COMMENT '优惠券编号',
  `coupon_num` int(10) DEFAULT '0' COMMENT '优惠券数',
  `receive_num` int(10) DEFAULT '0' COMMENT '已领取优惠券数',
  `content` text COMMENT '邀约介绍',
  `template` varchar(255) DEFAULT 'default' COMMENT '模板名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_invite
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_invite_code`
-- ----------------------------
DROP TABLE IF EXISTS `wp_invite_code`;
CREATE TABLE `wp_invite_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(100) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_invite_code
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_invite_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_invite_user`;
CREATE TABLE `wp_invite_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `invite_id` int(10) DEFAULT NULL COMMENT '邀约ID',
  `invite_num` int(10) DEFAULT NULL COMMENT '已邀请人数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_invite_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_join_count`
-- ----------------------------
DROP TABLE IF EXISTS `wp_join_count`;
CREATE TABLE `wp_join_count` (
  `follow_id` int(10) DEFAULT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aim_id` int(10) DEFAULT NULL,
  `count` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fid_aim` (`follow_id`,`aim_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_join_count
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_keyword`
-- ----------------------------
DROP TABLE IF EXISTS `wp_keyword`;
CREATE TABLE `wp_keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `addon` varchar(255) NOT NULL COMMENT '关键词所属插件',
  `aim_id` int(10) unsigned NOT NULL COMMENT '插件表里的ID值',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `keyword_length` int(10) unsigned DEFAULT '0' COMMENT '关键词长度',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '匹配类型',
  `extra_text` text COMMENT '文本扩展',
  `extra_int` int(10) DEFAULT NULL COMMENT '数字扩展',
  `request_count` int(10) DEFAULT '0' COMMENT '请求数',
  PRIMARY KEY (`id`),
  KEY `keyword_token` (`keyword`,`token`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_keyword
-- ----------------------------
INSERT INTO `wp_keyword` VALUES ('1', '机器人学习时间', '0', 'Robot', '0', '1393210483', '7', '0', '', '0', '0');

-- ----------------------------
-- Table structure for `wp_lottery_games`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lottery_games`;
CREATE TABLE `wp_lottery_games` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '活动名称',
  `game_type` char(10) DEFAULT '1' COMMENT '游戏类型',
  `status` char(10) DEFAULT '1' COMMENT '状态',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  `day_attend_limit` int(10) DEFAULT '0' COMMENT '每人每天抽奖次数',
  `attend_limit` int(10) DEFAULT '0' COMMENT '每人总共抽奖次数',
  `day_win_limit` int(10) DEFAULT '0' COMMENT '每人每天中奖次数',
  `win_limit` int(10) DEFAULT '0' COMMENT '每人总共中奖次数',
  `day_winners_count` int(10) DEFAULT '0' COMMENT '每天最多中奖人数',
  `url` varchar(300) DEFAULT NULL COMMENT '关注链接',
  `remark` text COMMENT '活动说明',
  `keyword` varchar(255) DEFAULT NULL COMMENT '微信关键词',
  `attend_num` int(10) DEFAULT '0' COMMENT '参与总人数',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lottery_games
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lottery_games_award_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lottery_games_award_link`;
CREATE TABLE `wp_lottery_games_award_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `award_id` int(10) DEFAULT NULL COMMENT '奖品id',
  `games_id` int(10) DEFAULT NULL COMMENT '抽奖游戏id',
  `grade` varchar(255) DEFAULT NULL COMMENT '中奖等级',
  `num` int(10) DEFAULT NULL COMMENT '奖品数量',
  `max_count` int(10) DEFAULT NULL COMMENT '最多抽奖',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lottery_games_award_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lottery_prize_list`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lottery_prize_list`;
CREATE TABLE `wp_lottery_prize_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sports_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `award_id` varchar(255) DEFAULT NULL COMMENT '奖品编号',
  `award_num` int(10) DEFAULT NULL COMMENT '奖品数量',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  PRIMARY KEY (`id`),
  KEY `sports_id` (`sports_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lottery_prize_list
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lucky_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lucky_follow`;
CREATE TABLE `wp_lucky_follow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `draw_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `sport_id` int(10) DEFAULT NULL COMMENT '场次编号',
  `award_id` int(10) DEFAULT NULL COMMENT '奖品编号',
  `follow_id` int(10) DEFAULT NULL COMMENT '粉丝id',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `num` int(10) DEFAULT '0' COMMENT '获奖数',
  `state` tinyint(2) DEFAULT '0' COMMENT '兑奖状态',
  `zjtime` int(10) DEFAULT NULL COMMENT '中奖时间',
  `djtime` int(10) DEFAULT NULL COMMENT '兑奖时间',
  `remark` text COMMENT '备注',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '活动标识',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `scan_code` varchar(255) DEFAULT NULL COMMENT '核销码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lucky_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_activities`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_activities`;
CREATE TABLE `wp_lzwg_activities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '活动名称',
  `remark` text COMMENT '活动描述',
  `logo_img` int(10) unsigned DEFAULT NULL COMMENT '活动LOGO',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  `get_prize_tip` varchar(255) DEFAULT NULL COMMENT '中奖提示信息',
  `no_prize_tip` varchar(255) DEFAULT NULL COMMENT '未中奖提示信息',
  `ctime` int(10) DEFAULT NULL COMMENT '活动创建时间',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `lottery_number` int(10) DEFAULT '1' COMMENT '抽奖次数',
  `comment_status` char(10) DEFAULT '0' COMMENT '评论是否需要审核',
  `get_prize_count` int(10) DEFAULT '1' COMMENT '中奖次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_activities
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_activities_vote`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_activities_vote`;
CREATE TABLE `wp_lzwg_activities_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `lzwg_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `lzwg_type` char(10) DEFAULT '0' COMMENT '活动类型',
  `vote_id` int(10) DEFAULT NULL COMMENT '题目编号',
  `vote_type` char(10) DEFAULT '1' COMMENT '问题类型',
  `vote_limit` int(10) DEFAULT NULL COMMENT '最多选择几项',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_activities_vote
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_manager`
-- ----------------------------
DROP TABLE IF EXISTS `wp_manager`;
CREATE TABLE `wp_manager` (
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `has_public` tinyint(2) DEFAULT '0' COMMENT '是否配置公众号',
  `headface_url` int(10) unsigned DEFAULT NULL COMMENT '管理员头像',
  `GammaAppId` varchar(30) DEFAULT NULL COMMENT '摇电视的AppId',
  `GammaSecret` varchar(100) DEFAULT NULL COMMENT '摇电视的Secret',
  `copy_right` varchar(255) DEFAULT NULL COMMENT '授权信息',
  `tongji_code` text COMMENT '统计代码',
  `website_logo` int(10) unsigned DEFAULT NULL COMMENT '网站LOGO',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_manager
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_manager_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wp_manager_menu`;
CREATE TABLE `wp_manager_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `menu_type` tinyint(2) DEFAULT '0' COMMENT '菜单类型',
  `pid` varchar(50) DEFAULT '0' COMMENT '上级菜单',
  `title` varchar(50) DEFAULT NULL COMMENT '菜单名',
  `url_type` tinyint(2) DEFAULT '0' COMMENT '链接类型',
  `addon_name` varchar(30) DEFAULT NULL COMMENT '插件名',
  `url` varchar(255) DEFAULT NULL COMMENT '外链',
  `target` char(50) DEFAULT '_self' COMMENT '打开方式',
  `is_hide` tinyint(2) DEFAULT '0' COMMENT '是否隐藏',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  `uid` int(10) DEFAULT NULL COMMENT '管理员ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=356 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_manager_menu
-- ----------------------------
INSERT INTO `wp_manager_menu` VALUES ('14', '0', '', '首页', '1', '', 'home/index/main', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('15', '0', '', '用户管理', '0', 'UserCenter', '', '_self', '0', '1', '1');
INSERT INTO `wp_manager_menu` VALUES ('16', '1', '15', '微信用户', '0', 'UserCenter', '', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('17', '0', '', '互动应用', '0', 'Vote', '', '_self', '0', '2', '1');
INSERT INTO `wp_manager_menu` VALUES ('18', '1', '17', '普通投票', '0', 'Vote', '', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('19', '1', '17', '微调研', '0', 'Survey', '', '_self', '0', '3', '1');
INSERT INTO `wp_manager_menu` VALUES ('20', '1', '17', '刮刮卡', '0', 'Scratch', '', '_self', '1', '6', '1');
INSERT INTO `wp_manager_menu` VALUES ('30', '1', '17', '微邀约', '0', 'Invite', '', '_self', '0', '10', '1');
INSERT INTO `wp_manager_menu` VALUES ('22', '1', '17', '大转盘', '0', 'Xydzp', '', '_self', '1', '7', '1');
INSERT INTO `wp_manager_menu` VALUES ('23', '1', '17', '通用表单', '0', 'Forms', '', '_self', '0', '2', '1');
INSERT INTO `wp_manager_menu` VALUES ('24', '1', '17', '竞猜', '0', 'Guess', '', '_self', '0', '11', '1');
INSERT INTO `wp_manager_menu` VALUES ('25', '1', '17', '微贺卡', '0', 'WishCard', '', '_self', '0', '8', '1');
INSERT INTO `wp_manager_menu` VALUES ('26', '1', '17', '微信卡券', '0', 'CardVouchers', '', '_self', '0', '16', '1');
INSERT INTO `wp_manager_menu` VALUES ('27', '1', '17', '优惠券', '0', 'Coupon', '', '_self', '0', '15', '1');
INSERT INTO `wp_manager_menu` VALUES ('29', '1', '17', '实物奖励', '0', 'RealPrize', '', '_self', '0', '18', '1');
INSERT INTO `wp_manager_menu` VALUES ('31', '0', '', '微网站', '0', 'WeiSite', '', '_self', '0', '3', '1');
INSERT INTO `wp_manager_menu` VALUES ('32', '1', '31', '微网设置', '1', '', 'WeiSite://WeiSite/config', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('46', '1', '45', '欢迎语设置', '0', 'Wecome', '', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('33', '1', '31', '分类管理', '1', '', 'WeiSite://Category/lists', '_self', '0', '2', '1');
INSERT INTO `wp_manager_menu` VALUES ('34', '1', '31', '幻灯片', '1', '', 'WeiSite://Slideshow/lists', '_self', '0', '3', '1');
INSERT INTO `wp_manager_menu` VALUES ('35', '1', '31', '底部导航', '1', '', 'WeiSite://Footer/lists', '_self', '0', '4', '1');
INSERT INTO `wp_manager_menu` VALUES ('36', '1', '31', '文章管理', '1', '', 'WeiSite://Cms/lists', '_self', '0', '5', '1');
INSERT INTO `wp_manager_menu` VALUES ('44', '1', '15', '微信咨询', '1', '', 'home/WeixinMessage/lists', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('37', '1', '31', '模版管理', '1', '', 'WeiSite://Template/index', '_self', '0', '6', '1');
INSERT INTO `wp_manager_menu` VALUES ('45', '0', '', '公众号功能', '0', 'Wecome', '', '_self', '0', '1', '1');
INSERT INTO `wp_manager_menu` VALUES ('38', '0', '', '微商城', '1', '', 'Shop://Shop/summary', '_self', '0', '4', '1');
INSERT INTO `wp_manager_menu` VALUES ('41', '1', '15', '用户分组', '1', '', 'home/AuthGroup/lists', '_self', '0', '2', '1');
INSERT INTO `wp_manager_menu` VALUES ('42', '1', '15', '用户积分', '1', '', 'home/CreditConfig/lists', '_self', '0', '3', '1');
INSERT INTO `wp_manager_menu` VALUES ('341', '1', '17', '代金券', '0', 'ShopCoupon', '', '_self', '0', '31', '1');
INSERT INTO `wp_manager_menu` VALUES ('47', '1', '45', '自定义菜单', '0', 'CustomMenu', '', '_self', '0', '3', '1');
INSERT INTO `wp_manager_menu` VALUES ('48', '1', '45', '自动回复', '0', 'AutoReply', '', '_self', '0', '2', '1');
INSERT INTO `wp_manager_menu` VALUES ('49', '1', '45', '微信宣传页', '0', 'Leaflets', '', '_self', '0', '6', '1');
INSERT INTO `wp_manager_menu` VALUES ('50', '1', '45', '群发消息', '1', '', 'home/Message/add', '_self', '0', '4', '1');
INSERT INTO `wp_manager_menu` VALUES ('51', '1', '38', '店铺汇总', '1', '', 'Shop://Shop/summary', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('52', '1', '38', '基本信息', '1', '', 'Shop://Shop/edit', '_self', '0', '1', '1');
INSERT INTO `wp_manager_menu` VALUES ('53', '1', '38', '商品分类', '1', '', 'Shop://Category/lists', '_self', '0', '4', '1');
INSERT INTO `wp_manager_menu` VALUES ('56', '1', '38', '首页幻灯片', '1', '', 'Shop://Slideshow/lists', '_self', '0', '7', '1');
INSERT INTO `wp_manager_menu` VALUES ('55', '1', '38', '商品管理', '1', '', 'Shop://Goods/lists', '_self', '0', '2', '1');
INSERT INTO `wp_manager_menu` VALUES ('57', '1', '38', '订单管理', '1', '', 'Shop://Order/lists', '_self', '0', '3', '1');
INSERT INTO `wp_manager_menu` VALUES ('58', '1', '38', '模板管理', '1', '', 'Shop://Template/lists', '_self', '1', '5', '1');
INSERT INTO `wp_manager_menu` VALUES ('59', '1', '38', '支付配置', '1', '', 'Payment://Payment/lists', '_self', '0', '9', '1');
INSERT INTO `wp_manager_menu` VALUES ('60', '0', '', '素材管理', '1', '', 'Home/Material/material_lists', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('335', '1', '38', '页面管理', '1', null, 'Shop://DiyPage/lists', '_self', '0', '8', '1');
INSERT INTO `wp_manager_menu` VALUES ('334', '1', '38', '商品规格', '1', null, 'Shop://Spec/lists', '_self', '0', '6', '1');
INSERT INTO `wp_manager_menu` VALUES ('81', '0', '', '微场景', '1', '', '/scene/index/index', '_self', '0', '4', '1');
INSERT INTO `wp_manager_menu` VALUES ('336', '1', '38', '功能配置', '1', null, 'Shop://Shop/config', '_self', '0', '10', '1');
INSERT INTO `wp_manager_menu` VALUES ('337', '1', '38', '个人渠道', '1', null, 'Shop://ShopDistributionUser/lists', '_self', '0', '11', '1');
INSERT INTO `wp_manager_menu` VALUES ('338', '1', '38', '工作授权', '1', null, 'Servicer://Servicer/lists', '_self', '0', '12', '1');
INSERT INTO `wp_manager_menu` VALUES ('339', '1', '45', '未识别回复', '0', 'NoAnswer', '', '_self', '0', '1', '1');
INSERT INTO `wp_manager_menu` VALUES ('340', '1', '45', '融合第三方', '0', 'Extensions', '', '_self', '0', '5', '1');
INSERT INTO `wp_manager_menu` VALUES ('342', '1', '15', '会员卡', '0', 'Card', '', '_self', '0', '0', '1');
INSERT INTO `wp_manager_menu` VALUES ('343', '1', '17', '帮拆礼包', '0', 'HelpOpen', '', '_self', '0', '13', '1');
INSERT INTO `wp_manager_menu` VALUES ('344', '1', '17', '秒杀活动', '0', 'Seckill', '', '_self', '0', '32', '1');
INSERT INTO `wp_manager_menu` VALUES ('345', '1', '17', '比赛投票', '1', '', 'Vote://ShopVote/lists', '_self', '0', '1', '1');
INSERT INTO `wp_manager_menu` VALUES ('346', '1', '17', '微签到', '0', 'SingIn', '', '_self', '0', '14', '1');
INSERT INTO `wp_manager_menu` VALUES ('347', '1', '17', '促销活动', '0', 'ShopReward', '', '_self', '0', '30', '1');
INSERT INTO `wp_manager_menu` VALUES ('348', '1', '38', '门店管理', '1', '', 'Coupon://Shop/lists', '_self', '0', '13', '1');
INSERT INTO `wp_manager_menu` VALUES ('349', '1', '17', '微预约', '0', 'Reserve', '', '_self', '0', '9', '1');
INSERT INTO `wp_manager_menu` VALUES ('350', '1', '17', '抽奖游戏', '1', '', 'Draw://Games/lists', '_self', '0', '19', '1');
INSERT INTO `wp_manager_menu` VALUES ('351', '1', '17', '微考试', '0', 'Exam', '', '_self', '0', '4', '1');
INSERT INTO `wp_manager_menu` VALUES ('352', '1', '17', '微测试', '0', 'Test', '', '_self', '0', '5', '1');
INSERT INTO `wp_manager_menu` VALUES ('353', '1', '17', '微抢答', '0', 'Ask', '', '_self', '0', '12', '1');
INSERT INTO `wp_manager_menu` VALUES ('354', '1', '45', '机器学习', '0', 'Robot', '', '_self', '0', '7', '1');
INSERT INTO `wp_manager_menu` VALUES ('355', '1', '45', '智能聊天', '0', 'Chat', '', '_self', '0', '8', '1');

-- ----------------------------
-- Table structure for `wp_material_file`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_file`;
CREATE TABLE `wp_material_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `file_id` int(10) DEFAULT NULL COMMENT '上传文件',
  `cover_url` varchar(255) DEFAULT NULL COMMENT '本地URL',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `wechat_url` varchar(255) DEFAULT NULL COMMENT '微信端的文件地址',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `title` varchar(100) DEFAULT NULL COMMENT '素材名称',
  `type` int(10) DEFAULT NULL COMMENT '类型',
  `introduction` text COMMENT '描述',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_file
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_image`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_image`;
CREATE TABLE `wp_material_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cover_id` int(10) DEFAULT NULL COMMENT '图片在本地的ID',
  `cover_url` varchar(255) DEFAULT NULL COMMENT '本地URL',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `wechat_url` varchar(255) DEFAULT NULL COMMENT '微信端的图片地址',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_image
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_news`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_news`;
CREATE TABLE `wp_material_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `cover_id` int(10) unsigned DEFAULT NULL COMMENT '封面',
  `intro` varchar(255) DEFAULT NULL COMMENT '摘要',
  `content` longtext COMMENT '内容',
  `link` varchar(255) DEFAULT NULL COMMENT '外链',
  `group_id` int(10) DEFAULT '0' COMMENT '多图文组的ID',
  `thumb_media_id` varchar(100) DEFAULT NULL COMMENT '图文消息的封面图片素材id（必须是永久mediaID）',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `url` varchar(255) DEFAULT NULL COMMENT '图文页url',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_news
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_text`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_text`;
CREATE TABLE `wp_material_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content` text COMMENT '文本内容',
  `token` varchar(50) DEFAULT NULL COMMENT 'Token',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_text
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wp_menu`;
CREATE TABLE `wp_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 COMMENT='后台导航数据表';

-- ----------------------------
-- Records of wp_menu
-- ----------------------------
INSERT INTO `wp_menu` VALUES ('4', '新增', '3', '0', 'article/add', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('5', '编辑', '3', '0', 'article/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('6', '改变状态', '3', '0', 'article/setStatus', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('7', '保存', '3', '0', 'article/update', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('8', '保存草稿', '3', '0', 'article/autoSave', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('9', '移动', '3', '0', 'article/move', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('10', '复制', '3', '0', 'article/copy', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('11', '粘贴', '3', '0', 'article/paste', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('12', '导入', '3', '0', 'article/batchOperate', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('14', '还原', '13', '0', 'article/permit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('15', '清空', '13', '0', 'article/clear', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('16', '用户', '0', '2', 'User/index', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('17', '用户信息', '16', '0', 'User/index', '0', '', '用户管理', '0');
INSERT INTO `wp_menu` VALUES ('18', '新增用户', '17', '0', 'User/add', '0', '添加新用户', '', '0');
INSERT INTO `wp_menu` VALUES ('19', '用户行为', '16', '0', 'User/action', '0', '', '行为管理', '0');
INSERT INTO `wp_menu` VALUES ('20', '新增用户行为', '19', '0', 'User/addaction', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('21', '编辑用户行为', '19', '0', 'User/editaction', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('22', '保存用户行为', '19', '0', 'User/saveAction', '0', '\"用户->用户行为\"保存编辑和新增的用户行为', '', '0');
INSERT INTO `wp_menu` VALUES ('23', '变更行为状态', '19', '0', 'User/setStatus', '0', '\"用户->用户行为\"中的启用,禁用和删除权限', '', '0');
INSERT INTO `wp_menu` VALUES ('24', '禁用会员', '19', '0', 'User/changeStatus?method=forbidUser', '0', '\"用户->用户信息\"中的禁用', '', '0');
INSERT INTO `wp_menu` VALUES ('25', '启用会员', '19', '0', 'User/changeStatus?method=resumeUser', '0', '\"用户->用户信息\"中的启用', '', '0');
INSERT INTO `wp_menu` VALUES ('26', '删除会员', '19', '0', 'User/changeStatus?method=deleteUser', '0', '\"用户->用户信息\"中的删除', '', '0');
INSERT INTO `wp_menu` VALUES ('27', '用户组管理', '16', '0', 'AuthManager/index', '0', '', '用户管理', '0');
INSERT INTO `wp_menu` VALUES ('28', '删除', '27', '0', 'AuthManager/changeStatus?method=deleteGroup', '0', '删除用户组', '', '0');
INSERT INTO `wp_menu` VALUES ('29', '禁用', '27', '0', 'AuthManager/changeStatus?method=forbidGroup', '0', '禁用用户组', '', '0');
INSERT INTO `wp_menu` VALUES ('30', '恢复', '27', '0', 'AuthManager/changeStatus?method=resumeGroup', '0', '恢复已禁用的用户组', '', '0');
INSERT INTO `wp_menu` VALUES ('31', '新增', '27', '0', 'AuthManager/createGroup', '0', '创建新的用户组', '', '0');
INSERT INTO `wp_menu` VALUES ('32', '编辑', '27', '0', 'AuthManager/editGroup', '0', '编辑用户组名称和描述', '', '0');
INSERT INTO `wp_menu` VALUES ('33', '保存用户组', '27', '0', 'AuthManager/writeGroup', '0', '新增和编辑用户组的\"保存\"按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('34', '授权', '27', '0', 'AuthManager/group', '0', '\"后台 \\ 用户 \\ 用户信息\"列表页的\"授权\"操作按钮,用于设置用户所属用户组', '', '0');
INSERT INTO `wp_menu` VALUES ('35', '访问授权', '27', '0', 'AuthManager/access', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"访问授权\"操作按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('36', '成员授权', '27', '0', 'AuthManager/user', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"成员授权\"操作按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('37', '解除授权', '27', '0', 'AuthManager/removeFromGroup', '0', '\"成员授权\"列表页内的解除授权操作按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('38', '保存成员授权', '27', '0', 'AuthManager/addToGroup', '0', '\"用户信息\"列表页\"授权\"时的\"保存\"按钮和\"成员授权\"里右上角的\"添加\"按钮)', '', '0');
INSERT INTO `wp_menu` VALUES ('39', '分类授权', '27', '0', 'AuthManager/category', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"分类授权\"操作按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('40', '保存分类授权', '27', '0', 'AuthManager/addToCategory', '0', '\"分类授权\"页面的\"保存\"按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('41', '模型授权', '27', '0', 'AuthManager/modelauth', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"模型授权\"操作按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('42', '保存模型授权', '27', '0', 'AuthManager/addToModel', '0', '\"分类授权\"页面的\"保存\"按钮', '', '0');
INSERT INTO `wp_menu` VALUES ('43', '插件管理', '0', '7', 'Addons/index', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('44', '插件管理', '43', '1', 'Admin/Plugin/index', '0', '', '扩展', '0');
INSERT INTO `wp_menu` VALUES ('45', '创建', '44', '0', 'Addons/create', '0', '服务器上创建插件结构向导', '', '0');
INSERT INTO `wp_menu` VALUES ('46', '检测创建', '44', '0', 'Addons/checkForm', '0', '检测插件是否可以创建', '', '0');
INSERT INTO `wp_menu` VALUES ('47', '预览', '44', '0', 'Addons/preview', '0', '预览插件定义类文件', '', '0');
INSERT INTO `wp_menu` VALUES ('48', '快速生成插件', '44', '0', 'Addons/build', '0', '开始生成插件结构', '', '0');
INSERT INTO `wp_menu` VALUES ('49', '设置', '44', '0', 'Addons/config', '0', '设置插件配置', '', '0');
INSERT INTO `wp_menu` VALUES ('50', '禁用', '44', '0', 'Addons/disable', '0', '禁用插件', '', '0');
INSERT INTO `wp_menu` VALUES ('51', '启用', '44', '0', 'Addons/enable', '0', '启用插件', '', '0');
INSERT INTO `wp_menu` VALUES ('52', '安装', '44', '0', 'Addons/install', '0', '安装插件', '', '0');
INSERT INTO `wp_menu` VALUES ('53', '卸载', '44', '0', 'Addons/uninstall', '0', '卸载插件', '', '0');
INSERT INTO `wp_menu` VALUES ('54', '更新配置', '44', '0', 'Addons/saveconfig', '0', '更新插件配置处理', '', '0');
INSERT INTO `wp_menu` VALUES ('55', '插件后台列表', '44', '0', 'Addons/adminList', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('56', 'URL方式访问插件', '44', '0', 'Addons/execute', '0', '控制是否有权限通过url访问插件控制器方法', '', '0');
INSERT INTO `wp_menu` VALUES ('57', '钩子管理', '43', '3', 'Addons/hooks', '0', '', '扩展', '0');
INSERT INTO `wp_menu` VALUES ('58', '模型管理', '68', '3', 'Model/index', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('59', '新增', '58', '0', 'model/add', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('60', '编辑', '58', '0', 'model/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('61', '改变状态', '58', '0', 'model/setStatus', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('62', '保存数据', '58', '0', 'model/update', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('64', '新增', '63', '0', 'Attribute/add', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('65', '编辑', '63', '0', 'Attribute/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('66', '改变状态', '63', '0', 'Attribute/setStatus', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('67', '保存数据', '63', '0', 'Attribute/update', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('68', '系统', '0', '1', 'Config/group', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('69', '网站设置', '68', '1', 'Config/group', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('70', '配置管理', '68', '4', 'Config/index', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('71', '编辑', '70', '0', 'Config/edit', '0', '新增编辑和保存配置', '', '0');
INSERT INTO `wp_menu` VALUES ('72', '删除', '70', '0', 'Config/del', '0', '删除配置', '', '0');
INSERT INTO `wp_menu` VALUES ('73', '新增', '70', '0', 'Config/add', '0', '新增配置', '', '0');
INSERT INTO `wp_menu` VALUES ('74', '保存', '70', '0', 'Config/save', '0', '保存配置', '', '0');
INSERT INTO `wp_menu` VALUES ('75', '菜单管理', '68', '5', 'Menu/index', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('76', '导航管理', '68', '6', 'Channel/index', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('77', '新增', '76', '0', 'Channel/add', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('78', '编辑', '76', '0', 'Channel/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('79', '删除', '76', '0', 'Channel/del', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('146', '权限节点', '16', '0', 'Admin/Rule/index', '0', '', '用户管理', '1');
INSERT INTO `wp_menu` VALUES ('81', '编辑', '80', '0', 'Category/edit', '0', '编辑和保存栏目分类', '', '0');
INSERT INTO `wp_menu` VALUES ('82', '新增', '80', '0', 'Category/add', '0', '新增栏目分类', '', '0');
INSERT INTO `wp_menu` VALUES ('83', '删除', '80', '0', 'Category/remove', '0', '删除栏目分类', '', '0');
INSERT INTO `wp_menu` VALUES ('84', '移动', '80', '0', 'Category/operate/type/move', '0', '移动栏目分类', '', '0');
INSERT INTO `wp_menu` VALUES ('85', '合并', '80', '0', 'Category/operate/type/merge', '0', '合并栏目分类', '', '0');
INSERT INTO `wp_menu` VALUES ('86', '备份数据库', '68', '0', 'Database/index?type=export', '0', '', '数据备份', '0');
INSERT INTO `wp_menu` VALUES ('87', '备份', '86', '0', 'Database/export', '0', '备份数据库', '', '0');
INSERT INTO `wp_menu` VALUES ('88', '优化表', '86', '0', 'Database/optimize', '0', '优化数据表', '', '0');
INSERT INTO `wp_menu` VALUES ('89', '修复表', '86', '0', 'Database/repair', '0', '修复数据表', '', '0');
INSERT INTO `wp_menu` VALUES ('90', '还原数据库', '68', '0', 'Database/index?type=import', '0', '', '数据备份', '0');
INSERT INTO `wp_menu` VALUES ('91', '恢复', '90', '0', 'Database/import', '0', '数据库恢复', '', '0');
INSERT INTO `wp_menu` VALUES ('92', '删除', '90', '0', 'Database/del', '0', '删除备份文件', '', '0');
INSERT INTO `wp_menu` VALUES ('96', '新增', '75', '0', 'Menu/add', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('98', '编辑', '75', '0', 'Menu/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('104', '下载管理', '102', '0', 'Think/lists?model=download', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('105', '配置管理', '102', '0', 'Think/lists?model=config', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('106', '行为日志', '16', '0', 'Action/actionlog', '0', '', '行为管理', '0');
INSERT INTO `wp_menu` VALUES ('108', '修改密码', '16', '0', 'User/updatePassword', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('109', '修改昵称', '16', '0', 'User/updateNickname', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('110', '查看行为日志', '106', '0', 'action/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('112', '新增数据', '58', '0', 'think/add', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('113', '编辑数据', '58', '0', 'think/edit', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('114', '导入', '75', '0', 'Menu/import', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('115', '生成', '58', '0', 'Model/generate', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('116', '新增钩子', '57', '0', 'Addons/addHook', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('117', '编辑钩子', '57', '0', 'Addons/edithook', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('118', '文档排序', '3', '0', 'Article/sort', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('119', '排序', '70', '0', 'Config/sort', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('120', '排序', '75', '0', 'Menu/sort', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('121', '排序', '76', '0', 'Channel/sort', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('124', '微信插件', '43', '0', 'Admin/Addons/index', '0', '', '扩展', '0');
INSERT INTO `wp_menu` VALUES ('126', '公众号等级', '16', '0', 'admin/PublicGroup/PublicGroup', '0', '', '公众号管理', '0');
INSERT INTO `wp_menu` VALUES ('127', '公众号管理', '16', '1', 'admin/PublicGroup/PublicAdmin', '0', '', '公众号管理', '0');
INSERT INTO `wp_menu` VALUES ('128', '在线升级', '68', '5', 'Admin/Update/index', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('129', '清除缓存', '68', '10', 'Admin/Update/delcache', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('130', '应用商店', '0', '8', 'admin/store/index', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('131', '素材图标', '130', '2', 'admin/store/index?type=material', '0', '', '应用类型', '0');
INSERT INTO `wp_menu` VALUES ('132', '微站模板', '130', '1', 'admin/store/index?type=template', '0', '', '应用类型', '0');
INSERT INTO `wp_menu` VALUES ('133', '我是开发者', '130', '1', '/index.php?s=/home/developer/myApps', '0', '', '开发者', '0');
INSERT INTO `wp_menu` VALUES ('134', '新手安装指南', '130', '0', 'admin/store/index?type=help', '0', '', '我是站长', '0');
INSERT INTO `wp_menu` VALUES ('135', '万能页面', '130', '3', 'admin/store/index?type=diy', '0', '', '应用类型', '0');
INSERT INTO `wp_menu` VALUES ('136', '上传新应用', '130', '2', '/index.php?s=/home/developer/submitApp', '0', '', '开发者', '0');
INSERT INTO `wp_menu` VALUES ('137', '二次开发教程', '130', '3', '/wiki', '0', '', '开发者', '0');
INSERT INTO `wp_menu` VALUES ('138', '网站信息', '130', '0', 'admin/store/index?type=home', '0', '', '我是站长', '0');
INSERT INTO `wp_menu` VALUES ('139', '充值记录', '130', '0', 'admin/store/index?type=recharge', '0', '', '我是站长', '0');
INSERT INTO `wp_menu` VALUES ('140', '消费记录', '130', '0', 'admin/store/index?type=bug', '0', '', '我是站长', '0');
INSERT INTO `wp_menu` VALUES ('141', '官方交流论坛', '130', '4', '/bbs', '0', '', '开发者', '0');
INSERT INTO `wp_menu` VALUES ('142', '在线充值', '130', '0', 'admin/store/index?type=online_recharge', '0', '', '我是站长', '0');
INSERT INTO `wp_menu` VALUES ('143', '微信插件', '130', '0', 'admin/store/index?type=addon', '0', '', '应用类型', '0');
INSERT INTO `wp_menu` VALUES ('144', '公告管理', '68', '4', 'Notice/lists', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('147', '图文样式编辑', '68', '4', 'ArticleStyle/lists', '0', '', '系统设置', '0');
INSERT INTO `wp_menu` VALUES ('148', '增加', '147', '0', 'ArticleStyle/add', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('149', '分组管理', '147', '0', 'ArticleStyle/group', '0', '', '', '0');
INSERT INTO `wp_menu` VALUES ('150', '微信接口节点', '16', '0', 'Admin/Rule/wechat', '0', '', '用户管理', '0');
INSERT INTO `wp_menu` VALUES ('151', '公众号组管理', '16', '0', 'Admin/AuthManager/wechat', '0', '', '用户管理', '0');
INSERT INTO `wp_menu` VALUES ('152', '积分选项管理', '16', '6', 'Admin/Credit/lists', '0', '', '用户管理', '1');

-- ----------------------------
-- Table structure for `wp_message`
-- ----------------------------
DROP TABLE IF EXISTS `wp_message`;
CREATE TABLE `wp_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bind_keyword` varchar(50) DEFAULT NULL COMMENT '关联关键词',
  `preview_openids` text COMMENT '预览人OPENID',
  `group_id` int(10) DEFAULT '0' COMMENT '群发对象',
  `type` tinyint(2) DEFAULT '0' COMMENT '素材来源',
  `media_id` varchar(100) DEFAULT NULL COMMENT '微信素材ID',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '发送方式',
  `send_openids` text COMMENT '要发送的OpenID',
  `msg_id` varchar(255) DEFAULT NULL COMMENT 'msg_id',
  `content` text COMMENT '文本消息内容',
  `msgtype` varchar(255) DEFAULT NULL COMMENT '消息类型',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `appmsg_id` int(10) DEFAULT NULL COMMENT '图文id',
  `voice_id` int(10) DEFAULT NULL COMMENT '语音id',
  `video_id` int(10) DEFAULT NULL COMMENT '视频id',
  `cTime` int(10) DEFAULT NULL COMMENT '群发时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_model`
-- ----------------------------
DROP TABLE IF EXISTS `wp_model`;
CREATE TABLE `wp_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned DEFAULT '0' COMMENT '继承的模型',
  `relation` varchar(30) DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `need_pk` tinyint(1) unsigned DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text COMMENT '表单字段排序',
  `field_group` varchar(255) DEFAULT '1:基础' COMMENT '字段分组',
  `attribute_list` text COMMENT '属性列表（表的字段）',
  `template_list` varchar(100) DEFAULT '' COMMENT '列表模板',
  `template_add` varchar(100) DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(100) DEFAULT '' COMMENT '编辑模板',
  `list_grid` text COMMENT '列表定义',
  `list_row` smallint(2) unsigned DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) DEFAULT '' COMMENT '高级搜索的字段',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) DEFAULT 'MyISAM' COMMENT '数据库引擎',
  `addon` varchar(50) DEFAULT NULL COMMENT '所属插件',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1232 DEFAULT CHARSET=utf8 COMMENT='系统模型表';

-- ----------------------------
-- Records of wp_model
-- ----------------------------
INSERT INTO `wp_model` VALUES ('1', 'user', '用户信息表', '0', '', '0', '[\"come_from\",\"nickname\",\"password\",\"truename\",\"mobile\",\"email\",\"sex\",\"headimgurl\",\"city\",\"province\",\"country\",\"language\",\"score\",\"experience\",\"unionid\",\"login_count\",\"reg_ip\",\"reg_time\",\"last_login_ip\",\"last_login_time\",\"status\",\"is_init\",\"is_audit\"]', '1:基础', '', '', '', '', 'headimgurl|url_img_html:头像\r\nlogin_name:登录账号\r\nlogin_password:登录密码\r\nnickname|deal_emoji:用户昵称\r\nsex|get_name_by_status:性别\r\ngroup:分组\r\nscore:金币值\r\nexperience:经历值\r\nids:操作:set_login?uid=[uid]|设置登录账号,detail?uid=[uid]|详细资料,[EDIT]|编辑', '20', '', '', '1436929111', '1441187405', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('2', 'manager', '公众号管理员配置', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1436932532', '1436942362', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('3', 'manager_menu', '公众号管理员菜单', '0', '', '1', '[\"menu_type\",\"pid\",\"title\",\"url_type\",\"addon_name\",\"url\",\"target\",\"is_hide\",\"sort\"]', '1:基础', '', '', '', '', 'title:菜单名\r\nmenu_type|get_name_by_status:菜单类型\r\naddon_name:插件名\r\nurl:外链\r\ntarget|get_name_by_status:打开方式\r\nis_hide|get_name_by_status:隐藏\r\nsort:排序号\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1435215960', '1437623073', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('4', 'keyword', '关键词表', '0', '', '1', '[\"keyword\",\"keyword_type\",\"addon\",\"aim_id\",\"keyword_length\",\"cTime\",\"extra_text\",\"extra_int\"]', '1:基础', '', '', '', '', 'id:编号\r\nkeyword:关键词\r\naddon:所属插件\r\naim_id:插件数据ID\r\ncTime|time_format:增加时间\r\nrequest_count|intval:请求数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'keyword', '', '1388815871', '1407251192', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('5', 'qr_code', '二维码表', '0', '', '1', '[\"qr_code\",\"addon\",\"aim_id\",\"cTime\",\"extra_text\",\"extra_int\",\"scene_id\",\"action_name\"]', '1:基础', '', '', '', '', 'scene_id:事件KEY值\r\nqr_code|get_code_img:二维码\r\naction_name|get_name_by_status: 	二维码类型\r\naddon:所属插件\r\naim_id:插件数据ID\r\ncTime|time_format:增加时间\r\nrequest_count|intval:请求数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'qr_code', '', '1388815871', '1406130247', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('6', 'public', '公众号管理', '0', '', '1', '[\"public_name\",\"public_id\",\"wechat\",\"headface_url\",\"type\",\"appid\",\"secret\",\"encodingaeskey\",\"tips_url\",\"GammaAppId\",\"GammaSecret\",\"public_copy_right\"]', '1:基础', '', '', '', '', 'id:公众号ID\r\npublic_name:公众号名称\r\ntoken:Token\r\ncount:管理员数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,main&public_id=[id]|进入管理', '20', 'public_name', '', '1391575109', '1447231672', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('7', 'public_group', '公众号等级', '0', '', '1', '[\"title\",\"addon_status\"]', '1:基础', '', '', '', '', 'id:等级ID\r\ntitle:等级名\r\naddon_status:授权的插件\r\npublic_count:公众号数\r\nids:操作:editPublicGroup&id=[id]|编辑,delPublicGroup&id=[id]|删除', '20', 'title', '', '1393724788', '1393730663', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('8', 'public_link', '公众号与管理员的关联关系', '0', '', '1', '[\"uid\",\"addon_status\"]', '1:基础', '', '', '', '', 'uid|get_nickname|deal_emoji:15%管理员(不包括创始人)\r\naddon_status:授权的插件\r\nids:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1398933192', '1447233745', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('9', 'import', '导入数据', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1407554076', '1407554076', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('10', 'addon_category', '插件分类', '0', '', '1', '[\"icon\",\"title\",\"sort\"]', '1:基础', '', '', '', '', 'icon|get_img_html:分类图标\r\ntitle:分类名\r\nsort:排序号\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1400047655', '1437451028', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('12', 'common_category', '通用分类', '0', '', '1', '[\"pid\",\"title\",\"icon\",\"intro\",\"sort\",\"is_show\"]', '1:基础', '', '', '', '', 'code:编号\r\ntitle:标题\r\nicon|get_img_html:图标\r\nsort:排序号\r\nis_show|get_name_by_status:显示\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1397529095', '1404182789', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('13', 'common_category_group', '通用分类分组', '0', '', '1', '[\"name\",\"title\"]', '1:基础', '', '', '', '', 'name:分组标识\r\ntitle:分组标题\r\nids:操作:cascade?target=_blank&module=[name]|数据管理,[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1403664378', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('14', 'credit_config', '积分配置', '0', '', '1', '[\"name\",\"title\",\"score\",\"experience\"]', '1:基础', '', '', '', '', 'title:积分描述\r\nname:积分标识\r\nexperience:经验值\r\nscore:金币值\r\nids:操作:[EDIT]|配置', '20', 'title', '', '1396061373', '1438591151', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('15', 'credit_data', '用户积分记录', '0', '', '1', '[\"uid\",\"experience\",\"score\",\"credit_name\"]', '1:基础', '', '', '', '', 'uid:用户\r\ncredit_title:积分来源\r\nexperience:经验值\r\nscore:金币值\r\ncTime|time_format:记录时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'uid', '', '1398564291', '1447250833', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('16', 'material_image', '图片素材', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1438684613', '1438684613', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('17', 'material_news', '图文素材', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1438670890', '1438670890', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('18', 'message', '群发消息', '0', '', '1', '[\"type\",\"bind_keyword\",\"media_id\",\"openid\",\"send_type\",\"group_id\",\"send_openids\"]', '1:基础', '', '', '', '', '', '20', '', '', '1437984111', '1438049406', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('19', 'visit_log', '网站访问日志', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1439448351', '1439448351', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('20', 'auth_group', '用户组', '0', '', '1', '[\"title\",\"description\"]', '1:基础', '', '', '', '', 'title:分组名称\r\ndescription:描述\r\nqr_code:二维码\r\nids:操作:export?id=[id]|导出用户,[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1437633503', '1447660681', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('21', 'analysis', '统计分析', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1432806941', '1432806941', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('22', 'article_style', '图文样式', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1436845488', '1436845488', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('23', 'article_style_group', '图文样式分组', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1436845186', '1436845186', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1217', 'youaskservice_wechat_grouplist', 'youaskservice_wechat_grouplist', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1404027300', '1404027300', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1218', 'youaskservice_wxlogs', '你问我答- 微信聊天记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1406094050', '1406094093', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1219', 'prize_address', '奖品收货地址', '0', '', '1', '[\"address\",\"mobile\",\"turename\",\"remark\"]', '1:基础', '', '', '', '', 'prizeid:奖品名称\r\nturename:收货人\r\nmobile:联系方式\r\naddress:收货地址\r\nremark:备注\r\nids:操作:address_edit&id=[id]&_controller=RealPrize&_addons=RealPrize|编辑,[DELETE]|删除', '20', '', '', '1429521514', '1447831599', '1', 'MyISAM', 'RealPrize');
INSERT INTO `wp_model` VALUES ('1220', 'real_prize', '实物奖励', '0', '', '1', '[\"prize_title\",\"prize_name\",\"prize_conditions\",\"prize_count\",\"prize_image\",\"prize_type\",\"use_content\",\"fail_content\",\"template\"]', '1:基础', '', '', '', '', 'prize_name:20%奖品名称\r\nprize_conditions:20%活动说明\r\nprize_count:10%奖品个数\r\nprize_type|get_name_by_status:10%奖品类型\r\nuse_content:20%使用说明\r\nid:20%操作:[EDIT]|编辑,[DELETE]|删除,address_lists?target_id=[id]|查看数据,preview?id=[id]&target=_blank|预览', '20', '', '', '1429515376', '1437452269', '1', 'MyISAM', 'RealPrize');
INSERT INTO `wp_model` VALUES ('1221', 'xydzp', '幸运大转盘', '0', '', '1', '[\"keyword\",\"title\",\"picurl\",\"des_jj\",\"guiz\",\"choujnum\",\"start_date\",\"end_date\",\"experience\",\"background\",\"template\"]', '1:基础', '', '', '', '', 'id:编号\r\nkeyword:触发关键词\r\ntitle:标题\r\nstart_date|time_format:开始时间\r\nend_date|time_format:结束日期\r\nchoujnum:每日抽奖次数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,zjloglists?id=[id]|中奖记录,jplists?xydzp_id=[id]|奖品设置,preview?id=[id]&target=_blank|预览', '20', 'title', 'des', '1395395179', '1437449460', '1', 'MyISAM', 'Xydzp');
INSERT INTO `wp_model` VALUES ('1222', 'xydzp_jplist', '幸运大转盘奖品列表', '0', '', '1', '[\"gailv\",\"gailv_maxnum\"]', '1:基础', '', '', '', '', 'xydzp_option_id:奖品名称\r\ngailv:中奖概率（0-100）\r\ngailv_maxnum:单日发放上限\r\nids:操作:jpedit?id=[id]|编辑,jpdel?id=[id]|删除', '20', '', '', '1395554963', '1419305652', '1', 'MyISAM', 'Xydzp');
INSERT INTO `wp_model` VALUES ('1223', 'xydzp_log', '幸运大转盘中奖列表', '0', '', '1', '[\"xydzp_id\",\"xydzp_option_id\",\"zip\",\"iphone\",\"address\",\"message\"]', '1:基础', '', '', '', '', 'id:编号\r\ntruename:用户名称\r\nopenid:用户ID\r\nmobile:联系电话\r\ntitle:奖品名称\r\nstate|get_name_by_status:领奖状态\r\nzjdate|time_format:中奖时间\r\nid:标记:ylingqu?id=[id]|已领取,wlingqu?id=[id]|未领取', '20', '', '', '1395395200', '1420358394', '1', 'MyISAM', 'Xydzp');
INSERT INTO `wp_model` VALUES ('1224', 'xydzp_option', '幸运大转盘奖品库设置', '0', '', '1', '[\"title\",\"jptype\",\"coupon_id\",\"experience\",\"num\",\"pic\",\"miaoshu\"]', '1:基础', '', '', '', '', 'pic|get_img_html:奖品图片\r\ntitle:奖品名称\r\njptype|get_name_by_status:奖品类型\r\nnum:库存数量\r\nids:操作:jpopedit?id=[id]|编辑,jpopdel?id=[id]|删除', '20', 'title', '', '1395395190', '1419303406', '1', 'MyISAM', 'Xydzp');
INSERT INTO `wp_model` VALUES ('1225', 'xydzp_userlog', '幸运大转盘用户抽奖记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1395567366', '1395567366', '1', 'MyISAM', 'Xydzp');
INSERT INTO `wp_model` VALUES ('1226', 'reserve', '微预约', '0', '', '1', '[\"title\",\"intro\",\"cover\",\"can_edit\",\"finish_tip\",\"jump_url\",\"content\",\"template\",\"status\",\"start_time\",\"end_time\",\"pay_online\"]', '1:基础', '', '', '', '', 'title:标题\r\nstatus|get_name_by_status:状态\r\nstart_time:报名时间\r\nids:操作:preview&id=[id]|预览,[EDIT]|编辑,reserve_value&id=[id]|预约列表,[DELETE]|删除,index&_addons=Reserve&_controller=Wap&reserve_id=[id]|复制链接', '20', 'title', '', '1396061373', '1445409060', '1', 'MyISAM', 'Reserve');
INSERT INTO `wp_model` VALUES ('1227', 'reserve_attribute', '微预约字段', '0', '', '1', '[\"name\",\"title\",\"type\",\"extra\",\"value\",\"remark\",\"is_must\",\"validate_rule\",\"error_info\",\"sort\"]', '1:基础', '', '', '', '', 'title:字段标题\r\nname:字段名\r\ntype|get_name_by_status:字段类型\r\nids:操作:[EDIT]&reserve_id=[reserve_id]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1396710959', '1', 'MyISAM', 'Reserve');
INSERT INTO `wp_model` VALUES ('1228', 'reserve_value', '微预约数据', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1396687959', '1396687959', '1', 'MyISAM', 'Reserve');
INSERT INTO `wp_model` VALUES ('1229', 'reserve_option', '预约选项', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1444962050', '1444962050', '1', 'MyISAM', 'Reserve');
INSERT INTO `wp_model` VALUES ('1230', 'payment_order', '订单支付记录', '0', '', '1', '[\"from\",\"orderName\",\"single_orderid\",\"price\",\"token\",\"wecha_id\",\"paytype\",\"showwxpaytitle\",\"status\"]', '1:基础', '', '', '', '', '', '20', '', '', '1420596259', '1423534012', '1', 'MyISAM', 'Payment');
INSERT INTO `wp_model` VALUES ('1231', 'payment_set', '支付配置', '0', '', '1', '[\"wxappid\",\"wxappsecret\",\"wxpaysignkey\",\"zfbname\",\"pid\",\"key\",\"partnerid\",\"partnerkey\",\"wappartnerid\",\"wappartnerkey\",\"quick_security_key\",\"quick_merid\",\"quick_merabbr\",\"wxmchid\"]', '1:基础', '', '', '', '', '', '10', '', '', '1406958084', '1439364636', '1', 'MyISAM', 'Payment');
INSERT INTO `wp_model` VALUES ('1209', 'prize', '奖项设置', '0', '', '1', '[\"title\",\"name\",\"num\",\"img\",\"sort\"]', '1:基础', '', '', '', '', 'title:奖项标题\r\nname:奖项\r\nnum:名额数量\r\nimg|get_img_html:奖品图片\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1399348610', '1399702991', '1', 'MyISAM', 'Scratch');
INSERT INTO `wp_model` VALUES ('1210', 'scratch', '刮刮卡', '0', '', '1', '[\"keyword\",\"title\",\"intro\",\"cover\",\"use_tips\",\"start_time\",\"end_time\",\"end_tips\",\"end_img\",\"predict_num\",\"max_num\",\"follower_condtion\",\"credit_conditon\",\"credit_bug\",\"addon_condition\",\"collect_count\",\"view_count\",\"template\"]', '1:基础', '', '', '', '', 'id:刮刮卡ID\r\nkeyword:关键词\r\ntitle:标题\r\ncollect_count:获取人数\r\ncTime|time_format:发布时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,lists?target_id=[id]&target=_blank&_controller=Sn|中奖管理,lists?target_id=[id]&target=_blank&_controller=Prize|奖品管理,preview?id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1437035669', '1', 'MyISAM', 'Scratch');
INSERT INTO `wp_model` VALUES ('1200', 'shop_address', '收货地址', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1423477477', '1423477477', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1201', 'shop_slideshow', '幻灯片', '0', '', '1', '[\"title\",\"img\",\"url\",\"is_show\",\"sort\"]', '1:基础', '', '', '', '', 'title:标题\r\nimg:图片\r\nurl:链接地址\r\nis_show|get_name_by_status:显示\r\nsort:排序\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除', '20', 'title', '', '1396098264', '1408323347', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1202', 'shop_order_log', '订单跟踪', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1439525562', '1439525562', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1203', 'shop_order', '订单记录', '0', '', '1', '[\"uid\",\"goods_datas\",\"remark\",\"order_number\",\"cTime\",\"total_price\",\"address_id\",\"is_send\",\"send_code\",\"send_number\",\"send_type\",\"shop_id\"]', '1:基础', '', '', '', '', 'order_number:15%订单编号\r\ngoods:20%下单商品\r\nuid:10%客户\r\ntotal_price:7%总价\r\ncTime|time_format:17%下单时间\r\ncommon|get_name_by_status:10%支付类型\r\nstatus_code|get_name_by_status:10%订单跟踪\r\naction:11%操作', '20', 'order_number:请输入订单编号 或 客户昵称', '', '1420269240', '1440147136', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1204', 'shop_goods_score', '商品评分记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1422930901', '1422930901', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1205', 'shop_goods_category', '商品分类', '0', '', '1', '[\"title\",\"icon\",\"sort\",\"is_show\",\"is_recommend\"]', '1:基础', '', '', '', '', 'title:20%分组\r\nicon|get_img_html:20%图标\r\nsort:20%排序号\r\nis_show|get_name_by_status:20%显示\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1397529095', '1438326713', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1206', 'wish_card', '微贺卡', '0', '', '1', '[\"send_name\",\"receive_name\",\"content\",\"template\"]', '1:基础', '', '', '', '', 'send_name:10%发送人\r\nreceive_name:10%接收人\r\ncontent:40%祝福语\r\ncreate_time|time_format:15%创建时间\r\nread_count:10%浏览次数\r\nid:15%操作:[EDIT]|编辑,card_show?id=[id]&target=_blank&_controller=Wap|预览,[DELETE]|删除', '20', 'content:祝福语', '', '1429346197', '1429760720', '1', 'MyISAM', 'WishCard');
INSERT INTO `wp_model` VALUES ('1207', 'wish_card_content', '祝福语', '0', '', '1', '[\"content_cate\",\"content\"]', '1:基础', '', '', '', '', 'content_cate_id:10%类别Id\r\ncontent_cate:20%类别\r\ncontent:50%祝福语\r\nid:20%操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1429348863', '1429841596', '1', 'MyISAM', 'WishCard');
INSERT INTO `wp_model` VALUES ('1208', 'wish_card_content_cate', '祝福语类别', '0', '', '1', '[\"content_cate_name\",\"content_cate_icon\"]', '1:基础', '', '', '', '', 'content_cate_name:类别\r\ncontent_cate_icon|get_img_html:图标\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'content_cate_name:类别', '', '1429348818', '1429598114', '1', 'MyISAM', 'WishCard');
INSERT INTO `wp_model` VALUES ('81', 'sn_code', 'SN码', '0', '', '1', '[\"prize_title\"]', '1:基础', '', '', '', '', 'sn:SN码\r\nuid|get_nickname|deal_emoji:昵称\r\nprize_title:奖项\r\ncTime|time_format:创建时间\r\nis_use|get_name_by_status:是否已使用\r\nuse_time|time_format:使用时间\r\nids:操作:[DELETE]|删除,set_use?id=[id]|改变使用状态', '20', 'sn', '', '1399272054', '1401013099', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('87', 'store', '应用商店', '0', '', '1', '[\"type\",\"title\",\"price\",\"attach\",\"logo\",\"content\",\"img_1\",\"img_2\",\"img_3\",\"img_4\",\"is_top\",\"audit\",\"audit_time\"]', '1:基础', '', '', '', '', 'id:ID值\r\ntype|get_name_by_status:应用类型\r\ntitle:应用标题\r\nprice:价格\r\nlogo|get_img_html:应用LOGO\r\nmTime|time_format:更新时间\r\naudit|get_name_by_status:审核状态\r\naudit_time|time_format:审核时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1394033250', '1402885526', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('88', 'sucai', '素材管理', '0', '', '1', '[\"name\",\"status\",\"cTime\",\"url\",\"type\",\"detail\",\"reason\",\"create_time\",\"checked_time\",\"source\",\"source_id\"]', '1:基础', '', '', '', '', 'name:素材名称\r\nstatus|get_name_by_status:状态\r\nurl:页面URL\r\ncreate_time|time_format:申请时间\r\nchecked_time|time_format:入库时间\r\nids:操作', '20', 'name', '', '1424611702', '1425386629', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('89', 'sucai_template', '素材模板库', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1431575544', '1431575544', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('93', 'system_notice', '系统公告表', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1431141043', '1431141043', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('94', 'update_version', '系统版本升级', '0', '', '1', '[\"version\",\"title\",\"description\",\"create_date\",\"package\"]', '1:基础', '', '', '', '', 'version:版本号\r\ntitle:升级包名\r\ndescription:描述\r\ncreate_date|time_format:创建时间\r\ndownload_count:下载统计数\r\nids:操作:[EDIT]&id=[id]|编辑,[DELETE]&id=[id]|删除', '20', '', '', '1393770420', '1393771807', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1197', 'shop_goods', '商品列表', '0', '', '1', '[\"title\",\"imgs\",\"category_id\",\"price\",\"is_recommend\",\"content\",\"cover\",\"inventory\",\"is_show\",\"old_price\"]', '1:基础', '', '', '', '', 'category_id:商品分类\r\ncover|get_img_html:封面图\r\ntitle:商品名称\r\nprice:价格\r\ninventory:库存量\r\nsale_count:销售量\r\nis_show|get_name_by_status:是否上架\r\nids:操作:set_show?id=[id]&is_show=[is_show]|改变上架状态,[EDIT]|编辑,[DELETE]|删除', '20', 'title:请输入商品名称', '', '1422672084', '1440124560', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1196', 'shop', '微商城', '0', '', '1', '[\"title\",\"logo\",\"intro\",\"mobile\",\"qq\",\"wechat\",\"content\"]', '1:基础', '', '', '', '', 'title:商店名称\r\nlogo|get_img_html:商店LOGO\r\nmobile:联系电话\r\nqq:QQ号\r\nwechat:微信号\r\nids:操作:[EDIT]&id=[id]|编辑,lists&_controller=Category&target=_blank&shop_id=[id]|商品分类,lists&_controller=Slideshow&target=_blank&shop_id=[id]|幻灯片,lists&_controller=Goods&target=_blank&shop_id=[id]|商品管理,lists&_controller=Order&target=_blank&shop_id=[id]|订单管理,lists&_addons=Payment&_controller=Payment&target=_blank&shop_id=[id]|支付配置,lists&_controller=Template&target=_blank&shop_id=[id]|模板选择,[DELETE]|删除,index&_controller=Wap&target=_blank&shop_id=[id]|预览', '20', 'title:请输入商店名称', '', '1422670956', '1423640744', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('103', 'weixin_message', '微信消息管理', '0', '', '1', '', '1:基础', '', '', '', '', 'FromUserName:用户\r\ncontent:内容\r\nCreateTime:时间', '20', '', '', '1438142999', '1438151555', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1211', 'youaskservice_behavior', 'youaskservice_behavior', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1404033501', '1404033501', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1212', 'youaskservice_group', '你问我答-客服分组', '0', '', '1', '[\"groupname\"]', '1:基础', '', '', '', '', 'id:编号\r\ngroupname:分组名称\r\ntoken:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'groupname', '', '1404475456', '1404491410', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1213', 'youaskservice_keyword', '你问我答-关键词指配', '0', '', '1', '[\"msgkeyword\",\"msgkeyword_type\",\"zdtype\",\"msgstate\"]', '1:基础', '', '', '', '', 'id:编号\r\nmsgkeyword:关键字\r\nmsgkeyword_type|get_name_by_status:匹配类型\r\nmsgkfaccount:指定的接待客服或分组\r\nmsgstate|get_name_by_status:状态\r\nzdtype:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'msgkeyword', '', '1404399143', '1404493938', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1214', 'youaskservice_logs', '你问我答-聊天记录管理', '0', '', '1', '[\"pid\",\"openid\",\"enddate\",\"keyword\",\"status\"]', '1:基础', '', '', '', '', 'id:编号\r\nkeyword:回复内容\r\nenddate:回复时间', '20', 'keyword', '', '1403947270', '1404060187', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1215', 'youaskservice_user', '你问我答-客服工号', '0', '', '1', '[\"name\",\"userName\",\"userPwd\",\"state\",\"kfid\"]', '1:基础', '', '', '', '', 'kfid:编号\r\nname:客服昵称\r\nuserName:客服帐号', '20', 'name', 'userName', '1403947253', '1404398415', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1192', 'sms', '短信记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1446107661', '1446107661', '1', 'MyISAM', 'Sms');
INSERT INTO `wp_model` VALUES ('1193', 'survey', '调研问卷', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"cover\",\"intro\",\"finish_tip\",\"template\",\"start_time\",\"end_time\"]', '1:基础', '', '', '', '', 'id:微调研ID\r\nkeyword:关键词\r\nkeyword_type|get_name_by_status:关键词类型\r\ntitle:标题\r\ncTime|time_format:发布时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,survey_answer&id=[id]|数据管理,preview?id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1447640225', '1', 'MyISAM', 'Survey');
INSERT INTO `wp_model` VALUES ('1194', 'survey_answer', '调研回答', '0', '', '1', '', '1:基础', '', '', '', '', 'openid:OpenId\r\nnickname:昵称\r\nmobile:手机号\r\ncTime|time_format:参与时间\r\nids:操作:detail?uid=[uid]&survey_id=[survey_id]|回答内容', '20', 'title', '', '1396061373', '1447645551', '1', 'MyISAM', 'Survey');
INSERT INTO `wp_model` VALUES ('201', 'custom_sendall', '客服群发消息', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1447241925', '1447241925', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('148', 'material_text', '文本素材', '0', '', '1', '[\"content\"]', '1:基础', '', '', '', '', 'id:编号\r\ncontent:文本内容\r\nids:操作:text_edit?id=[id]|编辑,text_del?id=[id]|删除', '10', 'content:请输入文本内容搜索', '', '1442976119', '1442977453', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('149', 'material_file', '文件素材', '0', '', '1', '[\"title\",\"file_id\"]', '1:基础', '', '', '', '', '', '10', '', '', '1438684613', '1442982212', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1190', 'test_question', '测试题目', '0', '', '1', '{\"1\":[\"title\",\"extra\",\"intro\",\"sort\"]}', '1:基础', '', '', '', '', 'id:问题编号\r\ntitle:标题\r\nextra:参数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'title', '', '1396061373', '1397145854', '1', 'MyISAM', 'Test');
INSERT INTO `wp_model` VALUES ('1177', 'guess_option', '竞猜项目', '0', '', '1', '[\"name\",\"image\",\"order\"]', '1:基础', '', '', '', '', 'title:活动名称\r\nname:选项名称\r\nimage|get_img_html:选项图片\r\norder:选项顺序\r\nguess_count:竞猜人数\r\nids:操作:optionLog&guess_id=[guess_id]&option_id=[id]|查看选项竞猜记录', '20', '', '', '1428659140', '1430374342', '1', 'MyISAM', 'Guess');
INSERT INTO `wp_model` VALUES ('1178', 'comment', '评论互动', '0', '', '1', '[\"is_audit\"]', '1:基础', '', '', '', '', 'headimgurl|url_img_html:用户头像\r\nnickname|deal_emoji:用户姓名\r\ncontent:评论内容\r\ncTime|time_format:评论时间\r\nis_audit|get_name_by_status:审核状态\r\nids:操作:[DELETE]|删除', '20', 'content:请输入评论内容', '', '1432602310', '1435310857', '1', 'MyISAM', 'Comment');
INSERT INTO `wp_model` VALUES ('1179', 'card_vouchers', '微信卡券', '0', '', '1', '[\"appsecre\",\"code\",\"content\",\"background\",\"title\",\"button_color\",\"head_bg_color\",\"shop_name\",\"uid\",\"token\",\"shop_logo\",\"more_button\",\"template\"]', '1:基础', '', '', '', '', 'title:卡券名称\r\ncard_id:卡券ID\r\nappsecre:商家公众号密钥\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,preview?id=[id]&target=_blank|预览', '20', 'card_id', '', '1421980317', '1437451096', '1', 'MyISAM', 'CardVouchers');
INSERT INTO `wp_model` VALUES ('1180', 'ask', '抢答问卷', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"cover\",\"intro\",\"finish_tip\",\"shop_address\",\"appids\",\"finish_button\",\"content\",\"card_id\",\"appsecre\",\"template\"]', '1:基础', '', '', '', '', 'id:微抢答ID\r\nkeyword:关键词\r\ntitle:标题\r\ncTime|time_format:发布时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,ask_question&id=[id]|问题管理,ask_answer&id=[id]|数据管理,preview&id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1437449751', '1', 'MyISAM', 'Ask');
INSERT INTO `wp_model` VALUES ('1181', 'ask_answer', '抢答回答', '0', '', '1', '', '1:基础', '', '', '', '', 'uid:用户ID\r\nnickname|deal_emoji:昵称\r\nquestion_id:问题\r\nanswer:回答\r\nis_correct:是否正确\r\ntrue_answer:正确答案\r\ntimes:第几轮\r\ncTime|time_format:回答时间', '20', 'uid:请输入用户ID', '', '1396061373', '1430290975', '1', 'MyISAM', 'Ask');
INSERT INTO `wp_model` VALUES ('1182', 'ask_question', '抢答问题', '0', '', '1', '[\"title\",\"type\",\"extra\",\"answer\",\"wait_time\",\"sort\",\"percent\",\"intro\"]', '1:基础', '', '', '', '', 'title:标题\r\ntype|get_name_by_status:问题类型\r\nwait_time:时间间隔\r\npercent:抢中概率\r\nanswer:正确答案\r\nsort:排序号\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1421749210', '1', 'MyISAM', 'Ask');
INSERT INTO `wp_model` VALUES ('1183', 'business_card', '微名片', '0', '', '1', '[\"truename\",\"mobile\",\"company\",\"service\",\"position\",\"department\",\"company_url\",\"address\",\"telephone\",\"Email\",\"wechat\",\"qq\",\"weibo\",\"tag\",\"wishing\",\"interest\",\"personal_url\",\"intro\",\"permission\",\"token\"]', '1:基础', '', '', '', '', 'uid:用户ID\r\ntruename:名称\r\nposition:职位\r\naddress:地址\r\nmobile:电话\r\ncompany:公司\r\nqq:QQ号\r\nwechat:微信号\r\nemail:邮箱\r\nqrcode:二维码\r\nids:操作:[EDIT]|编辑', '10', 'truename:请输入名称搜索', '', '1438931238', '1439291025', '1', 'MyISAM', 'BusinessCard');
INSERT INTO `wp_model` VALUES ('1184', 'business_card_collect', '名片收藏', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1439188441', '1439188441', '1', 'MyISAM', 'BusinessCard');
INSERT INTO `wp_model` VALUES ('1185', 'business_card_column', '名片栏目', '0', '', '1', '[\"type\",\"cate_id\",\"title\",\"url\",\"sort\"]', '1:基础', '', '', '', '', 'type|get_name_by_status:栏目类型\r\ncate_id:分类名\r\ntitle:标题\r\nurl:url\r\nsort:排序\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', '10', '', '', '1441511425', '1441782615', '1', 'MyISAM', 'BusinessCard');
INSERT INTO `wp_model` VALUES ('1186', 'invite', '微邀约', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cover\",\"experience\",\"num\",\"coupon_id\",\"content\",\"template\"]', '1:基础', '', '', '', '', 'keyword:关键词\r\ntitle:标题\r\nexperience:消耗经验值\r\ncoupon_id:优惠券编号\r\ncoupon_name:优惠券标题\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,lists?target_id=[coupon_id]&target=_blank&_addons=Coupon&_controller=Sn|领取记录,preview?id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1437448319', '1', 'MyISAM', 'Invite');
INSERT INTO `wp_model` VALUES ('1187', 'invite_user', '微邀约用户记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1418192328', '1418192328', '1', 'MyISAM', 'Invite');
INSERT INTO `wp_model` VALUES ('1188', 'tongji', '运营统计', '0', '', '1', '{\"1\":[\"month\",\"day\",\"content\"]}', '1:基础', '', '', '', '', 'day:日期', '10', 'day', '', '1401371050', '1401371409', '1', 'MyISAM', 'Tongji');
INSERT INTO `wp_model` VALUES ('1189', 'test', '测试问卷', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cover\",\"finish_tip\"]', '1:基础', '', '', '', '', 'keyword:关键词\r\nkeyword_type|get_name_by_status:关键词匹配类型\r\ntitle:问卷标题\r\nid:操作:[EDIT]|编辑,[DELETE]|删除,test_question&target=_blank&id=[id]|题目管理,test_answer&target=_blank&id=[id]|用户记录,preview&target=_blank&id=[id]|问卷预览', '10', 'title', '', '1396061373', '1448248071', '1', 'MyISAM', 'Test');
INSERT INTO `wp_model` VALUES ('1173', 'coupon_shop', '适用门店', '0', '', '1', '[\"name\",\"address\",\"gps\",\"phone\"]', '1:基础', '', '', '', '', 'name:店名\r\nphone:联系电话\r\naddress:详细地址\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'name:店名搜索', '', '1427164604', '1439465222', '1', 'MyISAM', 'Coupon');
INSERT INTO `wp_model` VALUES ('1174', 'coupon_shop_link', '门店关联', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1427356350', '1427356350', '1', 'MyISAM', 'Coupon');
INSERT INTO `wp_model` VALUES ('1175', 'guess', '竞猜', '0', '', '1', '[\"title\",\"desc\",\"start_time\",\"end_time\",\"template\",\"cover\"]', '1:基础', '', '', '', '', 'title:活动名称\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nguess_count:参与人数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,guessOption&guess_id=[id]&target=_blank|竞猜选项,guessLog&guess_id=[id]&target=_blank|竞猜记录,preview?id=[id]&target=_blank|预览', '20', 'title:活动名称', '', '1428654951', '1437450636', '1', 'MyISAM', 'Guess');
INSERT INTO `wp_model` VALUES ('1176', 'guess_log', '竞猜记录', '0', '', '1', '[\"token\"]', '1:基础', '', '', '', '', 'optionIds:竞猜选项\r\nuser_id:用户id\r\nuser_name:用户昵称\r\ntoken:用户token\r\ncTime|time_format:竞猜时间\r\n', '20', '', '', '1428738271', '1430374436', '1', 'MyISAM', 'Guess');
INSERT INTO `wp_model` VALUES ('176', 'update_score_log', '修改积分记录', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1444302325', '1444302325', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1171', 'forms_value', '通用表单数据', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1396687959', '1396687959', '1', 'MyISAM', 'Forms');
INSERT INTO `wp_model` VALUES ('1172', 'coupon', '优惠券', '0', '', '1', '[\"title\",\"cover\",\"use_tips\",\"start_time\",\"start_tips\",\"end_time\",\"end_tips\",\"end_img\",\"num\",\"max_num\",\"over_time\",\"empty_prize_tips\",\"pay_password\",\"background\",\"more_button\",\"use_start_time\",\"shop_name\",\"shop_logo\",\"head_bg_color\",\"button_color\",\"template\",\"member\"]', '1:基础', '', '', '', '', 'id:优惠券编号\r\ntitle:标题\r\nnum:计划发送数\r\ncollect_count:已领取数\r\nuse_count:已使用数\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,lists?target_id=[id]&target=_blank&_controller=Sn|成员管理,preview?id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1447756274', '1', 'MyISAM', 'Coupon');
INSERT INTO `wp_model` VALUES ('1170', 'forms_attribute', '表单字段', '0', '', '1', '[\"name\",\"title\",\"type\",\"extra\",\"value\",\"remark\",\"is_must\",\"validate_rule\",\"error_info\",\"sort\"]', '1:基础', '', '', '', '', 'title:字段标题\r\nname:字段名\r\ntype|get_name_by_status:字段类型\r\nids:操作:[EDIT]&forms_id=[forms_id]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1396710959', '1', 'MyISAM', 'Forms');
INSERT INTO `wp_model` VALUES ('1166', 'lottery_games_award_link', '抽奖游戏奖品设置', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1444900969', '1444900969', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1167', 'draw_follow_log', '粉丝抽奖记录', '0', '', '1', '[\"follow_id\",\"sports_id\",\"count\",\"cTime\"]', '1:基础', '', '', '', '', '', '20', '', '', '1432619171', '1432719012', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1168', 'extensions', '融合第三方', '0', '', '1', '', '1:基础', '', '', '', '', 'keyword:关键词\r\nkeyword_filter|get_name_by_status:关键词过滤\r\noutput_format|get_name_by_status:数据输出格式\r\napi_url:第三方地址\r\napi_token:Token\r\ncTime|time_format:增加时间\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'keyword', '', '1393911774', '1394267850', '1', 'MyISAM', 'Extensions');
INSERT INTO `wp_model` VALUES ('1169', 'forms', '通用表单', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cover\",\"can_edit\",\"finish_tip\",\"jump_url\",\"content\",\"template\"]', '1:基础', '', '', '', '', 'id:通用表单ID\r\nkeyword:关键词\r\nkeyword_type|get_name_by_status:关键词类型\r\ntitle:标题\r\ncTime|time_format:发布时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,forms_attribute&id=[id]|字段管理,forms_value&id=[id]|数据管理,preview&id=[id]|预览', '20', 'title', '', '1396061373', '1437450012', '1', 'MyISAM', 'Forms');
INSERT INTO `wp_model` VALUES ('1143', 'custom_reply_mult', '多图文配置', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1396602475', '1396602475', '1', 'MyISAM', 'CustomReply');
INSERT INTO `wp_model` VALUES ('1144', 'custom_reply_news', '图文回复', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cate_id\",\"cover\",\"content\",\"sort\"]', '1:基础', '', '', '', '', 'id:5%ID\r\nkeyword:10%关键词\r\nkeyword_type|get_name_by_status:20%关键词类型\r\ntitle:30%标题\r\ncate_id:10%所属分类\r\nsort:7%排序号\r\nview_count:8%浏览数\r\nid:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1401368247', '1', 'MyISAM', 'CustomReply');
INSERT INTO `wp_model` VALUES ('1145', 'custom_reply_text', '文本回复', '0', '', '1', '[\"keyword\",\"keyword_type\",\"content\",\"sort\"]', '1:基础', '', '', '', '', 'id:ID\r\nkeyword:关键词\r\nkeyword_type|get_name_by_status:关键词类型\r\nsort:排序号\r\nview_count:浏览数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'keyword', '', '1396578172', '1401017369', '1', 'MyISAM', 'CustomReply');
INSERT INTO `wp_model` VALUES ('1147', 'auto_reply', '自动回复', '0', '', '1', '[\"keyword\",\"content\",\"group_id\",\"image_id\"]', '1:基础', '', '', '', '', 'keyword:关键词\r\ncontent:文件内容\r\ngroup_id:图文\r\nimage_id:图片\r\nids:操作:[EDIT]&type=[msg_type]|编辑,[DELETE]|删除', '10', 'keyword:请输入关键词', '', '1439194522', '1439258843', '1', 'MyISAM', 'AutoReply');
INSERT INTO `wp_model` VALUES ('1148', 'weisite_category', '微官网分类', '0', '', '1', '[\"title\",\"icon\",\"url\",\"is_show\",\"sort\",\"pid\"]', '1:基础', '', '', '', '', 'title:15%分类标题\r\nicon|get_img_html:分类图片\r\nurl:30%外链\r\nsort:10%排序号\r\npid:10%一级目录\r\nis_show|get_name_by_status:10%显示\r\nid:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1395987942', '1439522869', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1149', 'weisite_cms', '文章管理', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cate_id\",\"cover\",\"content\",\"sort\"]', '1:基础', '', '', '', '', 'keyword:关键词\r\nkeyword_type|get_name_by_status:关键词类型\r\ntitle:标题\r\ncate_id:所属分类\r\nsort:排序号\r\nview_count:浏览数\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1408326292', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1150', 'weisite_footer', '底部导航', '0', '', '1', '[\"pid\",\"title\",\"url\",\"sort\"]', '1:基础', '', '', '', '', 'title:菜单名\r\nicon:图标\r\nurl:关联URL\r\nsort:排序号\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1394518309', '1396507698', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1151', 'weisite_slideshow', '幻灯片', '0', '', '1', '[\"title\",\"img\",\"url\",\"is_show\",\"sort\"]', '1:基础', '', '', '', '', 'title:标题\r\nimg:图片\r\nurl:链接地址\r\nis_show|get_name_by_status:显示\r\nsort:排序\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除', '20', 'title', '', '1396098264', '1408323347', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1152', 'exam', '考试试卷', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cover\",\"finish_tip\"]', '1:基础', '', '', '', '', 'keyword:关键词\r\nkeyword_type|get_name_by_status:关键词匹配类型\r\ntitle:试卷标题\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nid:操作:[EDIT]|编辑,[DELETE]|删除,exam_question&target=_blank&id=[id]|题目管理,exam_answer&target=_blank&id=[id]|考生成绩,preview&id=[id]&target=_blank|试卷预览', '10', 'title:请输入试卷标题搜索', '', '1396061373', '1447755312', '1', 'MyISAM', 'Exam');
INSERT INTO `wp_model` VALUES ('1153', 'exam_question', '考试题目', '0', '', '1', '{\"1\":[\"title\",\"type\",\"extra\",\"intro\",\"is_must\",\"sort\"]}', '1:基础', '', '', '', '', 'title:标题\r\ntype|get_name_by_status:题目类型\r\nscore:分值\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'title', '', '1396061373', '1397035409', '1', 'MyISAM', 'Exam');
INSERT INTO `wp_model` VALUES ('1154', 'exam_answer', '考试回答', '0', '', '1', '', '1:基础', '', '', '', '', 'openid:OpenId\r\ntruename:姓名\r\nmobile:手机号\r\nscore:成绩\r\ncTime|time_format:考试时间\r\nid:操作:detail?uid=[uid]&exam_id=[exam_id]|答题详情', '10', 'title', '', '1396061373', '1397036455', '1', 'MyISAM', 'Exam');
INSERT INTO `wp_model` VALUES ('1155', 'draw_follow_log', '粉丝抽奖记录', '0', '', '1', '[\"follow_id\",\"sports_id\",\"count\",\"cTime\"]', '1:基础', '', '', '', '', '', '20', '', '', '1432619171', '1432719012', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1156', 'lottery_prize_list', '抽奖奖品列表', '0', '', '1', '[\"sports_id\",\"award_id\",\"award_num\"]', '1:基础', '', '', '', '', 'sports_id:比赛场次\r\naward_id:奖品名称\r\naward_num:奖品数量\r\nid:编辑:[EDIT]|编辑,[DELETE]|删除,add?sports_id=[sports_id]|添加', '20', '', '', '1432613700', '1432710817', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1157', 'lucky_follow', '中奖者信息', '0', '', '1', '[\"draw_id\",\"sport_id\",\"award_id\",\"follow_id\",\"address\",\"num\",\"state\",\"zjtime\",\"djtime\"]', '1:基础', '', '', '', '', 'nickname|deal_emoji:8%微信昵称\r\narea:6%地区\r\nmobile:12%手机号\r\ntruename:7%姓名\r\naddress:6%地址\r\naward_id:10%中奖奖品\r\nnum:5%数量\r\nsport_id:9%中奖场次\r\nzjtime|time_format:8%中奖时间\r\nstate|get_name_by_status:6%兑奖状态\r\ndjtime|time_format:9%兑奖时间\r\ndrum_count:7%擂鼓次数\r\nid:8%中奖人其他信息:luckyFollowDetail?id=[id]|查看\r\n\r\n\r\n', '20', 'award_id:输入奖品名称', '', '1432618091', '1435031703', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1158', 'lzwg_activities', '靓妆活动', '0', '', '1', '[\"title\",\"remark\",\"logo_img\",\"start_time\",\"end_time\",\"get_prize_tip\",\"no_prize_tip\",\"lottery_number\",\"get_prize_count\",\"comment_status\"]', '1:基础', '', '', '', '', 'title:活动名称\r\nremark:活动描述\r\nlogo_img|get_img_html:活动LOGO\r\nactivitie_time:活动时间\r\nget_prize_tip:中将提示信息\r\nno_prize_tip:未中将提示信息\r\ncomment_list:评论列表\r\nset_vote:设置投票\r\nset_award:设置奖品\r\nget_prize_list:中奖列表\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1435306468', '1436181872', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1159', 'lzwg_activities_vote', '投票答题活动', '0', '', '1', '[\"lzwg_id\",\"vote_type\",\"vote_limit\",\"lzwg_type\",\"vote_id\"]', '1:基础', '', '', '', '', 'lzwg_name:活动名称\r\nstart_time|time_format:活动开始时间\r\nend_time|time_format:活动结束时间\r\nlzwg_type|get_name_by_status:活动类型\r\nvote_title:题目\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,tongji&id=[id]|用户参与分析\r\n', '20', 'lzwg_id:活动名称', '', '1435734819', '1435825972', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1160', 'sport_award', '抽奖奖品', '0', '', '1', '[\"award_type\",\"name\",\"count\",\"img\",\"price\",\"score\",\"explain\"]', '1:基础', '', '', '', '', 'id:6%编号\r\nname:23%奖项名称\r\nimg|get_img_html:8%商品图片\r\nprice:8%商品价格\r\nexplain:24%奖品说明\r\ncount:8%奖品数量\r\nid:20%操作:[EDIT]|编辑,[DELETE]|删除,getlistByAwardId?awardId=[id]&_controller=LuckyFollow|中奖者列表', '20', 'name:请输入抽奖名称', '', '1432607100', '1433312389', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1161', 'sports', '体育赛事', '0', '', '1', '[\"home_team\",\"visit_team\",\"start_time\",\"score\",\"content\",\"countdown\",\"comment_status\"]', '1:基础', '', '', '', '', 'start_time|time_format:20%比赛场次\r\nvs_team:20%对战球队（主场VS客场）\r\nscore_title:8%比分\r\ncontent|lists_msubstr:27%对战球队的介绍\r\nids:23%操作:add?sports_id=[id]&_controller=LotteryPrizeList&_addons=Draw&target=_blank|奖品配置,lists?sports_id=[id]&_addons=Draw&_controller=LuckyFollow&target=_blank|中奖列表,lists?sports_id=[id]&_addons=Comment&_controller=Comment&target=_blank|评论列表,package?id=[id]&_controller=Sucai&_addons=Sucai&source=Sports&is_preview=1&target=_blank|预览,package?id=[id]&_controller=Sucai&_addons=Sucai&source=Sports&is_download=1&target=_blank|下载素材,[EDIT]|编辑,[DELETE]|删除', '20', 'home_team:请输入球队名', '', '1432556238', '1436173617', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1162', 'sports_drum', '擂鼓记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1432642253', '1432642253', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1163', 'sports_support', '球队支持记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1432635084', '1432635084', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1164', 'sports_team', '比赛球队', '0', '', '1', '[\"title\",\"logo\",\"intro\"]', '1:基础', '', '', '', '', 'logo|get_img_html:球队图标\r\ntitle:球队名称\r\nintro|lists_msubstr:球队说明\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title:请输入球队名', '', '1432556797', '1432886417', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1198', 'shop_collect', '商品收藏', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1423471275', '1423471275', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1199', 'shop_cart', '购物车', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1419577864', '1419577864', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1216', 'youaskservice_wechat_enddate', 'youaskservice_wechat_enddate', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1404026714', '1404026714', '1', 'MyISAM', 'YouaskService');
INSERT INTO `wp_model` VALUES ('1191', 'test_answer', '测试回答', '0', '', '1', '', '1:基础', '', '', '', '', 'openid:OpenId\r\ntruename:姓名\r\nmobile:手机号\r\nscore:得分\r\ncTime|time_format:测试时间\r\nid:操作:detail?uid=[uid]&test_id=[test_id]|答题详情', '10', 'title', '', '1396061373', '1397145984', '1', 'MyISAM', 'Test');
INSERT INTO `wp_model` VALUES ('1165', 'lottery_games', '抽奖游戏', '0', '', '1', '[\"title\",\"keyword\",\"game_type\",\"start_time\",\"end_time\",\"status\",\"day_attend_limit\",\"attend_limit\",\"day_win_limit\",\"win_limit\",\"day_winners_count\",\"remark\"]', '1:基础', '', '', '', '', 'id:序号\r\ntitle:活动名称\r\ngame_type|get_name_by_status:游戏类型\r\nkeyword:关键词\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nstatus:活动状态\r\nattend_num:参与人数\r\nwinners_list:中奖人列表\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,preview&games_id=[id]|预览,index&_addons=Draw&_controller=Wap&games_id=[id]|复制链接', '10', '', '', '1444877287', '1445482517', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1195', 'survey_question', '调研问题', '0', '', '1', '[\"title\",\"type\",\"extra\",\"intro\",\"is_must\",\"sort\"]', '1:基础', '', '', '', '', 'title:标题\r\ntype|get_name_by_status:问题类型\r\nis_must|get_name_by_status:是否必填\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1396955090', '1', 'MyISAM', 'Survey');
INSERT INTO `wp_model` VALUES ('1142', 'custom_menu', '自定义菜单', '0', '', '1', '[\"pid\",\"title\",\"from_type\",\"type\",\"jump_type\",\"addon\",\"sucai_type\",\"keyword\",\"url\",\"sort\"]', '1:基础', '', '', '', '', 'title:10%菜单名\r\nkeyword:10%关联关键词\r\nurl:50%关联URL\r\nsort:5%排序号\r\nid:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1394518309', '1447317015', '1', 'MyISAM', 'CustomMenu');

-- ----------------------------
-- Table structure for `wp_model_copy`
-- ----------------------------
DROP TABLE IF EXISTS `wp_model_copy`;
CREATE TABLE `wp_model_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned DEFAULT '0' COMMENT '继承的模型',
  `relation` varchar(30) DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `need_pk` tinyint(1) unsigned DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text COMMENT '表单字段排序',
  `field_group` varchar(255) DEFAULT '1:基础' COMMENT '字段分组',
  `attribute_list` text COMMENT '属性列表（表的字段）',
  `template_list` varchar(100) DEFAULT '' COMMENT '列表模板',
  `template_add` varchar(100) DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(100) DEFAULT '' COMMENT '编辑模板',
  `list_grid` text COMMENT '列表定义',
  `list_row` smallint(2) unsigned DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) DEFAULT '' COMMENT '高级搜索的字段',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) DEFAULT 'MyISAM' COMMENT '数据库引擎',
  `addon` varchar(50) DEFAULT NULL COMMENT '所属插件',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1144 DEFAULT CHARSET=utf8 COMMENT='系统模型表';

-- ----------------------------
-- Table structure for `wp_online_count`
-- ----------------------------
DROP TABLE IF EXISTS `wp_online_count`;
CREATE TABLE `wp_online_count` (
  `publicid` int(11) DEFAULT NULL,
  `addon` varchar(30) DEFAULT NULL,
  `aim_id` int(11) DEFAULT NULL,
  `time` bigint(12) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  KEY `tc` (`time`,`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_online_count
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment_order`;
CREATE TABLE `wp_payment_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `from` varchar(50) NOT NULL COMMENT '回调地址',
  `orderName` varchar(255) DEFAULT NULL COMMENT '订单名称',
  `single_orderid` varchar(100) NOT NULL COMMENT '订单号',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `token` varchar(100) NOT NULL COMMENT 'Token',
  `wecha_id` varchar(200) NOT NULL COMMENT 'OpenID',
  `paytype` varchar(30) NOT NULL COMMENT '支付方式',
  `showwxpaytitle` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否显示标题',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `uid` int(10) DEFAULT NULL COMMENT '用户uid',
  `aim_id` int(10) DEFAULT NULL COMMENT 'aim_id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment_set`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment_set`;
CREATE TABLE `wp_payment_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wxmchid` varchar(255) DEFAULT NULL COMMENT '微信支付商户号',
  `shop_id` int(10) DEFAULT '0' COMMENT '商店ID',
  `quick_merid` varchar(255) DEFAULT NULL COMMENT '银联在线merid',
  `quick_merabbr` varchar(255) DEFAULT NULL COMMENT '商户名称',
  `wxpartnerid` varchar(255) DEFAULT NULL COMMENT '微信partnerid',
  `wxpartnerkey` varchar(255) DEFAULT NULL COMMENT '微信partnerkey',
  `partnerid` varchar(255) DEFAULT NULL COMMENT '财付通标识',
  `key` varchar(255) DEFAULT NULL COMMENT 'KEY',
  `ctime` int(10) DEFAULT NULL COMMENT '创建时间',
  `quick_security_key` varchar(255) DEFAULT NULL COMMENT '银联在线Key',
  `wappartnerkey` varchar(255) DEFAULT NULL COMMENT 'WAP财付通Key',
  `wappartnerid` varchar(255) DEFAULT NULL COMMENT '财付通标识WAP',
  `partnerkey` varchar(255) DEFAULT NULL COMMENT '财付通Key',
  `pid` varchar(255) DEFAULT NULL COMMENT 'PID',
  `zfbname` varchar(255) DEFAULT NULL COMMENT '帐号',
  `wxappsecret` varchar(255) DEFAULT NULL COMMENT 'AppSecret',
  `wxpaysignkey` varchar(255) DEFAULT NULL COMMENT '支付密钥',
  `wxappid` varchar(255) DEFAULT NULL COMMENT 'AppID',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `wx_cert_pem` int(10) unsigned DEFAULT NULL COMMENT '上传证书',
  `wx_key_pem` int(10) unsigned DEFAULT NULL COMMENT '上传密匙',
  `shop_pay_score` int(10) DEFAULT '0' COMMENT '支付返积分',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment_set
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_picture`
-- ----------------------------
DROP TABLE IF EXISTS `wp_picture`;
CREATE TABLE `wp_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'Token',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_picture
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_plugin`
-- ----------------------------
DROP TABLE IF EXISTS `wp_plugin`;
CREATE TABLE `wp_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `cate_id` int(11) DEFAULT NULL,
  `is_show` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sti` (`status`,`is_show`)
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COMMENT='系统插件表';

-- ----------------------------
-- Records of wp_plugin
-- ----------------------------
INSERT INTO `wp_plugin` VALUES ('15', 'EditorForAdmin', '后台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"2\",\"editor_height\":\"500px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1383126253', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('2', 'SiteStat', '站点统计信息', '统计站点的基础信息', '0', '{\"title\":\"\\u7cfb\\u7edf\\u4fe1\\u606f\",\"width\":\"2\",\"display\":\"1\"}', 'thinkphp', '0.1', '1379512015', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('22', 'DevTeam', '开发团队信息', '开发团队成员信息', '0', '{\"title\":\"OneThink\\u5f00\\u53d1\\u56e2\\u961f\",\"width\":\"2\",\"display\":\"1\"}', 'thinkphp', '0.1', '1391687096', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('4', 'SystemInfo', '系统环境信息', '用于显示一些服务器的信息', '1', '{\"title\":\"\\u7cfb\\u7edf\\u4fe1\\u606f\",\"width\":\"2\",\"display\":\"1\"}', 'thinkphp', '0.1', '1379512036', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('5', 'Editor', '前台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"1\",\"editor_height\":\"300px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1379830910', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('9', 'SocialComment', '通用社交化评论', '集成了各种社交化评论插件，轻松集成到系统中。', '1', '{\"comment_type\":\"1\",\"comment_uid_youyan\":\"1669260\",\"comment_short_name_duoshuo\":\"\",\"comment_form_pos_duoshuo\":\"buttom\",\"comment_data_list_duoshuo\":\"10\",\"comment_data_order_duoshuo\":\"asc\"}', 'thinkphp', '0.1', '1380273962', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('58', 'Cascade', '级联菜单', '支持无级级联菜单，用于地区选择、多层分类选择等场景。菜单的数据来源支持查询数据库和直接用户按格式输入两种方式', '1', 'null', '凡星', '0.1', '1398694996', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('120', 'DynamicSelect', '动态下拉菜单', '支持动态从数据库里取值显示', '1', 'null', '凡星', '0.1', '1435223177', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('125', 'News', '图文素材选择器', '', '1', 'null', '凡星', '0.1', '1439198046', '0', null, '1');

-- ----------------------------
-- Table structure for `wp_prize`
-- ----------------------------
DROP TABLE IF EXISTS `wp_prize`;
CREATE TABLE `wp_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `addon` varchar(255) DEFAULT 'Scratch' COMMENT '来源插件',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT '来源ID',
  `title` varchar(255) DEFAULT NULL COMMENT '奖项标题',
  `name` varchar(255) DEFAULT NULL COMMENT '奖项',
  `num` int(10) unsigned DEFAULT NULL COMMENT '名额数量',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `img` int(10) unsigned DEFAULT NULL COMMENT '奖品图片',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_prize
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_prize_address`
-- ----------------------------
DROP TABLE IF EXISTS `wp_prize_address`;
CREATE TABLE `wp_prize_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `address` varchar(255) DEFAULT NULL COMMENT '奖品收货地址',
  `mobile` varchar(50) DEFAULT NULL COMMENT '手机',
  `turename` varchar(255) DEFAULT NULL COMMENT '收货人姓名',
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `prizeid` int(10) DEFAULT NULL COMMENT '奖品编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_prize_address
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_public`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public`;
CREATE TABLE `wp_public` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `public_name` varchar(50) DEFAULT NULL COMMENT '公众号名称',
  `public_id` varchar(100) DEFAULT NULL COMMENT '公众号原始id',
  `wechat` varchar(100) DEFAULT NULL COMMENT '微信号',
  `interface_url` varchar(255) DEFAULT NULL COMMENT '接口地址',
  `headface_url` varchar(255) DEFAULT NULL COMMENT '公众号头像',
  `area` varchar(50) DEFAULT NULL COMMENT '地区',
  `addon_config` text COMMENT '插件配置',
  `addon_status` text COMMENT '插件状态',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否为当前公众号',
  `type` char(10) DEFAULT '0' COMMENT '公众号类型',
  `appid` varchar(255) DEFAULT NULL COMMENT 'AppID',
  `secret` varchar(255) DEFAULT NULL COMMENT 'AppSecret',
  `group_id` int(10) unsigned DEFAULT '0' COMMENT '等级',
  `encodingaeskey` varchar(255) DEFAULT NULL COMMENT 'EncodingAESKey',
  `tips_url` varchar(255) DEFAULT NULL COMMENT '提示关注公众号的文章地址',
  `domain` varchar(30) DEFAULT NULL COMMENT '自定义域名',
  `is_bind` tinyint(2) DEFAULT '0' COMMENT '是否为微信开放平台绑定账号',
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_public_auth`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_auth`;
CREATE TABLE `wp_public_auth` (
  `name` char(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type_0` tinyint(1) DEFAULT '0' COMMENT '普通订阅号的开关',
  `type_1` tinyint(1) DEFAULT '0' COMMENT '微信认证订阅号的开关',
  `type_2` tinyint(1) DEFAULT '0' COMMENT '普通服务号的开关',
  `type_3` tinyint(1) DEFAULT '0' COMMENT '微信认证服务号的开关',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_auth
-- ----------------------------
INSERT INTO `wp_public_auth` VALUES ('GET_ACCESS_TOKEN', '基础支持-获取access_token', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('GET_WECHAT_IP', '基础支持-获取微信服务器IP地址', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('GET_MSG', '接收消息-验证消息真实性、接收普通消息、接收事件推送、接收语音识别结果', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('SEND_REPLY_MSG', '发送消息-被动回复消息', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('SEND_CUSTOM_MSG', '发送消息-客服接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('SEND_GROUP_MSG', '发送消息-群发接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('SEND_NOTICE', '发送消息-模板消息接口（发送业务通知）', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('USER_GROUP', '用户管理-用户分组管理', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('USER_REMARK', '用户管理-设置用户备注名', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('USER_BASE_INFO', '用户管理-获取用户基本信息', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('USER_LIST', '用户管理-获取用户列表', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('USER_LOCATION', '用户管理-获取用户地理位置', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('USER_OAUTH', '用户管理-网页授权获取用户openid/用户基本信息', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('QRCODE', '推广支持-生成带参数二维码', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('LONG_URL', '推广支持-长链接转短链接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('MENU', '界面丰富-自定义菜单', '0', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('MATERIAL', '素材管理-素材管理接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('SEMANTIC', '智能接口-语义理解接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('CUSTOM_SERVICE', '多客服-获取多客服消息记录、客服管理', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('PAYMENT', '微信支付接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('SHOP', '微信小店接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('CARD', '微信卡券接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('DEVICE', '微信设备功能接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_BASE', '微信JS-SDK-基础接口', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_SHARE', '微信JS-SDK-分享接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_IMG', '微信JS-SDK-图像接口', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_AUDIO', '微信JS-SDK-音频接口', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_SEMANTIC', '微信JS-SDK-智能接口（网页语音识别）', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_DEVICE', '微信JS-SDK-设备信息', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_LOCATION', '微信JS-SDK-地理位置', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_MENU', '微信JS-SDK-界面操作', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_SCAN', '微信JS-SDK-微信扫一扫', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_SHOP', '微信JS-SDK-微信小店', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_CARD', '微信JS-SDK-微信卡券', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('JSSKD_PAYMENT', '微信JS-SDK-微信支付', '0', '0', '0', '1');

-- ----------------------------
-- Table structure for `wp_public_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_follow`;
CREATE TABLE `wp_public_follow` (
  `openid` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `has_subscribe` tinyint(1) DEFAULT '0',
  `syc_status` tinyint(1) DEFAULT '2' COMMENT '0 开始同步中 1 更新用户信息中 2 完成同步',
  `remark` varchar(100) DEFAULT NULL,
  UNIQUE KEY `openid` (`openid`,`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_public_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_group`;
CREATE TABLE `wp_public_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(50) DEFAULT NULL COMMENT '等级名',
  `addon_status` text COMMENT '插件权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_group
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_public_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_link`;
CREATE TABLE `wp_public_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '管理员UID',
  `mp_id` int(10) unsigned NOT NULL COMMENT '公众号ID',
  `is_creator` tinyint(2) DEFAULT '0' COMMENT '是否为创建者',
  `addon_status` text COMMENT '插件权限',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否为当前管理的公众号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `um` (`uid`,`mp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_qr_code`
-- ----------------------------
DROP TABLE IF EXISTS `wp_qr_code`;
CREATE TABLE `wp_qr_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `qr_code` varchar(255) NOT NULL COMMENT '二维码',
  `addon` varchar(255) NOT NULL COMMENT '二维码所属插件',
  `aim_id` int(10) unsigned NOT NULL COMMENT '插件表里的ID值',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `action_name` char(30) DEFAULT 'QR_SCENE' COMMENT '二维码类型',
  `extra_text` text COMMENT '文本扩展',
  `extra_int` int(10) DEFAULT NULL COMMENT '数字扩展',
  `request_count` int(10) DEFAULT '0' COMMENT '请求数',
  `scene_id` int(10) DEFAULT '0' COMMENT '场景ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_qr_code
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_real_prize`
-- ----------------------------
DROP TABLE IF EXISTS `wp_real_prize`;
CREATE TABLE `wp_real_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `prize_name` varchar(255) DEFAULT NULL COMMENT '奖品名称',
  `prize_conditions` text COMMENT '活动说明',
  `prize_count` int(10) DEFAULT NULL COMMENT '奖品个数',
  `prize_image` varchar(255) DEFAULT '上传奖品图片' COMMENT '奖品图片',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `fail_content` text COMMENT '领取失败提示',
  `prize_type` tinyint(2) DEFAULT '1' COMMENT '奖品类型',
  `use_content` text COMMENT '使用说明',
  `prize_title` varchar(255) DEFAULT NULL COMMENT '活动标题',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_real_prize
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_reserve`
-- ----------------------------
DROP TABLE IF EXISTS `wp_reserve`;
CREATE TABLE `wp_reserve` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `password` varchar(255) DEFAULT NULL COMMENT '微预约密码',
  `jump_url` varchar(255) DEFAULT NULL COMMENT '提交后跳转的地址',
  `content` text COMMENT '详细介绍',
  `finish_tip` text COMMENT '用户提交后提示内容',
  `can_edit` tinyint(2) DEFAULT '0' COMMENT '是否允许编辑',
  `intro` text COMMENT '封面简介',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `template` varchar(255) DEFAULT 'default' COMMENT '模板',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `start_time` int(10) DEFAULT NULL COMMENT '报名开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '报名结束时间',
  `pay_online` tinyint(2) DEFAULT '0' COMMENT '是否支持在线支付',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_reserve
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_reserve_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `wp_reserve_attribute`;
CREATE TABLE `wp_reserve_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `reserve_id` int(10) unsigned DEFAULT NULL COMMENT '微预约ID',
  `error_info` varchar(255) DEFAULT NULL COMMENT '出错提示',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `validate_rule` varchar(255) DEFAULT NULL COMMENT '正则验证',
  `is_must` tinyint(2) DEFAULT NULL COMMENT '是否必填',
  `remark` varchar(255) DEFAULT NULL COMMENT '字段备注',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `value` varchar(255) DEFAULT NULL COMMENT '默认值',
  `title` varchar(255) NOT NULL COMMENT '字段标题',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `extra` text COMMENT '参数',
  `type` char(50) NOT NULL DEFAULT 'string' COMMENT '字段类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_reserve_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_reserve_option`
-- ----------------------------
DROP TABLE IF EXISTS `wp_reserve_option`;
CREATE TABLE `wp_reserve_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `reserve_id` int(10) DEFAULT NULL COMMENT '预约活动ID',
  `name` varchar(100) DEFAULT NULL COMMENT '名称',
  `money` decimal(11,2) DEFAULT '0.00' COMMENT '报名费用',
  `max_limit` int(10) DEFAULT '0' COMMENT '最大预约数',
  `init_count` int(10) DEFAULT '0' COMMENT '初始化预约数',
  `join_count` int(10) DEFAULT '0' COMMENT '参加人数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_reserve_option
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_reserve_value`
-- ----------------------------
DROP TABLE IF EXISTS `wp_reserve_value`;
CREATE TABLE `wp_reserve_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `is_check` int(10) DEFAULT '0' COMMENT '验证是否成功',
  `reserve_id` int(10) unsigned DEFAULT NULL COMMENT '微预约ID',
  `value` text COMMENT '微预约值',
  `cTime` int(10) DEFAULT NULL COMMENT '增加时间',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenId',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `is_pay` int(10) DEFAULT '0' COMMENT '是否支付',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_reserve_value
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_scratch`
-- ----------------------------
DROP TABLE IF EXISTS `wp_scratch`;
CREATE TABLE `wp_scratch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `use_tips` varchar(255) NOT NULL COMMENT '使用说明',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_tips` text COMMENT '过期说明',
  `end_img` int(10) unsigned DEFAULT NULL COMMENT '过期提示图片',
  `predict_num` int(10) unsigned NOT NULL COMMENT '预计参与人数',
  `max_num` int(10) unsigned DEFAULT '1' COMMENT '每人最多允许抽奖次数',
  `follower_condtion` char(50) DEFAULT '1' COMMENT '粉丝状态',
  `credit_conditon` int(10) unsigned DEFAULT '0' COMMENT '积分限制',
  `credit_bug` int(10) unsigned DEFAULT '0' COMMENT '积分消费',
  `addon_condition` varchar(255) DEFAULT NULL COMMENT '插件场景限制',
  `collect_count` int(10) unsigned DEFAULT '0' COMMENT '已领取人数',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览人数',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_scratch
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop`;
CREATE TABLE `wp_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '商店名称',
  `logo` int(10) DEFAULT NULL COMMENT '商店LOGO',
  `intro` text COMMENT '店铺简介',
  `mobile` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `qq` int(10) DEFAULT NULL COMMENT 'QQ',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信',
  `template` varchar(30) DEFAULT NULL COMMENT '模板',
  `content` text COMMENT '店铺介绍',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_address`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_address`;
CREATE TABLE `wp_shop_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `truename` varchar(100) DEFAULT NULL COMMENT '收货人姓名',
  `mobile` varchar(50) DEFAULT NULL COMMENT '手机号码',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `address` varchar(255) DEFAULT NULL COMMENT '具体地址',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否设置为默认',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_address
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_cart`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_cart`;
CREATE TABLE `wp_shop_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `shop_id` varchar(255) NOT NULL COMMENT '商店id',
  `goods_id` varchar(255) NOT NULL COMMENT '商品id',
  `num` int(10) unsigned NOT NULL COMMENT '数量',
  `price` varchar(30) NOT NULL COMMENT '单价',
  `goods_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '商品类型',
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_cart
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_collect`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_collect`;
CREATE TABLE `wp_shop_collect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '使用UID',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `cTime` int(10) DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_collect
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods`;
CREATE TABLE `wp_shop_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '商品封面图',
  `content` text NOT NULL COMMENT '商品介绍',
  `title` varchar(255) NOT NULL COMMENT '商品名称',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `imgs` varchar(255) NOT NULL COMMENT '商品图片',
  `inventory` int(10) DEFAULT '0' COMMENT '库存数量',
  `shop_id` int(10) DEFAULT '0' COMMENT '商店ID',
  `is_show` tinyint(2) DEFAULT '0' COMMENT '是否上架',
  `sale_count` int(10) DEFAULT '0' COMMENT '销售量',
  `is_recommend` tinyint(2) DEFAULT NULL COMMENT '是否推荐',
  `rank` int(10) DEFAULT '0' COMMENT '热销度',
  `show_time` int(10) DEFAULT '0' COMMENT '上架时间',
  `old_price` int(10) DEFAULT NULL COMMENT '原价',
  `type` tinyint(2) DEFAULT '0' COMMENT '商品类型',
  `category_id` char(50) DEFAULT NULL COMMENT '商品分类',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_category`;
CREATE TABLE `wp_shop_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '分类标题',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '上一级分类',
  `path` varchar(255) DEFAULT NULL COMMENT '分类路径',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商店ID',
  `is_recommend` tinyint(2) DEFAULT '0' COMMENT '是否推荐',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_score`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_score`;
CREATE TABLE `wp_shop_goods_score` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `score` int(10) DEFAULT '0' COMMENT '得分',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_score
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_order`;
CREATE TABLE `wp_shop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_datas` text NOT NULL COMMENT '商品序列化数据',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `remark` text NOT NULL COMMENT '备注',
  `order_number` varchar(255) NOT NULL COMMENT '订单编号',
  `cTime` int(10) NOT NULL COMMENT '订单时间',
  `total_price` decimal(10,2) DEFAULT NULL COMMENT '总价',
  `openid` varchar(255) NOT NULL COMMENT 'OpenID',
  `pay_status` int(10) DEFAULT NULL COMMENT '支付状态',
  `pay_type` int(10) DEFAULT NULL COMMENT '支付类型',
  `address_id` int(10) DEFAULT NULL COMMENT '配送信息',
  `is_send` int(10) DEFAULT '0' COMMENT '是否发货',
  `send_code` varchar(255) DEFAULT NULL COMMENT '快递公司编号',
  `send_number` varchar(255) DEFAULT NULL COMMENT '快递单号',
  `send_type` char(10) DEFAULT NULL COMMENT '发货类型',
  `is_new` tinyint(2) DEFAULT '1' COMMENT '是否为新订单',
  `shop_id` int(10) DEFAULT '0' COMMENT '商店编号',
  `status_code` char(50) DEFAULT '0' COMMENT '订单跟踪状态码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_order_log`;
CREATE TABLE `wp_shop_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) DEFAULT NULL COMMENT '订单ID',
  `status_code` char(50) DEFAULT '0' COMMENT '状态码',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注内容',
  `cTime` int(10) DEFAULT NULL COMMENT '时间',
  `extend` varchar(255) DEFAULT NULL COMMENT '扩展信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_order_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_slideshow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_slideshow`;
CREATE TABLE `wp_shop_slideshow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `img` int(10) unsigned NOT NULL COMMENT '图片',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `shop_id` int(10) DEFAULT '0' COMMENT '商店ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_slideshow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_smalltools`
-- ----------------------------
DROP TABLE IF EXISTS `wp_smalltools`;
CREATE TABLE `wp_smalltools` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tooltype` tinyint(2) DEFAULT '0' COMMENT '工具类型',
  `keyword` varchar(255) DEFAULT NULL COMMENT ' 关键词 ',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `toolname` varchar(255) DEFAULT NULL COMMENT '工具名称',
  `tooldes` text COMMENT '工具描述',
  `toolnum` varchar(255) DEFAULT NULL COMMENT '工具唯一编号',
  `toolstate` tinyint(2) DEFAULT '0' COMMENT '工具状态',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_smalltools
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sms`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sms`;
CREATE TABLE `wp_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `from_type` varchar(255) DEFAULT NULL COMMENT '用途',
  `code` varchar(255) DEFAULT NULL COMMENT '验证码',
  `smsId` varchar(255) DEFAULT NULL COMMENT '短信唯一标识',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `status` int(10) DEFAULT NULL COMMENT '使用状态',
  `plat_type` int(10) DEFAULT NULL COMMENT '平台标识',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sms
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sn_code`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sn_code`;
CREATE TABLE `wp_sn_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sn` varchar(255) DEFAULT NULL COMMENT 'SN码',
  `uid` int(10) DEFAULT NULL COMMENT '粉丝UID',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否已使用',
  `use_time` int(10) DEFAULT NULL COMMENT '使用时间',
  `addon` varchar(255) DEFAULT 'Coupon' COMMENT '来自的插件',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT '来源ID',
  `prize_id` int(10) unsigned DEFAULT NULL COMMENT '奖项ID',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `prize_title` varchar(255) DEFAULT NULL COMMENT '奖项',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `can_use` tinyint(2) DEFAULT '1' COMMENT '是否可用',
  `server_addr` varchar(50) DEFAULT NULL COMMENT '服务器IP',
  `admin_uid` int(10) DEFAULT NULL COMMENT '核销管理员ID',
  PRIMARY KEY (`id`),
  KEY `id` (`uid`,`target_id`,`addon`),
  KEY `addon` (`target_id`,`addon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sn_code
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sport_award`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sport_award`;
CREATE TABLE `wp_sport_award` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `img` int(10) NOT NULL COMMENT '奖品图片',
  `name` varchar(255) NOT NULL COMMENT '奖项名称',
  `score` int(10) DEFAULT '0' COMMENT '积分数',
  `award_type` varchar(30) DEFAULT '1' COMMENT '奖品类型',
  `price` float DEFAULT '0' COMMENT '商品价格',
  `explain` text COMMENT '奖品说明',
  `count` int(10) NOT NULL COMMENT '奖品数量',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `coupon_id` char(50) DEFAULT NULL COMMENT '选择赠送券',
  `money` float DEFAULT NULL COMMENT '返现金额',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '活动标识',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sport_award
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sports`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sports`;
CREATE TABLE `wp_sports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `score` varchar(30) DEFAULT NULL COMMENT '比分',
  `content` text COMMENT '说明',
  `start_time` int(10) DEFAULT NULL COMMENT '时间',
  `visit_team` varchar(255) DEFAULT NULL COMMENT '客场球队',
  `home_team` varchar(255) DEFAULT NULL COMMENT '主场球队',
  `countdown` int(10) DEFAULT '60' COMMENT '擂鼓时长',
  `drum_count` int(10) DEFAULT '0' COMMENT '擂鼓次数',
  `drum_follow_count` int(10) DEFAULT '0' COMMENT '擂鼓人数',
  `home_team_support_count` int(10) DEFAULT '0' COMMENT '主场球队支持数',
  `visit_team_support_count` int(10) DEFAULT '0' COMMENT '客场球队支持人数',
  `home_team_drum_count` int(10) DEFAULT '0' COMMENT '主场球队擂鼓数',
  `visit_team_drum_count` int(10) DEFAULT '0' COMMENT '客场球队擂鼓数',
  `yaotv_count` int(10) DEFAULT '0' COMMENT '摇一摇总次',
  `draw_count` int(10) DEFAULT '0' COMMENT '抽奖总次数',
  `is_finish` tinyint(2) DEFAULT '0' COMMENT '是否已结束',
  `yaotv_follow_count` int(10) DEFAULT '0' COMMENT '摇电视总人数',
  `draw_follow_count` int(10) DEFAULT '0' COMMENT '抽奖总人数',
  `comment_status` tinyint(2) DEFAULT '0' COMMENT '评论是否需要审核',
  PRIMARY KEY (`id`),
  KEY `start_time` (`start_time`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sports
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sports_drum`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sports_drum`;
CREATE TABLE `wp_sports_drum` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sports_id` int(10) DEFAULT NULL COMMENT '场次ID',
  `team_id` int(10) DEFAULT NULL COMMENT '球队ID',
  `follow_id` int(10) DEFAULT NULL COMMENT '用户ID',
  `drum_count` int(10) DEFAULT NULL COMMENT '擂鼓次数',
  `cTime` int(10) DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `ctime` (`sports_id`,`cTime`),
  KEY `team_id` (`sports_id`,`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sports_drum
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sports_support`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sports_support`;
CREATE TABLE `wp_sports_support` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sports_id` int(10) DEFAULT NULL COMMENT '场次ID',
  `team_id` int(10) DEFAULT NULL COMMENT '球队ID',
  `follow_id` int(10) DEFAULT NULL COMMENT '用户ID',
  `cTime` int(10) DEFAULT NULL COMMENT '支持时间',
  PRIMARY KEY (`id`),
  KEY `sf` (`sports_id`,`follow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sports_support
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sports_team`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sports_team`;
CREATE TABLE `wp_sports_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  `intro` text COMMENT '球队说明',
  `pid` int(10) DEFAULT '0' COMMENT 'pid',
  `logo` int(10) unsigned DEFAULT NULL COMMENT '球队图标',
  `title` varchar(100) DEFAULT NULL COMMENT '球队名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sports_team
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_store`
-- ----------------------------
DROP TABLE IF EXISTS `wp_store`;
CREATE TABLE `wp_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `uid` int(10) DEFAULT '0' COMMENT '用户ID',
  `content` text COMMENT '内容',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `attach` varchar(255) DEFAULT NULL COMMENT '插件安装包',
  `is_top` int(10) DEFAULT '0' COMMENT '置顶',
  `cid` tinyint(4) DEFAULT NULL COMMENT '分类',
  `view_count` int(11) unsigned DEFAULT '0' COMMENT '浏览数',
  `img_1` int(10) unsigned DEFAULT NULL COMMENT '插件截图1',
  `img_2` int(10) unsigned DEFAULT NULL COMMENT '插件截图2',
  `img_3` int(10) unsigned DEFAULT NULL COMMENT '插件截图3',
  `img_4` int(10) unsigned DEFAULT NULL COMMENT '插件截图4',
  `download_count` int(10) unsigned DEFAULT '0' COMMENT '下载数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_store
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sucai`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sucai`;
CREATE TABLE `wp_sucai` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) DEFAULT NULL COMMENT '素材名称',
  `status` char(10) DEFAULT 'UnSubmit' COMMENT '状态',
  `cTime` int(10) DEFAULT NULL COMMENT '提交时间',
  `url` varchar(255) DEFAULT NULL COMMENT '实际摇一摇所使用的页面URL',
  `type` varchar(255) DEFAULT NULL COMMENT '素材类型',
  `detail` text COMMENT '素材内容',
  `reason` text COMMENT '入库失败的原因',
  `create_time` int(10) DEFAULT NULL COMMENT '申请时间',
  `checked_time` int(10) DEFAULT NULL COMMENT '入库时间',
  `source` varchar(50) DEFAULT NULL COMMENT '来源',
  `source_id` int(10) DEFAULT NULL COMMENT '来源ID',
  `wechat_id` int(10) DEFAULT NULL COMMENT '微信端的素材ID',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sucai
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sucai_template`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sucai_template`;
CREATE TABLE `wp_sucai_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '管理员id',
  `token` varchar(255) DEFAULT NULL COMMENT '用户token',
  `addons` varchar(255) DEFAULT NULL COMMENT '插件名称',
  `template` varchar(255) DEFAULT NULL COMMENT '模版名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sucai_template
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_survey`
-- ----------------------------
DROP TABLE IF EXISTS `wp_survey`;
CREATE TABLE `wp_survey` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `finish_tip` text COMMENT '结束语',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_survey
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_survey_answer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_survey_answer`;
CREATE TABLE `wp_survey_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `answer` text COMMENT '回答内容',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenId',
  `uid` int(10) DEFAULT NULL COMMENT '用户UID',
  `question_id` int(10) unsigned NOT NULL COMMENT 'question_id',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `survey_id` int(10) unsigned NOT NULL COMMENT 'survey_id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_survey_answer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_survey_question`
-- ----------------------------
DROP TABLE IF EXISTS `wp_survey_question`;
CREATE TABLE `wp_survey_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '问题描述',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `is_must` tinyint(2) DEFAULT '0' COMMENT '是否必填',
  `extra` text COMMENT '参数',
  `type` char(50) NOT NULL DEFAULT 'radio' COMMENT '问题类型',
  `survey_id` int(10) unsigned NOT NULL COMMENT 'survey_id',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_survey_question
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_system_notice`
-- ----------------------------
DROP TABLE IF EXISTS `wp_system_notice`;
CREATE TABLE `wp_system_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '公告标题',
  `content` text COMMENT '公告内容',
  `create_time` int(10) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_system_notice
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_test`
-- ----------------------------
DROP TABLE IF EXISTS `wp_test`;
CREATE TABLE `wp_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '关键词匹配类型',
  `title` varchar(255) NOT NULL COMMENT '问卷标题',
  `intro` text NOT NULL COMMENT '封面简介',
  `mTime` int(10) NOT NULL COMMENT '修改时间',
  `cover` int(10) unsigned NOT NULL COMMENT '封面图片',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `finish_tip` text NOT NULL COMMENT '评论语',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_test
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_test_answer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_test_answer`;
CREATE TABLE `wp_test_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `answer` text NOT NULL COMMENT '回答内容',
  `openid` varchar(255) NOT NULL COMMENT 'OpenId',
  `uid` int(10) NOT NULL COMMENT '用户UID',
  `question_id` int(10) unsigned NOT NULL COMMENT 'question_id',
  `cTime` int(10) unsigned NOT NULL COMMENT '发布时间',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `test_id` int(10) unsigned NOT NULL COMMENT 'test_id',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '得分',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_test_answer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_test_question`
-- ----------------------------
DROP TABLE IF EXISTS `wp_test_question`;
CREATE TABLE `wp_test_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '题目标题',
  `intro` text NOT NULL COMMENT '题目描述',
  `cTime` int(10) unsigned NOT NULL COMMENT '发布时间',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `is_must` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否必填',
  `extra` text NOT NULL COMMENT '参数',
  `type` char(50) NOT NULL DEFAULT 'radio' COMMENT '题目类型',
  `test_id` int(10) unsigned NOT NULL COMMENT 'test_id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_test_question
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_tongji`
-- ----------------------------
DROP TABLE IF EXISTS `wp_tongji`;
CREATE TABLE `wp_tongji` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `token` varchar(255) NOT NULL COMMENT 'Token',
  `month` int(10) NOT NULL COMMENT '月份',
  `day` int(10) NOT NULL COMMENT '日期',
  `content` text NOT NULL COMMENT '统计数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_tongji
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_update_score_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_update_score_log`;
CREATE TABLE `wp_update_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `score` int(10) DEFAULT NULL COMMENT '修改积分',
  `branch_id` int(10) DEFAULT NULL COMMENT '修改门店',
  `operator` varchar(255) DEFAULT NULL COMMENT '操作员',
  `cTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `member_id` int(10) DEFAULT NULL COMMENT '会员卡id',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_update_score_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_update_version`
-- ----------------------------
DROP TABLE IF EXISTS `wp_update_version`;
CREATE TABLE `wp_update_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `version` int(10) unsigned NOT NULL COMMENT '版本号',
  `title` varchar(50) NOT NULL COMMENT '升级包名',
  `description` text COMMENT '描述',
  `create_date` int(10) DEFAULT NULL COMMENT '创建时间',
  `download_count` int(10) unsigned DEFAULT '0' COMMENT '下载统计',
  `package` varchar(255) NOT NULL COMMENT '升级包地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_update_version
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_user`;
CREATE TABLE `wp_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` text COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT '登录密码',
  `truename` varchar(30) DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱地址',
  `sex` tinyint(2) DEFAULT NULL COMMENT '性别',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `city` varchar(30) DEFAULT NULL COMMENT '城市',
  `province` varchar(30) DEFAULT NULL COMMENT '省份',
  `country` varchar(30) DEFAULT NULL COMMENT '国家',
  `language` varchar(20) DEFAULT 'zh-cn' COMMENT '语言',
  `score` int(10) DEFAULT '0' COMMENT '金币值',
  `experience` int(10) DEFAULT '0' COMMENT '经验值',
  `unionid` varchar(50) DEFAULT NULL COMMENT '微信第三方ID',
  `login_count` int(10) DEFAULT '0' COMMENT '登录次数',
  `reg_ip` varchar(30) DEFAULT NULL COMMENT '注册IP',
  `reg_time` int(10) DEFAULT NULL COMMENT '注册时间',
  `last_login_ip` varchar(30) DEFAULT NULL COMMENT '最近登录IP',
  `last_login_time` int(10) DEFAULT NULL COMMENT '最近登录时间',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态',
  `is_init` tinyint(2) DEFAULT '0' COMMENT '初始化状态',
  `is_audit` tinyint(2) DEFAULT '0' COMMENT '审核状态',
  `subscribe_time` int(10) DEFAULT NULL COMMENT '用户关注公众号时间',
  `remark` varchar(100) DEFAULT NULL COMMENT '微信用户备注',
  `groupid` int(10) DEFAULT NULL COMMENT '微信端的分组ID',
  `come_from` tinyint(1) DEFAULT '0' COMMENT '来源',
  `login_name` varchar(100) DEFAULT NULL COMMENT 'login_name',
  `login_password` varchar(255) DEFAULT NULL COMMENT '登录密码',
  `manager_id` int(10) DEFAULT '0' COMMENT '公众号管理员ID',
  `level` tinyint(2) DEFAULT '0' COMMENT '管理等级',
  `membership` char(50) DEFAULT '0' COMMENT '会员等级',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_user_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_user_follow`;
CREATE TABLE `wp_user_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `publicid` int(11) DEFAULT NULL,
  `follow_id` int(11) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_user_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_visit_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_visit_log`;
CREATE TABLE `wp_visit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `publicid` int(10) DEFAULT '0' COMMENT 'publicid',
  `module_name` varchar(30) DEFAULT NULL COMMENT 'module_name',
  `controller_name` varchar(30) DEFAULT NULL COMMENT 'controller_name',
  `action_name` varchar(30) DEFAULT NULL COMMENT 'action_name',
  `uid` varchar(255) DEFAULT '0' COMMENT 'uid',
  `ip` varchar(30) DEFAULT NULL COMMENT 'ip',
  `brower` varchar(30) DEFAULT NULL COMMENT 'brower',
  `param` text COMMENT 'param',
  `referer` varchar(255) DEFAULT NULL COMMENT 'referer',
  `cTime` int(10) DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_visit_log
-- ----------------------------


-- ----------------------------
-- Table structure for `wp_vote`
-- ----------------------------
DROP TABLE IF EXISTS `wp_vote`;
CREATE TABLE `wp_vote` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`keyword`  varchar(50) NOT NULL  COMMENT '关键词',
`title`  varchar(100) NOT NULL  COMMENT '投票标题',
`description`  text NULL  COMMENT '投票描述',
`picurl`  int(10) unsigned NULL   COMMENT '封面图片',
`type`  char(10) NOT NULL  DEFAULT 0 COMMENT '选择类型',
`start_date`  int(10) NULL  COMMENT '开始日期',
`end_date`  int(10) NULL  COMMENT '结束日期',
`is_img`  tinyint(2) NULL  DEFAULT 0 COMMENT '文字/图片投票',
`vote_count`  int(10) unsigned NULL   DEFAULT 0 COMMENT '投票数',
`cTime`  int(10) NULL  COMMENT '投票创建时间',
`mTime`  int(10) NULL  COMMENT '更新时间',
`token`  varchar(255) NULL  COMMENT 'Token',
`template`  varchar(255) NULL  DEFAULT 'default' COMMENT '素材模板',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('vote','投票','0','','1','["keyword","title","description","picurl","start_date","end_date","template"]','1:基础','','','','','id:投票ID\r\nkeyword:关键词\r\ntitle:投票标题\r\ntype|get_name_by_status:类型\r\nis_img|get_name_by_status:状态\r\nvote_count:投票数\r\nids:操作:[EDIT]&id=[id]|编辑,[DELETE]|删除,showLog&id=[id]|投票记录,showCount&id=[id]|选项票数,preview?id=[id]&target=_blank|预览','20','title','description','1388930292','1437446751','1','MyISAM','Vote');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('keyword','关键词','varchar(50) NOT NULL','string','','用户在微信里回复此关键词将会触发此投票。','1','','0','1','1','1392969972','1388930888','keyword_unique','1','此关键词已经存在，请换成别的关键词再试试','function','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','投票标题','varchar(100) NOT NULL','string','','','1','','0','1','1','1388931041','1388931041','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('description','投票描述','text NULL','textarea','','','1','','0','0','1','1400633517','1388931173','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('picurl','封面图片','int(10) unsigned NULL ','picture','','支持JPG、PNG格式，较好的效果为大图360*200，小图200*200','1','','0','0','1','1388931285','1388931285','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','选择类型','char(10) NOT NULL','radio','0','','0','0:单选\r\n1:多选','0','1','1','1430376146','1388931487','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_date','开始日期','int(10) NULL','datetime','','','1','','0','0','1','1388931734','1388931734','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_date','结束日期','int(10) NULL','datetime','','','1','','0','0','1','1388931769','1388931769','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_img','文字/图片投票','tinyint(2) NULL','radio','0','','0','0:文字投票\r\n1:图片投票','0','1','1','1389081985','1388931941','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('vote_count','投票数','int(10) unsigned NULL ','num','0','','0','','0','0','1','1388932035','1388932035','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','投票创建时间','int(10) NULL','datetime','','','0','','0','1','1','1388932128','1388932128','','1','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('mTime','更新时间','int(10) NULL','datetime','','','0','','0','0','1','1430379170','1390634006','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(255) NULL','string','','','0','','0','0','1','1391397388','1391397388','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('template','素材模板','varchar(255) NULL','string','default','','1','','0','0','1','1430188739','1430188739','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

-- ----------------------------
-- Records of wp_vote
-- ----------------------------


-- ----------------------------
-- Table structure for `wp_vote_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_vote_log`;
CREATE TABLE `wp_vote_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`vote_id`  int(10) unsigned NULL   COMMENT '投票ID',
`user_id`  int(10) NULL   COMMENT '用户ID',
`token`  varchar(255) NULL  COMMENT '用户TOKEN',
`options`  varchar(255) NULL  COMMENT '选择选项',
`cTime`  int(10) NULL  COMMENT '创建时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('vote_log','投票记录','0','','1','["vote_id","user_id","options"]','1:基础','','','','','vote_id:25%用户头像\r\nuser_id:25%用户\r\noptions:25%投票选项\r\ncTime|time_format:25%创建时间\r\n\r\n\r\n\r\n','20','','','1388934136','1447743392','1','MyISAM','Vote');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('vote_id','投票ID','int(10) unsigned NULL ','num','','','1','','0','1','1','1429846753','1388934189','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('user_id','用户ID','int(10) NULL ','num','','','1','','0','1','1','1429855665','1388934265','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','用户TOKEN','varchar(255) NULL','string','','','0','','0','1','1','1429855713','1388934296','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('options','选择选项','varchar(255) NULL','string','','','1','','0','1','1','1429855086','1388934351','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','0','','0','0','1','1429874378','1388934392','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

-- ----------------------------
-- Records of wp_vote_log
-- ----------------------------


-- ----------------------------
-- Table structure for `wp_vote_option`
-- ----------------------------
DROP TABLE IF EXISTS `wp_vote_option`;
CREATE TABLE `wp_vote_option` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`order`  int(10) unsigned NULL   DEFAULT 0 COMMENT '选项排序',
`opt_count`  int(10) unsigned NULL   DEFAULT 0 COMMENT '当前选项投票数',
`image`  int(10) unsigned NULL   COMMENT '图片选项',
`name`  varchar(255) NOT NULL  COMMENT '选项标题',
`vote_id`  int(10) unsigned NOT NULL   COMMENT '投票ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('vote_option','投票选项','0','','1','["name","opt_count","order"]','1:基础','','','','','image|get_img_html:选项图片\r\nname:选项标题\r\nopt_count:投票数','20','','','1388933346','1447745055','1','MyISAM','Vote');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('order','选项排序','int(10) unsigned NULL ','num','0','','1','','0','0','1','1388933951','1388933951','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('opt_count','当前选项投票数','int(10) unsigned NULL ','num','0','','1','','0','0','1','1429861248','1388933860','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('image','图片选项','int(10) unsigned NULL ','picture','','','5','','0','0','1','1388984467','1388933679','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','选项标题','varchar(255) NOT NULL','string','','','1','','0','1','1','1388933552','1388933552','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('vote_id','投票ID','int(10) unsigned NOT NULL ','num','','','4','','0','1','1','1388982678','1388933478','','3','','regex','$_REQUEST[\'vote_id\']','3','string');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

-- ----------------------------
-- Records of wp_vote_option
-- ----------------------------


-- ----------------------------
-- Table structure for `wp_weisite_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_category`;
CREATE TABLE `wp_weisite_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) NOT NULL COMMENT '分类标题',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图片',
  `url` varchar(255) DEFAULT NULL COMMENT '外链',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '显示',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  `pid` int(10) DEFAULT '0' COMMENT '一级目录',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_cms`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_cms`;
CREATE TABLE `wp_weisite_cms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT NULL COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '简介',
  `cate_id` int(10) unsigned DEFAULT '0' COMMENT '所属类别',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `content` text COMMENT '内容',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_cms
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_footer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_footer`;
CREATE TABLE `wp_weisite_footer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `url` varchar(255) DEFAULT NULL COMMENT '关联URL',
  `title` varchar(50) NOT NULL COMMENT '菜单名',
  `pid` tinyint(2) DEFAULT '0' COMMENT '一级菜单',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`),
  KEY `token` (`token`,`pid`,`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_footer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_slideshow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_slideshow`;
CREATE TABLE `wp_weisite_slideshow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `img` int(10) unsigned NOT NULL COMMENT '图片',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_slideshow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weixin_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weixin_log`;
CREATE TABLE `wp_weixin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cTime` int(11) DEFAULT NULL,
  `cTime_format` varchar(30) DEFAULT NULL,
  `data` text,
  `data_post` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weixin_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weixin_message`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weixin_message`;
CREATE TABLE `wp_weixin_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ToUserName` varchar(100) DEFAULT NULL COMMENT 'Token',
  `FromUserName` varchar(100) DEFAULT NULL COMMENT 'OpenID',
  `CreateTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `MsgType` varchar(30) DEFAULT NULL COMMENT '消息类型',
  `MsgId` varchar(100) DEFAULT NULL COMMENT '消息ID',
  `Content` text COMMENT '文本消息内容',
  `PicUrl` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `MediaId` varchar(100) DEFAULT NULL COMMENT '多媒体文件ID',
  `Format` varchar(30) DEFAULT NULL COMMENT '语音格式',
  `ThumbMediaId` varchar(30) DEFAULT NULL COMMENT '缩略图的媒体id',
  `Title` varchar(100) DEFAULT NULL COMMENT '消息标题',
  `Description` text COMMENT '消息描述',
  `Url` varchar(255) DEFAULT NULL COMMENT 'Url',
  `collect` tinyint(1) DEFAULT '0' COMMENT '收藏状态',
  `deal` tinyint(1) DEFAULT '0' COMMENT '处理状态',
  `is_read` tinyint(1) DEFAULT '0' COMMENT '是否已读',
  `type` tinyint(1) DEFAULT '0' COMMENT '消息分类',
  `is_material` int(10) DEFAULT '0' COMMENT '设置为文本素材',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weixin_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_wish_card`
-- ----------------------------
DROP TABLE IF EXISTS `wp_wish_card`;
CREATE TABLE `wp_wish_card` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `send_name` varchar(255) DEFAULT NULL COMMENT '发送人',
  `receive_name` varchar(255) DEFAULT NULL COMMENT '接收人',
  `content` text COMMENT '祝福语',
  `create_time` int(10) DEFAULT NULL COMMENT ' 创建时间',
  `template` char(50) DEFAULT NULL COMMENT '模板',
  `template_cate` varchar(255) DEFAULT NULL COMMENT '模板分类',
  `read_count` int(10) DEFAULT '0' COMMENT '浏览次数',
  `mid` varchar(255) DEFAULT NULL COMMENT '用户Id',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_wish_card
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_wish_card_content`
-- ----------------------------
DROP TABLE IF EXISTS `wp_wish_card_content`;
CREATE TABLE `wp_wish_card_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content_cate_id` int(10) DEFAULT '0' COMMENT '祝福语类别Id',
  `content` text COMMENT '祝福语',
  `content_cate` varchar(255) DEFAULT NULL COMMENT '类别',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_wish_card_content
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_wish_card_content_cate`
-- ----------------------------
DROP TABLE IF EXISTS `wp_wish_card_content_cate`;
CREATE TABLE `wp_wish_card_content_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content_cate_name` varchar(255) DEFAULT NULL COMMENT '祝福语类别',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `content_cate_icon` int(10) unsigned DEFAULT NULL COMMENT '类别图标',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_wish_card_content_cate
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_xdlog`
-- ----------------------------
DROP TABLE IF EXISTS `wp_xdlog`;
CREATE TABLE `wp_xdlog` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid_int` int(11) NOT NULL,
  `biztitle` text,
  `biztype` int(11) NOT NULL DEFAULT '0',
  `opttime` bigint(20) DEFAULT NULL,
  `xd` bigint(20) DEFAULT NULL,
  `sceneid` bigint(20) DEFAULT '0',
  `remark` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_xdlog
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_xydzp`
-- ----------------------------
DROP TABLE IF EXISTS `wp_xydzp`;
CREATE TABLE `wp_xydzp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `end_date` int(10) DEFAULT NULL COMMENT '结束日期',
  `cTime` int(10) DEFAULT NULL COMMENT '活动创建时间',
  `states` char(10) DEFAULT '0' COMMENT '活动状态',
  `picurl` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `title` varchar(255) DEFAULT NULL COMMENT '活动标题',
  `guiz` text COMMENT '活动规则',
  `choujnum` int(10) unsigned DEFAULT '0' COMMENT '每日抽奖次数',
  `des` text COMMENT '活动介绍',
  `des_jj` text COMMENT '活动介绍',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `start_date` int(10) DEFAULT NULL COMMENT '开始时间',
  `experience` int(10) DEFAULT '0' COMMENT '消耗经验值',
  `background` int(10) unsigned DEFAULT NULL COMMENT '背景图',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_xydzp
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_xydzp_jplist`
-- ----------------------------
DROP TABLE IF EXISTS `wp_xydzp_jplist`;
CREATE TABLE `wp_xydzp_jplist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `gailv` int(10) unsigned DEFAULT '0' COMMENT '中奖概率',
  `gailv_str` varchar(255) DEFAULT NULL COMMENT '参数',
  `xydzp_id` int(10) unsigned DEFAULT '0' COMMENT '幸运大转盘关联的活动id',
  `jlnum` int(10) unsigned DEFAULT '1' COMMENT '奖励数量',
  `type` char(50) DEFAULT '0' COMMENT '奖品中奖方式',
  `gailv_maxnum` int(10) unsigned DEFAULT '0' COMMENT '单日发放上限',
  `xydzp_option_id` int(10) unsigned DEFAULT NULL COMMENT '幸运大转盘关联的全局奖品id',
  PRIMARY KEY (`id`),
  KEY `xydzp_id` (`xydzp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_xydzp_jplist
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_xydzp_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_xydzp_log`;
CREATE TABLE `wp_xydzp_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` varchar(255) DEFAULT NULL COMMENT '用户openid',
  `message` text COMMENT '留言',
  `address` text COMMENT '收件地址',
  `iphone` varchar(255) DEFAULT NULL COMMENT '电话',
  `zip` int(10) unsigned DEFAULT NULL COMMENT '邮编',
  `state` tinyint(2) DEFAULT '0' COMMENT '领奖状态',
  `xydzp_option_id` int(10) unsigned DEFAULT '0' COMMENT '奖品id',
  `xydzp_id` int(10) unsigned DEFAULT '0' COMMENT '活动id',
  `zjdate` int(10) unsigned DEFAULT NULL COMMENT '中奖时间',
  PRIMARY KEY (`id`),
  KEY `xydzp_id` (`uid`,`xydzp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_xydzp_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_xydzp_option`
-- ----------------------------
DROP TABLE IF EXISTS `wp_xydzp_option`;
CREATE TABLE `wp_xydzp_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `jptype` char(10) DEFAULT '0' COMMENT '奖品类型',
  `duijma` text COMMENT '兑奖码',
  `title` varchar(255) DEFAULT NULL COMMENT '奖品名称',
  `pic` int(10) unsigned DEFAULT NULL COMMENT '奖品图片',
  `miaoshu` text COMMENT '奖品描述',
  `num` int(10) unsigned DEFAULT '0' COMMENT '库存数量',
  `isdf` tinyint(2) DEFAULT '0' COMMENT '是否为谢谢惠顾类',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `coupon_id` int(10) DEFAULT NULL COMMENT '优惠券编号',
  `experience` int(10) DEFAULT '0' COMMENT '奖励经验值',
  `card_url` varchar(255) DEFAULT NULL COMMENT '领取卡券的地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_xydzp_option
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_xydzp_userlog`
-- ----------------------------
DROP TABLE IF EXISTS `wp_xydzp_userlog`;
CREATE TABLE `wp_xydzp_userlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` varchar(255) DEFAULT NULL COMMENT '用户id',
  `xydzp_id` int(10) unsigned DEFAULT NULL COMMENT '幸运大转盘关联的活动id',
  `num` int(10) unsigned DEFAULT '0' COMMENT '已经抽奖的次数',
  `cjdate` int(10) DEFAULT NULL COMMENT '抽奖日期',
  PRIMARY KEY (`id`),
  KEY `xydzp_id` (`uid`,`xydzp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_xydzp_userlog
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_behavior`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_behavior`;
CREATE TABLE `wp_youaskservice_behavior` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `fid` int(11) DEFAULT NULL,
  `token` varchar(60) DEFAULT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `date` varchar(11) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  `model` varchar(60) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `keyword` varchar(60) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_behavior
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_group`;
CREATE TABLE `wp_youaskservice_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `groupname` varchar(255) DEFAULT NULL COMMENT '分组名称',
  `groupdata` text COMMENT '分组数据源',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_group
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_keyword`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_keyword`;
CREATE TABLE `wp_youaskservice_keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `msgkeyword` varchar(555) DEFAULT NULL COMMENT '消息关键字',
  `msgkeyword_type` char(50) DEFAULT '3' COMMENT '关键字类型',
  `msgkfaccount` varchar(255) DEFAULT NULL COMMENT '接待的客服人员',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `msgstate` tinyint(2) DEFAULT '1' COMMENT '关键字状态',
  `zjnum` int(10) DEFAULT NULL COMMENT '转接次数',
  `zdtype` char(10) DEFAULT '0' COMMENT '指定类型',
  `kfgroupid` int(10) DEFAULT '0' COMMENT '客服分组id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_keyword
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_logs`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_logs`;
CREATE TABLE `wp_youaskservice_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(11) DEFAULT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  `keyword` varchar(200) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_user`;
CREATE TABLE `wp_youaskservice_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(60) DEFAULT NULL COMMENT '客服昵称',
  `token` varchar(60) DEFAULT NULL COMMENT 'token',
  `userName` varchar(60) DEFAULT NULL COMMENT '客服帐号',
  `userPwd` varchar(32) DEFAULT NULL COMMENT '客服密码',
  `endJoinDate` int(11) DEFAULT NULL COMMENT '客服加入时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '客服在线状态',
  `state` tinyint(2) DEFAULT '0' COMMENT '客服状态',
  `isdelete` tinyint(2) DEFAULT '0' COMMENT '是否删除',
  `kfid` varchar(255) DEFAULT NULL COMMENT '客服编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_wechat_enddate`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_wechat_enddate`;
CREATE TABLE `wp_youaskservice_wechat_enddate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `openid` varchar(60) DEFAULT NULL,
  `enddate` int(11) DEFAULT NULL,
  `joinUpDate` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `token` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_wechat_enddate
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_wechat_grouplist`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_wechat_grouplist`;
CREATE TABLE `wp_youaskservice_wechat_grouplist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `g_id` varchar(20) DEFAULT NULL,
  `nickname` varchar(60) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `province` varchar(20) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `headimgurl` varchar(200) DEFAULT NULL,
  `subscribe_time` int(11) DEFAULT NULL,
  `token` varchar(30) DEFAULT NULL,
  `openid` varchar(60) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_wechat_grouplist
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_youaskservice_wxlogs`
-- ----------------------------
DROP TABLE IF EXISTS `wp_youaskservice_wxlogs`;
CREATE TABLE `wp_youaskservice_wxlogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `opercode` int(10) DEFAULT NULL COMMENT '会话状态',
  `text` text COMMENT '消息',
  `time` int(10) DEFAULT NULL COMMENT '时间',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `worker` varchar(255) DEFAULT NULL COMMENT '客服名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_youaskservice_wxlogs
-- ----------------------------
