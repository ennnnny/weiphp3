<?php

namespace Addons\CardVouchers\Model;

use Think\Model;

/**
 * CardVouchers模型
 */
class CardVouchersModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'CardVouchers_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			
			$more_button = wp_explode ( $info ['more_button'] );
			foreach ( $more_button as $v ) {
				$arr = explode ( '|', $v );
				$more_buttonArr [$arr [1]] = $arr [0];
			}
			$info ['more_button_arr'] = $more_buttonArr;
			
			S ( $key, $info );
		}
		
		return $info;
	}
	// 通用的清缓存的方法
	function clear($ids, $type = '', $uid = '') {
		is_array ( $ids ) || $ids = explode ( ',', $ids );
		
		foreach ( $ids as $id ) {
			$this->getInfo ( $id, true );
		}
	}
	// 素材相关
	function getSucaiList($search = '') {
		$map['token'] = get_token();
		$map['uid'] = session('mid');
		empty ( $search ) || $map ['card_id'] = array (
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
		$info = $this->getInfo ( $id );
		
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['appsecre'] = trim ( $info ['appsecre'] );
		$sha1 ['card_id'] = $card_id = trim ( $info ['card_id'] );
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		
		$info ['card_ext'] = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		
		$data ['info'] = $info;
		return $data;
	}
}
