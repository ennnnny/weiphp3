<?php

namespace Addons\Vote\Model;

use Think\Model;

/**
 * Vote模型
 */
class VoteOptionModel extends Model {
	function set($vote_id, $post) {
		$opt_data ['vote_id'] = $vote_id;
		foreach ( $post ['name'] as $key => $opt ) {
			if (empty ( $opt ))
				continue;
			
			$opt_data ['name'] = $opt;
			$opt_data ['image'] = $post ['image'] [$key];
			$opt_data ['order'] = intval ( $post ['order'] [$key] );
			if ($key > 0) {
				// 更新选项
				$optIds [] = $map ['id'] = $key;
				$this->where ( $map )->save ( $opt_data );
			} else {
				// 增加新选项
				$optIds [] = $this->add ( $opt_data );
			}
			// dump(M()->getLastSql());
		}
		// 删除旧选项
		$map2 ['id'] = array (
				'not in',
				$optIds 
		);
		$map2 ['vote_id'] = $opt_data ['vote_id'];
		$this->where ( $map2 )->delete ();
		
		$this->clear ( $vote_id );
	}
	
	// 获取信息
	function getList($vote_id, $update = false, $list = array()) {
		$key = 'VoteOptioin_getList_' . $vote_id;
		$info = S ( $key );
		if ($info === false || $update) {
			if (empty ( $list )) {
				$map ['vote_id'] = $vote_id;
				$info = M ( 'vote_option' )->where ( $map )->order ( '`order` asc' )->select ();
			} else {
				$info = $list;
			}
			S ( $key, $info );
		}
		
		return $info;
	}
	
	// 通用的清缓存的方法
	function clear($vote_ids, $type = '', $uid = '') {
		is_array ( $vote_ids ) || $vote_ids = explode ( ',', $vote_ids );
		
		foreach ( $vote_ids as $vote_id ) {
			$this->getList ( $vote_id, true );
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
	function getPackageData($vote_id) {
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['vote_id'] = $vote_id;
		
		$data ['jumpURL'] = addons_url ( "Vote://Vote/Vote", $param );
		
		$data ['vote'] = $this->getInfo ( $vote_id );
		$data ['button_name'] = '马上开始';
		
		return $data;
	}
	function updateOptCount($vote_id, $ids) {
		$key_time = 'VoteOptioin_update_time_' . $vote_id;
		$key_data = 'VoteOptioin_update_data_' . $vote_id;
		$time = S ( $key_time );
		$list_data = S ( $key_data );
		// 解决最后的缓存无法写入数据库的问题
		if (empty ( $ids ) && ! empty ( $list_data )) {
			foreach ( $list_data as $d ) {
				$map ['id'] = $d ['id'];
				$res = $this->where ( $map )->setField ( 'opt_count', $d ['opt_count'] );
			}
			$this->getList ( $vote_id, true, $list_data );
			S ( $key_time, NOW_TIME );
			return true;
		}
		if ($time === false) {
			S ( $key_time, NOW_TIME );
		}
		
		if ($list_data === false) {
			$list = $this->getList ( $vote_id );
			foreach ( $list as $v ) {
				$list_data [$v ['id']] = $v;
			}
		}
		
		foreach ( $ids as $id ) {
			$list_data [$id] ['opt_count'] += 1;
		}
		if ($time === false || NOW_TIME - $time < 10) {
			// 记录到缓存
			S ( $key_data, $list_data );
		} else {
			// 记录到数据库
			foreach ( $list_data as $d ) {
				$map ['id'] = $d ['id'];
				$res = $this->where ( $map )->setField ( 'opt_count', $d ['opt_count'] );
			}
			S ( $key_data, $list_data );
			S ( $key_time, NOW_TIME );
		}
		$this->getList ( $vote_id, true, $list_data );
		
		return true;
	}
}
