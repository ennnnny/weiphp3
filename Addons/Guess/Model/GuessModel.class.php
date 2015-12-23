<?php

namespace Addons\Guess\Model;

use Think\Model;

/**
 * Guess模型
 */
class GuessModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Guess_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	function update($id, $save = array()) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$this->getInfo ( $id, true );
		}
		return $res;
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
		$token = get_token ();
		$return ['publicInfo'] = $info = $publicInfo = get_token_appinfo ( $token );
		
		//$uid = session ( 'mid' );
		$param ['publicid'] = $info ['id'];
		$param ['id'] = $id = I ( 'id' );
		$openid = get_openid ();
		
		// $return ['canJoin'] = ! empty ( $openid ) && ! empty ( $token ) && ! ($this->_is_overtime ( $id )) && ! ($this->_is_join ( $id, $uid, $token ));
		
		return $return;
	}
}
