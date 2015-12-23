<?php

namespace Addons\Survey\Model;

use Think\Model;

/**
 * Survey模型
 */
class SurveyModel extends Model {
	// 素材相关
	function getSucaiList($search = '') {
		$map['token'] = get_token();
		$map['uid'] = session('mid');
		empty ( $search ) || $map ['title'] = array (
				'like',
				"%$search%" 
		);
		
		$data_list = $this->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->selectPage ();
		
		return $data_list;
	}
	function getPackageData($id) {
		$id = I ( 'id', 0, 'intval' );
		$map ['token'] = get_token ();
		$return ['public_info'] = get_token_appinfo ( $map ['token'] );
		$return ['info'] = M ( 'survey' )->where ( $map )->find ();
		// 添加模板目录
		$return ['template'] = $return ['info'] ['template'] == "" ? "default" : $return ['info'] ['template'];
		
		return $return;
	}
	
	function getSurveyInfo($id, $update = false, $data = array()) {
		$key = 'Survey_getSurveyInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['id'] = $id;
			$info = ( array ) (empty ( $data ) ? $this->where ( $map )->find () : $data);
				
			S ( $key, $info );
		}
	
		return $info;
	}
	// 通用的清缓存的方法
	function clear($ids, $type = '', $uid = '') {
		is_array ( $ids ) || $ids = explode ( ',', $ids );
	
		foreach ( $ids as $id ) {
			$this->getSurveyInfo ( $id, true );
		}
	}
	
}
