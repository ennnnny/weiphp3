<?php

namespace Addons\Scratch\Model;

use Think\Model;

/**
 * Scratch模型
 */
class ScratchModel extends Model {
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
		$return ['public_info'] = $public_info = get_token_appinfo ();
		$id = $map ['target_id'] = I ( 'id' );
		$return ['data'] = $data = M ( 'scratch' )->find ( $id );
		// dump($data);
		
		// 奖项
		$map ['addon'] = 'Scratch';
		$return ['prizes'] = $prizes = M ( 'prize' )->where ( $map )->select ();
		
		// 抽奖记录
		$all_prizes = M ( 'sn_code' )->where ( $map )->order ( 'id desc' )->select ();
		// dump ( $all_prizes );
		foreach ( $all_prizes as $all ) {
			if ($all ['prize_id'] > 0) {
				$has [$all ['prize_id']] += 1; // 每个奖项已经中过的次数
				$new_prizes [] = $all; // 最新中奖记录
				$all ['uid'] == $GLOBALS ['mid'] && $my_prizes [] = $all; // 我的中奖记录
			} else {
				$no_count += 1; // 没有中奖的次数
			}
			
			// 记录我已抽奖的次数
			$all ['uid'] == $GLOBALS ['mid'] && $my_count += 1;
		}
		$return ['new_prizes'] = $new_prizes;
		$return ['my_prizes'] = $my_prizes;
		// dump ( $new_prizes );
		// dump ( $my_prizes );
		
		// 权限判断
		unset ( $map );
		$follow = get_followinfo ( $GLOBALS ['mid'] );
		$is_admin = is_login ();
		$error = '';
		if ($data ['end_time'] <= time ()) {
			$error = '活动已结束';
		} else if ($data ['max_num'] > 0 && $data ['max_num'] <= $my_count) {
			$error = '您的刮卡机会已用完啦';
		} else if ($data ['follower_condtion'] > intval ( $follow ['status'] ) && ! $is_admin) {
			switch ($data ['follower_condtion']) {
				case 1 :
					$error = '关注后才能参与';
					break;
				case 2 :
					$error = '用户绑定后才能参与';
					break;
				case 3 :
					$error = '领取会员卡后才能参与';
					break;
			}
		} else if ($data ['credit_conditon'] > intval ( $follow ['score'] ) && ! $is_admin) {
			$error = '您的金币值不足';
		} else if ($data ['credit_bug'] > intval ( $follow ['score'] ) && ! $is_admin) {
			$error = '您的金币值不够扣除';
		} else if (! empty ( $data ['addon_condition'] )) {
			addon_condition_check ( $data ['addon_condition'] ) || $error = '您没权限参与';
		}
		$return ['error'] = $error;
		// 抽奖算法
		if (empty ( $error )) {
			$return ['prize'] = $this->_lottery ( $data, $prizes, $new_prizes, $my_count, $has, $no_count );
		}
		
		// 添加模板目录
		$return ['template'] = $data ['template'] == "" ? "default" : $data ['template'];
		
		return $return;
	}
	// 抽奖算法 中奖概率 = 奖品总数/(预估活动人数*每人抽奖次数)
	function _lottery($data, $prizes, $new_prizes, $my_count = 0, $has = array(), $no_count = 0) {
		$max_num = empty ( $data ['max_num'] ) ? 1 : $data ['max_num'];
		$count = $data ['predict_num'] * $max_num; // 总基数
		                                       // 获取已经中过的奖
		foreach ( $prizes as $p ) {
			$prizesArr [$p ['id']] = $p;
			
			$prize_num = $p ['num'] - $has [$p ['id']];
			for($i = 0; $i < $prize_num; $i ++) {
				$rand [] = $p ['id']; // 中奖的记录，同时通过ID可以知道中的是哪个奖
			}
		}
		// dump ( $rand );
		// dump ( $prizesArr );
		
		if ($data ['predict_num'] != 1) {
			$remain = $count - count ( $rand ) - $no_count;
			$remain > 5000 && $remain = 5000; // 防止数组过大导致内存溢出
			for($i = 0; $i < $remain; $i ++) {
				$rand [] = 0; // 不中奖的记录
			}
		}
		if (empty ( $rand )) {
			$rand [] = - 1;
		}
		
		shuffle ( $rand ); // 所有记录随机排序
		$prize_id = $rand [0]; // 第一个记录作为当前用户的中奖记录
		
		$prize = array ();
		
		if ($prize_id > 0) {
			$prize = $prizesArr [$prize_id];
		} elseif ($prize_id == - 1) {
			$prize ['id'] = 0;
			$prize ['title'] = '奖品已抽完';
		} else {
			$prize ['id'] = 0;
			$prize ['title'] = '谢谢参与';
		}
		
		// 获取我的抽奖机会
		if (empty ( $data ['max_num'] )) {
			$prize ['count'] = 1;
		} else {
			$prize ['count'] = $max_num - $my_count - 1;
			$prize ['count'] < 0 && $prize ['count'] = 0;
		}
		return $prize;
	}
	
	function getScratchInfo($id, $update = false, $data = array()) {
		$key = 'Scratch_getScratchInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	// 通用的清缓存的方法
	function clear($ids, $type = '', $uid = '') {
		is_array ( $ids ) || $ids = explode ( ',', $ids );
		foreach ( $ids as $id ) {
			$this->getScratchInfo ( $id, true );
		}
	}
	
	function updateCount($id, $save = array()){
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$this->getScratchInfo ( $id, true );
		}
		return $res;
	}
	
	
}
