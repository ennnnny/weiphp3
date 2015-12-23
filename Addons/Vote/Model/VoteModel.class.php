<?php

namespace Addons\Vote\Model;

use Think\Model;

/**
 * Vote模型
 */
class VoteModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'Vote_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	function update($id, $save = array()) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$this->getInfo ( $id, true );
		}
		return $res;
	}
	// 通用的清缓存的方法
	function clear($ids, $type = '', $uid = '') {
		is_array ( $ids ) || $ids = explode ( ',', $ids );
		
		foreach ( $ids as $id ) {
			$this->getInfo ( $id, true );
		}
	}
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
	function getPackageData($vote_id) {
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['vote_id'] = $vote_id;
		
		$data ['jumpURL'] = addons_url ( "Vote://Vote/Vote", $param );
		
		$data ['vote'] = $this->getInfo ( $vote_id );
		$data ['button_name'] = '马上开始';
		
		return $data;
	}

}
