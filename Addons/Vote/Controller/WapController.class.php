<?php

namespace Addons\Vote\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	function index() {
		$voteId = I ( 'vote_id', 0, 'intval' );
		if (! $voteId) {
			$this->error ( '未配置活动' );
		}
		// 投票活动信息
		$voteInfo = D ( 'Addons://Vote/ShopVote' )->getInfo ( $voteId );
		// 判断是否过期
		if ($this->_is_overtime ( $voteInfo )) {
			// 过期
			$isOvertime = 1;
		} else {
			$isOvertime = 0;
		}
		
		$this->_options ( $voteInfo );
		
		$this->assign ( 'vote_info', $voteInfo );
		$this->assign ( 'overtime', $isOvertime );
		$this->display ();
	}
	
	// 已过期返回 true ,否则返回 false
	private function _is_overtime($voteInfo) {
		if (! empty ( $voteInfo ['start_time'] ) && $voteInfo ['start_time'] > NOW_TIME)
			return ture;
		
		if (! empty ( $voteInfo ['end_date'] ) && $voteInfo ['end_time'] <= NOW_TIME)
			return ture;
		
		return false;
	}
	// 判断用户用户是否已经参加
	private function _is_join($voteInfo, $optionId = 0) {
		$selectType = $voteInfo ['select_type'];
		if ($selectType == 1) {
			$limitNum = 1;
		} elseif ($selectType == 2) {
			$limitNum = $voteInfo ['multi_num'];
		}
		$uid = $this->mid;
		$voteLog = D ( 'Addons://Vote/ShopVoteOption' )->getUserVoteLog ( $voteInfo ['id'], $uid );
		
		$is_in = false;
		foreach ( $voteLog as $vo ) {
			if ($vo ['option_id'] == $optionId) {
				$is_in = true;
			}
		}
		$count = count ( $voteLog );
		// dump ( $voteLog );exit;
		if ($count >= $limitNum || $is_in) {
			return true;
		}
		return false;
	}
	
	// 用户投票信息
	function join() {
		$voteId = I ( 'vote_id' );
		$optionId = I ( 'option_id' );
		$verify = I('verify');
		
		$voteInfo = D ( 'Addons://Vote/ShopVote' )->getInfo ( $voteId );
		if ($this->_is_overtime ( $voteInfo )) {
			// $this->error ( "请在指定的时间内投票" );
			
			$ajax_result ['error'] = '投票活动时间已经过期';
			$this->ajaxReturn ( $ajax_result );
			return false;
		}
		if ($this->_is_join ( $voteInfo, $optionId )) {
			// $this->error ( "您已经投过,请不要重复投" );
			$ajax_result ['error'] = '您已经投过票了';
			$this->ajaxReturn ( $ajax_result );
			return false;
		}
		if($voteInfo['is_verify'] && (empty($verify) || !check_verify ( $verify ))){
			$ajax_result ['error'] = '请输入正确的验证码';
			$this->ajaxReturn ( $ajax_result );
			return false;
		}
		
		// 如果没投过，就添加
		$data ["uid"] = $this->mid;
		$data ["vote_id"] = $voteId;
		$data ["token"] = get_token ();
		$data ["ctime"] = time ();
		$data ['option_id'] = $optionId;
		$addid = M ( 'shop_vote_log' )->add ( $data );
		
		$newlog = D ( 'Addons://Vote/ShopVoteOption' )->getUserVoteLog ( $voteId, $data ["uid"], true );
		// 更新投票数
		$map ['id'] = $optionId;
		D ( 'Addons://Vote/ShopVoteOption' )->where ( $map )->setInc ( 'opt_count' );
		D ( 'Addons://Vote/ShopVoteOption' )->getInfo ( $optionId, true );
		D ( 'Addons://Vote/ShopVoteOption' )->getOptions ( $voteId, true );
		if ($addid) {
			if ($voteInfo ['select_type'] == 2) {
				$also = $voteInfo ['multi_num'] - count ( $newlog );
			}
			if ($also <= 0) {
				$ajax_result ['success'] = '投票成功';
			} else {
				$ajax_result ['success'] = '投票成功,你还可再投' . $also . ' 票！';
			}
		} else {
			$ajax_result ['error'] = '投票失败';
		}
		
		$this->ajaxReturn ( $ajax_result );
	}
	
	// 选项详细信息
	function option_detail() {
		$optId = I ( 'option_id' );
		$voteId = I ( 'vote_id' );
		
		// 投票活动信息
		$voteInfo = D ( 'Addons://Vote/ShopVote' )->getInfo ( $voteId );
		// 判断是否过期
		if ($this->_is_overtime ( $voteInfo )) {
			// 过期
			$isOvertime = 1;
		} else {
			$isOvertime = 0;
		}
		
		$this->_options ( $voteInfo, $optId );
		
		$this->assign ( 'vote_info', $voteInfo );
		$this->assign ( 'overtime', $isOvertime );
		$this->display ();
	}
	function _options($voteInfo, $option_id = 0) {
		// 活动选项信息
		$optionInfo = D ( 'Addons://Vote/ShopVoteOption' )->getOptions ( $voteInfo ['id'] );
		
		$voteLog = D ( 'Addons://Vote/ShopVoteOption' )->getUserVoteLog ( $voteInfo ['id'], $this->mid );
		foreach ( $voteLog as $log ) {
			$logArr [$log ['option_id']] = 1;
		}
		
		$vote_count = 0;
		$finish_vote = 1;
		foreach ( $optionInfo as $vo ) {
			$vote_count += $vo ['has_vote'] = intval ( $logArr [$vo ['id']] );
			$options [$vo ['id']] = $vo;
		}
		if ($voteInfo ['select_type'] == 1) {
			$limitNum = 1;
		} else {
			$limitNum = $voteInfo ['multi_num'];
		}
		// 判断用户是否已经完成全部投票
		if ($limitNum > $vote_count) {
			$finish_vote = 0;
		}
		
		if ($option_id) {
			$this->assign ( 'option', $options [$option_id] );
		} else {
			$this->assign ( 'options', $options );
		}
		$this->assign ( 'finish_vote', $finish_vote );
	}
	
	function verify(){
		//暂且不用
		$this ->display();
	}
	/* 验证码，用于投票 */
	function verify_img() {
		$verify = new \Think\Verify ();
		$verify->entry ( 1 );
	}
}
