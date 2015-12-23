<?php

namespace Addons\Vote\Model;

use Think\Model;

/**
 * Vote模型
 */
class ShopVoteOptionModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'ShopVoteOption_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	// 为每个选项分配编号
	function getNumber($voteId) {
		$map ['vote_id'] = $voteId;
		$map ['token'] = get_token ();
		$number = $this->where ( $map )->field ( 'count(id) as num' )->select ();
		return intval ( $number [0] ['num'] ) + 1;
	}
	
	// 获取活动选项
	function getOptions($voteId, $update = false) {
		// 以实时的opt_count来排序时，不能再用缓存
		$map ['vote_id'] = $voteId;
		$map ['token'] = get_token ();
		$info = $this->where ( $map )->order ( 'opt_count desc,number asc' )->select ();
		return $info;
	}
	
	// 通用的清缓存的方法
	function clear($optIds, $voteId = 0) {
		is_array ( $optIds ) || $optIds = explode ( ',', $optIds );
		foreach ( $optIds as $vo ) {
			$this->getInfo ( $vo, true );
		}
		if ($voteId) {
			$this->getOptions ( $voteId, true );
		}
	}
	
	// 获取用户投票信息
	function getUserVoteLog($vote_id, $uid, $update = false) {
		$key = 'ShopVoteOption_getFollowLog_' . $vote_id . '_' . $uid;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['vote_id'] = $vote_id;
			$map ['uid'] = $uid;
			$map ['token'] = get_token ();
			$info = M ( 'shop_vote_log' )->where ( $map )->select ();
			S ( $key, $info );
		}
		return $info;
	}
}
