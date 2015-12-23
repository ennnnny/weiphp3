<?php

namespace Addons\Invite\Model;

use Think\Model;

/**
 * Invite模型
 */
class InviteUserModel extends Model {
	function getUserInfo($invite_id, $follow_id,$update=false,$data=array()) {
		$key = 'InviteUser_getUserInfo_' . $invite_id . '_' . $follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['invite_id'] = $invite_id;
			$map ['uid'] = $follow_id;
			$info = ( array ) (empty ( $data ) ? M ( 'invite_user' )->where ( $map )->find () : $data);
			
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	function getInfo($id,$update=false,$data=array()) {
		$key = 'InviteUser_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['id'] = $id;
			$info = ( array ) (empty ( $data ) ? M ( 'invite_user' )->where ( $map )->find () : $data);
				
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	function updateNum($id,$save=array()){
		$map['id']=$id;
		$res=M ( 'invite_user' )->where ( $map )->save($save);
		if($res){
			$this->getInfo($id,true);
		}
		return $res;
	}
}
