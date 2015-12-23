<?php

namespace Common\Model;

use Think\Model;

/**
 * 粉丝操作
 */
class FollowModel extends Model {
	protected $tableName = 'user';
	function init_follow($openid, $token = '', $has_subscribe = false) {
		empty ( $token ) && $token = get_token ();
		addWeixinLog ( $openid . '::_' . $token, 'init_follow_in' );
		if (empty ( $openid ) || $openid == - 1 || empty ( $token ) || $token == - 1)
			return false;
		
		$data ['token'] = $token;
		$data ['openid'] = $openid;
		$datas = $data;
		$uid = M ( 'public_follow' )->where ( $data )->getField ( 'uid' );
		addWeixinLog ( $uid, 'init_follow_check_uid' );
		if ($uid) {
			return $uid;
		}
		
		// 自动注册
		$config = getAddonConfig ( 'UserCenter', $token );
		
		$user = array (
				'experience' => intval ( $config ['experience'] ),
				'score' => intval ( $config ['score'] ),
				
				'reg_ip' => get_client_ip ( 1 ),
				'reg_time' => NOW_TIME,
				'last_login_ip' => get_client_ip ( 1 ),
				'last_login_time' => NOW_TIME,
				
				'status' => 1,
				'is_init' => 1,
				'is_audit' => 1,
				'come_from' => 1 
		);
		$user2 = getWeixinUserInfo ( $openid );
		
		$user = array_merge ( $user, $user2 );
		$data ['uid'] = $uid = D ( 'Common/User' )->add ( $user );
		
		if ($has_subscribe !== false) {
			$data ['has_subscribe'] = $has_subscribe;
		}
		M ( 'public_follow' )->add ( $data );
		
		return $uid;
	}
	
	/**
	 * 兼容旧的写法
	 */
	public function getFollowInfo($id, $update = false) {
		return D ( 'Common/User' )->getUserInfo ( $id, $update );
	}
	function update($id, $data) {
		return D ( 'Common/User' )->updateInfo ( $id, $data );
	}
	function updateByMap($map, $data) {
		return false; // 已停用该方法
	}
	function updateField($id, $field, $val) {
		return D ( 'Common/User' )->updateInfo ( $id, array (
				$field => $val 
		) );
	}
	function set_subscribe($user_id, $has_subscribe = 1) {
		if (is_numeric ( $user_id )) {
			$map ['uid'] = $user_id;
		} else {
			$map ['openid'] = $user_id;
		}
		if ($token && $token != '-1') {
			$map ['token'] = $token;
		}
		
		M ( 'public_follow' )->where ( $map )->setField ( 'has_subscribe', $has_subscribe );
	}
}
?>
