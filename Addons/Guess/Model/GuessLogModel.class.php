<?php

namespace Addons\Guess\Model;

use Think\Model;

/**
 * Vote模型
 */
class GuessLogModel extends Model {
// 获取信息
	function getFollowLog($follow_id, $guess_id, $token,$update = false) {
		$key = 'GuessLog_getFollowLog_' . $follow_id . '_' . $guess_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = (array)M ( "guess_log" )->where ( "guess_id='$guess_id' AND user_id='$follow_id' AND token='$token' AND optionIds <>''" )->select ();
			S ( $key, $info );
		}
		return $info;
	}
}
