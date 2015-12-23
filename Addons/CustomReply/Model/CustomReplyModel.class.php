<?php

namespace Addons\CustomReply\Model;

use Think\Model;

/**
 * CustomReply模型
 */
class CustomReplyModel extends Model {
	protected $tableName = 'custom_reply_news';
	function getInfo($id, $update = false) {
		$key = 'CustomReply_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['id'] = intval ( $id );
			$info = $this->where ( $map )->find ();
			
			S ( $key, $info, 60 );
		}
		
		return $info;
	}
}
