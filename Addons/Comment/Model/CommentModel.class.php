<?php

namespace Addons\Comment\Model;

use Think\Model;

/**
 * Comment模型
 */
class CommentModel extends Model {
	function addComment($data) {
		return $this->add ( $data );
	}
	function getComment($aim_id, $aim_table, $limit = 20, $order = 'id desc') {
		$map ['aim_id'] = $aim_id;
		$map ['aim_table'] = $aim_table;
		$map['is_audit']=1;
		$list = $this->where ( $map )->limit ( $limit )->order ( $order )->select ();
		foreach ( $list as &$v ) {
			$follow = get_followinfo ( $v ['follow_id'] );
			$v = array_merge ( $follow, $v );
		}
		rsort($list);
		return $list;
	}
	function getCommentByPage($aim_id, $aim_table, $limit = 20, $order = 'id desc') {
		$map ['aim_id'] = $aim_id;
		$map ['aim_table'] = $aim_table;
		//$map['is_audit']=1;
		$list = $this->where ( $map )->order ( $order )->selectPage ();
		foreach ( $list['list_data'] as &$v ) {
			$follow = get_followinfo ( $v ['follow_id'] );
			if($follow){
				$v = array_merge ( $follow, $v );
			}
		}
		return $list;
	}
}
