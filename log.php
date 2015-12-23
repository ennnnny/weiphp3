<?php
define ( 'SITE_PATH', dirname ( __FILE__ ) );
define ( 'SITE_DIR_NAME', str_replace ( '.', '_', pathinfo ( SITE_PATH, PATHINFO_BASENAME ) ) );
define ( 'RUNTIME_PATH', './Runtime/' );

date_default_timezone_set ( 'PRC' );
error_reporting ( E_ERROR );
// error_reporting ( E_ALL );
// ini_set ( 'display_errors', true );
/* ===================================== 配置部分 ========================================== */
$config = require SITE_PATH . '/Application/Common/Conf/config.php';

/* ===================================== 公共部分 ========================================== */
// 获取客户端IP地址
function getClientIp() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return addslashes ( $ip );
}
// 获取用户浏览器型号。新加浏览器，修改代码，增加特征字符串.把IE加到12.0 可以使用5-10年了.
function getBrower() {
	if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Maxthon' )) {
		$browser = 'Maxthon';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 12.0' )) {
		$browser = 'IE12.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 11.0' )) {
		$browser = 'IE11.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 10.0' )) {
		$browser = 'IE10.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 9.0' )) {
		$browser = 'IE9.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 8.0' )) {
		$browser = 'IE8.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 7.0' )) {
		$browser = 'IE7.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 6.0' )) {
		$browser = 'IE6.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'NetCaptor' )) {
		$browser = 'NetCaptor';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Netscape' )) {
		$browser = 'Netscape';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Lynx' )) {
		$browser = 'Lynx';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Opera' )) {
		$browser = 'Opera';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Chrome' )) {
		$browser = 'Google';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Firefox' )) {
		$browser = 'Firefox';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Safari' )) {
		$browser = 'Safari';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'iphone' ) || strpos ( $_SERVER ['HTTP_USER_AGENT'], 'ipod' )) {
		$browser = 'iphone';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'ipad' )) {
		$browser = 'iphone';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'android' )) {
		$browser = 'android';
	} else {
		$browser = 'other';
	}
	return addslashes ( $browser );
}
// 过滤非法html标签
function t($text) {
	// 过滤标签
	$text = nl2br ( $text );
	$text = real_strip_tags ( $text );
	$text = addslashes ( $text );
	$text = trim ( $text );
	return addslashes ( $text );
}
function safe($str, $allowable_tags = "") {
	$str = stripslashes ( htmlspecialchars_decode ( $str ) );
	return strip_tags ( $str, $allowable_tags );
}

// 浏览器友好的变量输出
function dump($var) {
	ob_start ();
	var_dump ( $var );
	$output = ob_get_clean ();
	if (! extension_loaded ( 'xdebug' )) {
		$output = preg_replace ( "/\]\=\>\n(\s+)/m", "] => ", $output );
		$output = '<pre style="text-align:left">' . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
	}
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo ($output);
}

// +----------------------------------------------------------------------
// | ThinkPHP 简洁模式数据库中间层实现类 只支持mysql
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.thinksns.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liuxiaoqing <liuxiaoqing@zhishisoft.com>
// +----------------------------------------------------------------------
//
class SimpleDB {
	private static $_instance = null;
	// 是否显示调试信息 如果启用会在知识文件记录sql语句
	public $debug = false;
	// 是否使用永久连接
	protected $pconnect = false;
	// 当前SQL指令
	protected $queryStr = '';
	// 最后插入ID
	protected $lastInsID = null;
	// 返回或者影响记录数
	protected $numRows = 0;
	// 返回字段数
	protected $numCols = 0;
	// 事务指令数
	protected $transTimes = 0;
	// 错误信息
	protected $error = '';
	// 当前连接ID
	protected $linkID = null;
	// 当前查询ID
	protected $queryID = null;
	// 是否已经连接数据库
	protected $connected = false;
	// 数据库连接参数配置
	protected $config = '';
	// SQL 执行时间记录
	protected $beginTime;
	/**
	 * +----------------------------------------------------------
	 * 架构函数
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @param array $config
	 *        	数据库配置数组
	 *        	+----------------------------------------------------------
	 */
	public function __construct($config = '') {
		if (! extension_loaded ( 'mysql' )) {
			echo ('not support mysql');
		}
		$this->config = $this->parseConfig ( $config );
	}
	
	/**
	 * +----------------------------------------------------------
	 * 连接数据库方法
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	public function connect() {
		if (! $this->connected) {
			$config = $this->config;
			// 处理不带端口号的socket连接情况
			$host = $config ['hostname'] . ($config ['hostport'] ? ":{$config['hostport']}" : '');
			if ($this->pconnect) {
				$this->linkID = mysql_pconnect ( $host, $config ['username'], $config ['password'] );
			} else {
				$this->linkID = mysql_connect ( $host, $config ['username'], $config ['password'], true );
			}
			if (! $this->linkID || (! empty ( $config ['database'] ) && ! mysql_select_db ( $config ['database'], $this->linkID ))) {
				echo (mysql_error ());
			}
			$dbVersion = mysql_get_server_info ( $this->linkID );
			if ($dbVersion >= "4.1") {
				// 使用UTF8存取数据库 需要mysql 4.1.0以上支持
				mysql_query ( "SET NAMES 'UTF8'", $this->linkID );
			}
			// 设置 sql_model
			if ($dbVersion > '5.0.1') {
				mysql_query ( "SET sql_mode=''", $this->linkID );
			}
			// 标记连接成功
			$this->connected = true;
			// 注销数据库连接配置信息
			unset ( $this->config );
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 释放查询结果
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 */
	public function free() {
		mysql_free_result ( $this->queryID );
		$this->queryID = 0;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 执行查询 主要针对 SELECT, SHOW 等指令
	 * 返回数据集
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @param string $str
	 *        	sql指令
	 *        	+----------------------------------------------------------
	 * @return mixed +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	public function query($str = '') {
		$this->connect ();
		if (! $this->linkID)
			return false;
		if ($str != '')
			$this->queryStr = $str;
			// 释放前次的查询结果
		if ($this->queryID) {
			$this->free ();
		}
		$this->Q ( 1 );
		$this->queryID = mysql_query ( $this->queryStr, $this->linkID );
		$this->debug ();
		if (! $this->queryID) {
			if ($this->debug)
				echo ($this->error ());
			else
				return false;
		} else {
			$this->numRows = mysql_num_rows ( $this->queryID );
			return $this->getAll ();
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 执行语句 针对 INSERT, UPDATE 以及DELETE
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @param string $str
	 *        	sql指令
	 *        	+----------------------------------------------------------
	 * @return integer +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	public function execute($str = '') {
		$this->connect ();
		if (! $this->linkID)
			return false;
		if ($str != '')
			$this->queryStr = $str;
			// 释放前次的查询结果
		if ($this->queryID) {
			$this->free ();
		}
		$this->W ( 1 );
		$result = mysql_query ( $this->queryStr, $this->linkID );
		$this->debug ();
		if (false === $result) {
			if ($this->debug)
				echo ($this->error ());
			else
				return false;
		} else {
			$this->numRows = mysql_affected_rows ( $this->linkID );
			$this->lastInsID = mysql_insert_id ( $this->linkID );
			return $this->numRows;
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 获得所有的查询数据
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @return array +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	public function getAll() {
		if (! $this->queryID) {
			echo ($this->error ());
			return false;
		}
		// 返回数据集
		$result = array ();
		if ($this->numRows > 0) {
			while ( $row = mysql_fetch_assoc ( $this->queryID ) ) {
				$result [] = $row;
			}
			mysql_data_seek ( $this->queryID, 0 );
		}
		return $result;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 关闭数据库
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @throws ThinkExecption +----------------------------------------------------------
	 */
	public function close() {
		if (! empty ( $this->queryID ))
			mysql_free_result ( $this->queryID );
		if ($this->linkID && ! mysql_close ( $this->linkID )) {
			echo ($this->error ());
		}
		$this->linkID = 0;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 数据库错误信息
	 * 并显示当前的SQL语句
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 */
	public function error() {
		$this->error = mysql_error ( $this->linkID );
		if ($this->queryStr != '') {
			$this->error .= "\n [ SQL语句 ] : " . $this->queryStr;
		}
		return $this->error;
	}
	
	/**
	 * +----------------------------------------------------------
	 * SQL指令安全过滤
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @param string $str
	 *        	SQL字符串
	 *        	+----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 */
	public function escape_string($str) {
		return mysql_escape_string ( $str );
	}
	
	/**
	 * +----------------------------------------------------------
	 * 析构方法
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 */
	public function __destruct() {
		// 关闭连接
		$this->close ();
	}
	
	/**
	 * +----------------------------------------------------------
	 * 取得数据库类实例
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @return mixed 返回数据库驱动类
	 *         +----------------------------------------------------------
	 */
	public static function getInstance($db_config = '') {
		if (self::$_instance == null) {
			self::$_instance = new Db ( $db_config );
		}
		return self::$_instance;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 分析数据库配置信息，支持数组和DSN
	 * +----------------------------------------------------------
	 *
	 * @access private
	 *         +----------------------------------------------------------
	 * @param mixed $db_config
	 *        	数据库配置信息
	 *        	+----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 */
	private function parseConfig($_db_config = '') {
		// 如果配置为空，读取配置文件设置
		$db_config = array (
				'dbms' => $_db_config ['DB_TYPE'],
				'username' => $_db_config ['DB_USER'],
				'password' => $_db_config ['DB_PWD'],
				'hostname' => $_db_config ['DB_HOST'],
				'hostport' => $_db_config ['DB_PORT'],
				'database' => $_db_config ['DB_NAME'],
				'params' => $_db_config ['DB_PARAMS'] 
		);
		return $db_config;
	}
	
	/**
	 * +----------------------------------------------------------
	 * 数据库调试 记录当前SQL
	 * +----------------------------------------------------------
	 *
	 * @access protected
	 *         +----------------------------------------------------------
	 */
	protected function debug() {
		// 记录操作结束时间
		if ($this->debug) {
			$runtime = number_format ( microtime ( TRUE ) - $this->beginTime, 6 );
			Log::record ( " RunTime:" . $runtime . "s SQL = " . $this->queryStr, Log::SQL );
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 查询次数更新或者查询
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @param mixed $times
	 *        	+----------------------------------------------------------
	 * @return void +----------------------------------------------------------
	 */
	public function Q($times = '') {
		static $_times = 0;
		if (empty ( $times )) {
			return $_times;
		} else {
			$_times ++;
			// 记录开始执行时间
			$this->beginTime = microtime ( TRUE );
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 写入次数更新或者查询
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @param mixed $times
	 *        	+----------------------------------------------------------
	 * @return void +----------------------------------------------------------
	 */
	public function W($times = '') {
		static $_times = 0;
		if (empty ( $times )) {
			return $_times;
		} else {
			$_times ++;
			// 记录开始执行时间
			$this->beginTime = microtime ( TRUE );
		}
	}
	
	/**
	 * +----------------------------------------------------------
	 * 获取最近一次查询的sql语句
	 * +----------------------------------------------------------
	 *
	 * @access public
	 *         +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 */
	public function getLastSql() {
		return $this->queryStr;
	}
} // 类定义结束

$db = new SimpleDB ( $config );

$data ['publicid'] = safe ( $_GET ['publicid'] );
$data ['uid'] = safe ( $_GET ['uid'] );

$data ['module_name'] = isset ( $_GET ['_addons'] ) ? safe ( $_GET ['_addons'] ) : safe ( $_GET ['m'] );
$data ['controller_name'] = isset ( $_GET ['_controller'] ) ? safe ( $_GET ['_controller'] ) : safe ( $_GET ['c'] );
$data ['action_name'] = isset ( $_GET ['_action'] ) ? safe ( $_GET ['_action'] ) : safe ( $_GET ['a'] );

unset ( $_GET ['publicid'], $_GET ['uid'], $_GET ['_addons'], $_GET ['_controller'], $_GET ['_action'], $_GET ['m'], $_GET ['c'], $_GET ['a'], $_GET ['mdm'] );

$data ['param'] = json_encode ( $_GET );
$data ['ip'] = getClientIp ();
$data ['brower'] = getBrower ();
$data ['referer'] = $_SERVER ['HTTP_REFERER'];
$data ['cTime'] = time ();

$sql = "INSERT INTO " . $config ['DB_PREFIX'] . "visit_log (publicid,uid,module_name,controller_name,action_name,param,ip,brower,referer,cTime) VALUES ( '{$data [publicid]}','{$data [uid]}','{$data [module_name]}','{$data [controller_name]}','{$data [action_name]}','{$data [param]}','{$data [ip]}','{$data [brower]}','{$data [referer]}','{$data [cTime]}');";
// dump ( $sql );
echo $db->execute ( "$sql" );
// dump ( $result );

// 自动删除7天前的数据
$file = 'delete_visit_log.lock';
$time = filemtime ( RUNTIME_PATH . $file );
$over_time = $data ['cTime'] - 7 * 24 * 3600;
if ($time === false || $time < $over_time) {
	$fo = fopen ( RUNTIME_PATH . $file, "w" );
	
	$sql = "DELETE FROM " . $config ['DB_PREFIX'] . "visit_log where cTime<" . $over_time;
	//dump ( $sql );
	$db->execute ( "$sql" );
}


