<?php

namespace Common\Model;

use Think\Model;

/**
 * 公众号配置操作集成
 */
class PublicModel extends Model {
	protected $tableName = 'public';
	function getInfo($id, $filed = '', $update = false, $data = array()) {
		if (empty ( $id )) {
			return empty ( $filed ) ? array () : '';
		}
		
		$key = 'Common_Public_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = empty ( $data ) ? $this->find ( $id ) : $data;
			S ( $key, $info );
		}
		
		return empty ( $filed ) ? $info : $info [$filed];
	}
	function getInfoByToken($token, $filed = '', $update = false) {
		$key = 'Common_Public_getInfoByToken';
		$arr = S ( $key );
		if ($arr === false || ! isset ( $arr [$token] ) || $update) {
			$list = $this->field ( 'id,token' )->select ();
			foreach ( $list as $vo ) {
				$arr [$vo ['token']] = $vo ['id'];
			}
			
			S ( $key, $arr, 604800 ); // 缓存一周
		}
		
		return $this->getInfo ( $arr [$token], $filed, $update );
	}
	function clear($id, $type = '', $uid = '') {
		$info = $this->getInfo ( $id, '', true );
	}
	function getMyPublics($uid) {
		$map ['uid'] = $uid;
		$list = M ( 'public_link' )->where ( $map )->select ();
		foreach ( $list as $vo ) {
			$info = $this->getInfo ( $vo ['mp_id'] );
			$res [$vo ['mp_id']] = array_merge ( $vo, $info );
		}
		return $res;
	}
}
?>
