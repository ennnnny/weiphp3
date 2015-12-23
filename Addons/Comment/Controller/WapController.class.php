<?php

namespace Addons\Comment\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	/*
	 * addons_url('Comment://Wap/add');
	 * POST数据：aim_table，aim_id，content
	 */
	function add() {
		$data ['aim_table'] = I ( 'aim_table' );
		$data ['aim_id'] = I ( 'aim_id' );
		$data ['content'] = I ( 'content' );
		$data ['follow_id'] = $this->mid;
		$data ['cTime'] = NOW_TIME;
		$data ['uid'] = session ( 'manager_id' );
		$sensitiveStr = C ( 'SENSITIVE_WORDS' );
		$sensitiveArr = explode ( ',', $sensitiveStr );
		$badkeywords = array_combine ( $sensitiveArr, array_fill ( 0, count ( $sensitiveArr ), '***' ) );
		foreach ( $badkeywords as $k => $v ) {
			// if (mb_strlen ( $k, 'utf8' ) <= 2) {
			// unset ( $badkeywords [$k] );
			// }
			if (empty ( $k )) {
				unset ( $badkeywords [$k] );
			}
		}
		$data ['is_audit'] = 1;
		if ($data ['content'] == strtr ( $data ['content'], $badkeywords )) {
			if ($data ['aim_table'] == 'sports') {
				$sports = D ( 'Addons://Sports/Sports' )->getInfo ( $data ['aim_id'] );
				if ($sports ['comment_status'] != 0) {
					$data ['is_audit'] = 0;
				}
			} else if ($data ['aim_table'] == 'lzwg') {
				$lzwg = D ( 'Addons://Draw/Draw' )->getInfo ( $data ['aim_id'] );
				if ($lzwg ['comment_status'] != 0) {
					$data ['is_audit'] = 0;
				}
			} else if ($data ['aim_table'] == 'we_media') {
				$map ['id'] = $data ['aim_id'];
				M ( 'we_media' )->where ( $map )->setInc ( 'comment_count' );
			}else if($data['aim_table']=='business_card'){
			    //保存留言者姓名、手机号码
			    $save['truename']=I('truename');
			    $save['mobile']=I('mobile');
			    D ( 'Common/User' )->updateInfo($data ['follow_id'],$save);
			    $data ['uid']=I('uid');
			}
			$res ['id'] = D ( 'Comment' )->addComment ( $data );
			$res ['content'] = parseComment ( $data ['content'] );
			$res ['result'] = 'success';
		} else {
			$res ['result'] = 'fail';
		}
		$this->ajaxReturn ( $res, 'JSON' );
	}
	// addons_url('Comment://Wap/get', array('aim_id'=>$sports_id,'aim_table'=>'sports'));
	function get() {
		$aim_table = I ( 'aim_table' );
		$aim_id = I ( 'aim_id' );
		
		$list = D ( 'Comment' )->getComment ( $aim_id, $aim_table );
		if ($list == null)
			$list = array ();
		$sensitiveStr = C ( 'SENSITIVE_WORDS' );
		$sensitiveArr = explode ( ',', $sensitiveStr );
		$badkeywords = array_combine ( $sensitiveArr, array_fill ( 0, count ( $sensitiveArr ), '***' ) );
		foreach ( $badkeywords as $k => &$v ) {
			// if (mb_strlen ( $k, 'utf8' ) <= 2) {
			// unset ( $badkeywords [$k] );
			// }
			if (empty ( $k )) {
				unset ( $badkeywords [$k] );
			}
		}
		foreach ( $list as &$v ) {
			$v ['content'] = strtr ( $v ['content'], $badkeywords );
			$v ['content'] = parseComment ( $v ['content'] );
			$v ['time'] = time_format ( $v ['cTime'] );
			// dump($v['content']);
		}
		// dump($list);
		$this->ajaxReturn ( $list, 'JSON' );
	}
}
