<?php

namespace Addons\Xydzp\Model;

use Think\Model;

/**
 * CardVouchers模型
 */
class XydzpModel extends Model {
	// 素材相关
	function getSucaiList($search = '') {
		$map ['token'] = get_token ();
		$map ['uid'] = session ( 'mid' );
		empty ( $search ) || $map ['title'] = array (
				'like',
				"%$search%" 
		);
		
		$data_list = $this->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->selectPage ();
		
		return $data_list;
	}
	function getPackageData($id) {
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['id'] = $id;
		
		$data ['jumpURL'] = addons_url ( "Xydzp://Xydzp/show", $param );
		
		return $data;
	}
	function getXydzpInfo($id, $update = false, $data = array()) {
		$key = 'Xydzp_getXydzpInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info );
		}
		return $info;
	}
	
	//查询是否绑定了信息
	function getUserInfo($openid,$token,$update=false){
		$user_key='Xydzp_getUserInfo_'.$openid.'_'.$token;
		$info=S($user_key);
		if ($info===false||$update){
			$map['openid']=$openid;
			$map['token']=$token;
			$info=(array) M ( 'user' )->where ($map)->find ();
			isset($info['nickname']) && $info['nickname'] = deal_emoji ( $info ['nickname'], 1 );
			S($user_key,$info);
		}
		return $info;
	}
}
