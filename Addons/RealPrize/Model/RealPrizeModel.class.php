<?php

namespace Addons\RealPrize\Model;

use Think\Model;

/**
 * RealPrize模型
 */
class RealPrizeModel extends Model {
	// 素材相关
	function getSucaiList($search = '') {
		$map['token'] = get_token();
		$map['uid'] = session('mid');
		empty ( $search ) || $map ['title'] = array (
				'like',
				"%$search%" 
		);
		
		$data_list = $this->where ( $map )->field ( 'id,prize_name' )->order ( 'id desc' )->selectPage ();
		foreach ( $data_list ['list_data'] as &$v ) {
			$v ['title'] = $v ['prize_name'];
		}
		
		return $data_list;
	}
	function getPackageData($id) {
		$param ['prizeid'] = $id;
		$return ['service_info'] = $info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$return ['data'] = $data = $this->getInfo ( $id );
		// 设置奖品页面领取对应的跳转链接
		$prizetype = $data ['prize_type'];
		if ($prizetype == '0') {
			$return ['jumpurl'] = addons_url ( "RealPrize://RealPrize/save_address", $param );
		} else {
			$return ['jumpurl'] = addons_url ( "RealPrize://RealPrize/address", $param );
		}
		
		// 获取奖品类型名称，方便显示
		$return ['tname'] = $prizetype == '0' ? '虚拟物品' : '实体物品';
		// 服务号信息
		$return ['template'] = $template = $data ['template'] == "" ? "default" : $data ['template'];
		
		return $return;
	}
	
	function getInfo($id, $update = false, $data = array()) {
		$key = 'RealPrize_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
	
		return $info;
	}
	
	function updatePrizeCount($id,$update=false){
// 		$key = 'RealPrize_updatePrizeCount_' . $id;
// 		$cache = S ( $key );
// 		dump($cache);
// 		if ($cache===false){
// 			$cache = 0;
// 		}
// 		$info = $this->getInfo ( $id );
// 		if (! $cache || $cache >= 2 || $update) {
// 			// 更新数据库
// 			$this->where(array('id'=>$id))->setField('prize_count',$info['prize_count']);
// 			$cache = 1;
// 		} else {
// 			// 更新缓存
// 			$info ['prize_count'] -= 1;
// 			$cache += 1;
// 		}
// 		S ( $key, $cache);
// 		$this->getInfo ( $id, true, $info );
		$info=$this->getInfo($id);
		$info['prize_count']-=1;
		$res=$this->where(array('id'=>$id))->setField('prize_count',$info['prize_count']);
		if($res){
			$this->getInfo($id,true);
		}
		return $res;
	}
}
