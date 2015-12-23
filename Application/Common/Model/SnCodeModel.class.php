<?php

namespace Common\Model;

use Think\Model;

/**
 * SnCode模型
 */
class SnCodeModel extends Model {
	protected $tableName = 'sn_code';
	function getInfoById($id, $field = '', $update = false, $data = array()) {
		$key = 'SnCode_getInfoById_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
		
		return empty ( $field ) ? $info : $info [$field];
	}
	function delayAdd($data = array(), $delay = 10) {
		if (empty ( $data )) {
			return true;
		}
		
		$data ['server_addr'] = $_SERVER ['SERVER_ADDR'];
		$this->add ( $data );
		
		// 更新相关缓存
		$this->getCollectCount ( $data ['target_id'], $data ['addon'], true );
		$this->getMyList ( $data ['uid'], $data ['target_id'], $data ['addon'], true );
		$this->getMyAll ( $data ['uid'], $data ['addon'], true );
		D ( 'Addons://Coupon/Coupon' )->updateCollectCount ( $data ['target_id'], true );
		
		return true;
	}
	// 延时插入，也thinkphp的延时插入不同，它同时需要更新相关的缓存，保证数据的实时性
	function delayAdd_bar($data = array(), $delay = 10) {
		$key_time = 'SnCode_delayAdd_time';
		$time = S ( $key_time );
		
		// $key_id = 'SnCode_delayAdd_lastID';
		// $lastID = intval ( S ( $key_id ) );
		// if ($lastID == 0) {
		// $lastID = $this->getField ( 'max(id)' );
		// }
		
		$key_data = 'SnCode_delayAdd_data';
		$dataArr = S ( $key_data );
		$dataArr === false && $dataArr = array ();
		
		// 非插入时把缓存数据写入数据库，解决最后一批缓存数据无法写入数据库的问题
		if (empty ( $data )) {
			if (! empty ( $dataArr )) {
				foreach ( $dataArr as $k => $v ) {
					unset ( $dataArr [$k] ['id'] );
				}
				$this->addAll ( $dataArr );
				
				S ( $key_time, NOW_TIME );
				S ( $key_data, array () );
				// S ( $key_id, null );
				
				// 更新相关缓存
				foreach ( $dataArr as $d ) {
					$this->getCollectCount ( $d ['target_id'], $d ['addon'], true );
					$this->getMyList ( $d ['uid'], $d ['target_id'], $d ['addon'], true );
					$this->getMyAll ( $d ['uid'], $d ['addon'], true );
					D ( 'Addons://Coupon/Coupon' )->updateCollectCount ( $d ['target_id'], true );
				}
			}
			return true;
		}
		
		// $lastID += 1;
		// $data ['id'] = $lastID;
		$data ['server_addr'] = $_SERVER ['SERVER_ADDR'];
		$dataArr [] = $data;
		if (NOW_TIME > $time + $delay) {
			// 延时更新时间到了，删除缓存数据 并实际写入数据库
			$this->addAll ( $dataArr );
			
			S ( $key_time, NOW_TIME );
			S ( $key_data, array () );
			
			// 更新相关缓存
			$this->getCollectCount ( $data ['target_id'], $data ['addon'], true );
			$this->getMyList ( $data ['uid'], $data ['target_id'], $data ['addon'], true );
			$this->getMyAll ( $d ['uid'], $d ['addon'], true );
			D ( 'Addons://Coupon/Coupon' )->updateCollectCount ( $data ['target_id'], true );
		} else {
			// 追加数据到缓存
			S ( $key_data, $dataArr );
			
			// 更新相关缓存
			$this->getCollectCount ( $data ['target_id'], $data ['addon'], false, true );
			$this->getMyList ( $data ['uid'], $data ['target_id'], $data ['addon'], false, $data ['id'] );
			$this->getMyAll ( $d ['uid'], $d ['addon'], false, $data ['id'] );
		}
		// S ( $key_id, $lastID );
		
		return true;
	}
	function getCollectCount($target_id, $addon = 'Coupon', $update = false, $cache_update = false) {
		$key = 'SnCode_getCollectCount_' . $addon . '_' . $target_id;
		$count = S ( $key );
		if ($cache_update) {
			$count += 1;
			S ( $key, $count );
		} else if ($count === false || $update) {
			$map ['target_id'] = $target_id;
			$map ['addon'] = $addon;
			
			$count = $this->where ( $map )->count ();
			S ( $key, $count );
		}
		return intval ( $count );
	}
	function getMyList($uid, $target_id = '', $addon = 'Coupon', $update = false, $cache_id = '') {
		$key = 'SnCode_getMyList_' . $uid . '_' . $addon . '_' . $target_id;
		$ids = S ( $key );
		
		if (! empty ( $cache_id )) {
			$ids === false && $ids = array ();
			array_unshift ( $ids, $cache_id );
			S ( $key, $ids, 86400 );
		} else if ($ids === false || $update) {
			$map ['uid'] = $uid;
			$map ['target_id'] = $target_id;
			$map ['addon'] = $addon;
			
			$ids = $this->where ( $map )->getFields ( 'id' );
			S ( $key, $ids, 86400 );
		}
		
		foreach ( $ids as $id ) {
			$list [] = $this->getInfoById ( $id );
		}
		
		return $list;
	}
	function getMyAll($uid, $addon = 'Coupon', $update = false, $cache_id = '', $can_use = '') {
		$key = 'SnCode_getMyAllPage_' . $uid . '_' . $addon;
		$ids = S ( $key );
		
		if (! empty ( $cache_id )) {
			$ids === false && $ids = array ();
			array_unshift ( $ids, $cache_id );
			S ( $key, $ids, 86400 );
		} else if ($ids == false || $update) {
			$map ['uid'] = $uid;
			$map ['addon'] = $addon;
			if ($can_use != '') {
				$map ['can_use'] = $can_use;
			}
			$ids = $this->where ( $map )->order ( 'is_use asc, id desc' )->getFields ( 'id' );
			S ( $key, $ids, 86400 );
		}
		
		foreach ( $ids as $id ) {
			$list [] = $this->getInfoById ( $id );
		}
		
		return $list;
	}
	function update($id, $save = array()) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		$this->getInfoById ( $id, '', true );
		return $res;
	}
	function set_use($id) {
		$data = $this->getInfoById ( $id );
		
		if (! $data) {
			return - 1;
		}
		
		if ($data ['is_use']) {
			$data ['is_use'] = 0;
			$data ['use_time'] = '';
		} else {
			$data ['is_use'] = 1;
			$data ['use_time'] = time ();
			$data ['can_use'] = 0;
			$data ['admin_uid'] = $GLOBALS ['mid'];
		}
		
		$res = $this->update ( $id, $data );
		return $res;
	}
	function getSnCount($type = '') {
		$count = 0;
		$mid = get_mid ();
		$list = $this->getMyAll ( $mid, 'ShopCoupon' );
		foreach ( $list as $k => &$v ) {
			$coupon = ( array ) D ( 'Addons://ShopCoupon/Coupon' )->getInfo ( $v ['target_id'] );
			if ($coupon) {
				$v ['sn_id'] = $v ['id'];
				$v = array_merge ( $v, $coupon );
				if ($type != '') {
					if ($coupon ['type'] == $type) {
						$count ++;
					}
				} else {
					$count ++;
				}
			} else {
				unset ( $list [$k] );
			}
		}
		return $count;
	}
	function getUserCount($uid) {
		$map ['uid'] = $uid;
		$map ['token'] = get_token ();
		
		$list = $this->where ( $map )->field ( 'count(can_use) as total,sum(can_use) as left_count, addon' )->group ( 'addon' )->select ();
		foreach ( $list as $vo ) {
			$data [$vo ['addon']] ['total'] = $vo ['total'];
			$data [$vo ['addon']] ['left_count'] = $vo ['left_count'];
			$data [$vo ['addon']] ['use_count'] = $vo ['total'] - $vo ['left_count'];
		}
		return $data;
	}
}
