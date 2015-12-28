<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
// const ONETHINK_VERSION = '1.1.141101';
const ONETHINK_ADDON_PATH = './Addons/'; // 微信插件
const ONETHINK_PLUGIN_PATH = './Plugins/'; // 系统插件
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 *
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login() {
	$user = session ( 'user_auth' );
	if (empty ( $user )) {
		return 0;
	} else {
		return session ( 'user_auth_sign' ) == data_auth_sign ( $user ) ? $user ['uid'] : 0;
	}
}

/**
 * 检测当前用户是否为管理员
 *
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null) {
	$uid = is_null ( $uid ) ? is_login () : $uid;
	return $uid && (intval ( $uid ) === C ( 'USER_ADMINISTRATOR' ));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 *
 * @param string $str
 *        	要分割的字符串
 * @param string $glue
 *        	分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',') {
	return explode ( $glue, $str );
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 *
 * @param array $arr
 *        	要连接的数组
 * @param string $glue
 *        	分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',') {
	return implode ( $glue, $arr );
}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @access public
 * @param string $str
 *        	需要转换的字符串
 * @param string $start
 *        	开始位置
 * @param string $length
 *        	截取长度
 * @param string $charset
 *        	编码格式
 * @param string $suffix
 *        	截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists ( "mb_substr" ))
		$slice = mb_substr ( $str, $start, $length, $charset );
	elseif (function_exists ( 'iconv_substr' )) {
		$slice = iconv_substr ( $str, $start, $length, $charset );
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );
		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	}
	
	return $suffix && $str != $slice ? $slice . '...' : $slice;
}
/**
 * 方法增强，根据$length自动判断是否应该显示...
 * 字符串截取，支持中文和其他编码
 * QQ:125682133
 *
 * @access public
 * @param string $str
 *        	需要转换的字符串
 * @param string $start
 *        	开始位置
 * @param string $length
 *        	截取长度
 * @param string $charset
 *        	编码格式
 * @param string $suffix
 *        	截断显示字符
 * @return string
 */
function msubstr_local($str, $start = 0, $length, $charset = "utf-8") {
	if (function_exists ( "mb_substr" ))
		$slice = mb_substr ( $str, $start, $length, $charset );
	elseif (function_exists ( 'iconv_substr' )) {
		$slice = iconv_substr ( $str, $start, $length, $charset );
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );
		
		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	}
	return (strlen ( $str ) > strlen ( $slice )) ? $slice . '...' : $slice;
}
/**
 * 系统加密方法
 *
 * @param string $data
 *        	要加密的字符串
 * @param string $key
 *        	加密密钥
 * @param int $expire
 *        	过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
	
	$data = base64_encode ( $data );
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = '';
	
	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}
	
	$str = sprintf ( '%010d', $expire ? $expire + time () : 0 );
	
	for($i = 0; $i < $len; $i ++) {
		$str .= chr ( ord ( substr ( $data, $i, 1 ) ) + (ord ( substr ( $char, $i, 1 ) )) % 256 );
	}
	return str_replace ( array (
			'+',
			'/',
			'=' 
	), array (
			'-',
			'_',
			'' 
	), base64_encode ( $str ) );
}

/**
 * 系统解密方法
 *
 * @param string $data
 *        	要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key
 *        	加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
	$key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
	$data = str_replace ( array (
			'-',
			'_' 
	), array (
			'+',
			'/' 
	), $data );
	$mod4 = strlen ( $data ) % 4;
	if ($mod4) {
		$data .= substr ( '====', $mod4 );
	}
	$data = base64_decode ( $data );
	$expire = substr ( $data, 0, 10 );
	$data = substr ( $data, 10 );
	
	if ($expire > 0 && $expire < time ()) {
		return '';
	}
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = $str = '';
	
	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}
	
	for($i = 0; $i < $len; $i ++) {
		if (ord ( substr ( $data, $i, 1 ) ) < ord ( substr ( $char, $i, 1 ) )) {
			$str .= chr ( (ord ( substr ( $data, $i, 1 ) ) + 256) - ord ( substr ( $char, $i, 1 ) ) );
		} else {
			$str .= chr ( ord ( substr ( $data, $i, 1 ) ) - ord ( substr ( $char, $i, 1 ) ) );
		}
	}
	return base64_decode ( $str );
}

/**
 * 数据签名认证
 *
 * @param array $data
 *        	被认证的数据
 * @return string 签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
	// 数据类型检测
	if (! is_array ( $data )) {
		$data = ( array ) $data;
	}
	ksort ( $data ); // 排序
	$code = http_build_query ( $data ); // url编码并生成query字符串
	$sign = sha1 ( $code ); // 生成签名
	return $sign;
}

/**
 * 对查询结果集进行排序
 *
 * @access public
 * @param array $list
 *        	查询结果
 * @param string $field
 *        	排序的字段名
 * @param array $sortby
 *        	排序类型
 *        	asc正向排序 desc逆向排序 nat自然排序
 * @return array
 *
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array ( $list )) {
		$refer = $resultSet = array ();
		foreach ( $list as $i => $data )
			$refer [$i] = &$data [$field];
		switch ($sortby) {
			case 'asc' : // 正向排序
				asort ( $refer );
				break;
			case 'desc' : // 逆向排序
				arsort ( $refer );
				break;
			case 'nat' : // 自然排序
				natcasesort ( $refer );
				break;
		}
		foreach ( $refer as $key => $val )
			$resultSet [] = &$list [$key];
		return $resultSet;
	}
	return false;
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param array $list
 *        	要转换的数据集
 * @param string $pid
 *        	parent标记字段
 * @param string $level
 *        	level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array ();
	if (is_array ( $list )) {
		// 创建基于主键的数组引用
		$refer = array ();
		foreach ( $list as $key => $data ) {
			$refer [$data [$pk]] = & $list [$key];
		}
		foreach ( $list as $key => $data ) {
			// 判断是否存在parent
			$parentId = $data [$pid];
			if ($root == $parentId) {
				$tree [] = & $list [$key];
			} else {
				if (isset ( $refer [$parentId] )) {
					$parent = & $refer [$parentId];
					$parent [$child] [] = & $list [$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array $tree
 *        	原来的树
 * @param string $child
 *        	孩子节点的键
 * @param string $order
 *        	排序显示的键，一般是主键 升序排列
 * @param array $list
 *        	过渡用的中间数组，
 * @return array 返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array ( $tree )) {
		$refer = array ();
		foreach ( $tree as $key => $value ) {
			$reffer = $value;
			if (isset ( $reffer [$child] )) {
				unset ( $reffer [$child] );
				tree_to_list ( $value [$child], $child, $order, $list );
			}
			$list [] = $reffer;
		}
		$list = list_sort_by ( $list, $order, $sortby = 'asc' );
	}
	return $list;
}
/**
 * 树形列表
 *
 * @param array $list
 *        	数据库原始数据
 * @param array $res_list
 *        	返回的结果数组
 * @param int $pid
 *        	上级ID
 * @param int $level
 *        	当前处理的层级
 */
function list_tree($list, &$res_list, $pid = 0, $level = 0) {
	foreach ( $list as $k => $v ) {
		if (intval ( $v ['pid'] ) != $pid)
			continue;
		
		if ($level > 0) {
			$space = '';
			for($i = 1; $i < $level; $i ++) {
				$space .= '──';
			}
			$v ['title'] = '├──' . $space . $v ['title'];
		}
		
		$v ['level'] = $level;
		$res_list [] = $v;
		unset ( $list [$k] );
		
		list_tree ( $list, $res_list, $v ['id'], $level + 1 );
	}
}
/**
 * 格式化字节大小
 *
 * @param number $size
 *        	字节数
 * @param string $delimiter
 *        	数字和单位分隔符
 * @return string 格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array (
			'B',
			'KB',
			'MB',
			'GB',
			'TB',
			'PB' 
	);
	for($i = 0; $size >= 1024 && $i < 5; $i ++)
		$size /= 1024;
	return round ( $size, 2 ) . $delimiter . $units [$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 *
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url) {
	cookie ( 'redirect_url', $url );
}

/**
 * 获取跳转页面URL
 *
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url() {
	$url = cookie ( 'redirect_url' );
	return empty ( $url ) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 *
 * @param string $hook
 *        	钩子名称
 * @param mixed $params
 *        	传入参数
 * @return void
 */
function hook($hook, $params = array()) {
	\Think\Hook::listen ( $hook, $params );
}

/**
 * 获取插件类的类名
 *
 * @param strng $name
 *        	插件名
 */
function get_addon_class($name) {
	$class = "Addons\\{$name}\\{$name}Addon";
	if (! class_exists ( $class )) {
		$class = "Plugins\\{$name}\\{$name}Addon";
	}
	return $class;
}

/**
 * 获取插件类的配置文件数组
 *
 * @param string $name
 *        	插件名
 */
function get_addon_config($name) {
	$class = get_addon_class ( $name );
	if (class_exists ( $class )) {
		$addon = new $class ();
		return $addon->getConfig ();
	} else {
		return array ();
	}
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string $url
 *        	url
 * @param array $param
 *        	参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()) {
	// 凡星：修复如user_center://user_center/add 识别错误的问题
	$urlArr = explode ( '://', $url );
	if (stripos ( $urlArr [0], '_' ) !== false) {
		$addons = $urlArr [0];
		$url = 'http://' . $urlArr [1];
	}
	$url = parse_url ( $url );
	$case = C ( 'URL_CASE_INSENSITIVE' );
	! $addons || $url ['scheme'] = $addons;
	$addons = $case ? parse_name ( $url ['scheme'] ) : $url ['scheme'];
	$controller = $case ? parse_name ( $url ['host'] ) : $url ['host'];
	$action = trim ( $case ? strtolower ( $url ['path'] ) : $url ['path'], '/' );
	
	/* 解析URL带的参数 */
	if (isset ( $url ['query'] )) {
		parse_str ( $url ['query'], $query );
		$param = array_merge ( $query, $param );
	}
	
	/* 基础参数 */
	$params = array (
			'_addons' => ucfirst ( $addons ),
			'_controller' => ucfirst ( $controller ),
			'_action' => $action 
	);
	$params = array_merge ( $params, $param ); // 添加额外参数
	
	$qurl = is_dir ( ONETHINK_PLUGIN_PATH . $params ['_addons'] ) ? "Home/Addons/plugin" : "Home/Addons/execute";
	
	return U ( $qurl, $params );
}

/**
 * 时间戳格式化
 *
 * @param int $time        	
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	if (empty ( $time ))
		return '';
	
	$time = $time === NULL ? NOW_TIME : intval ( $time );
	return date ( $format, $time );
}
function day_format($time = NULL) {
	return time_format ( $time, 'Y-m-d' );
}
function hour_format($time = NULL) {
	return time_format ( $time, 'H:i' );
}
function time_offset($time = NULL) {
	if (empty ( $time ))
		return '00:00';
	
	$mod = $time % 60;
	$min = ($time - $mod) / 60;
	
	$mod < 10 && $mod = '0' . $mod;
	$min < 10 && $min = '0' . $min;
	
	return $min . ':' . $mod;
}
// 获取用户信息
function getUserInfo($uid, $field = '') {
	$info = D ( 'Common/User' )->getUserInfo ( $uid );
	// dump ( $info );
	return empty ( $field ) ? $info : $info [$field];
}
/**
 * 根据用户ID获取用户名
 *
 * @param integer $uid
 *        	用户ID
 * @return string 用户名
 */
function get_username($uid = 0) {
	return get_nickname ( $uid );
}
function get_userface($uid = 0) {
	return getUserInfo ( $uid, $field = 'headimgurl' );
}
/**
 * 以下几个获取用户信息都是兼容旧系统的写法
 */
function get_nickname($uid = 0) {
	if (empty ( $uid ))
		return '';
	
	return getUserInfo ( $uid, $field = 'nickname' );
}
function get_truename($uid) {
	return getUserInfo ( $uid, $field = 'truename' );
}
function get_userinfo($uid, $field = '') {
	return getUserInfo ( $uid, $field );
}
function get_followinfo($id, $field = '') {
	return getUserInfo ( $id, $field );
}
function get_mult_userinfo($uid) {
	return getUserInfo ( $uid );
}
function get_mult_username($uids) {
	is_array ( $uids ) || $uids = explode ( ',', $uids );
	
	$uids = array_filter ( $uids );
	if (empty ( $uids )) {
		return;
	}
	
	foreach ( $uids as $uid ) {
		$name = get_truename ( $uid );
		if ($name) {
			$nameArr [] = $name;
		}
	}
	
	return implode ( ', ', $nameArr );
}

/**
 * 获取分类信息并缓存分类
 *
 * @param integer $id
 *        	分类ID
 * @param string $field
 *        	要获取的字段名
 * @return string 分类信息
 */
function get_category($id, $field = null) {
	static $list;
	
	/* 非法分类ID */
	if (empty ( $id ) || ! is_numeric ( $id )) {
		return '';
	}
	
	/* 读取缓存数据 */
	if (empty ( $list )) {
		$list = S ( 'sys_category_list' );
	}
	
	/* 获取分类名称 */
	if (! isset ( $list [$id] )) {
		$cate = M ( 'Category' )->find ( $id );
		if (! $cate || 1 != $cate ['status']) { // 不存在分类，或分类被禁用
			return '';
		}
		$list [$id] = $cate;
		S ( 'sys_category_list', $list ); // 更新缓存
	}
	return is_null ( $field ) ? $list [$id] : $list [$id] [$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id) {
	return get_category ( $id, 'name' );
}

/* 根据ID获取分类名称 */
function get_category_title($id) {
	return get_category ( $id, 'title' );
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = null) {
	$map ['status'] = 1;
	if (! is_null ( $model_id )) {
		$map ['id'] = array (
				'neq',
				$model_id 
		);
	}
	$model = M ( 'model' )->where ( $map )->field ( true )->select ();
	foreach ( $model as $value ) {
		$list [$value ['id']] = $value;
	}
	return $list;
}

/**
 * 解析UBB数据
 *
 * @param string $data
 *        	UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data) {
	// TODO: 待完善，目前返回原始数据
	return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 *
 * @param string $action
 *        	行为标识
 * @param string $model
 *        	触发行为的模型名
 * @param int $record_id
 *        	触发行为的记录id
 * @param int $user_id
 *        	执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null) {
	
	// 参数检查
	if (empty ( $action ) || empty ( $model ) || empty ( $record_id )) {
		return '参数不能为空';
	}
	if (empty ( $user_id )) {
		$user_id = is_login ();
	}
	
	// 查询行为,判断是否执行
	$action_info = M ( 'Action' )->getByName ( $action );
	if ($action_info ['status'] != 1) {
		return '该行为被禁用或删除';
	}
	
	// 插入行为日志
	$data ['action_id'] = $action_info ['id'];
	$data ['user_id'] = $user_id;
	$data ['action_ip'] = ip2long ( get_client_ip () );
	$data ['model'] = $model;
	$data ['record_id'] = $record_id;
	$data ['create_time'] = NOW_TIME;
	
	// 解析日志规则,生成日志备注
	if (! empty ( $action_info ['log'] )) {
		if (preg_match_all ( '/\[(\S+?)\]/', $action_info ['log'], $match )) {
			$log ['user'] = $user_id;
			$log ['record'] = $record_id;
			$log ['model'] = $model;
			$log ['time'] = NOW_TIME;
			$log ['data'] = array (
					'user' => $user_id,
					'model' => $model,
					'record' => $record_id,
					'time' => NOW_TIME 
			);
			foreach ( $match [1] as $value ) {
				$param = explode ( '|', $value );
				if (isset ( $param [1] )) {
					$replace [] = call_user_func ( $param [1], $log [$param [0]] );
				} else {
					$replace [] = $log [$param [0]];
				}
			}
			$data ['remark'] = str_replace ( $match [0], $replace, $action_info ['log'] );
		} else {
			$data ['remark'] = $action_info ['log'];
		}
	} else {
		// 未定义日志规则，记录操作url
		$data ['remark'] = '操作url：' . $_SERVER ['REQUEST_URI'];
	}
	
	M ( 'ActionLog' )->add ( $data );
	
	if (! empty ( $action_info ['rule'] )) {
		// 解析行为
		$rules = parse_action ( $action, $user_id );
		
		// 执行行为
		$res = execute_action ( $rules, $action_info ['id'], $user_id );
	}
}

/**
 * 解析行为规则
 * 规则定义 table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 * field->要操作的字段；
 * condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 * rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 * cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 * max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 *
 * @param string $action
 *        	行为id或者name
 * @param int $self
 *        	替换规则里的变量为执行用户的id
 * @return boolean array: ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self) {
	if (empty ( $action )) {
		return false;
	}
	
	// 参数支持id或者name
	if (is_numeric ( $action )) {
		$map = array (
				'id' => $action 
		);
	} else {
		$map = array (
				'name' => $action 
		);
	}
	
	// 查询行为信息
	$info = M ( 'Action' )->where ( $map )->find ();
	if (! $info || $info ['status'] != 1) {
		return false;
	}
	
	// 解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	$rules = $info ['rule'];
	$rules = str_replace ( '{$self}', $self, $rules );
	$rules = explode ( ';', $rules );
	$return = array ();
	foreach ( $rules as $key => &$rule ) {
		$rule = explode ( '|', $rule );
		foreach ( $rule as $k => $fields ) {
			$field = empty ( $fields ) ? array () : explode ( ':', $fields );
			if (! empty ( $field )) {
				$return [$key] [$field [0]] = $field [1];
			}
		}
		// cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
		if (! array_key_exists ( 'cycle', $return [$key] ) || ! array_key_exists ( 'max', $return [$key] )) {
			unset ( $return [$key] ['cycle'], $return [$key] ['max'] );
		}
	}
	
	return $return;
}

/**
 * 执行行为
 *
 * @param array $rules
 *        	解析后的规则数组
 * @param int $action_id
 *        	行为id
 * @param array $user_id
 *        	执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null) {
	if (! $rules || empty ( $action_id ) || empty ( $user_id )) {
		return false;
	}
	
	$return = true;
	foreach ( $rules as $rule ) {
		
		// 检查执行周期
		$map = array (
				'action_id' => $action_id,
				'user_id' => $user_id 
		);
		$map ['create_time'] = array (
				'gt',
				NOW_TIME - intval ( $rule ['cycle'] ) * 3600 
		);
		$exec_count = M ( 'ActionLog' )->where ( $map )->count ();
		if ($exec_count > $rule ['max']) {
			continue;
		}
		
		// 执行数据库操作
		$Model = M ( ucfirst ( $rule ['table'] ) );
		$field = $rule ['field'];
		$res = $Model->where ( $rule ['condition'] )->setField ( $field, array (
				'exp',
				$rule ['rule'] 
		) );
		
		if (! $res) {
			$return = false;
		}
	}
	return $return;
}

// 基于数组创建目录和文件
function create_dir_or_files($files) {
	foreach ( $files as $key => $value ) {
		if (substr ( $value, - 1 ) == '/') {
			mkdir ( $value );
		} else {
			@file_put_contents ( $value, '' );
		}
	}
}

if (! function_exists ( 'array_column' )) {
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array ();
		if (null === $indexKey) {
			if (null === $columnKey) {
				$result = array_values ( $input );
			} else {
				foreach ( $input as $row ) {
					$result [] = $row [$columnKey];
				}
			}
		} else {
			if (null === $columnKey) {
				foreach ( $input as $row ) {
					$result [$row [$indexKey]] = $row;
				}
			} else {
				foreach ( $input as $row ) {
					$result [$row [$indexKey]] = $row [$columnKey];
				}
			}
		}
		return $result;
	}
}

/**
 * 获取表名（不含表前缀）
 *
 * @param string $model_id        	
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null) {
	if (empty ( $model_id )) {
		return false;
	}
	$Model = M ( 'model' );
	$info = $Model->getById ( $model_id );
	return $info ['name'];
}

/**
 * 获取属性信息并缓存
 *
 * @param integer $id
 *        	属性ID
 * @param string $field
 *        	要获取的字段名
 * @return string 属性信息
 */
function get_model_attribute($model_id, $field_sort = false) {
	static $list;
	
	/* 非法ID */
	if (empty ( $model_id ) || ! is_numeric ( $model_id )) {
		return '';
	}
	
	/* 获取属性 */
	if (! isset ( $list [$model_id] )) {
		$map ['model_id'] = $map2 ['id'] = $model_id;
		
		$alist = M ( 'attribute' )->where ( $map )->order ( 'id asc' )->select ();
		foreach ( $alist as $vo ) {
			$res [$vo ['name']] = $vo;
		}
		unset ( $alist );
		
		$field_sort === false && $field_sort = M ( 'model' )->where ( $map2 )->getField ( 'field_sort' );
		if ($field_sort) {
			$field_sort = json_decode ( $field_sort, true );
			$sort = array ();
			foreach ( $field_sort as $s ) {
				$sort [$s] = $res [$s];
				unset ( $res [$s] );
			}
			
			$res = array_merge ( $sort, $res );
		}
		
		$list [$model_id] = $res;
	}
	
	return ( array ) $list [$model_id];
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5'); 调用Admin模块的User接口
 *
 * @param string $name
 *        	格式 [模块名]/接口名/方法名
 * @param array|string $vars
 *        	参数
 */
function api($name, $vars = array()) {
	$array = explode ( '/', $name );
	$method = array_pop ( $array );
	$classname = array_pop ( $array );
	$module = $array ? array_pop ( $array ) : 'Common';
	$callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
	if (is_string ( $vars )) {
		parse_str ( $vars, $vars );
	}
	return call_user_func_array ( $callback, $vars );
}

/**
 * 根据条件字段获取指定表的数据
 *
 * @param mixed $value
 *        	条件，可用常量或者数组
 * @param string $condition
 *        	条件字段
 * @param string $field
 *        	需要返回的字段，不传则返回整个数据
 * @param string $table
 *        	需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null) {
	if (empty ( $value ) || empty ( $table )) {
		return false;
	}
	
	// 拼接参数
	$map [$condition] = $value;
	$info = M ( ucfirst ( $table ) )->where ( $map );
	if (empty ( $field )) {
		$info = $info->field ( true )->find ();
	} else {
		$info = $info->getField ( $field );
	}
	return $info;
}

/**
 * 获取文档封面图片
 *
 * @param int $cover_id        	
 * @param string $field        	
 * @return 完整的数据 或者 指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null) {
	if (empty ( $cover_id ))
		return false;
	
	$key = 'Picture_' . $cover_id;
	$picture = S ( $key );
	
	if (! $picture) {
		$map ['status'] = 1;
		$picture = M ( 'Picture' )->where ( $map )->getById ( $cover_id );
		S ( $key, $picture, 86400 );
	}
	
	if (empty ( $picture ))
		return '';
	
	return empty ( $field ) ? $picture : $picture [$field];
}
function get_cover_url($cover_id, $width = '', $height = '') {
	$info = get_cover ( $cover_id );
	
	if ($width > 0 && $height > 0) {
		$thumb = "?imageMogr2/thumbnail/{$width}x{$height}";
	} elseif ($width > 0) {
		$thumb = "?imageMogr2/thumbnail/{$width}x";
	} elseif ($height > 0) {
		$thumb = "?imageMogr2/thumbnail/x{$height}";
	}
	if ($info ['url'])
		return $info ['url'] . $thumb;
	
	$url = $info ['path'];
	if (empty ( $url ))
		return '';
	return SITE_URL . $url . $thumb;
}
// 兼容旧方法
function get_picture_url($cover_id) {
	return get_cover_url ( $cover_id );
}
function get_img_html($cover_id) {
	$url = get_cover_url ( $cover_id );
	
	return url_img_html ( $url );
}
function url_img_html($url) {
	if (empty ( $url ))
		return '';
	
	return '<img class="list_img" src="' . $url . '" >';
}
/* 根据id获取fiel路径 */
function get_file_url($id) {
	if (empty ( $id ))
		return false;
	
	$key = 'File_' . $id;
	$file = S ( $key );
	
	if (! $file) {
		$file = M ( 'File' )->where ( array (
				'id' => $id 
		) )->find ();
		S ( $key, $file, 86400 );
	}
	
	if (empty ( $file ))
		return '';
	
	$info = $file;
	
	if ($info ['url'])
		return $info ['url'];
	
	$url = $info ['savepath'];
	if (empty ( $url ))
		return '';
	
	return SITE_URL . '/Uploads/Download/' . $info ['savepath'] . $info ['savename'];
}
/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 *
 * @param number $pos
 *        	推荐位的值
 * @param number $contain
 *        	指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0) {
	if (empty ( $pos ) || empty ( $contain )) {
		return false;
	}
	
	// 将两个参数进行按位与运算，不为0则表示$contain属于$pos
	$res = $pos & $contain;
	if ($res !== 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取数据的所有子孙数据的id值
 *
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */
function get_stemma($pids, Model &$model, $field = 'id') {
	$collection = array ();
	
	// 非空判断
	if (empty ( $pids )) {
		return $collection;
	}
	
	if (is_array ( $pids )) {
		$pids = trim ( implode ( ',', $pids ), ',' );
	}
	$result = $model->field ( $field )->where ( array (
			'pid' => array (
					'IN',
					( string ) $pids 
			) 
	) )->select ();
	$child_ids = array_column ( ( array ) $result, 'id' );
	
	while ( ! empty ( $child_ids ) ) {
		$collection = array_merge ( $collection, $result );
		$result = $model->field ( $field )->where ( array (
				'pid' => array (
						'IN',
						$child_ids 
				) 
		) )->select ();
		$child_ids = array_column ( ( array ) $result, 'id' );
	}
	return $collection;
}

/**
 * 判断关键词是否唯一
 *
 * @author weiphp
 */
function keyword_unique($keyword) {
	if (empty ( $keyword ))
		return false;
	
	$map ['keyword'] = $keyword;
	$info = M ( 'keyword' )->where ( $map )->find ();
	return empty ( $info );
}
// 分析枚举类型配置值 格式 a:名称1,b:名称2
// weiphp 该函数是从admin的function的文件里提取这到里
function parse_config_attr($string) {
	$array = preg_split ( '/[\s;\r\n]+/', trim ( $string, ",;\s\r\n" ) );
	if (strpos ( $string, ':' )) {
		$value = array ();
		foreach ( $array as $val ) {
			list ( $k, $v ) = explode ( ':', $val );
			$value [$k] = $v;
		}
	} else {
		$value = $array;
	}
	foreach ( $value as &$vo ) {
		$vo = clean_hide_attr ( $vo );
	}
	// dump($value);
	return $value;
}
function clean_hide_attr($str) {
	$arr = explode ( '|', $str );
	return $arr [0];
}
function get_hide_attr($str) {
	$arr = explode ( '|', $str );
	return $arr [1];
}
// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string) {
	if (0 === strpos ( $string, ':' )) {
		// 采用函数定义
		$str = substr($string,1).';';
		eval("\$str = $str");
		return $str;
//		return eval ( substr ( $string, 1 ) . ';' );
	}
	$array = preg_split ( '/[\s;\r\n]+/', trim ( $string, ",;\r\n" ) );
	// dump($array);
	if (strpos ( $string, ':' )) {
		$value = array ();
		foreach ( $array as $val ) {
			list ( $k, $v ) = explode ( ':', $val );
			empty ( $v ) && $v = $k;
			$k = clean_hide_attr ( $k );
			$value [$k] = $v;
		}
	} else {
		$value = $array;
	}
	// dump($value);
	return $value;
}
/* 解析列表定义规则 */
function get_list_field($data, $grid, $model) {
	// 获取当前字段数据
	$field = $grid ['field'];
	$array = explode ( '|', $field );
	$field_name = trim ( $array [0] );
	$value = $data [$field_name];
	
	// 函数支持
	if (isset ( $array [1] )) {
		if ($array [1] == 'get_name_by_status') {
			$value = get_name_by_status ( $value, $field_name, $model ['id'] );
		} else {
			$value = call_user_func ( $array [1], $value );
		}
	}
	
	// 链接支持
	if (! empty ( $grid ['href'] )) {
		$links = explode ( ',', $grid ['href'] );
		foreach ( $links as $link ) {
			$array = explode ( '|', $link );
			$href = $array [0];
			
			$show = isset ( $array [1] ) ? $array [1] : $value;
			// 增加跳转方式处理 weiphp
			$target = '_self';
			if (preg_match ( '/target=(\w+)/', $href, $matches )) {
				$target = $matches [1];
				$href = str_replace ( '&' . $matches [0], '', $href );
			}
			
			// 替换系统特殊字符串
			$href = str_replace ( array (
					'[DELETE]',
					'[EDIT]',
					'[MODEL]' 
			), array (
					'del?id=[id]&model=[MODEL]',
					'edit?id=[id]&model=[MODEL]',
					$model ['id'] 
			), $href );
			
			// 替换数据变量
			$href = preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use($data) {
				return $data [$match [1]];
			}, $href );
			
			// 兼容多种写法
			if (strpos ( $href, '?' ) === false && strpos ( $href, '&' ) !== false) {
				$href = preg_replace ( "/&/i", "?", $href, 1 );
			}
			if ($show == '删除') {
				$val [] = '<a class="confirm"   href="' . urldecode ( U ( $href, $GLOBALS ['get_param'] ) ) . '">' . $show . '</a>';
			} else if ($show == '复制链接') {
				$publicid = get_token_appinfo ( '', 'id' );
				$val [] = '<a class="list_copy_link" id="copyLink_' . $data ['id'] . '"   data-clipboard-text="' . urldecode ( U ( $href, $GLOBALS ['get_param'] ) ) . '&publicid=' . $publicid . '">' . $show . '</a>';
			} else {
				$val [] = '<a  target="' . $target . '" href="' . urldecode ( U ( $href, $GLOBALS ['get_param'] ) ) . '">' . $show . '</a>';
			}
		}
		$value = implode ( ' ', $val );
	}
	return $value;
}
/**
 * 获取状态值对应的标题
 *
 * @author weiphp
 */
function get_name_by_status($val, $name, $model_id) {
	static $_name = array ();
	if (! isset ( $_name [$model_id] )) {
		$_name [$model_id] = array ();
		$map ['extra'] = array (
				'EXP',
				'!=""' 
		);
		$map ['model_id'] = $model_id;
		$list = M ( 'attribute' )->where ( $map )->select ();
		foreach ( $list as $attr ) {
			if (empty ( $attr ['extra'] ))
				continue;
			
			$extra = parse_config_attr ( $attr ['extra'] );
			if (is_array ( $extra ) && ! empty ( $extra )) {
				$_name [$model_id] [$attr ['name']] ['value'] = $extra;
				$_name [$model_id] [$attr ['name']] ['type'] = $attr ['type'];
			}
		}
	}
	
	if ($_name [$model_id] [$name] ['type'] == 'checkbox') {
		$valArr = explode ( ',', $val );
		foreach ( $valArr as $v ) {
			$res [] = empty ( $_name [$model_id] [$name] ['value'] [$v] ) ? $v : $_name [$model_id] [$name] ['value'] [$v];
		}
		
		return implode ( ', ', $res );
	} else {
		return empty ( $_name [$model_id] [$name] ['value'] [$val] ) ? $val : $_name [$model_id] [$name] ['value'] [$val];
	}
}
function addWeixinLog($data, $data_post = '', $wechat = false) {
	$log ['cTime'] = time ();
	$log ['cTime_format'] = date ( 'Y-m-d H:i:s', $log ['cTime'] );
	$log ['data'] = is_array ( $data ) ? serialize ( $data ) : $data;
	$log ['data_post'] = is_array ( $data_post ) ? serialize ( $data_post ) : $data_post;
	M ( 'weixin_log' )->add ( $log );
	
	// 通过微信客服接口直接回复调试信息
	if ($wechat && C ( 'SEND_CUSTOM_MSG' )) {
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . get_access_token ();
		
		$param ['touser'] = get_openid ();
		$param ['msgtype'] = 'text';
		$param ['text'] ['content'] = 'data: ' . $log ['data'] . '<br/> data_post: ' . $log ['data_post'];
		
		post_data ( $url, $param );
	}
}
/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 *
 * @param $pArray 一个二维数组        	
 * @param $pKey 数组的键的名称        	
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "") {
	$result = array ();
	if (is_array ( $pArray )) {
		foreach ( $pArray as $temp_array ) {
			if (is_object ( $temp_array )) {
				$temp_array = ( array ) $temp_array;
			}
			if (("" != $pCondition && $temp_array [$pCondition [0]] == $pCondition [1]) || "" == $pCondition) {
				$result [] = ("" == $pKey) ? $temp_array : isset ( $temp_array [$pKey] ) ? $temp_array [$pKey] : "";
			}
		}
		return $result;
	} else {
		return false;
	}
}
// 判断是否是在微信浏览器里
function isWeixinBrowser($from = 0) {
	if ((! $from && defined ( 'IN_WEIXIN' ) && IN_WEIXIN) || isset ( $_GET ['is_stree'] ))
		return true;
	
	$agent = $_SERVER ['HTTP_USER_AGENT'];
	if (! strpos ( $agent, "icroMessenger" )) {
		return false;
	}
	return true;
}
// php获取当前访问的完整url地址
function GetCurUrl() {
	$url = 'http://';
	if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
		$url = 'https://';
	}
	if ($_SERVER ['SERVER_PORT'] != '80') {
		$url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
	} else {
		$url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
	}
	// 兼容后面的参数组装
	if (stripos ( $url, '?' ) === false) {
		$url .= '?t=' . time ();
	}
	return $url;
}
// 获取当前用户的OpenId
function get_openid($openid = NULL) {
	$token = get_token ();
	if ($openid !== NULL && $openid != '-1') {
		session ( 'openid_' . $token, $openid );
	} elseif (! empty ( $_REQUEST ['openid'] ) && $_REQUEST ['openid'] != '-1' && $_REQUEST ['openid'] != '-2') {
		session ( 'openid_' . $token, $_REQUEST ['openid'] );
	}
	$openid = session ( 'openid_' . $token );
	$isWeixinBrowser = isWeixinBrowser ();
	if ((empty ( $openid ) || $openid == '-1') && $isWeixinBrowser && $_REQUEST ['openid'] != '-2' && IS_GET && ! IS_AJAX) {
		$callback = GetCurUrl ();
		OAuthWeixin ( $callback, $token );
	}
	if (empty ( $openid )) {
		return '-1';
		// exit ( 'openid获取失败error' );
	}
	return $openid;
}
// 通过UID获取openid
function getOpenidByUid($uid, $token = '') {
	empty ( $token ) && $token = get_token ();
	
	$map ['uid'] = $uid;
	$map ['token'] = $token;
	return M ( 'public_follow' )->where ( $map )->getField ( 'openid' );
}
/*
 * 获取支付的appid的openid
 * 微信支付和红包使用
 */
function getPaymentOpenid($appId = "", $serect = "") { // echo '444';
	if (empty ( $appId ) || empty ( $serect )) {
		get_openid ();
		exit ();
	}
	$callback = GetCurUrl ();
	if ((defined ( 'IN_WEIXIN' ) && IN_WEIXIN) || isset ( $_GET ['is_stree'] ))
		return false;
	
	$callback = urldecode ( $callback );
	$isWeixinBrowser = isWeixinBrowser ();
	
	if (strpos ( $callback, '?' ) === false) {
		$callback .= '?';
	} else {
		$callback .= '&';
	}
	$param ['appid'] = $appId;
	
	if (! isset ( $_GET ['getOpenId'] )) {
		$param ['redirect_uri'] = $callback . 'getOpenId=1';
		$param ['response_type'] = 'code';
		$param ['scope'] = 'snsapi_base';
		$param ['state'] = 123;
		
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
		redirect ( $url );
	} else if ($_GET ['state']) {
		$param ['secret'] = $serect;
		$param ['code'] = I ( 'code' );
		$param ['grant_type'] = 'authorization_code';
		
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
		$content = file_get_contents ( $url );
		$content = json_decode ( $content, true );
		return $content ['openid'];
	}
}
// 获取当前用户的Token
function get_token($token = NULL) {
	$stoken = session ( 'token' );
	$domain = explode ( '.', SITE_DOMAIN );
	
	if ($token !== NULL && $token != '-1') {
		session ( 'token', $token );
	} elseif (empty ( $stoken ) && C ( 'DIV_DOMAIN' ) && ! is_numeric ( $domain [0] ) && SITE_DOMAIN != 'localhost') { // 泛域名支持
		$domain = explode ( '.', SITE_DOMAIN );
		$map ['domain'] = $domain [0];
		! $GLOBALS ['is_wap'] && $GLOBALS ['mid'] && $map ['uid'] = $GLOBALS ['uid'];
		$token = D ( 'Common/Public' )->where ( $map )->getField ( 'token' );
		$token && session ( 'token', $token );
	} elseif (! empty ( $_REQUEST ['token'] ) && $_REQUEST ['token'] != '-1') {
		session ( 'token', $_REQUEST ['token'] );
	} elseif (! empty ( $_REQUEST ['publicid'] )) {
		$publicid = I ( 'publicid' );
		$token = D ( 'Common/Public' )->getInfo ( $publicid, 'token' );
		$token && session ( 'token', $token );
	}
	$token = session ( 'token' );
	
	if (empty ( $token ) || $token == '-1') {
		// $map ['uid'] = session ( 'mid' );
		// if ($map ['uid'] > 0) {
		// $user = get_userinfo ( $map ['uid'] );
		
		// $user ['level'] < 2 && $user ['manager_id'] > 0 && $map ['uid'] = $user ['manager_id'];
		// $token = $user ['level'] < 2 || $user ['has_public'] ? D ( 'Common/Public' )->where ( $map )->getField ( 'token' ) : DEFAULT_TOKEN;
		
		// isset ( $user ['has_public'] ) && $token && session ( 'token', $token );
		// } else {
		$token = DEFAULT_TOKEN;
		// }
	}
	
	return $token;
}
// 获取当前用户的UID,方便在模型里的自动填充功能使用
function get_mid() {
	return session ( 'mid' );
}
// 通过openid获取微信用户基本信息,此功能只有认证的服务号才能用
function getWeixinUserInfo($openid) {
	if (! C ( 'USER_BASE_INFO' )) {
		return array ();
	}
	$access_token = get_access_token ();
	if (empty ( $access_token )) {
		return array ();
	}
	
	$param2 ['access_token'] = $access_token;
	$param2 ['openid'] = $openid;
	$param2 ['lang'] = 'zh_CN';
	
	$url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query ( $param2 );
	$content = file_get_contents ( $url );
	$content = json_decode ( $content, true );
	return $content;
}
// 获取公众号的信息
function get_token_appinfo($token = '', $field = '') {
	empty ( $token ) && $token = get_token ();
	if ($token != 'gh_3c884a361561') {
		$info = D ( 'Common/Public' )->getInfoByToken ( $token, $field );
	}
	return $info;
}
// 兼容旧方法
function get_service_info($field = '') {
	return get_token_appinfo ( '', $field );
}
// 判断公众号的类型：是订阅号还是服务号
function get_token_type($token = '') {
	$info = get_token_appinfo ( $token );
	return intval ( $info ['type'] );
}
// 获取access_token，自动带缓存功能
function get_access_token($token = '') {
	empty ( $token ) && $token = get_token ();
	
	$info = get_token_appinfo ( $token );
	
	// 微信开放平台一键绑定
	if ($token == 'gh_3c884a361561' || (C ( 'PUBLIC_BIND' ) && $info ['is_bind'])) {
		$dao = D ( 'Addons://PublicBind/PublicBind' );
		$auth_code = $dao->_get_pre_auth_code ();
		$info = $dao->getAuthInfo ( $auth_code );
		return $info ['authorization_info'] ['authorizer_access_token'];
	}
	
	return get_access_token_by_apppid ( $info ['appid'], $info ['secret'] );
}
// 获取access_token，自动带缓存功能
function get_tv_access_token($uid = '') {
	empty ( $uid ) && $uid = session ( 'mid' );
	
	$info = get_userinfo ( $uid );
	return get_access_token_by_apppid ( $info ['GammaAppId'], $info ['GammaSecret'] );
}
function get_access_token_by_apppid($appid, $secret) {
	if (empty ( $appid ) || empty ( $secret )) {
		return 0;
	}
	
	$key = 'access_token_apppid_' . $appid . '_' . $secret;
	$res = S ( $key );
	if ($res !== false)
		return $res;
	
	$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
	$tempArr = json_decode ( file_get_contents ( $url ), true );
	if (@array_key_exists ( 'access_token', $tempArr )) {
		S ( $key, $tempArr ['access_token'], $tempArr ['expires_in'] );
		return $tempArr ['access_token'];
	} else {
		return 0;
	}
}
function OAuthWeixin($callback, $token = '') {
	if ((defined ( 'IN_WEIXIN' ) && IN_WEIXIN) || isset ( $_GET ['is_stree'] ))
		return false;
	
	$callback = urldecode ( $callback );
	$isWeixinBrowser = isWeixinBrowser ();
	$info = get_token_appinfo ( $token );
	
	if (strpos ( $callback, '?' ) === false) {
		$callback .= '?';
	} else {
		$callback .= '&';
	}
	
	if (! $isWeixinBrowser || ! C ( 'USER_OAUTH' ) || empty ( $info ['appid'] )) {
		redirect ( $callback . 'openid=-2' );
	}
	$param ['appid'] = $info ['appid'];
	
	if (! isset ( $_GET ['getOpenId'] )) {
		$param ['redirect_uri'] = $callback . 'getOpenId=1';
		$param ['response_type'] = 'code';
		$param ['scope'] = 'snsapi_base';
		$param ['state'] = 123;
		$info ['is_bind'] && $param ['component_appid'] = C ( 'COMPONENT_APPID' );
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
		redirect ( $url );
	} elseif ($_GET ['state']) {
		$param ['code'] = I ( 'code' );
		$param ['grant_type'] = 'authorization_code';
		
		if ($info ['is_bind']) {
			$param ['appid'] = I ( 'appid' );
			$param ['component_appid'] = C ( 'COMPONENT_APPID' );
			$param ['component_access_token'] = D ( 'Addons://PublicBind/PublicBind' )->_get_component_access_token ();
			
			$url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?' . http_build_query ( $param );
		} else {
			$param ['secret'] = $info ['secret'];
			
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
		}
		
		$content = file_get_contents ( $url );
		$content = json_decode ( $content, true );
		redirect ( $callback . 'openid=' . $content ['openid'] );
	}
}
/**
 * 执行SQL文件
 */
function execute_sql_file($sql_path) {
	// 读取SQL文件
	$sql = wp_file_get_contents ( $sql_path );
	$sql = str_replace ( "\r", "\n", $sql );
	$sql = explode ( ";\n", $sql );
	
	// 替换表前缀
	$orginal = 'wp_';
	$prefix = C ( 'DB_PREFIX' );
	$sql = str_replace ( "{$orginal}", "{$prefix}", $sql );
	
	// 开始安装
	foreach ( $sql as $value ) {
		$value = trim ( $value );
		if (empty ( $value ))
			continue;
		
		$res = M ()->execute ( $value );
		// dump($res);
		// dump(M()->getLastSql());
	}
}
// 设置微信关联聊天中用到的用户状态
function set_user_status($addon, $keywordArr = array()) {
	// 设置用户状态
	$user_status ['addon'] = $addon;
	$user_status ['keywordArr'] = $keywordArr;
	
	$openid = get_openid ();
	return S ( 'user_status_' . $openid, $user_status );
}

// 获取公众号等级名
function get_public_group_name($group_id) {
	static $_public_group_name;
	
	$group_id = intval ( $group_id );
	if (! isset ( $_public_group_name [$group_id] )) {
		$group_list = M ( 'public_group' )->field ( 'id, title' )->select ();
		foreach ( $group_list as $g ) {
			$_public_group_name [$g ['id']] = $g ['title'];
		}
		$_public_group_name [0] = '无';
	}
	
	return $_public_group_name [$group_id];
}

// 截取内容
function getShort($str, $length = 40, $ext = '') {
	$str = htmlspecialchars ( $str );
	$str = strip_tags ( $str );
	$str = htmlspecialchars_decode ( $str );
	$strlenth = 0;
	$out = '';
	preg_match_all ( "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match );
	foreach ( $match [0] as $v ) {
		preg_match ( "/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs );
		if (! empty ( $matchs [0] )) {
			$strlenth += 1;
		} elseif (is_numeric ( $v )) {
			$strlenth += 0.5; // 字符字节长度比例 汉字为1
		} else {
			$strlenth += 0.5; // 字符字节长度比例 汉字为1
		}
		
		if ($strlenth > $length) {
			$output .= $ext;
			break;
		}
		
		$output .= $v;
	}
	return $output;
}

// 防超时的file_get_contents改造函数
function wp_file_get_contents($url) {
	$context = stream_context_create ( array (
			'http' => array (
					'timeout' => 30 
			) 
	) ); // 超时时间，单位为秒
	
	return file_get_contents ( $url, 0, $context );
}

// 全局的安全过滤函数
function safe($text, $type = 'html') {
	// 无标签格式
	$text_tags = '';
	// 只保留链接
	$link_tags = '<a>';
	// 只保留图片
	$image_tags = '<img>';
	// 只存在字体样式
	$font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
	// 标题摘要基本格式
	$base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike><section><header><footer><article><nav><audio><video>';
	// 兼容Form格式
	$form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
	// 内容等允许HTML的格式
	$html_tags = $base_tags . '<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
	// 全HTML格式
	$all_tags = $form_tags . $html_tags . '<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
	// 过滤标签
	$text = html_entity_decode ( $text, ENT_QUOTES, 'UTF-8' );
	$text = strip_tags ( $text, ${$type . '_tags'} );
	
	// 过滤攻击代码
	if ($type != 'all') {
		// 过滤危险的属性，如：过滤on事件lang js
		while ( preg_match ( '/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat ) ) {
			$text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
		}
		while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
			$text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
		}
	}
	return $text;
}
// 创建多级目录
function mkdirs($dir) {
	if (! is_dir ( $dir )) {
		if (! mkdirs ( dirname ( $dir ) )) {
			return false;
		}
		if (! mkdir ( $dir, 0777 )) {
			return false;
		}
	}
	return true;
}
// 组装查询条件
function getIdsForMap($ids, $map = array(), $field = 'id') {
	$ids = safe ( $ids );
	$ids = preg_split ( '/[\s,;]+/', $ids ); // 支持以空格tab逗号分号分割ID
	$ids = array_filter ( $ids );
	if (empty ( $ids ))
		return $map;
	
	$map [$field] = array (
			'in',
			$ids 
	);
	
	return $map;
}
// 获取通用分类级联菜单的标题，改方法仅支持级联的数据源配置成数据表common_category的情况，其它情况需要使用下面的getCascadeTitle方法
function getCommonCategoryTitle($ids) {
	$extra = 'type=db&table=common_category';
	
	return getCascadeTitle ( $ids, $extra );
}
// 获取级联菜单的标题的通用处理方法
function getCascadeTitle($ids, $extra) {
	$idArr = explode ( ',', $ids );
	$idArr = array_filter ( $idArr );
	if (empty ( $idArr ))
		return '';
	
	parse_str ( $extra, $arr );
	if ($arr ['type'] == 'db') {
		$table = $arr ['table'];
		unset ( $arr ['type'], $arr ['table'] );
		
		$arr ['token'] = get_token ();
		$arr ['id'] = array (
				'in',
				$idArr 
		);
		$list = M ( $table )->where ( $arr )->field ( 'title' )->select ();
		$titleArr = getSubByKey ( $list, 'title' );
	} else {
		$str = str_replace ( '，', ',', $extra );
		$str = str_replace ( '【', '[', $str );
		$str = str_replace ( '】', ']', $str );
		$str = str_replace ( '：', ':', $str );
		
		$arr = StringToArray ( $str );
		$str = '';
		foreach ( $arr as $v ) {
			if ($v == '[' || $v == ']' || $v == ',') {
				if ($str) {
					$block = explode ( ':', trim ( $str ) );
					if (in_array ( $block [0], $idArr )) {
						$titleArr [] = isset ( $block [1] ) ? $block [1] : $block [0];
					}
				}
				$str = '';
			} else {
				$str .= $v;
			}
		}
	}
	return implode ( ' > ', $titleArr );
}
// 把字符串转成数组，支持汉字，只能是utf-8格式的
function StringToArray($str) {
	$result = array ();
	$len = strlen ( $str );
	$i = 0;
	while ( $i < $len ) {
		$chr = ord ( $str [$i] );
		if ($chr == 9 || $chr == 10 || (32 <= $chr && $chr <= 126)) {
			$result [] = substr ( $str, $i, 1 );
			$i += 1;
		} elseif (192 <= $chr && $chr <= 223) {
			$result [] = substr ( $str, $i, 2 );
			$i += 2;
		} elseif (224 <= $chr && $chr <= 239) {
			$result [] = substr ( $str, $i, 3 );
			$i += 3;
		} elseif (240 <= $chr && $chr <= 247) {
			$result [] = substr ( $str, $i, 4 );
			$i += 4;
		} elseif (248 <= $chr && $chr <= 251) {
			$result [] = substr ( $str, $i, 5 );
			$i += 5;
		} elseif (252 <= $chr && $chr <= 253) {
			$result [] = substr ( $str, $i, 6 );
			$i += 6;
		}
	}
	return $result;
}
/**
 * 增加用户积分函数
 *
 * @param string $name
 *        	积分标识名
 * @param int $lock_time
 *        	解锁时间，即多长时间内才能重复加积分，为0时不作控制
 * @param array $credit
 *        	自定义金币值，格式：array('score'=>金币值,'experience'=>经历值,'title'=>'积分项名称');为空时默认取管理中心积分管理里的配置值
 * @param int $admin_uid
 *        	管理员UID，用于管理员给用户手工加积分时的场景
 */
function add_credit($name, $lock_time = 5, $credit = array(), $admin_uid = 0) {
	if (empty ( $name ))
		return false;
	
	if ($lock_time > 0) {
		$key = 'credit_lock_' . get_token () . '_' . get_openid () . '_' . $name;
		if (S ( $key ))
			return false;
		
		S ( $key, 1, $lock_time );
	}
	
	$data ['credit_name'] = $name;
	$data ['admin_uid'] = $admin_uid;
	$data = array_merge ( $data, $credit );
	$credit = D ( 'Common/Credit' )->addCredit ( $data );
}
/**
 * 增加用户余额函数
 *
 * @param int $uid
 *        	用户UID
 * @param float $money
 *        	充值金额
 * @param string $log
 *        	recharge_log 表需要的数据
 */
function add_money($uid, $money, $log = array()) {
	if (empty ( $uid ) || empty ( $money ))
		return false;
	
	return D ( 'Addons://Card/Card' )->addMoney ( $uid, $money, $log );
}
// 判断用户最大可创建的公众号数
function getPublicMax($uid) {
	$map ['uid'] = $uid;
	$public_count = M ( 'user' )->where ( $map )->getField ( 'public_count' );
	if ($public_count === NULL) {
		$public_count = C ( 'DEFAULT_PUBLIC_CREATE_MAX_NUMB' );
	}
	return intval ( $public_count );
}
function diyPage($keyword) {
	$map ['keyword'] = $keyword;
	$map ['token'] = get_token ();
	$page = M ( 'diy' )->where ( $map )->find ();
	
	if (! $page) {
		$map ['token'] = '0';
		$page = M ( 'diy' )->where ( $map )->find ();
	}
	// dump($page);
	if (! $page) {
		return false;
	}
	
	$model = A ( 'Addons://Diy/Diy' );
	// dump($model);exit;
	$model->show ( $page ['id'] );
}
// 各插件获取关联抽奖活动的地址 暂只支持刮刮卡
function event_url($addon_title, $id = '0') {
	$map ['token'] = get_token ();
	$map ['addon_condition'] = array (
			'exp',
			"='[{$addon_title}:*]' or addon_condition='[{$addon_title}:{$id}]'" 
	);
	
	$event = M ( 'Scratch' )->where ( $map )->order ( 'id desc' )->find ();
	$event_url = '';
	if ($event) {
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		$param ['id'] = $event ['id'];
		$event_url = addons_url ( 'Scratch://Scratch/show', $param );
	}
	return $event_url;
}
// 抽奖或者优惠券领取的插件条件判断
function addon_condition_check($addon_condition) {
	if (empty ( $addon_condition ))
		return true;
	preg_match_all ( "/\[([\s\S]*):([\*,\d]*)\]/i", $addon_condition, $match );
	if (empty ( $match [1] [0] ) || empty ( $match [2] [0] )) {
		return true;
	}
	
	$conditon ['token'] = get_token ();
	$conditon ['uid'] = get_mid ();
	switch ($match [1] [0]) {
		case '投票' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['vote_id'] = $match [2] [0];
			$conditon ['user_id'] = get_mid ();
			unset ( $conditon ['uid'] );
			$res = M ( 'vote_log' )->where ( $conditon )->find ();
			break;
		case '通用表单' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['forms_id'] = $match [2] [0];
			$res = M ( 'forms_value' )->where ( $conditon )->find ();
			break;
		case '微考试' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['exam_id'] = $match [2] [0];
			$res = M ( 'exam_answer' )->where ( $conditon )->find ();
			break;
		case '微测试' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['test_id'] = $match [2] [0];
			$res = M ( 'test_answer' )->where ( $conditon )->find ();
			break;
		case '微调研' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['survey_id'] = $match [2] [0];
			$res = M ( 'survey_answer' )->where ( $conditon )->find ();
			break;
		default :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['id'] = $match [2] [0];
			$res = M ( $match [1] [0] )->where ( $conditon )->find ();
	}
	// dump ( $res );
	// dump ( M ()->getLastSql () );
	
	return ! empty ( $res );
}
// 抽奖或者优惠券领取的插件条件提示
function condition_tips($addon_condition) {
	if (empty ( $addon_condition ))
		return '';
	
	preg_match_all ( "/\[([\s\S]*):([\*,\d]*)\]/i", $addon_condition, $match );
	if (empty ( $match [1] [0] ) || empty ( $match [2] [0] )) {
		return '';
	}
	
	$conditon ['token'] = get_token ();
	$conditon ['id'] = $match [2] [0];
	$title = '';
	$has_title = $conditon ['id'] != '*' && $conditon ['id'] > 0;
	
	switch ($match [1] [0]) {
		case '投票' :
			$has_title && $title = M ( 'vote' )->where ( $conditon )->getField ( 'title' );
			break;
		case '通用表单' :
			$has_title && $title = M ( 'forms' )->where ( $conditon )->getField ( 'title' );
			break;
		case '微考试' :
			$has_title && $title = M ( 'exam' )->where ( $conditon )->getField ( 'title' );
			break;
		case '微测试' :
			$has_title && $title = M ( 'test' )->where ( $conditon )->getField ( 'title' );
			break;
		case '微调研' :
			$has_title && $title = M ( 'survey' )->where ( $conditon )->getField ( 'title' );
			break;
		default :
			$has_title && $title = M ( $match [1] [0] )->where ( $conditon )->getField ( 'title' );
	}
	$result = '需要参与' . $title . $match [1] [0] . '后才能领取';
	
	return $result;
}
function lastsql() {
	dump ( M ()->getLastSql () );
}
// 商业代码解密
function code_decode($text) {
	$key = substr ( C ( 'WEIPHP_STORE_LICENSE' ), 0, 5 );
	return think_decrypt ( $text, $key );
}
function outExcel($dataArr, $fileName = '', $sheet = false) {
	require_once VENDOR_PATH . 'download-xlsx.php';
	export_csv ( $dataArr, $fileName, $sheet );
	unset ( $sheet );
	unset ( $dataArr );
}
// 获取通用分类表的分类标题
function category_title($cate_id) {
	static $_category_title = array ();
	if (isset ( $_category_title [$cate_id] )) {
		return $_category_title [$cate_id];
	}
	
	$map ['token'] = get_token ();
	$list = M ( 'common_category' )->where ( $map )->field ( 'id,title' )->select ();
	foreach ( $list as $v ) {
		$_category_title [$v ['id']] = $v ['title'];
	}
	if (! isset ( $_category_title [$cate_id] )) {
		$_category_title [$cate_id] = '';
	}
	return $_category_title [$cate_id];
}
function get_lecturer_name($lecturer_id) {
	static $_lecturer_name = array ();
	if (isset ( $_lecturer_name [$lecturer_id] )) {
		return $_lecturer_name [$lecturer_id];
	}
	
	$map ['token'] = get_token ();
	$list = M ( 'classes_lecturer' )->where ( $map )->field ( 'id,name' )->select ();
	foreach ( $list as $v ) {
		$_lecturer_name [$v ['id']] = $v ['name'];
	}
	if (! isset ( $_lecturer_name [$lecturer_id] )) {
		$_lecturer_name [$lecturer_id] = '';
	}
	return $_lecturer_name [$lecturer_id];
}
function check_token_purview($table, $id, $field = 'token') {
	$token = get_token ();
	$map ['id'] = $id;
	$info = M ( $table )->where ( $map )->field ( $field )->find ();
	if ($info === false || $info [$field] == $token)
		return true; // 没有这个字段或者没有这个记录直接返回
	
	exit ( '非法访问' );
}
// weiphp专用分割函数，同时支持常见的按空格、逗号、分号、换行进行分割
function wp_explode($string, $delimiter = "\s,;\r\n") {
	if (empty ( $string ))
		return array ();
		
		// 转换中文符号
		// $string = iconv ( 'utf-8', 'gbk', $string );
		// $string = preg_replace ( '/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)', $string );
		// $string = iconv ( 'gbk', 'utf-8', $string );
	
	$arr = preg_split ( '/[' . $delimiter . ']+/', $string );
	return array_unique ( array_filter ( $arr ) );
}
function get_code_img($qr_code) {
	if (! $qr_code)
		return '';
	
	$html = '<img src="' . $qr_code . '" width="50" height="50" />';
	return $html;
}
function get_file_title($attach_ids) {
	$ids = wp_explode ( $attach_ids );
	if (empty ( $ids ))
		return '';
	
	$map ['id'] = array (
			'in',
			$ids 
	);
	$names = M ( 'file' )->where ( $map )->getFields ( 'name' );
	
	return implode ( ', ', $names );
}
// 阿拉伯数字转中文表述，如101转成一百零一
function num2cn($number) {
	$number = intval ( $number );
	$capnum = array (
			"零",
			"一",
			"二",
			"三",
			"四",
			"五",
			"六",
			"七",
			"八",
			"九" 
	);
	$capdigit = array (
			"",
			"十",
			"百",
			"千",
			"万" 
	);
	
	$data_arr = str_split ( $number );
	$count = count ( $data_arr );
	for($i = 0; $i < $count; $i ++) {
		$d = $capnum [$data_arr [$i]];
		$arr [] = $d != '零' ? $d . $capdigit [$count - $i - 1] : $d;
	}
	$cncap = implode ( "", $arr );
	
	$cncap = preg_replace ( "/(零)+/", "0", $cncap ); // 合并连续“零”
	$cncap = trim ( $cncap, '0' );
	$cncap = str_replace ( "0", "零", $cncap ); // 合并连续“零”
	$cncap == '一十' && $cncap = '十';
	$cncap == '' && $cncap = '零';
	// echo ( $data.' : '.$cncap.' <br/>' );
	return $cncap;
}
function week_name($number = null) {
	if ($number === null)
		$number = date ( 'w' );
	
	$arr = array (
			"日",
			"一",
			"二",
			"三",
			"四",
			"五",
			"六" 
	);
	
	return '星期' . $arr [$number];
}
// 日期转换成星期几
function daytoweek($day = null) {
	$day === null && $day = date ( 'Y-m-d' );
	if (empty ( $day ))
		return '';
	
	$number = date ( 'w', strtotime ( $day ) );
	
	return week_name ( $number );
}
/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map
 *        	映射关系二维数组 array(
 *        	'字段名1'=>array(映射关系数组),
 *        	'字段名2'=>array(映射关系数组),
 *        	......
 *        	)
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array array(
 *         array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *         ....
 *         )
 *        
 */
function int_to_string(&$data, $map = array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
	if ($data === false || $data === null) {
		return $data;
	}
	$data = ( array ) $data;
	foreach ( $data as $key => $row ) {
		foreach ( $map as $col => $pair ) {
			if (isset ( $row [$col] ) && isset ( $pair [$row [$col]] )) {
				$data [$key] [$col . '_text'] = $pair [$row [$col]];
			}
		}
	}
	return $data;
}
function importFormExcel($attach_id, $column, $dateColumn = array()) {
	$res = array (
			'status' => 0,
			'data' => '' 
	);
	if (empty ( $attach_id ) || ! is_numeric ( $attach_id )) {
		$res ['data'] = '上传文件ID无效！';
		return $res;
	}
	$file = M ( 'file' )->where ( 'id=' . $attach_id )->find ();
	$root = C ( 'DOWNLOAD_UPLOAD.rootPath' );
	$filename = SITE_PATH . '/Uploads/Download/' . $file ['savepath'] . $file ['savename'];
	// dump($filename);
	if (! file_exists ( $filename )) {
		$res ['data'] = '上传的文件失败';
		return $res;
	}
	$extend = $file ['ext'];
	if (! ($extend == 'xls' || $extend == 'xlsx' || $extend == 'csv')) {
		$res ['data'] = '文件格式不对，请上传xls,xlsx格式的文件';
		return $res;
	}
	
	vendor ( 'PHPExcel' );
	vendor ( 'PHPExcel.PHPExcel_IOFactory' );
	vendor ( 'PHPExcel.Reader.Excel5' );
	
	switch (strtolower ( $extend )) {
		case 'csv' :
			$format = 'CSV';
			$objReader = \PHPExcel_IOFactory::createReader ( $format )->setDelimiter ( ',' )->setInputEncoding ( 'GBK' )->setEnclosure ( '"' )->setLineEnding ( "\r\n" )->setSheetIndex ( 0 );
			break;
		case 'xls' :
			$format = 'Excel5';
			$objReader = \PHPExcel_IOFactory::createReader ( $format );
			break;
		default :
			$format = 'excel2007';
			$objReader = \PHPExcel_IOFactory::createReader ( $format );
	}
	
	$objPHPExcel = $objReader->load ( $filename );
	$objPHPExcel->setActiveSheetIndex ( 0 );
	$sheet = $objPHPExcel->getSheet ( 0 );
	$highestRow = $sheet->getHighestRow (); // 取得总行数
	for($j = 2; $j <= $highestRow; $j ++) {
		$addData = array ();
		foreach ( $column as $k => $v ) {
			if ($dateColumn) {
				foreach ( $dateColumn as $d ) {
					if ($k == $d) {
						$addData [$v] = gmdate ( "Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP ( $objPHPExcel->getActiveSheet ()->getCell ( "$k$j" )->getValue () ) );
					} else {
						$addData [$v] = trim ( ( string ) $objPHPExcel->getActiveSheet ()->getCell ( $k . $j )->getValue () );
					}
				}
			} else {
				$addData [$v] = trim ( ( string ) $objPHPExcel->getActiveSheet ()->getCell ( $k . $j )->getValue () );
			}
		}
		
		$isempty = true;
		foreach ( $column as $v ) {
			$isempty && $isempty = empty ( $addData [$v] );
		}
		
		if (! $isempty)
			$result [$j] = $addData;
	}
	$res ['status'] = 1;
	$res ['data'] = $result;
	return $res;
}
function showNewIcon($time, $day = 3) {
	$img = '';
	if (NOW_TIME < ($time + 86400 * $day)) {
		$img = '<img src="' . C ( 'TMPL_PARSE_STRING.__IMG__' ) . '/new.png"/>';
	}
	return $img;
}
function replace_url($content) {
	$param ['token'] = get_token ();
	$param ['openid'] = get_openid ();
	
	$sreach = array (
			'[follow]',
			'[website]',
			'[token]',
			'[openid]' 
	);
	$replace = array (
			addons_url ( 'UserCenter://UserCenter/bind', $param ),
			addons_url ( 'WeiSite://WeiSite/index', $param ),
			$param ['token'],
			$param ['openid'] 
	);
	$content = str_replace ( $sreach, $replace, $content );
	
	return $content;
}
/**
 * 验证分类是否允许发布内容
 *
 * @param integer $id
 *        	分类ID
 * @return boolean true-允许发布内容，false-不允许发布内容
 */
function check_category($id) {
	if (is_array ( $id )) {
		$id ['type'] = ! empty ( $id ['type'] ) ? $id ['type'] : 2;
		$type = get_category ( $id ['category_id'], 'type' );
		$type = explode ( ",", $type );
		return in_array ( $id ['type'], $type );
	} else {
		$publish = get_category ( $id, 'allow_publish' );
		return $publish ? true : false;
	}
}

/**
 * 检测分类是否绑定了指定模型
 *
 * @param array $info
 *        	模型ID和分类ID数组
 * @return boolean true-绑定了模型，false-未绑定模型
 */
function check_category_model($info) {
	$cate = get_category ( $info ['category_id'] );
	$array = explode ( ',', $info ['pid'] ? $cate ['model_sub'] : $cate ['model'] );
	return in_array ( $info ['model_id'], $array );
}
// 获取随机的字符串，用于token，EncodingAESKey等的生成
function get_rand_char($length = 6) {
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$strLength = 61;
	
	for($i = 0; $i < $length; $i ++) {
		$res .= $str [rand ( 0, $strLength )];
	}
	
	return $res;
}
/**
 * 根据两点间的经纬度计算距离
 *
 * @param float $lat
 *        	纬度值
 * @param float $lng
 *        	经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
	$earthRadius = 6367000; // approximate radius of earth in meters
	                        
	// Convert these degrees to radians to work with the formula
	$lat1 = ($lat1 * pi ()) / 180;
	$lng1 = ($lng1 * pi ()) / 180;
	
	$lat2 = ($lat2 * pi ()) / 180;
	$lng2 = ($lng2 * pi ()) / 180;
	
	// Using the Haversine formula http://en.wikipedia.org/wiki/Haversine_formula calculate the distance
	
	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow ( sin ( $calcLatitude / 2 ), 2 ) + cos ( $lat1 ) * cos ( $lat2 ) * pow ( sin ( $calcLongitude / 2 ), 2 );
	$stepTwo = 2 * asin ( min ( 1, sqrt ( $stepOne ) ) );
	$calculatedDistance = $earthRadius * $stepTwo;
	
	return round ( $calculatedDistance );
}
function getMyDistance($shopGPS) {
	$arr = explode ( ',', $shopGPS );
	if (empty ( $arr [0] ) || empty ( $arr [1] ) || ! empty ( $_SESSION ['my_location_' . $GLOBALS ['mid']] ))
		return ''; // 无法计算
	
	$my = explode ( ',', $_SESSION ['my_location_' . $GLOBALS ['mid']] );
	return getDistance ( $arr [0], $arr [1], $my [0], $my [1] );
}
function GPS2Address($location) {
	$url = 'http://api.map.baidu.com/geocoder/v2/?ak=' . BAIDU_GPS_AK . '&coordtype=wgs84ll&location=' . $location . '&output=json&pois=0';
	$res = wp_file_get_contents ( $url );
	// dump ( $url );
	$res = json_decode ( $res, true );
	// dump ( $res );
	return $res ['result'] ['formatted_address'];
}
function xml_to_array($xml) {
	$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
	if (preg_match_all ( $reg, $xml, $matches )) {
		$count = count ( $matches [0] );
		$arr = array ();
		for($i = 0; $i < $count; $i ++) {
			$key = $matches [1] [$i];
			$val = xml_to_array ( $matches [2] [$i] ); // 递归
			if (array_key_exists ( $key, $arr )) {
				if (is_array ( $arr [$key] )) {
					if (! array_key_exists ( 0, $arr [$key] )) {
						$arr [$key] = array (
								$arr [$key] 
						);
					}
				} else {
					$arr [$key] = array (
							$arr [$key] 
					);
				}
				$arr [$key] [] = $val;
			} else {
				$arr [$key] = $val;
			}
		}
		return $arr;
	} else {
		return $xml;
	}
}
// Xml 转 数组, 不包括根键
function xmltoarray($xml) {
	$arr = xml_to_array ( $xml );
	$key = array_keys ( $arr );
	return $arr [$key [0]];
}

/**
 * ************************************************************
 *
 * 使用特定function对数组中所有元素做处理
 *
 * @param
 *        	string &$array 要处理的字符串
 * @param string $function
 *        	要执行的函数
 * @return boolean $apply_to_keys_also 是否也应用到key上
 * @access public
 *        
 *         ***********************************************************
 */
function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
	static $recursive_counter = 0;
	if (++ $recursive_counter > 1000) {
		die ( 'possible deep recursion attack' );
	}
	foreach ( $array as $key => $value ) {
		if (is_array ( $value )) {
			arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
		} else {
			$array [$key] = $function ( $value );
		}
		
		if ($apply_to_keys_also && is_string ( $key )) {
			$new_key = $function ( $key );
			if ($new_key != $key) {
				$array [$new_key] = $array [$key];
				unset ( $array [$key] );
			}
		}
	}
	$recursive_counter --;
}

/**
 * ************************************************************
 *
 * 将数组转换为JSON字符串（兼容中文）
 *
 * @param array $array
 *        	要转换的数组
 * @return string 转换得到的json字符串
 * @access public
 *        
 *         ***********************************************************
 */
function JSON($array) {
	arrayRecursive ( $array, 'urlencode', true );
	$json = json_encode ( $array );
	return urldecode ( $json );
}
/**
 * 短链接功能
 *
 * @param float $long_url
 *        	长链接
 * @return string 如果没有微信短链接接口权限或者不成功，就原样返回长链接，否则返回短链接
 */
function short_url($long_url) {
	$access_token = get_access_token ( $token );
	if (empty ( $access_token )) {
		return $long_url;
	}
	
	$url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $access_token;
	
	$data ['action'] = 'long2short';
	$data ['long_url'] = $long_url;
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$res = curl_exec ( $ch );
	curl_close ( $ch );
	$res = json_decode ( $res, true );
	if ($res ['errcode'] == 0) {
		return $res ['short_url'];
	} else {
		return $long_url;
	}
}
function makeKeyVal($list, $val = 'title', $key = 'id') {
	foreach ( $list as $v ) {
		$arr [$v [$key]] = $v [$val];
	}
	return $arr;
}
/**
 * 检查是否是以手机浏览器进入(IN_MOBILE)
 */
function isMobile() {
	$mobile = array ();
	static $mobilebrowser_list = 'Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
	// note 获取手机浏览器
	if (preg_match ( "/$mobilebrowser_list/i", $_SERVER ['HTTP_USER_AGENT'], $mobile )) {
		return true;
	} else {
		if (preg_match ( '/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER ['HTTP_USER_AGENT'] )) {
			return false;
		} else {
			if ($_GET ['mobile'] === 'yes') {
				return true;
			} else {
				return false;
			}
		}
	}
}
function isiPhone() {
	return strpos ( $_SERVER ['HTTP_USER_AGENT'], 'iPhone' ) !== false;
}
function isiPad() {
	return strpos ( $_SERVER ['HTTP_USER_AGENT'], 'iPad' ) !== false;
}
function isiOS() {
	return isiPhone () || isiPad ();
}
function isAndroid() {
	return strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Android' ) !== false;
}

// 通过服务号获取用户UID
function get_uid_by_openid($init = true) {
	$info = get_token_appinfo ();
	$openid = get_openid ();
	if (! $openid) {
		return 0;
	}
	
	$map ['openid'] = $openid;
	$map ['token'] = $info ['token'];
	$uid = M ( 'public_follow' )->where ( $map )->getField ( 'uid' );
	
	if ($uid) {
		return $uid;
	}
	
	if (! $init)
		return 0;
		
		// 不存在就初始化
	$uid = D ( 'Common/Follow' )->init_follow ( $openid, $map ['token'] );
	return $uid;
}
/**
 * 用SHA1算法生成安全签名
 */
function getSHA1($array) {
	// 排序
	sort ( $array, SORT_STRING );
	$str = implode ( $array );
	return sha1 ( $str );
}
function getModelByName($name) {
	$name = parse_name ( $name );
	$key = 'getModelByName_' . $name;
	$model = S ( $key );
	if (! $model) {
		$model = M ( 'model' )->getByName ( $name );
		S ( $key, $model, 86400 );
	}
	
	return $model;
}
// 复制目录，目前用于生成素材
function copydir($strSrcDir, $strDstDir) {
	$dir = opendir ( $strSrcDir );
	if (! $dir) {
		return false;
	}
	if (! is_dir ( $strDstDir )) {
		if (! mkdir ( $strDstDir )) {
			return false;
		}
	}
	while ( false !== ($file = readdir ( $dir )) ) {
		if ($file == '.' || $file == '..' || $file == '.svn' || $file == '.DS_Store' || $file == '__MACOSX' || $file == 'Thumbs.db' || $file == 'Thumbs.db' || $file == 'info.php') {
			continue;
		}
		if (is_dir ( $strSrcDir . '/' . $file )) {
			if (! copydir ( $strSrcDir . '/' . $file, $strDstDir . '/' . $file )) {
				return false;
			}
		} else {
			if (! copy ( $strSrcDir . '/' . $file, $strDstDir . '/' . $file )) {
				return false;
			}
		}
	}
	closedir ( $dir );
	return true;
}
/**
 * 获取插件的配置数组
 */
function getAddonConfig($name, $token = '') {
	static $_config = array ();
	if (isset ( $_config [$name] )) {
		return $_config [$name];
	}
	$config = array ();
	
	$token = empty ( $token ) ? get_token () : $token;
	// dump($token);
	if (! empty ( $token )) {
		$addon_config = get_token_appinfo ( $token, 'addon_config' );
		$addon_config = json_decode ( $addon_config, true );
		if (isset ( $addon_config [$name] ))
			$config = $addon_config [$name];
	}
	if (empty ( $config )) {
		$config = D ( 'Home/Addons' )->getInfoByName ( $name );
		$config = json_decode ( $config ['config'], true );
	}
	if (! $config) {
		$temp_arr = include_once ONETHINK_ADDON_PATH . $name . '/config.php';
		foreach ( $temp_arr as $key => $value ) {
			$config [$key] = $temp_arr [$key] ['value'];
		}
	}
	$_config [$name] = $config;
	return $config;
}
function getPluginConfig($name, $token = '') {
	static $_config = array ();
	if (isset ( $_config [$name] )) {
		return $_config [$name];
	}
	
	$config = array ();
	
	$token = empty ( $token ) ? get_token () : $token;
	// dump($token);
	if (! empty ( $token )) {
		$addon_config = get_token_appinfo ( $token, 'addon_config' );
		$addon_config = json_decode ( $addon_config, true );
		if (isset ( $addon_config [$name] ))
			$config = $addon_config [$name];
	}
	if (empty ( $config )) {
		$config = D ( 'Home/Plugins' )->getInfoByName ( $name );
		$config = json_decode ( $config ['config'], true );
	}
	if (! $config) {
		$temp_arr = include_once ONETHINK_PLUGIN_PATH . $name . '/config.php';
		foreach ( $temp_arr as $key => $value ) {
			$config [$key] = $temp_arr [$key] ['value'];
		}
	}
	$_config [$name] = $config;
	return $config;
}
// 删除目录及目录下的所有子目录和文件
function rmdirr($dirname) {
	if (! file_exists ( $dirname )) {
		return false;
	}
	if (is_file ( $dirname ) || is_link ( $dirname )) {
		return unlink ( $dirname );
	}
	$dir = dir ( $dirname );
	if ($dir) {
		while ( false !== $entry = $dir->read () ) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			rmdirr ( $dirname . DIRECTORY_SEPARATOR . $entry );
		}
	}
	$dir->close ();
	return rmdir ( $dirname );
}
function wp_money_format($number, $decimals = '2') {
	return number_format ( $number, $decimals );
}

// 以POST方式提交数据
function post_data($url, $param, $is_file = false, $return_array = true) {
	if (! $is_file && is_array ( $param )) {
		$param = JSON ( $param );
	}
	if ($is_file) {
		$header [] = "content-type: multipart/form-data; charset=UTF-8";
	} else {
		$header [] = "content-type: application/json; charset=UTF-8";
	}
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
	curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false);
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
	curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$res = curl_exec ( $ch );
	
	$flat = curl_errno ( $ch );
	if ($flat) {
		$data = curl_error ( $ch );
		addWeixinLog ( $flat, 'post_data flat' );
		addWeixinLog ( $data, 'post_data msg' );
	}
	
	curl_close ( $ch );
	
	$return_array && $res = json_decode ( $res, true );
	
	return $res;
}
// 生成签名
function make_sign($paraMap = array(), $partner_key = '') {
	$buff = "";
	ksort ( $paraMap );
	$paraMap ['key'] = $partner_key;
	foreach ( $paraMap as $k => $v ) {
		if (null != $v && "null" != $v && '' != $v && "sign" != $k) {
			$buff .= strtolower ( $k ) . "=" . $v . "&";
		}
	}
	$reqPar;
	if (strlen ( $buff ) > 0) {
		$reqPar = substr ( $buff, 0, strlen ( $buff ) - 1 );
	}
	return strtoupper ( md5 ( $reqPar ) );
}
// 更具目录获取素材信息
function get_sucai_template_info($template = "default", $addons) {
	$dir = SITE_PATH . '/SucaiTemplate/' . $addons;
	if (file_exists ( $dir . '/' . $template . '/info.php' )) {
		$info = require_once $dir . '/' . $template . '/info.php';
		return $info;
		exit ();
	}
	return NULL;
}
// //////靓妆//////////
/*
 * 分时分段的抽奖算法 prizeArr 奖品数据，结构是：array(array(prize_id=>1,prize_num=>2),array(prize_id=>2,prize_num=>4),...); start_time 开始的时间戳 end_time 结束的时间戳 event_id 抽奖的活动ID，以确保每个抽奖不冲突
 */
function get_lottery1($prizeArr, $start_time, $end_time, $event_id = 0, $uid = 0, $update = false, $token = '') {
	if (empty ( $uid ) && $token) {
		// $uid = get_mid ();
		$key = 'function_lottery_' . $token . '_' . $event_id;
	}
	if (! empty ( $uid )) {
		$key = 'function_lottery_' . $uid . '_' . $event_id;
	}
	$res = S ( $key );
	if ($res === false || $update) {
		$res = false;
		// 奖品ID组处理
		$count = 0;
		foreach ( $prizeArr as $p ) {
			for($i = 0; $i < $p ['prize_num']; $i ++) {
				$rand [] = $p ['prize_id'];
				$count += 1;
			}
		}
		// 奖品ID排序随机
		shuffle ( $rand );
		
		// 时间分段
		$start_time < NOW_TIME && $start_time = NOW_TIME; // 如果活动已经开始，以当前时间为开始时间
		
		$total_time = $end_time - $start_time;
		
		$section_time = floor ( $total_time / $count );
		
		// 生成中奖的数组 结构是：array('中奖的随机时间点'=>'中奖的奖品ID');
		$stime = $start_time;
		for($j = 0; $j < $count; $j ++) {
			$etime = $stime + $section_time;
			$index = rand ( $stime, $etime );
			$res [$index] = $rand [$j];
			
			$stime = $etime;
		}
		S ( $key, $res );
	}
	
	return $res;
}
function del_lottery1($index, $event_id = 0, $uid = 0, $token = '') {
	if (empty ( $uid ) && $token) {
		$key = 'function_lottery_' . $token . '_' . $event_id;
	}
	if (! empty ( $uid )) {
		$key = 'function_lottery_' . $uid . '_' . $event_id;
	}
	
	$res = S ( $key );
	if ($res === false)
		return false;
	
	unset ( $res [$index] );
	S ( $key, $res );
}
// ////////////sports//////////////
/*
 * 分时分段的抽奖算法 prizeArr 奖品数据，结构是：array(array(prize_id=>1,prize_num=>2),array(prize_id=>2,prize_num=>4),...); start_time 开始的时间戳 end_time 结束的时间戳 event_id 抽奖的活动ID，以确保每个抽奖不冲突
 */
function get_lottery($prizeArr, $start_time, $end_time, $event_id = 0, $update = false) {
	$key = 'function_lottery_' . $event_id;
	$res = S ( $key );
	if ($res === false || $update) {
		$res = false;
		// 奖品ID组处理
		$count_shiwu = 0;
		foreach ( $prizeArr ['shiwu'] as $p ) {
			for($i = 0; $i < $p ['prize_num']; $i ++) {
				$rand_shiwu [] = $p ['prize_id'];
				$count_shiwu += 1;
			}
		}
		// $count_xuni = 0;
		// foreach ( $prizeArr ['xuni'] as $p ) {
		// for($i = 0; $i < $p ['prize_num']; $i ++) {
		// $rand_xuni [] = $p ['prize_id'];
		// $count_xuni += 1;
		// }
		// }
		// 奖品ID排序随机
		shuffle ( $rand_shiwu );
		// shuffle ( $rand_xuni );
		
		// 时间分段
		$start_time < NOW_TIME && $start_time = NOW_TIME; // 如果活动已经开始，以当前时间为开始时间
		
		$total_time = $end_time - $start_time;
		$section_time = floor ( $total_time / $count_shiwu );
		// 生成中奖的数组 结构是：array('中奖的随机时间点'=>'中奖的奖品ID');
		$stime = $start_time;
		for($j = 0; $j < $count_shiwu; $j ++) {
			
			$etime = $stime + $section_time;
			// $index = rand ( $stime, $etime );
			// $res [$index] = $rand_shiwu [$j];
			$shiwu_prize [$etime] = $rand_shiwu [$j];
			$stime = $etime;
		}
		$res ['shiwu'] = $shiwu_prize;
		// $res ['xuni'] = $rand_xuni;
		S ( $key, $res );
	}
	
	return $res;
}
// 根据概率随机抽取积分奖品
function get_jifen_lottery($proArr) {
	$allPro = array_sum ( $proArr );
	$result = 0;
	foreach ( $proArr as $k => $p ) {
		$randNum = mt_rand ( 1, $allPro );
		if ($randNum <= $p) {
			$result = $k;
		} else {
			$allPro -= $p;
		}
	}
	unset ( $proArr );
	return $result;
}
function del_lottery($index, $event_id = 0, $delkeyname = 'shiwu') {
	$key = 'function_lottery_' . $event_id;
	$res = S ( $key );
	if ($res === false)
		return false;
	
	unset ( $res [$delkeyname] [$index] );
	S ( $key, $res );
}
function lists_msubstr($str) {
	return msubstr ( $str, 0, 30 );
}
function parseComment($comment, $file = 'lzwg', $width = '40') {
	preg_match_all ( '/\[[a-zA-Z0-9]+\]/', $comment, $res );
	$faceData = $res [0];
	if (count ( $faceData ) != 0) {
		foreach ( $faceData as $v ) {
			$faceName = substr ( $v, 1, strlen ( $v ) - 2 );
			$faceUrl = '<img width=' . $width . ' src="' . SITE_URL . '/Public/static/face/' . $file . '/' . $faceName . '.png"/>';
			$replaceArr [$v] = $faceUrl;
		}
	}
	if ($replaceArr) {
		$comment = strtr ( $comment, $replaceArr );
	}
	return $comment;
}
/**
 * 系统非常规MD5加密方法
 *
 * @param string $str
 *        	要加密的字符串
 * @return string
 */
function think_weiphp_md5($str, $key = '') {
	empty ( $key ) && $key = C ( 'DATA_AUTH_KEY' );
	return '' === $str ? '' : md5 ( sha1 ( $str ) . $key );
}
/**
 * 检测验证码
 *
 * @param integer $id
 *        	验证码ID
 * @return boolean 检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
	$verify = new \Think\Verify ();
	return $verify->check ( $code, $id );
}
// 权限检查
function checkRule($rule = '', $uid = '') {
	static $Auth = null;
	if (! $Auth) {
		$Auth = new \Think\Auth ();
	}
	return $Auth->checkRule ( $rule, $uid );
}
// 微信端的错误码转中文解释
function error_msg($return, $more_tips = '') {
	$msg = array (
			'-1' => '系统繁忙，此时请开发者稍候再试',
			'0' => '请求成功',
			'40001' => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
			'40002' => '不合法的凭证类型',
			'40003' => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
			'40004' => '不合法的媒体文件类型',
			'40005' => '不合法的文件类型',
			'40006' => '不合法的文件大小',
			'40007' => '不合法的媒体文件id',
			'40008' => '不合法的消息类型',
			'40009' => '不合法的图片文件大小',
			'40010' => '不合法的语音文件大小',
			'40011' => '不合法的视频文件大小',
			'40012' => '不合法的缩略图文件大小',
			'40013' => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
			'40014' => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
			'40015' => '不合法的菜单类型',
			'40016' => '不合法的按钮个数',
			'40017' => '不合法的按钮个数',
			'40018' => '不合法的按钮名字长度',
			'40019' => '不合法的按钮KEY长度',
			'40020' => '不合法的按钮URL长度',
			'40021' => '不合法的菜单版本号',
			'40022' => '不合法的子菜单级数',
			'40023' => '不合法的子菜单按钮个数',
			'40024' => '不合法的子菜单按钮类型',
			'40025' => '不合法的子菜单按钮名字长度',
			'40026' => '不合法的子菜单按钮KEY长度',
			'40027' => '不合法的子菜单按钮URL长度',
			'40028' => '不合法的自定义菜单使用用户',
			'40029' => '不合法的oauth_code',
			'40030' => '不合法的refresh_token',
			'40031' => '不合法的openid列表',
			'40032' => '不合法的openid列表长度',
			'40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
			'40035' => '不合法的参数',
			'40038' => '不合法的请求格式',
			'40039' => '不合法的URL长度',
			'40050' => '不合法的分组id',
			'40051' => '分组名字不合法',
			'40117' => '分组名字不合法',
			'40118' => 'media_id大小不合法',
			'40119' => 'button类型错误',
			'40120' => 'button类型错误',
			'40121' => '不合法的media_id类型',
			'40132' => '微信号不合法',
			'40137' => '不支持的图片格式',
			'41001' => '缺少access_token参数',
			'41002' => '缺少appid参数',
			'41003' => '缺少refresh_token参数',
			'41004' => '缺少secret参数',
			'41005' => '缺少多媒体文件数据',
			'41006' => '缺少media_id参数',
			'41007' => '缺少子菜单数据',
			'41008' => '缺少oauth code',
			'41009' => '缺少openid',
			'42001' => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
			'42002' => 'refresh_token超时',
			'42003' => 'oauth_code超时',
			'43001' => '需要GET请求',
			'43002' => '需要POST请求',
			'43003' => '需要HTTPS请求',
			'43004' => '需要接收者关注',
			'43005' => '需要好友关系',
			'44001' => '多媒体文件为空',
			'44002' => 'POST的数据包为空',
			'44003' => '图文消息内容为空',
			'44004' => '文本消息内容为空',
			'45001' => '多媒体文件大小超过限制',
			'45002' => '消息内容超过限制',
			'45003' => '标题字段超过限制',
			'45004' => '描述字段超过限制',
			'45005' => '链接字段超过限制',
			'45006' => '图片链接字段超过限制',
			'45007' => '语音播放时间超过限制',
			'45008' => '图文消息超过限制',
			'45009' => '接口调用超过限制',
			'45010' => '创建菜单个数超过限制',
			'45015' => '回复时间超过限制',
			'45016' => '系统分组，不允许修改',
			'45017' => '分组名字过长',
			'45018' => '分组数量超过上限',
			'46001' => '不存在媒体数据',
			'46002' => '不存在的菜单版本',
			'46003' => '不存在的菜单数据',
			'46004' => '不存在的用户',
			'47001' => '解析JSON/XML内容错误',
			'48001' => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
			'50001' => '用户未授权该api',
			'50002' => '用户受限，可能是违规后接口被封禁',
			'61451' => '参数错误(invalid parameter)',
			'61452' => '无效客服账号(invalid kf_account)',
			'61453' => '客服帐号已存在(kf_account exsited)',
			'61454' => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid kf_acount length)',
			'61455' => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in kf_account)',
			'61456' => '客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
			'61457' => '无效头像文件类型(invalid file type)',
			'61450' => '系统错误(system error)',
			'61500' => '日期格式错误',
			'61501' => '日期范围错误',
			'9001001' => 'POST数据参数不合法',
			'9001002' => '远端服务不可用',
			'9001003' => 'Ticket不合法',
			'9001004' => '获取摇周边用户信息失败',
			'9001005' => '获取商户信息失败',
			'9001006' => '获取OpenID失败',
			'9001007' => '上传文件缺失',
			'9001008' => '上传素材的文件类型不合法',
			'9001009' => '上传素材的文件尺寸不合法',
			'9001010' => '上传失败',
			'9001020' => '帐号不合法',
			'9001021' => '已有设备激活率低于50%，不能新增设备',
			'9001022' => '设备申请数不合法，必须为大于0的数字',
			'9001023' => '已存在审核中的设备ID申请',
			'9001024' => '一次查询设备ID数量不能超过50',
			'9001025' => '设备ID不合法',
			'9001026' => '页面ID不合法',
			'9001027' => '页面参数不合法',
			'9001028' => '一次删除页面ID数量不能超过10',
			'9001029' => '页面已应用在设备中，请先解除应用关系再删除',
			'9001030' => '一次查询页面ID数量不能超过50',
			'9001031' => '时间区间不合法',
			'9001032' => '保存设备与页面的绑定关系参数错误',
			'9001033' => '门店ID不合法',
			'9001034' => '设备备注信息过长',
			'9001035' => '设备申请参数不合法',
			'9001036' => '查询起始值begin不合法' 
	);
	
	if ($more_tips) {
		$res = $more_tips . ': ';
	} else {
		$res = '';
	}
	if (isset ( $msg [$return ['errcode']] )) {
		$res .= $msg [$return ['errcode']];
	} else {
		$res .= $return ['errmsg'];
	}
	
	$res .= ', 返回码：' . $return ['errcode'];
	
	return $res;
}
/* yqx */
function virifylocal() {
	define ( 'VIRIFY', true );
	define ( 'HTML_VESION', 'index3_6' );
	C ( 'JS_VISION', 3.6 );
}
/**
 * 获取随机字符串
 *
 * @param number $length        	
 * @return string
 */
function createNonceStr($length = 16) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$str = "";
	for($i = 0; $i < $length; $i ++) {
		$str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
	}
	return $str;
}
function checkAllow_nums() {
	/*
	 * $_scene = M('scene'); $where['uid'] = intval(session('mid')); $where['delete_int'] =0; $count = $_scene->where($where)->count(); $allowNum=M('user')->where($where)->getField('allow_nums');
	 */
	$allowNum = 20;
	if ($count >= $allowNum) {
		echo '{"success":true,"code":1006,"msg":' . $allowNum . ',"obj":null,"map":null,"list":null}';
		exit ();
	}
	return true;
}
function randorderno($length = 10, $type = 0) {
	$arr = array (
			1 => "3425678934567892345678934567892",
			2 => "ABCDEFGHJKLMNPQRSTUVWXY" 
	);
	$code = '';
	if ($type == 0) {
		array_pop ( $arr );
		$string = implode ( "", $arr );
	} else if ($type == "-1") {
		$string = implode ( "", $arr );
	} else {
		$string = $arr [$type];
	}
	$count = strlen ( $string ) - 1;
	for($i = 0; $i < $length; $i ++) {
		$str [$i] = $string [rand ( 0, $count )];
		$code .= $str [$i];
	}
	return $code;
}
function aboutaa() {
	virifylocal ();
}
// 泛域名情况下的域名替换
function chang_domain($url, $domain) {
	if (! C ( 'DIV_DOMAIN' ) || is_numeric ( $domain ) || SITE_DOMAIN == 'localhost' || empty ( $domain ))
		return $url;
	
	$arr = explode ( '.', SITE_DOMAIN );
	if (count ( $arr ) < 3) { // 顶级域名
		$new_site_domain = $domain . '.' . SITE_DOMAIN;
	} else {
		$arr [0] = $domain;
		$new_site_domain = implode ( '.', $arr );
	}
	
	$url = str_replace ( SITE_DOMAIN, $new_site_domain, $url );
	
	return $url;
}
// 获取当前网址的顶级域名
function top_domain() {
	if (strpos ( SITE_DOMAIN, 'weiphp.cn' ) !== false) {
		return '.weiphp.cn';
	} else {
		return '.oftenchat.cn';
	}
}
// 处理带Emoji的数据，type=0表示写入数据库前的emoji转为HTML，为1时表示HTML转为emoji码
function deal_emoji($msg, $type = 1) {
	if ($type == 0) {
		$msg = json_encode ( $msg );
	} else {
		$txt = json_decode ( $msg );
		if ($txt !== null) {
			$msg = $txt;
		}
	}
	
	return $msg;
}
// 从微信下载临时图片文件
// $media_id 媒体文件ID
function down_media($media_id) {
	$savePath = SITE_PATH . '/Uploads/Picture/' . time_format ( NOW_TIME, 'Y-m-d' );
	mkdirs ( $savePath );
	// 获取图片URL
	$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . get_access_token () . '&media_id=' . $media_id;
	
	$picContent = outputCurl ( $url );
	$picjson = json_decode ( $picContent, true );
	if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
		return 0;
	}
	// if ($picContent) {
	$picName = NOW_TIME . '.jpg';
	$picPath = $savePath . '/' . $picName;
	$res = file_put_contents ( $picPath, $picContent );
	// }
	$cover_id = 0;
	if ($res) {
		// 保存记录，添加到picture表里，获取coverid
		$url = U ( 'File/uploadPicture', array (
				'session_id' => session_id () 
		) );
		$_FILES ['download'] = array (
				'name' => $picName,
				'type' => 'application/octet-stream',
				'tmp_name' => $picPath,
				'size' => $res,
				'error' => 0 
		);
		$Picture = D ( 'Picture' );
		$pic_driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		$info = $Picture->upload ( $_FILES, C ( 'PICTURE_UPLOAD' ), C ( 'PICTURE_UPLOAD_DRIVER' ), C ( "UPLOAD_{$pic_driver}_CONFIG" ) );
		$cover_id = $info ['download'] ['id'];
		unlink ( $picPath );
	}
	return $cover_id;
}
function outputCurl($url) {
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$output = curl_exec ( $ch );
	curl_close ( $ch );
	return $output;
}

// 上传多媒体文体
function upload_media($path, $type = 'image') {
	$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token ();
	$param ['type'] = $type;
	$param ['media'] = '@' . realpath ( $path );
	$res = post_data ( $url, $param, true );
	if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
		$this->error ( error_msg ( $res, '图片上传' ) );
		exit ();
	}
	return $res ['media_id'];
}
// 短信锁
function smsLock($tel, $time = 60) {
	
	// 锁定时间
	$locktime = $time;
	// 创建目录
	if (! is_dir ( SITE_PATH . '/Data/smslock/' ))
		mkdir ( SITE_PATH . '/Data/smslock/', 0777, true );
		// 锁文件
	$lockfile = SITE_PATH . '/Data/smslock/' . $tel . '.txt';
	// 文件验证时间间隔
	if (file_exists ( $lockfile )) {
		if ((time () - filemtime ( $lockfile )) < $locktime) {
			return false;
		} else {
			$k = fopen ( $lockfile, "a+" );
			fwrite ( $k, ',' . time () );
			fclose ( $k );
		}
	} else {
		touch ( $lockfile );
	}
	return true;
}
// 下载永久素材
function do_down_image($media_id, $picUrl = '') {
	$savePath = SITE_PATH . '/Uploads/Picture/' . time_format ( NOW_TIME, 'Y-m-d' );
	mkdirs ( $savePath );
	if (empty ( $picUrl )) {
		// 获取图片URL
		$url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token ();
		$param ['media_id'] = $media_id;
		$picContent = post_data ( $url, $param, false, false );
		$picjson = json_decode ( $picContent, true );
		if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
			// $this->error ( error_msg ( $picjson, '下载图片' ) );
			return 0;
			exit ();
		}
		// dump($picContent);
		// dump($picjson);
		// if ($picContent){
		$picName = NOW_TIME . '.jpg';
		$picPath = $savePath . '/' . $picName;
		$res = file_put_contents ( $picPath, $picContent );
		// }
	} else {
		$content = wp_file_get_contents ( $picUrl );
		// 获取图片扩展名
		$picExt = substr ( $picUrl, strrpos ( $picUrl, '=' ) + 1 );
		// $picExt=='jpeg'
		if (empty ( $picExt ) || $picExt == 'jpeg') {
			$picExt = 'jpg';
		}
		$picName = NOW_TIME . '.' . $picExt;
		$picPath = $savePath . '/' . $picName;
		$res = file_put_contents ( $picPath, $content );
		if (! $res) {
			// $this->error ( '远程图片下载失败' );
			// exit ();
			return 0;
			exit ();
		}
	}
	$cover_id = 0;
	if ($res) {
		// 保存记录，添加到picture表里，获取coverid
		$url = U ( 'File/uploadPicture', array (
				'session_id' => session_id () 
		) );
		$_FILES ['download'] = array (
				'name' => $picName,
				'type' => 'application/octet-stream',
				'tmp_name' => $picPath,
				'size' => $res,
				'error' => 0 
		);
		$Picture = D ( 'Picture' );
		$pic_driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		$info = $Picture->upload ( $_FILES, C ( 'PICTURE_UPLOAD' ), C ( 'PICTURE_UPLOAD_DRIVER' ), C ( "UPLOAD_{$pic_driver}_CONFIG" ) );
		$cover_id = $info ['download'] ['id'];
		unlink ( $picPath );
	}
	return $cover_id;
}
// 下载临时语言、视频素材
function down_file_media($media_id, $type = 'voice') {
	$savePath = SITE_PATH . '/Uploads/Download/' . time_format ( NOW_TIME, 'Y-m-d' );
	mkdirs ( $savePath );
	// 获取图片URL
	$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . get_access_token () . '&media_id=' . $media_id;
	$picContent = outputCurl ( $url );
	$picjson = json_decode ( $picContent, true );
	if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
		return 0;
	}
	if ($type == 'voice') {
		$ext = 'mp3';
	} else if ($type == 'video') {
		$ext = 'mp4';
	}
	if ($picContent) {
		$picName = NOW_TIME . '.' . $ext;
		$picPath = $savePath . '/' . $picName;
		$res = file_put_contents ( $picPath, $picContent );
	}
	$cover_id = 0;
	if ($res) {
		// 保存记录，添加到picture表里，获取coverid
		$_FILES ['download'] = array (
				'name' => $picName,
				'type' => 'application/octet-stream',
				'tmp_name' => $picPath,
				'size' => $res,
				'error' => 0 
		);
		$File = D ( 'Home/File' );
		$file_driver = C ( 'DOWNLOAD_UPLOAD_DRIVER' );
		$info = $File->upload ( $_FILES, C ( 'DOWNLOAD_UPLOAD' ), C ( 'DOWNLOAD_UPLOAD_DRIVER' ), C ( "UPLOAD_{$file_driver}_CONFIG" ) );
		$cover_id = $info ['download'] ['id'];
		unlink ( $picPath );
	}
	return $cover_id;
}