<?php

namespace Addons\Shop\Model;

use Think\Model;

/**
 * Shop模型
 */
class AddressModel extends Model {
	protected $tableName = 'shop_address';
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Address_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			if (! isset ( $info ['city_name'] ) && ! empty ( $info ['city'] )) {
				$info ['city_name'] = $this->getCityName ( $info ['city'] );
			}
			S ( $key, $info );
		}
		
		return $info;
	}
	function getCityName($city) {
		$ids = array_filter ( explode ( ',', $city ) );
		$map ['id'] = array (
				'in',
				$ids 
		);
		$list = M ( 'common_category' )->where ( $map )->select ();
		foreach ( $list as $v ) {
			$arr [$v ['id']] = $v ['title'];
		}
		foreach ( $ids as $id ) {
			isset ( $arr [$id] ) && $title [] = $arr [$id];
		}
		return implode ( ' ', $title );
	}
	function deal($data) {
		if ($data ['is_use'] == 1) {
			$this->where ( 'uid=' . $data ['uid'] )->setField ( 'is_use', 0 );
		}
		
		if (! empty ( $data ['id'] )) {
			$map ['id'] = $data ['id'];
			$res = $this->where ( $map )->save ( $data );
		} else {
			$res = $this->add ( $data );
		}
		
		// 更新用户缓存
		$list = $this->where ( 'uid=' . $data ['uid'] )->select ();
		foreach ( $list as $v ) {
			$this->getInfo ( $v ['id'], true, $v );
		}
		
		return $res;
	}
	function getUserList($uid) {
		$map ['uid'] = $uid;
		$list = $this->where ( $map )->field ( 'id' )->select ();
		foreach ( $list as &$v ) {
			$v = $this->getInfo ( $v ['id'] );
		}
		
		return $list;
	}
	function getMyAddress($uid) {
		$map ['uid'] = $uid;
		$id = $this->where ( $map )->order ( 'is_use desc, id desc' )->getField ( 'id' );
		
		return empty ( $id ) ? '' : $this->getInfo ( $id );
	}
}
