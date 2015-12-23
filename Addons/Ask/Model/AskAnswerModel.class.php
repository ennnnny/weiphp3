<?php

namespace Addons\Ask\Model;

use Think\Model;

/**
 * Ask模型
 */
class AskAnswerModel extends Model {
	var $cache_prefix = 'AskAnswer_';
	function myLastAnswer($ask_id, $uid = 0, $update = false, $cache_data = array()) {
		empty ( $uid ) && $uid = $GLOBALS ['mid'];
		$map ['uid'] = $uid;
		$key = 'myLastAnswer_' . $ask_id . '_' . $map ['uid'];
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['ask_id'] = $ask_id;
			$info = empty ( $cache_data ) ? $this->where ( $map )->order ( 'cTime desc' )->find () : $cache_data;
			// lastsql();
			// dump($info);
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	function hasAnswer($uid, $question_id, $times = 0, $cache_id = 0) {
		$key = $this->cache_prefix . 'hasAnswer_' . $uid . '_' . $question_id . '_' . $times;
		$id = S ( $key );
		if ($cache_id > 0) {
			$id = $cache_id;
			S ( $key, $id );
		} elseif ($id === false) {
			$map ['uid'] = $uid;
			$map ['question_id'] = $question_id;
			$map ['times'] = $times;
			$id = $this->where ( $map )->getField ( 'id' );
			S ( $key, $id );
		}
		return $id;
	}
	function update($id, $save) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$save ['id'] = $id;
			$this->myLastAnswer ( $save ['ask_id'], $save ['uid'], true, $save );
			$this->hasAnswer ( $save ['uid'], $save ['question_id'], $save ['times'], $id );
		}
	}
	// 延时插入，也thinkphp的延时插入不同，它同时需要更新相关的缓存，保证数据的实时性
	function delayAdd($data = array(), $delay = 10) {
		$key_time = $this->cache_prefix . 'delayAdd_time';
		$time = S ( $key_time );
		
		$key_id = $this->cache_prefix . 'delayAdd_lastID';
		$lastID = intval ( S ( $key_id ) );
		if ($lastID == 0) {
			$lastID = $this->getField ( 'max(id)' );
		}
		
		$key_data = $this->cache_prefix . 'delayAdd_data';
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
				S ( $key_id, null );
				
				// 更新相关缓存
				foreach ( $dataArr as $d ) {
					$this->myLastAnswer ( $d ['ask_id'], $d ['uid'], true, $d );
					$this->hasAnswer ( $d ['uid'], $d ['question_id'], $d ['times'], $d ['id'] );
				}
			}
			return true;
		}
		
		$lastID += 1;
		$data ['id'] = $lastID;
		$dataArr [] = $data;
		if (NOW_TIME > $time + $delay) {
			// 延时更新时间到了，删除缓存数据 并实际写入数据库
			$this->addAll ( $dataArr );
			
			S ( $key_time, NOW_TIME );
			S ( $key_data, array () );
		} else {
			// 追加数据到缓存
			S ( $key_data, $dataArr );
		}
		// 更新相关缓存
		$this->myLastAnswer ( $data ['ask_id'], $data ['uid'], true, $data );
		$this->hasAnswer ( $data ['uid'], $data ['question_id'], $data ['times'], $lastID );
		
		S ( $key_id, $lastID );
		
		return $lastID;
	}
	function clear($ids, $type = '', $uid = '') {
		if (empty ( $ids ))
			return false;
		
		empty ( $uid ) && $uid = $GLOBALS ['mid'];
		
		if (! is_array ( $ids )) {
			$ids = array (
					$ids 
			);
		}
		
		foreach ( $ids as $id ) {
			$key = 'myLastAnswer_' . $id . '_' . $uid;
			S ( $key, null );
		}
	}
}
