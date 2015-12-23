<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Model;

use Think\Model;

/**
 * 分类模型
 */
class CronModel extends Model {
	// 单个任务最大执行时间
	protected $options = array (
			'CRON_MAX_TIME' => 60 
	);
	public function run(&$params) {
		// 锁定自动执行
		$lockfile = RUNTIME_PATH . 'cron.lock';
		if (is_writable ( $lockfile ) && filemtime ( $lockfile ) > $_SERVER ['REQUEST_TIME'] - C ( 'CRON_MAX_TIME' )) {
			return;
		} else {
			touch ( $lockfile );
		}
		set_time_limit ( 1000 );
		ignore_user_abort ( true );
		
		// 载入cron配置文件
		// 格式 return array(
		// 'cronname'=>array('filename',intervals,nextruntime),...
		// );
		if (is_file ( RUNTIME_PATH . '~crons.php' )) {
			$crons = include RUNTIME_PATH . '~crons.php';
		} elseif (is_file ( CONF_PATH . 'crons.php' )) {
			$crons = include CONF_PATH . 'crons.php';
		}
		if (isset ( $crons ) && is_array ( $crons )) {
			$update = false;
			$log = array ();
			foreach ( $crons as $key => $cron ) {
				if (empty ( $cron [2] ) || $_SERVER ['REQUEST_TIME'] >= $cron [2]) {
					// 到达时间 执行cron文件
					G ( 'cronStart' );
					include LIB_PATH . 'Cron/' . $cron [0] . '.php';
					$_useTime = G ( 'cronStart', 'cronEnd', 6 );
					// 更新cron记录
					$cron [2] = $_SERVER ['REQUEST_TIME'] + $cron [1];
					$crons [$key] = $cron;
					$log [] = "Cron:$key Runat " . date ( 'Y-m-d H:i:s' ) . " Use $_useTime s\n";
					$update = true;
				}
			}
			if ($update) {
				// 记录Cron执行日志
				Log::write ( implode ( '', $log ) );
				// 更新cron文件
				$content = "<?php\nreturn " . var_export ( $crons, true ) . ";\n?>";
				file_put_contents ( RUNTIME_PATH . '~crons.php', $content );
			}
		}
		// 解除锁定
		unlink ( $lockfile );
		return;
	}
}
