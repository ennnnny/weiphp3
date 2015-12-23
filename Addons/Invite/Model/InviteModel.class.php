<?php

namespace Addons\Invite\Model;

use Think\Model;

/**
 * Invite模型
 */
class InviteModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Invite_getInfo_' . $id;
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
	function clear($id, $type = '', $uid = '') {
		$this->getInfo ( $id, true );
	}
	// 素材相关
	function getSucaiList($search = '') {
		$map['token'] = get_token();
		$map['uid'] = session('mid');
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
}
