<?php

namespace Addons\Vote\Model;

use Think\Model;

/**
 * Vote模型
 */
class VoteLogModel extends Model {
	// 获取信息
	function getFollowLog($follow_id, $vote_id, $update = false) {
		$key = 'VoteLog_getFollowLog_' . $follow_id . '_' . $vote_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['vote_id'] = $vote_id;
			$map ['user_id'] = $follow_id;
			$info = (array)$this->where ( $map )->select ();
			S ( $key, $info );
		}
		return $info;
	}
}
