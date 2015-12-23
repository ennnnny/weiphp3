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
		'SESSION_PREFIX' => SITE_DIR_NAME . '_admin', // session前缀
		'COOKIE_PREFIX' => SITE_DIR_NAME . '_admin_', // Cookie前缀 避免冲突
		
		/* 后台错误页面模板 */
		'TMPL_ACTION_ERROR' => MODULE_PATH . 'View/Public/error.html', // 默认错误跳转对应的模板文件
		'TMPL_ACTION_SUCCESS' => MODULE_PATH . 'View/Public/success.html', // 默认成功跳转对应的模板文件
		'TMPL_EXCEPTION_FILE' => MODULE_PATH . 'View/Public/exception.html',
		
		// 模板相关配置
		'TMPL_PARSE_STRING' => array (
				'__STATIC__' => __ROOT__ . '/Public/static',
				'__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
				'__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
				'__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
				'__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js' 
		) 
); 


