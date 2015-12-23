<?php

namespace Addons\Xydzp\Model;

use Think\Model;

/**
 * CardVouchers模型
 */
class XydzpLogModel extends Model {
	function getXydzpLogs($xydzp_id, $update = false,$data=array()) {
		$key = 'XydzpLog_getXydzpLogs_' .$xydzp_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map['xydzp_id']=$xydzp_id;
			$info = ( array )(empty($data)? $this->where ($map)->order ( "zjdate desc" )->limit ( 10 )->select():$data);
			S ( $key, $info );
		}
		return $info;
	}
	
	//获取个人中奖信息
	function getZjUserList($xydzp_id,$openid,$update = false){
		$key = 'XydzpLog_getZjUserList_' .$xydzp_id.'_'.$openid;
		$info = S ( $key );
		if ($info === false || $update) {
			$fix = C ( "DB_PREFIX" );
			$info = ( array )M ( 'xydzp_log' )->join ( 'left join ' . $fix . 'user on ' . $fix . 'xydzp_log.uid=' . $fix . 'user.uid' )->join ( 'left join ' . $fix . 'xydzp_option on ' . $fix . 'xydzp_log.xydzp_option_id=' . $fix . 'xydzp_option.id' )->field ( $fix . "xydzp_log.id," . $fix . "user.nickname," . $fix . "xydzp_option.title,zjdate" )->where ( array (
				'xydzp_id' => $xydzp_id,
				$fix . 'xydzp_log.uid' => $openid 
		) )->order ( "zjdate desc" )->select ();
			S ( $key, $info );
		}
		return $info;
	}
	
// 	//根据uid获取中奖信息
// 	function getLogByUid($uid,$update = false){
// 		$key = 'XydzpLog_getLogByUid_' .$uid;
// 		$info = S ( $key );
// 		if ($info === false || $update) {
// 			$map['uid']=$uid;
// 			$info = ( array )$this->where ($map)->select();
// 			S ( $key, $info );
// 		}
// 		return $info;
// 	}
	
}
