<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('Your PHP Version is '.PHP_VERSION.', but WeiPHP require PHP > 5.3.0 !');

$_GET['m'] = 'Install';

/**
 * 此处APP_DEBUG一定要设置为true，否则安装后会生成错误的缓存文件
 */
define ( 'APP_DEBUG', true );
define ( 'BIND_MODULE','Install');

// 网站根路径设置
define ( 'SITE_PATH', dirname ( __FILE__ ) );

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Application/' );

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';