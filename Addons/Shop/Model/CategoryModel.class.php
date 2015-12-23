<?php

namespace Addons\Shop\Model;

use Think\Model;

/**
 * Shop模型
 */
class CategoryModel extends Model {
	protected $tableName = 'shop_goods_category';
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Category_getInfo_' . $id;
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
	function getShopCategory($shop_id) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		$list = $this->where ( $map )->select ();
		return $list;
	}
	function getRecommendList($shop_id) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		$map ['is_recommend'] = 1;
		$map ['icon'] = array (
				'gt',
				0 
		);
		$list = $this->where ( $map )->select ();
		return $list;
	}
}
