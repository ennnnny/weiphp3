<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;

/**
 * ThinkPHP 控制器基类 抽象类
 */
abstract class Controller {
	
	/**
	 * 视图实例对象
	 *
	 * @var view
	 * @access protected
	 */
	protected $view = null;
	protected $mid = 0;
	protected $uid = 0;
	protected $user = array ();
	protected $top_more_button = array ();
	protected $get_param = array ();
	
	/**
	 * 控制器参数
	 *
	 * @var config
	 * @access protected
	 */
	protected $config = array ();
	
	/**
	 * 架构函数 取得模板对象实例
	 *
	 * @access public
	 */
	public function __construct() {
		Hook::listen ( 'action_begin', $this->config );
		// 实例化视图类
		$this->view = Think::instance ( 'Think\View' );
		
		if (strtolower ( MODULE_NAME ) != 'install') {
			$this->initSite ();
			
			$index_3 = strtolower ( MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME );
			if ($index_3 != 'home/weixin/index') { // 微信客户端请求的用户初始化在home/weixin/index里实现，这里不作处理
				$info = $this->initPublic ();
				$this->initUser ();
				
				$GLOBALS ['is_wap'] ? $this->initMoblie ( $info ) : $this->initWeb ( $info );
				
				// 权限验证，原则：开放一切未禁止的页面
				if ($this->mid > 0 && ! checkRule ( '', $this->mid )) {
					$this->error ( '您无权访问！' );
				}
			}
		}
		
		// 控制器初始化
		if (method_exists ( $this, '_initialize' ))
			$this->_initialize ();
	}
	
	/**
	 * 模板显示 调用内置的模板引擎显示方法，
	 *
	 * @access protected
	 * @param string $templateFile
	 *        	指定要调用的模板文件
	 *        	默认为空 由系统自动定位模板文件
	 * @param string $charset
	 *        	输出编码
	 * @param string $contentType
	 *        	输出类型
	 * @param string $content
	 *        	输出内容
	 * @param string $prefix
	 *        	模板缓存前缀
	 * @return void
	 */
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$this->view->display ( $templateFile, $charset, $contentType, $content, $prefix );
	}
	
	/**
	 * 输出内容文本可以包括Html 并支持内容解析
	 *
	 * @access protected
	 * @param string $content
	 *        	输出内容
	 * @param string $charset
	 *        	模板输出字符集
	 * @param string $contentType
	 *        	输出类型
	 * @param string $prefix
	 *        	模板缓存前缀
	 * @return mixed
	 */
	protected function show($content, $charset = '', $contentType = '', $prefix = '') {
		$this->view->display ( '', $charset, $contentType, $content, $prefix );
	}
	
	/**
	 * 获取输出页面内容
	 * 调用内置的模板引擎fetch方法，
	 *
	 * @access protected
	 * @param string $templateFile
	 *        	指定要调用的模板文件
	 *        	默认为空 由系统自动定位模板文件
	 * @param string $content
	 *        	模板输出内容
	 * @param string $prefix
	 *        	模板缓存前缀*
	 * @return string
	 */
	protected function fetch($templateFile = '', $content = '', $prefix = '') {
		return $this->view->fetch ( $templateFile, $content, $prefix );
	}
	
	/**
	 * 创建静态页面
	 *
	 * @access protected
	 *         @htmlfile 生成的静态文件名称
	 *         @htmlpath 生成的静态文件路径
	 * @param string $templateFile
	 *        	指定要调用的模板文件
	 *        	默认为空 由系统自动定位模板文件
	 * @return string
	 */
	protected function buildHtml($htmlfile = '', $htmlpath = '', $templateFile = '') {
		$content = $this->fetch ( $templateFile );
		$htmlpath = ! empty ( $htmlpath ) ? $htmlpath : HTML_PATH;
		$htmlfile = $htmlpath . $htmlfile . C ( 'HTML_FILE_SUFFIX' );
		Storage::put ( $htmlfile, $content, 'html' );
		return $content;
	}
	
	/**
	 * 模板主题设置
	 *
	 * @access protected
	 * @param string $theme
	 *        	模版主题
	 * @return Action
	 */
	protected function theme($theme) {
		$this->view->theme ( $theme );
		return $this;
	}
	
	/**
	 * 模板变量赋值
	 *
	 * @access protected
	 * @param mixed $name
	 *        	要显示的模板变量
	 * @param mixed $value
	 *        	变量的值
	 * @return Action
	 */
	protected function assign($name, $value = '') {
		$this->view->assign ( $name, $value );
		return $this;
	}
	public function __set($name, $value) {
		$this->assign ( $name, $value );
	}
	
	/**
	 * 取得模板显示变量的值
	 *
	 * @access protected
	 * @param string $name
	 *        	模板显示变量
	 * @return mixed
	 */
	public function get($name = '') {
		return $this->view->get ( $name );
	}
	public function __get($name) {
		return $this->get ( $name );
	}
	
	/**
	 * 检测模板变量的值
	 *
	 * @access public
	 * @param string $name
	 *        	名称
	 * @return boolean
	 */
	public function __isset($name) {
		return $this->get ( $name );
	}
	
	/**
	 * 魔术方法 有不存在的操作的时候执行
	 *
	 * @access public
	 * @param string $method
	 *        	方法名
	 * @param array $args
	 *        	参数
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (0 === strcasecmp ( $method, ACTION_NAME . C ( 'ACTION_SUFFIX' ) )) {
			if (method_exists ( $this, '_empty' )) {
				// 如果定义了_empty操作 则调用
				$this->_empty ( $method, $args );
			} elseif (file_exists_case ( $this->view->parseTemplate () )) {
				// 检查是否存在默认模版 如果有直接输出模版
				$this->display ();
			} else {
				E ( L ( '_ERROR_ACTION_' ) . ':' . ACTION_NAME );
			}
		} else {
			E ( __CLASS__ . ':' . $method . L ( '_METHOD_NOT_EXIST_' ) );
			return;
		}
	}
	
	/**
	 * 操作错误跳转的快捷方法
	 *
	 * @access protected
	 * @param string $message
	 *        	错误信息
	 * @param string $jumpUrl
	 *        	页面跳转地址
	 * @param mixed $ajax
	 *        	是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	protected function error($message = '', $jumpUrl = '', $ajax = false) {
		$this->dispatchJump ( $message, 0, $jumpUrl, $ajax );
	}
	
	/**
	 * 操作成功跳转的快捷方法
	 *
	 * @access protected
	 * @param string $message
	 *        	提示信息
	 * @param string $jumpUrl
	 *        	页面跳转地址
	 * @param mixed $ajax
	 *        	是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	protected function success($message = '', $jumpUrl = '', $ajax = false) {
		$this->dispatchJump ( $message, 1, $jumpUrl, $ajax );
	}
	
	/**
	 * Ajax方式返回数据到客户端
	 *
	 * @access protected
	 * @param mixed $data
	 *        	要返回的数据
	 * @param String $type
	 *        	AJAX返回数据格式
	 * @param int $json_option
	 *        	传递给json_encode的option参数
	 * @return void
	 */
	protected function ajaxReturn($data, $type = '', $json_option = 0) {
		if (empty ( $type ))
			$type = C ( 'DEFAULT_AJAX_RETURN' );
		switch (strtoupper ( $type )) {
			case 'JSON' :
				
				// 返回JSON数据格式到客户端 包含状态信息
				header ( 'Content-Type:application/json; charset=utf-8' );
				exit ( json_encode ( $data, $json_option ) );
			case 'XML' :
				
				// 返回xml格式数据
				header ( 'Content-Type:text/xml; charset=utf-8' );
				exit ( xml_encode ( $data ) );
			case 'JSONP' :
				
				// 返回JSON数据格式到客户端 包含状态信息
				header ( 'Content-Type:application/json; charset=utf-8' );
				$handler = isset ( $_GET [C ( 'VAR_JSONP_HANDLER' )] ) ? $_GET [C ( 'VAR_JSONP_HANDLER' )] : C ( 'DEFAULT_JSONP_HANDLER' );
				exit ( $handler . '(' . json_encode ( $data, $json_option ) . ');' );
			case 'EVAL' :
				
				// 返回可执行的js脚本
				header ( 'Content-Type:text/html; charset=utf-8' );
				exit ( $data );
			default :
				
				// 用于扩展其他返回格式数据
				Hook::listen ( 'ajax_return', $data );
		}
	}
	
	/**
	 * Action跳转(URL重定向） 支持指定模块和延时跳转
	 *
	 * @access protected
	 * @param string $url
	 *        	跳转的URL表达式
	 * @param array $params
	 *        	其它URL参数
	 * @param integer $delay
	 *        	延时跳转的时间 单位为秒
	 * @param string $msg
	 *        	跳转提示信息
	 * @return void
	 */
	protected function redirect($url, $params = array(), $delay = 0, $msg = '') {
		$url = U ( $url, $params );
		redirect ( $url, $delay, $msg );
	}
	
	/**
	 * 默认跳转操作 支持错误导向和正确跳转
	 * 调用模板显示 默认为public目录下面的success页面
	 * 提示页面为可配置 支持模板标签
	 *
	 * @param string $message
	 *        	提示信息
	 * @param Boolean $status
	 *        	状态
	 * @param string $jumpUrl
	 *        	页面跳转地址
	 * @param mixed $ajax
	 *        	是否为Ajax方式 当数字时指定跳转时间
	 * @access private
	 * @return void
	 */
	private function dispatchJump($message, $status = 1, $jumpUrl = '', $ajax = false) {
		if (true === $ajax || IS_AJAX) { // AJAX提交
			$data = is_array ( $ajax ) ? $ajax : array ();
			$data ['info'] = $message;
			$data ['status'] = $status;
			$data ['url'] = $jumpUrl;
			$this->ajaxReturn ( $data );
		}
		if (is_int ( $ajax ))
			$this->assign ( 'waitSecond', $ajax );
		if (! empty ( $jumpUrl ))
			$this->assign ( 'jumpUrl', $jumpUrl );
			// 提示标题
		$this->assign ( 'msgTitle', $status ? L ( '_OPERATION_SUCCESS_' ) : L ( '_OPERATION_FAIL_' ) );
		// 如果设置了关闭窗口，则提示完毕后自动关闭窗口
		if ($this->get ( 'closeWin' ))
			$this->assign ( 'jumpUrl', 'javascript:window.close();' );
		$this->assign ( 'status', $status ); // 状态
		                                     // 保证输出不受静态缓存影响
		C ( 'HTML_CACHE_ON', false );
		if ($status) { // 发送成功信息
			$this->assign ( 'message', $message ); // 提示信息
			                                       // 成功操作后默认停留1秒
			if (! isset ( $this->waitSecond ))
				$this->assign ( 'waitSecond', '1' );
				// 默认操作成功自动返回操作前页面
			if (! isset ( $this->jumpUrl ))
				$this->assign ( "jumpUrl", $_SERVER ["HTTP_REFERER"] );
			$this->display ( C ( 'TMPL_ACTION_SUCCESS' ) );
		} else {
			$this->assign ( 'error', $message ); // 提示信息
			                                     // 发生错误时候默认停留3秒
			if (! isset ( $this->waitSecond ))
				$this->assign ( 'waitSecond', '3' );
				// 默认发生错误的话自动返回上页
			if (! isset ( $this->jumpUrl ))
				$this->assign ( 'jumpUrl', "javascript:history.back(-1);" );
			$this->display ( C ( 'TMPL_ACTION_ERROR' ) );
			// 中止执行 避免出错后继续执行
			exit ();
		}
	}
	
	/**
	 * 析构方法
	 *
	 * @access public
	 */
	public function __destruct() {
		// 执行后续操作
		Hook::listen ( 'action_end' );
	}
	/**
	 * 应用信息初始化
	 *
	 * @access private
	 * @return void
	 */
	private function initSite() {
		/* 读取数据库中的配置 */
		$config = S ( 'DB_CONFIG_DATA' );
		if (! $config) {
			$config = api ( 'Config/lists' );
			S ( 'DB_CONFIG_DATA', $config );
		}
		C ( $config ); // 添加全站配置
		
		if (! C ( 'WEB_SITE_CLOSE' ) && strtolower ( MODULE_NAME ) != 'admin') {
			$this->error ( '站点已经关闭，请稍后访问~' );
		}
		
		$diff = array (
				'_addons' => 1,
				'_controller' => 1,
				'_action' => 1,
				'm' => 1,
				'id' => 1 
		);
		
		$GLOBALS ['get_param'] = $this->get_param = array_diff_key ( $_GET, $diff );
		$this->assign ( 'get_param', $this->get_param );
		
		// js,css的版本
		if (APP_DEBUG) {
			defined ( 'SITE_VERSION' ) or define ( 'SITE_VERSION', time () );
		} else {
			defined ( 'SITE_VERSION' ) or define ( 'SITE_VERSION', C ( 'SYSTEM_UPDATRE_VERSION' ) );
		}
	}
	// 初始化公众号信息
	private function initPublic() {
		$token = get_token ();
		if (! $token || $token == - 1)
			return false;
		
		$info = get_token_appinfo ( $token );
		if (! $info)
			return false;
			
			// 设置公众号管理者信息
		if ($info ['uid']) {
			$manager_id = $info ['uid'];
			session ( 'manager_id', $manager_id );
		}
		$manager = get_userinfo ( $manager_id );
		
		// 设置版权信息
		$this->assign ( 'system_copy_right', empty ( $manager ['copy_right'] ) ? C ( 'COPYRIGHT' ) : $manager ['copy_right'] );
		$tongji_code = empty ( $manager ['tongji_code'] ) ? C ( 'TONGJI_CODE' ) : $manager ['tongji_code'];
		
		$param = $_GET;
		$param ['publicid'] = $info ['id'];
		$param ['m'] = MODULE_NAME;
		$param ['c'] = CONTROLLER_NAME;
		$param ['a'] = ACTION_NAME;
		$param ['uid'] = intval ( session ( 'mid' ) );
		$tongji_code .= '<script>$.post("' . SITE_URL . '/log.php?' . http_build_query ( $param ) . '");</script>';
		$this->assign ( 'tongji_code', $tongji_code );
		
		// 公众号接口权限
		$config = S ( 'PUBLIC_AUTH_' . $info ['type'] );
		if (! $config) {
			$config = M ( 'public_auth' )->getField ( 'name,type_' . intval ( $info ['type'] ) . ' as val' );
			
			S ( 'PUBLIC_AUTH_' . $info ['type'], $config, 86400 );
		}
		C ( $config ); // 公众号接口权限
		               
		// 初始化微信JSAPI需要的参数
		Vendor ( 'jssdk.jssdk' );
		$jssdk = new \JSSDK ( $info ['appid'], $info ['secret'] );
		$jsapiParams = $jssdk->GetSignPackage ();
		$this->assign ( 'jsapiParams', $jsapiParams );
		
		$this->assign ( 'page_title', $info ['public_name'] ); // 用公众号名作为默认的页面标题
		$this->assign ( 'public_info', $info ); // 通用公众号信息
		
		return $info;
	}
	// 初始化用户信息
	private function initUser() {
		if (isset ( $_GET ['is_stree'] )) {
			$suid = $user ['uid'] = rand ( 1, 10000 );
		} else {
			$uid = session ( 'mid' );
		}
		if ((! $uid || $uid < 0) && $GLOBALS ['is_wap']) {
			$uid = get_uid_by_openid ();
			$uid > 0 && session ( 'mid', $uid );
		}
		
		if (! $uid) {
			$youke_uid = M ( 'config' )->where ( 'name="FOLLOW_YOUKE_UID"' )->getField ( 'value' ) - 1;
			$user ['uid'] = $youke_uid;
			M ( 'config' )->where ( 'name="FOLLOW_YOUKE_UID"' )->setField ( 'value', $youke_uid );
			session ( 'mid', $youke_uid );
		}
		// 当前登录者
		$GLOBALS ['mid'] = $this->mid = intval ( $uid );
		$myinfo = get_userinfo ( $this->mid );
		$GLOBALS ['myinfo'] = $myinfo;
		
		// 当前访问对象的uid
		$GLOBALS ['uid'] = $this->uid = intval ( $_REQUEST ['uid'] == 0 ? $this->mid : $_REQUEST ['uid'] );
		
		$this->assign ( 'mid', $this->mid ); // 登录者
		$this->assign ( 'uid', $this->uid ); // 访问对象
		$this->assign ( 'myinfo', $GLOBALS ['myinfo'] ); // 访问对象
	}
	/**
	 * 系统管理员信息初始化
	 *
	 * @access private
	 * @return void
	 */
	private function initWeb($info) {
		if (! IS_GET) {
			return false;
		}
		// 通用表单的控制开关
		$this->assign ( 'add_button', checkRule ( '__MODULE__/__CONTROLLER__/add', $this->mid ) );
		$this->assign ( 'del_button', checkRule ( '__MODULE__/__CONTROLLER__/del', $this->mid ) );
		$this->assign ( 'search_button', checkRule ( '__MODULE__/__CONTROLLER__/search', $this->mid ) );
		$this->assign ( 'check_all', checkRule ( '__MODULE__/__CONTROLLER__/del', $this->mid ) );
		$this->assign ( 'top_more_button', $this->top_more_button );
		
		$index_1 = strtolower ( MODULE_NAME . '/*/*' );
		$index_2 = strtolower ( MODULE_NAME . '/' . CONTROLLER_NAME . '/*' );
		$index_3 = strtolower ( MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME );
		
		// 当前用户信息
		$access = array_map ( 'trim', explode ( "\n", C ( 'access' ) ) );
		$access = array_map ( 'strtolower', $access );
		$access = array_flip ( $access );
		$guest_login = isset ( $access [$index_1] ) || isset ( $access [$index_2] ) || isset ( $access [$index_3] ) || $index_1 == 'admin/*/*' || $index_3 == 'home/addons/execute' || $index_2 == 'home/user/*';
		
		if (IS_GET && ! is_login () && ! $guest_login) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			redirect ( U ( 'home/user/login', array (
					'from' => 6 
			) ) );
		}
		
		// 判断公众号是否初始化、审核，跳转到对应的页面
		if (IS_GET && ! $guest_login && $index_2 != 'home/public/*' && $index_2 != 'home/publiclink/*') {
			if (! $info) {
				redirect ( U ( 'home/Public/lists', array (
						'form' => 1 
				) ) );
			}
			if ($GLOBALS ['myinfo'] ['is_init'] == 0) {
				redirect ( U ( 'home/Public/add', array (
						'from' => 1 
				) ) );
			} else if ($GLOBALS ['myinfo'] ['is_audit'] == 0 && ! C ( 'REG_AUDIT' )) {
				redirect ( U ( 'home/Public/waitAudit' ) );
			}
		}
		
		// 在开启泛域名下必须先配置自定义域名才能用
		if (C ( 'DIV_DOMAIN' ) && is_login () && $index_2 != 'home/public/*' && ! (defined ( '_ADDONS' ) && _ADDONS == 'PublicBind') && (empty ( $info ['domain'] ) || ($GLOBALS ['mid'] != 1 && $info ['domain'] == 'demo'))) {
			redirect ( U ( 'home/Public/step_0', array (
					'from' => 1 
			) ) );
		}
		
		/* 管理中心的导航 */
		$menus = D ( 'Common/ManagerMenu' )->get ( $this->mid );
		$this->assign ( $menus );
		
		$this->assign ( 'reg_audit_switch', C ( 'REG_AUDIT' ) );
		
		// 公众号下拉切换
		$myPublics = D ( 'Common/Public' )->getMyPublics ( $this->mid );
		if ($info) {
			unset ( $myPublics [$info ['id']] );
		}
		$this->assign ( 'myPublics', $myPublics );
	}
	
	// 公众号粉丝信息初始化
	private function initMoblie($info) {
		// 绑定配置
		$config = getAddonConfig ( 'UserCenter' );
		$userNeed = ($GLOBALS ['myinfo'] ['uid'] > 0 && $GLOBALS ['myinfo'] ['status'] < 2) || empty ( $GLOBALS ['myinfo'] );
		$is_need_bind = $config ['need_bind'] == 1 && $config ['bind_start'] != 1 && strtolower ( $_REQUEST ['_addons'] ) != 'usercenter';
		if (IS_GET && ! (defined ( 'IN_WEIXIN' ) && IN_WEIXIN) && $is_need_bind && $userNeed && C ( 'USER_OAUTH' )) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			redirect ( addons_url ( 'UserCenter://Wap/bind' ) );
		}
	}
	
	// ***************************通用的模型数据操作 begin 凡星********************************/
	public function getModel($model = null) {
		$model || $model = $_REQUEST ['_addons'];
		$model || $model = $_REQUEST ['model'];
		$model || $this->error ( '模型名标识必须！' );
		if (is_numeric ( $model )) {
			$model = M ( 'model' )->find ( $model );
		} else {
			$model = getModelByName ( $model );
		}
		
		$this->assign ( 'model', $model );
		return $model;
	}
	
	/**
	 * 显示指定模型列表数据
	 *
	 * @param String $model
	 *        	模型标识
	 * @author 凡星
	 */
	public function common_lists($model = null, $page = 0, $templateFile = '', $order = 'id desc') {
		// 获取模型信息
		is_array ( $model ) || $model = $this->getModel ( $model );
		
		$list_data = $this->_get_model_list ( $model, $page, $order );
		$this->assign ( $list_data );
		// dump($list_data);
		
		$templateFile || $templateFile = $model ['template_list'] ? $model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	public function common_export($model = null, $order = 'id desc', $return = false) {
		set_time_limit ( 0 );
		// 获取模型信息
		is_array ( $model ) || $model = $this->getModel ( $model );
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		
		foreach ( $grids as $v ) {
			if ($v ['title'] == '操作') {
				array_pop ( $grids );
			} else {
				$arr = explode ( '|', $v ['field'] );
				if (count ( $arr ) > 1) {
					$fun [$arr [0]] = $arr [1];
				}
				
				$ht [$arr [0]] = $v ['title'];
			}
		}
		$dataArr [0] = $ht;
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( $order )->select ();
		if ($data) {
			foreach ( $data as &$vo ) {
				foreach ( $vo as $name => &$vv ) {
					if (isset ( $fun [$name] )) {
						if ($fun [$name] == 'get_name_by_status') {
							$vv = get_name_by_status ( $vv, $name, $model ['id'] );
						} else {
							$vv = call_user_func ( $fun [$name], $vv );
						}
					}
				}
				
				foreach ( $ht as $key => $val ) {
					$newArr [$key] = empty ( $vo [$key] ) ? ' ' : $vo [$key];
				}
				$vo = $newArr;
			}
			
			$dataArr = array_merge ( $dataArr, $data );
		}
		
		if ($return)
			return $dataArr;
		else
			outExcel ( $dataArr, $map ['module'] );
	}
	public function common_del($model = null, $ids = null) {
		is_array ( $model ) || $model = $this->getModel ( $model );
		
		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );
		
		$Model = M ( get_table_name ( $model ['id'] ) );
		$map ['id'] = array (
				'in',
				$ids 
		);
		
		// 插件里的操作自动加上Token限制
		$token = get_token ();
		if (defined ( 'ADDON_PUBLIC_PATH' ) && ! empty ( $token )) {
			$map ['token'] = $token;
		}
		
		if ($Model->where ( $map )->delete ()) {
			// 清空缓存
			method_exists ( $Model, 'clear' ) && $Model->clear ( $ids, 'del' );
			
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	public function common_edit($model = null, $id = 0, $templateFile = '') {
		is_array ( $model ) || $model = $this->getModel ( $model );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$templateFile || $templateFile = $model ['template_edit'] ? $model ['template_edit'] : '';
			$this->display ( $templateFile );
		}
	}
	public function common_add($model = null, $templateFile = '') {
		is_array ( $model ) || $model = $this->getModel ( $model );
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$templateFile || $templateFile = $model ['template_add'] ? $model ['template_add'] : '';
			$this->display ( $templateFile );
		}
	}
	
	// 通用的保存关键词方法
	public function _saveKeyword($model, $id) {
		if (isset ( $_POST ['keyword'] ) && $model ['name'] != 'keyword' && defined ( '_ADDONS' ) && ! isset ( $_REQUEST ['keyword_no_deal'] )) {
			D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'] );
		}
	}
	protected function checkAttr($Model, $model_id) {
		
		$fields = get_model_attribute ( $model_id, false );
		$validate = $auto = array ();
		foreach ( $fields as $key => $attr ) {
			if ($attr ['is_must']) { // 必填字段
				$validate [] = array (
						$attr ['name'],
						'require',
						$attr ['title'] . '必须!' 
				);
			}
			// 自动验证规则
			if (! empty ( $attr ['validate_rule'] ) || $attr ['validate_type'] == 'unique') {
				$validate [] = array (
						$attr ['name'],
						$attr ['validate_rule'],
						$attr ['error_info'] ? $attr ['error_info'] : $attr ['title'] . '验证错误',
						0,
						$attr ['validate_type'],
						$attr ['validate_time'] 
				);
			}
			// 自动完成规则
			if (! empty ( $attr ['auto_rule'] )) {
				$auto [] = array (
						$attr ['name'],
						$attr ['auto_rule'],
						$attr ['auto_time'],
						$attr ['auto_type'] 
				);
			} elseif ('checkbox' == $attr ['type']) { // 多选型
				$auto [] = array (
						$attr ['name'],
						'arr2str',
						3,
						'function' 
				);
			} elseif ('datetime' == $attr ['type']) { // 日期型
				$auto [] = array (
						$attr ['name'],
						'strtotime',
						3,
						'function' 
				);
			} elseif ('date' == $attr ['type']) { // 日期型
				$auto [] = array (
						$attr ['name'],
						'strtotime',
						3,
						'function' 
				);
			}
		}
		return $Model->validate ( $validate )->auto ( $auto );
	}
	
	// 获取模型列表数据
	public function _get_model_list($model = null, $page = 0, $order = 'id desc') {
		if (empty ( $model ))
			return false;
		
		$page || $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                         
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		// 读取模型数据列表
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		return $list_data;
	}
	// 解析列表规则
	public function _list_grid($model) {
		$fields = array ();
		$grids = preg_split ( '/[;\r\n]+/s', htmlspecialchars_decode ( $model ['list_grid'] ) );
		foreach ( $grids as &$value ) {
			// 字段:标题:链接
			$val = explode ( ':', $value );
			// 支持多个字段显示
			$value = array (
					'field' => $val [0],
					'title' => $val [1] 
			);
			if (preg_match ( '/^([0-9]*)%/', $val [1], $matches )) {
				$value ['title'] = str_replace ( $matches [0], '', $value ['title'] );
				$value ['width'] = $matches [1];
			}
			if (isset ( $val [2] )) {
				// 链接信息
				$value ['href'] = $val [2];
				// 搜索链接信息中的字段信息
				preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
					$fields [] = $match [1];
				}, $value ['href'] );
			}
			if (strpos ( $val [1], '|' )) {
				// 显示格式定义
				list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
			}
			$array = explode ( '|', $val [0] );
			$fields [] = $array [0];
		}
		// 过滤重复和错误字段信息
		$model_fields = M ( 'attribute' )->where ( 'model_id=' . $model ['id'] )->getFields ( 'name' );
		in_array ( 'id', $model_fields ) || array_push ( $model_fields, 'id' );
		$fields = array_intersect ( $fields, $model_fields );
		$res ['fields'] = array_unique ( $fields );
		foreach ( $grids as $g ) {
			$list_grids [$g ['field']] = $g;
		}
		
		$res ['list_grids'] = $list_grids;
		return $res;
	}
	public function _search_map($model, $fields) {
		$map = array ();
		
		// 插件里的操作自动加上Token限制
		$token = get_token ();
		if (defined ( 'ADDON_PUBLIC_PATH' ) && ! empty ( $token )) {
			$map ['token'] = $token;
		}
		
		// 自定义的条件搜索
		$conditon = session ( 'common_condition' );
		if (! empty ( $conditon )) {
			$map = array_merge ( $map, $conditon );
		}
		session ( 'common_condition', null );
		
		// 关键字搜索
		$key = $model ['search_key'] ? $model ['search_key'] : 'title';
		$keyArr = explode ( ':', $key );
		$key = $keyArr [0];
		$placeholder = isset ( $keyArr [1] ) ? $keyArr [1] : '请输入关键字';
		$this->assign ( 'placeholder', $placeholder );
		$this->assign ( 'search_key', $key );
		
		if (isset ( $_REQUEST [$key] ) && ! isset ( $map [$key] )) {
			$map [$key] = array (
					'like',
					'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%' 
			);
			unset ( $_REQUEST [$key] );
		}
		
		// 条件搜索
		foreach ( $_REQUEST as $name => $val ) {
			if (! isset ( $map [$name] ) && in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		
		return $map;
	}
	/*
	 * 导出数据
	 */
	public function outExcel($dataArr, $fileName = '', $sheet = false) {
		require_once VENDOR_PATH . 'download-xlsx.php';
		export_csv ( $dataArr, $fileName, $sheet );
		unset ( $sheet );
		unset ( $dataArr );
	}
	public function inExcel() {
		require_once VENDOR_PATH . '/PHPExcel.php';
		require_once VENDOR_PATH . 'PHPExcel/IOFactory.php';
		require_once VENDOR_PATH . 'PHPExcel/Reader/Excel5.php';
	}
	/*
	 * 在大并时优化插入数据性能，该函数需要两次调用：
	 * 一是在插入数据的地方调用，这时table和data两个参数不能为空
	 * 二是管理在后台查看列表时调用，这时table参数不为空，但data为空，这些防止有部分数据在缓存里无法到达数据库
	 */
	function delayAdd($table = '', $data = array(), $delay = 10) {
		$data_key = 'common_delayAdd_data_' . $table;
		$time_key = 'common_delayAdd_time_' . $table;
		
		$last_time = S ( $time_key );
		
		$dataArr = ( array ) S ( $data_key );
		empty ( $data ) || $dataArr [] = $data;
		
		if (! empty ( $data ) && ($last_time === false || NOW_TIME - $last_time < $delay)) {
			$msg ['status'] = S ( $data_key, $dataArr );
			$msg ['msg'] = 'add_cache';
		} elseif (! empty ( $dataArr )) {
			$msg ['status'] = M ( $table )->addAll ( $dataArr );
			if ($msg ['status']) {
				S ( $data_key, array () );
				S ( $time_key, NOW_TIME );
				$msg ['msg'] = 'add_db_success';
			} else {
				$msg ['msg'] = 'add_db_fail';
			}
		} else {
			$msg ['status'] = 0;
			$msg ['msg'] = 'No data install';
		}
		
		return $msg;
	}
	
	
	
}
// 设置控制器别名 便于升级
class_alias ( 'Think\Controller', 'Think\Action' );