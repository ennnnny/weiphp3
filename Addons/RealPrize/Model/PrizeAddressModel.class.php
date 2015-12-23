<?php

namespace Addons\RealPrize\Model;

use Think\Model;

/**
 * PrizeAddress模型
 */
class PrizeAddressModel extends Model {
	
	function getInfo($id, $update = false, $data = array()) {
		$key = 'PrizeAddress_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
	
		return $info;
	}
	
	//判断用户是否填写地址 领取奖励
	function getAddressInfo($uid,$prizeid, $update = false) {
		$key = 'PrizeAddress_getAddressInfo_' . $uid.'_'.$prizeid;
		$info = S ( $key );
		if ($info === false || $update) {
			$map['uid']=$uid;
			$map['prizeid']=$prizeid;
			$info = ( array )  $this->where ($map)->find ();
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	
	
}
