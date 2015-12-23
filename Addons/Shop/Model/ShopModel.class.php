<?php

namespace Addons\Shop\Model;

use Think\Model;

/**
 * Shop模型
 */
class ShopModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Shop_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info );
		}
		
		return $info;
	}
	function updateById($id, $data) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $data );
		if ($res) {
			$this->getInfo ( $id, true );
		}
	}
	function getShop($uid){
		$map['uid'] = $uid;
		$res = $this->where($map)->select();
		return $res;
	}
}
