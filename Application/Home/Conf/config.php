<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.thinkphp.cn>
// +----------------------------------------------------------------------

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array (
		// 主题设置
		'DEFAULT_THEME' => 'default', // 默认模板主题名称
		                              
		// 预先加载的标签库
		                              // 'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Article,OT\\TagLib\\Think',
		'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Think',
		
		// SESSION 和 COOKIE 配置
		'SESSION_PREFIX' => SITE_DIR_NAME . '_home', // session前缀
		'COOKIE_PREFIX' => SITE_DIR_NAME . '_home',
		
		// 模板相关配置
		'TMPL_PARSE_STRING' => array (
				'__STATIC__' => __ROOT__ . '/Public/static',
				'__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
				'__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
				'__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
				'__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js' 
		) 
); 


