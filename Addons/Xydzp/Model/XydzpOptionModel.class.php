<?php

namespace Addons\Xydzp\Model;

use Think\Model;

/**
 * CardVouchers模型
 */
class XydzpOptionModel extends Model {
	function getXydzpOptionInfo($id, $update = false, $data = array()) {
		$key = 'XydzpOption_getXydzpOptionInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info );
		}
		return $info;
	}
	//获取指定字段的所有的奖品列表
	function getOptionTitles($update = false, $data = array()) {
		$map['token'] = get_token();
		$key = 'XydzpOption_getOptionTitles_'.$map['token'];
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->where($map)->field('`id`,`title`')->select() : $data);
			S ( $key, $info );
		}
		return $info;
	}
	
	function updateOptionNum($id,$save=array()){
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$this->getXydzpOptionInfo ( $id, true );
		}
		return $res;
	}
	
}
