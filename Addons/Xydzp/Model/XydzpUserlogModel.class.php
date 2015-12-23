<?php

namespace Addons\Xydzp\Model;

use Think\Model;

/**
 * CardVouchers模型
 */
class XydzpUserlogModel extends Model {
	function getUserlogInfo($user_id,$xydzp_id, $update = false) {
		$key = 'XydzpUserlog_getUserlogInfo_' . $user_id.'_'.$xydzp_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map['uid']=$user_id;
			$map['xydzp_id']=$xydzp_id;
			$info = ( array ) M ( "xydzp_userlog" )->where ($map)->find ();
			S ( $key, $info );
		}
		return $info;
	}
	
	function updateLog($user_id,$xydzp_id,$save=array()){
		$map['xydzp_id']=$xydzp_id;
		$map['uid']=$user_id;
		$res=$this->where($map)->save($save);
		if($res){
			$this->getUserlogInfo($user_id, $xydzp_id,true);
		}
		return $res;
	}
	
}
