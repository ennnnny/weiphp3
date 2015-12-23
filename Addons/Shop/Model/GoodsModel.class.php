<?php

namespace Addons\Shop\Model;

use Think\Model;

/**
 * Shop模型
 */
class GoodsModel extends Model {
	protected $tableName = 'shop_goods';
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Goods_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			if (! isset ( $info ['imgs_url'] ) && ! empty ( $info ['imgs'] )) {
				$imgs = array_filter ( explode ( ',', $info ['imgs'] ) );
				foreach ( $imgs as $img ) {
					$imgs_url [] = get_cover_url ( $img );
				}
				
				$info ['imgs_url'] = array_filter ( $imgs_url );
			}
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
	function getRecommendList($shop_id) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		$map ['is_recommend'] = 1;
		
		$list = $this->where ( $map )->order ( 'id desc' )->limit ( 5 )->select ();
		return $list;
	}
	function getNewsList($shop_id) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		
		$list = $this->where ( $map )->order ( 'id desc' )->limit ( 5 )->select ();
		return $list;
	}
	function getList($shop_id, $search_key = '', $order = 'id desc', $last_id = 0, $count = 10) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		empty ( $search_key ) || $map ['title'] = array (
				'like',
				"%$search_key%" 
		);
		if (false === strpos ( strtolower ( $order ), 'desc' )) {
			$map ['id'] = array (
					'gt',
					$last_id 
			);
		} elseif ($last_id > 0) {
			$map ['id'] = array (
					'lt',
					$last_id 
			);
		}
		
		$list = $this->where ( $map )->order ( $order )->limit ( $count )->select ();
		// lastsql ();
		return $list;
	}
	// 热销度计算
	function getRank($id, $info = array()) {
		static $_max_sale_count;
		empty ( $info ) && $info = $this->getInfo ( $id );
		
		if (empty ( $_max_sale_count )) {
			$map ['shop_id'] = $info['shop_id'];
			$map ['is_show'] = 0;
			$_max_sale_count = $this->where ( $map )->getField ( 'max(sale_count)' );
		}
		
		// 30天的时间权重值
		$time_rank = 25 * (30 - (date ( 'Ymd' ) - date ( 'Ymd', $info ['show_time'] ))) / 30;
		$time_rank < 0 && $time_rank = 0;
		
		// 推荐权重
		$recommend_rank = 25 * $info ['is_recommend'];
		
		// 销量权限
		$sale_rank = 50 * $info ['sale_count'] / $_max_sale_count;
		
		return $time_rank + $recommend_rank + $sale_rank;
	}
	//分页获取商品
	function getGoodsByShop($shop_id) {
		$map ['shop_id'] = $shop_id;
		$map ['is_show'] = 1;
		$list = $this->where ( $map )->order ( 'id desc' )->selectPage ();
		// lastsql ();
		return $list;
	}
}
