<?php

namespace Addons\Reserve\Model;
use Think\Model;

/**
 * Reserve模型
 */
class ReserveModel extends Model{
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Reserve_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
		
		return $info;
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
		}
		
		return $data_list;
	}
	function getPackageData($id) {
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['id'] = $id;
		
		$data ['reserve'] = $this->getInfo ( $id );
		
		return $data;
	}
}
