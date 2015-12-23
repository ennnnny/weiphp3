<?php

namespace Addons\Scratch\Model;

use Think\Model;

/**
 * Scratch模型
 */
class PrizeModel extends Model {
	
	function getPrizes($target_id,$addon, $update = false, $data = array()) {
		$key = 'Prize_getPrizes_' . $target_id.'_'.$addon;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['target_id']=$target_id;
			$map ['addon']=$addon;
			$info = ( array ) (empty ( $data ) ? M ( 'prize' )->where ( $map )->select () : $data);
			
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	
	function getPrizeInfo($id, $update = false, $data = array()) {
		$key = 'Prize_getPrizeInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
				
			S ( $key, $info, 86400 );
		}
		return $info;
	}	
	// 通用的清缓存的方法
	function clear($ids, $type = '', $uid = '') {
		is_array ( $ids ) || $ids = explode ( ',', $ids );
		foreach ( $ids as $id ) {
			$this->getPrizeInfo ( $id, true );
		}
	}
}
