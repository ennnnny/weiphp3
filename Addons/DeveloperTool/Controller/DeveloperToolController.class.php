<?php

namespace Addons\DeveloperTool\Controller;

use Home\Controller\AddonsController;

class DeveloperToolController extends AddonsController {
	function _initialize() {
		$act = strtolower ( _ACTION );
		
		$res ['title'] = '公众号信息';
		$res ['url'] = addons_url ( 'DeveloperTool://DeveloperTool/lists' );
		$res ['class'] = $act == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '管理员信息';
		$res ['url'] = addons_url ( 'DeveloperTool://DeveloperTool/manager' );
		$res ['class'] = $act == 'manager' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '常用功能测试';
		$res ['url'] = addons_url ( 'DeveloperTool://DeveloperTool/test' );
		$res ['class'] = $act == 'test' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '系统配置信息';
		$res ['url'] = addons_url ( 'DeveloperTool://DeveloperTool/config' );
		$res ['class'] = $act == 'config' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '系统常量信息';
		$res ['url'] = addons_url ( 'DeveloperTool://DeveloperTool/define' );
		$res ['class'] = $act == 'define' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = 'PHP配置信息';
		$res ['url'] = addons_url ( 'DeveloperTool://DeveloperTool/init' );
		$res ['class'] = $act == 'init' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$token = get_token ();
		$info = get_token_appinfo ();
		$info ['addon_config'] = json_decode ( $info ['addon_config'], true );
		
		$this->assign ( 'info', $info );
		$this->assign ( 'token', $token );
		// dump ( $info );
		$this->display ();
	}
	function manager() {
		$info = getUserInfo ( $this->mid );
		$this->assign ( 'info', $info );
		
		$this->display ();
	}
	function config() {
		$this->display ();
	}
	function define() {
		$list = array (
				'APP_DEBUG' => '是否开启调试模式',
				'SHOW_ERROR' => '是否显示真实错误',
				'IN_WEIXIN' => '是否必须在微信里打开',
				'DEFAULT_TOKEN' => '默认的公众号原始ID',
				'REMOTE_BASE_URL' => '官方远程同步服务器地址',
				'SITE_PATH' => '网站目录',
				'SITE_DOMAIN' => '网站域名',
				'SITE_URL' => '网站地址',
				'SITE_DIR_NAME' => '网站数据前缀',
				
				'URL_PATHINFO' => 'PATHINFO URL （1）',
				'URL_REWRITE' => 'REWRITE URL （2）',
				'URL_COMPAT' => '兼容模式 URL （3）',
				'EXT' => '类库文件后缀（.class.php）',
				'THINK_VERSION' => '框架版本号',
				
				'THINK_PATH' => '框架系统目录',
				'APP_PATH' => '应用目录（默认为入口文件所在目录）',
				'LIB_PATH' => '系统类库目录',
				'CORE_PATH' => '系统核心类库目录 ',
				'MODE_PATH' => '系统应用模式目录',
				'BEHAVIOR_PATH' => '行为目录',
				'COMMON_PATH' => '公共模块目录',
				'VENDOR_PATH' => '第三方类库目录',
				'RUNTIME_PATH' => '应用运行时目录',
				'HTML_PATH' => '应用静态缓存目录',
				'CONF_PATH' => '应用公共配置目录',
				'LANG_PATH' => '公共语言包目录',
				'LOG_PATH' => '应用日志目录 ',
				'CACHE_PATH' => '项目模板缓存目录',
				'TEMP_PATH' => '应用缓存目录',
				'DATA_PATH' => '应用数据目录',
				'ADDON_PATH' => '插件控制器目录',
				'IS_CGI' => '是否属于 CGI模式',
				'IS_WIN' => '是否属于Windows 环境',
				'IS_CLI' => '是否属于命令行模式',
				'__ROOT__' => '网站根目录地址',
				'__APP__' => '当前应用（入口文件）地址',
				'__MODULE__' => '当前模块的URL地址',
				'__CONTROLLER__' => '当前控制器的URL地址',
				'__ACTION__' => '当前操作的URL地址',
				'__SELF__' => '当前URL地址',
				'__INFO__' => '当前的PATH_INFO字符串',
				'__EXT__' => '当前URL地址的扩展名',
				'MODULE_NAME' => '当前模块名',
				'MODULE_PATH' => '当前模块路径',
				'CONTROLLER_NAME' => '当前控制器名',
				'CONTROLLER_PATH' => '当前控制器路径',
				'ACTION_NAME' => '当前操作名',
				'APP_MODE' => '当前应用模式名称',
				'APP_STATUS' => '当前应用状态',
				'STORAGE_TYPE' => ' 当前存储类型',
				'MODULE_PATHINFO_DEPR' => '模块的PATHINFO分割符',
				'MEMORY_LIMIT_ON' => '系统内存统计支持',
				'RUNTIME_FILE' => '项目编译缓存文件名',
				'THEME_NAME' => '当前主题名称',
				'THEME_PATH' => '当前模板主题路径',
				'LANG_SET' => '当前浏览器语言',
				'MAGIC_QUOTES_GPC' => 'MAGIC_QUOTES_GPC',
				'NOW_TIME' => '当前请求时间（时间戳）',
				'BIND_MODULE' => '当前绑定的模块',
				'BIND_CONTROLLER' => '当前绑定的控制器',
				'BIND_ACTION' => '当前绑定的操作',
				'CONF_EXT' => '配置文件后缀',
				'CONF_PARSE' => '配置文件解析方法',
				'TMPL_PATH' => '用于改变全局视图目录' 
		);
		
		$this->assign ( 'list', $list );
		
		$this->display ();
	}
	function init() {
		phpinfo ();
		// $this->display ();
	}
	function test() {
		$this->display ();
	}
}
