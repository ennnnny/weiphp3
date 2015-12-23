<?php

namespace Addons\Coupon\Model;

use Think\Model;

/**
 * Coupon模型
 */
class CouponModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Coupon_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			
			$more_button = wp_explode ( $info ['more_button'] );
			foreach ( $more_button as $v ) {
				$arr = explode ( '|', $v );
				$more_buttonArr [$arr [1]] = $arr [0];
			}
			$info ['more_button_arr'] = $more_buttonArr;
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	function updateCollectCount($id, $update = false) {
		$key = 'Coupon_updateCollectCount_' . $id;
		$cache = S ( $key );
		
		$info = $this->getInfo ( $id );
		if (! $cache || $cache >= 100 || $update) {
			$info ['collect_count'] = D ( 'Common/SnCode' )->getCollectCount ( $id, 'Coupon' );
			
			// 更新数据库
			$this->where ( 'id=' . $id )->setField ( "collect_count", $info ['collect_count'] );
			
			$cache = 1;
		} else {
			// 更新缓存
			$info ['collect_count'] += 1;
			$cache += 1;
		}
		S ( $key, $cache, 300 );
		$this->getInfo ( $id, true, $info );
	}
	function update($id, $save = array()) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$this->getInfo ( $id, true );
		}
		return $res;
	}
	// 通用的清缓存的方法
	function clear($ids, $type = '', $uid = '') {
		is_array ( $ids ) || $ids = explode ( ',', $ids );
		
		foreach ( $ids as $id ) {
			$this->updateCollectCount ( $id, true );
			$this->getInfo ( $id, true );
		}
	}
	
	// 素材相关
	function getSucaiList($search = '') {
		$map ['token'] = get_token ();
		$map ['uid'] = session ( 'mid' );
		empty ( $search ) || $map ['title'] = array (
				'like',
				"%$search%" 
		);
		
		$data_list = $this->where ( $map )->field ( 'id' )->order ( 'id desc' )->selectPage ();
		foreach ( $data_list ['list_data'] as &$v ) {
			$data = $this->getInfo ( $v ['id'] );
			$v ['title'] = $data ['title'];
		}
		
		return $data_list;
	}
	function getPackageData($id) {
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['id'] = $id;
		$data ['jumpURL'] = addons_url ( "Coupon://Wap/set_sn_code", $param );
		
		$data ['info'] = $this->getInfo ( $id );
		// 店铺地址
		$maps ['coupon_id'] = $id;
		$list = M ( 'coupon_shop_link' )->where ( $maps )->select ();
		$shop_ids = getSubByKey ( $list, 'shop_id' );
		if (! empty ( $shop_ids )) {
			$map_shop ['id'] = array (
					'in',
					$shop_ids 
			);
			$shop_list = M ( 'coupon_shop' )->where ( $map_shop )->select ();
			$data ['shop_list'] = $shop_list;
		}
		return $data;
	}
	// 赠送优惠券
	function sendCoupon($id, $uid) {
		$param ['id'] = $id;
		
		$info = $this->getInfo ( $id );
		
		$flat = true;
		if ($info ['collect_count'] >= $info ['num']) {
			$flat = false;
		} else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
			$flat = false;
		} else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
			$flat = false;
		}
		
		$list = D ( 'Common/SnCode' )->getMyList ( $uid, $id, 'Coupon' );
		$my_count = count ( $list );
		
		if ($info ['max_num'] > 0 && $my_count >= $info ['max_num']) {
			$flat = false;
		}
		if (! $flat)
			return false;
		
		$data ['target_id'] = $id;
		$data ['uid'] = $uid;
		$data ['addon'] = 'Coupon';
		$data ['sn'] = uniqid ();
		$data ['cTime'] = NOW_TIME;
		$data ['token'] = $info ['token'];
		
		$sn_id = D ( 'Common/SnCode' )->add ( $data );
		return $sn_id;
	}
	function getSelectList() {
		$map ['end_time'] = array (
				'gt',
				NOW_TIME
		);
		$map ['token'] = get_token ();
	
		$list = $this->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		return $list;
	}
}
