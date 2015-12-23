<?php

namespace Addons\Invite\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	// 邀约列表
	public function lists() {
		$map ['token'] = get_token ();
		$list = M ( 'invite' )->where ( $map )->order ( 'id desc' )->selectPage ();
		
		$this->assign ( $list );
		$this->display ();
	}
	// 领取优惠券
	function receive() {
		$info = D ( 'Invite' )->getInfo ( I ( 'id' ) );
		$this->assign ( 'info', $info );
		
		$this->deal ( $info );
		
		$detail_url = addons_url ( 'Invite://Wap/detail', array (
				'id' => $info ['id'],
				'invite_uid' => $this->mid,
				'token' => get_token () 
		) );
		$map ['uid'] = $this->mid;
		$map ['invite_id'] = $info ['id'];
		$recode = M ( 'invite_user' )->where ( $map )->find ();
		if ($recode) {
			redirect ( $detail_url );
		}
		
		// 优惠券是否领取 完或者到期
		$data = M ( 'coupon' )->find ( $info ['coupon_id'] );

		if ($data ['start_time'] > NOW_TIME) {
// 			$url = addons_url ( 'WeiSite://WeiSite/lists' );
			$url = addons_url ( 'Invite://Wap/lists' );
			$this->error ( '本次微邀约还没开始，下次再来吧。', $url );
		}
		if ($data ['num'] <= $data ['collect_count'] || ($data ['end_time'] > 0 && $data ['end_time'] < NOW_TIME)) {
// 			$url = addons_url ( 'WeiSite://WeiSite/lists' );

		    $url = addons_url ( 'Invite://Wap/lists' );
			$this->error ( '本次微邀约已经结束，下一次再来吧。', $url );
		}
		
		// 减少经历值
		$user = get_mult_userinfo ( $this->mid );//dump($user);die();
		
		if ($info ['experience'] > $user ['experience']) {
			$url = addons_url ( 'Invite://Wap/lists' );
			$this->error ( '你的经历值不足', $url );
		}
		
		// 增加领取记录
		$map ['invite_num'] = 0;
		M ( 'invite_user' )->add ( $map );
		$credit ['experience'] = 0 - $info ['experience'];
		$credit ['score'] = 0;
		add_credit ( 'invite', 5, $credit );
		
		// 发放优惠券
		$data ['sn'] = uniqid ();
		$data ['uid'] = $this->mid;
		$data ['cTime'] = time ();
		$data ['addon'] = 'Coupon';
		$data ['target_id'] = $info ['coupon_id'];
		$data ['can_use'] = 0;
		
		$data ['prize_id'] = 0;
		$data ['prize_title'] = '';
		unset ( $data ['id'] );
		// dump ( $data );
		
		$res = D ( 'Common/SnCode' )->delayAdd ( $data );
		if ($res) {
			// 更新获取数
			// M ( "coupon" )->where ( 'id=' . $info ['coupon_id'] )->setInc ( "collect_count" );
			M ( "invite" )->where ( 'id=' . $info ['id'] )->setInc ( "receive_num" );
		} else {
			$this->error ( '优惠券发送失败' );
		}
		
		redirect ( $detail_url );
	}
	// 领取成功
	function detail() {
		if (! isset ( $_GET ['invite_uid'] )) {
			$param ['id'] = I ( 'id' );
			$param ['invite_uid'] = $this->mid;
			$param ['token'] = get_token ();
			redirect ( U ( 'detail', $param ) );
		}
		
		$map ['invite_id'] = I ( 'id' );
		$map ['uid'] = $this->mid;
		// $map ['token'] = get_token ();
		$info = D ( 'Invite' )->getInfo ( $map ['invite_id'] );
		$this->assign ( 'info', $info );
		
		$this->deal ( $info );
		
		$recode = M ( 'invite_user' )->where ( $map )->find ();
		if (empty ( $_GET ['invite_uid'] )) {
			$recode ['invite_uid'] = $info ['uid'];
		} else {
			$recode ['invite_uid'] = I ( 'get.invite_uid' );
		}
		$this->assign ( 'recode', $recode );
	//	dump ( $recode );
		// get_nickname ();
		
		// dump ( D ( 'Common/SnCode' )->getInfoById ( 30109 ) );
		
		$this->display ();
	}
	function deal($info) {
		// 记录邀约登记
		// dump ( $_GET ['invite_uid'] );die();
		// dump ( $this->mid );
		// dump ( ! empty ( $_GET ['invite_uid'] ) && $_GET ['invite_uid'] != $this->mid );
		if (! empty ( $_GET ['invite_uid'] ) && $_GET ['invite_uid'] != $this->mid) { // && $this->mid>0
			$map ['invite_id'] = I ( 'id' );
			$map2 ['uid'] = $map ['uid'] = I ( 'invite_uid' );
			$res = M ( 'invite_user' )->where ( $map )->setInc ( 'invite_num' );
			// dump ( $res );
			// lastsql ();
			$invite_num = M ( 'invite_user' )->where ( $map )->getField ( 'invite_num' );
			// dump ( $invite_num );
			// lastsql ();
			$map2 ['addon'] = 'Coupon';
			$map2 ['target_id'] = $info ['coupon_id'];
			$sn_id = D ( 'Common/SnCode' )->where ( $map2 )->getField ( 'id' );
			$can_use = D ( 'Common/SnCode' )->getInfoById ( $sn_id, 'can_use' );
			// dump ( $can_use );
			// lastsql ();
			if (! $can_use && $invite_num >= $info ['num']) {
				$save ['can_use'] = 1;
				D ( 'Common/SnCode' )->update ( $sn_id, $save );
			}
		}
		// exit ();
	}
	function index(){ 
		$info = $public_info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['id'] = I ( 'id' );
		
		$url = addons_url ( 'Invite://Wap/receive', $param );
		$this->assign ( 'jumpURL', $url );
		$this -> assign('public_info',$public_info);
		$info = D ( 'Invite' )->getInfo ( $param ['id'] );
		$this->assign ( 'info', $info );
		$this->assign ( 'button_name', '我要参与' );

		$this -> display();
		
	}
}
