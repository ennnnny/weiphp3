<?php

namespace Addons\Shop\Model;

use Think\Model;

/**
 * Shop模型
 */
class SlideshowModel extends Model {
	protected $tableName = 'shop_slideshow';
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Slideshow_getInfo_' . $id;
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
	function getShopList($shop_id) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		$list = $this->where ( $map )->select ();
		return $list;
	}
}
