<?php

namespace Addons\Ask\Model;

use Think\Model;

/**
 * Ask模型
 */
class AskModel extends Model {
	function getAskInfo($ask_id, $update = false) {
		$key = 'getAskInfo_' . $ask_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['id'] = intval ( $ask_id );
			$info = $this->where ( $map )->find ();
			$appids = wp_explode ( $info ['appids'] );
			foreach ( $appids as $v ) {
				$arr = explode ( '|', $v );
				$appidArr [$arr [1]] = $arr [0];
			}
			$info ['appidArr'] = $appidArr;
			
			$finish_button = wp_explode ( $info ['finish_button'] );
			foreach ( $finish_button as $v ) {
				$arr = explode ( '|', $v );
				$finish_buttonArr [$arr [1]] = $arr [0];
			}
			$info ['finish_button_arr'] = $finish_buttonArr;
			
			$info ['shop_address_arr'] = wp_explode ( $info ['shop_address'] );
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	function clear($ids, $type = '', $uid = '') {
		if (empty ( $ids ))
			return false;
		
		if (! is_array ( $ids )) {
			$ids = array (
					$ids 
			);
		}
		
		foreach ( $ids as $id ) {
			$key = 'getAskInfo_' . $id;
			S ( $key, null );
		}
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
			$data = $this->getAskInfo ( $v ['id'] );
			$v ['title'] = $data ['title'];
		}
		
		return $data_list;
	}
	function getPackageData($ask_id) {
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['ask_id'] = $ask_id;
		
		$data ['jumpURL'] = addons_url ( "Ask://Ask/ask", $param );
		
		$data ['ask'] = $this->getAskInfo ( $ask_id );
		$data ['button_name'] = '马上开始';
		
		return $data;
	}
}
