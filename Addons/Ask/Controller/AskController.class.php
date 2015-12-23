<?php

namespace Addons\Ask\Controller;

use Home\Controller\AddonsController;

class AskController extends AddonsController {
	function index() {
		$info = $public_info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['ask_id'] = $ask_id = I ( 'id' );
		
		$url = addons_url ( "Ask://Ask/ask", $param );
		
		$ask = D ( 'Ask' )->getAskInfo ( $ask_id );
		$this->assign ( 'ask', $ask );
		$this->assign ( 'button_name', '马上开始' );
		
		$this->assign ( 'jumpURL', $url );
		// dump ( $content );
		// exit ();
		$this->assign ( 'public_info', $public_info );
		$this->display ();
	}
	function preview() {
		$id = I ( 'id', 0, 'intval' );
		$url = U ( 'index', array (
				'id' => $id 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function ask_question() {
		$param ['ask_id'] = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Ask://Question/lists', $param );
		// dump($url);
		redirect ( $url );
	}
	function ask_answer() {
		$param ['ask_id'] = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Ask://Answer/lists', $param );
		// dump($url);
		redirect ( $url );
	}
	function show() {
		$ask_id = intval ( $_REQUEST ['ask_id'] );
		$ask = D ( 'Ask' )->getAskInfo ( $ask_id );
		
		$this->assign ( 'ask', $ask );
		
		$answer = D ( 'AskAnswer' )->myLastAnswer ( $ask_id );
		if ($answer) {
			$this->assign ( 'button_name', '继续抢答' );
		} else {
			$this->assign ( 'button_name', '马上开始' );
		}
		
		$this->display ();
	}
	function ask() {
		$param ['ask_id'] = $ask_id = intval ( $_REQUEST ['ask_id'] );
		$ask = D ( 'Ask' )->getAskInfo ( $ask_id );
		$question_list = D ( 'AskQuestion' )->getQuestionsByAskid ( $ask_id );
		$answer = D ( 'AskAnswer' )->myLastAnswer ( $ask_id );
		$last_question = $question_list [$answer ['question_id']];
		$times = 0; // 次数
		
		if (($answer ['is_correct'] && $last_question ['is_last']) || ($answer ['question_id'] && ! $last_question)) {
			// 重新开始进入
			reset ( $question_list );
			$question = current ( $question_list );
			$times = $answer ['times'] + 1;
		} elseif ($answer) {
			$times = intval ( $answer ['times'] );
			$param ['time_left'] = $last_question ['wait_time'] - (NOW_TIME - $answer ['cTime']);
			if ($answer ['is_correct']) { // 上次已经回答正确
				if ((NOW_TIME - $answer ['cTime']) > $last_question ['wait_time']) {
					// 已经到时间了,显示下一题
					$flat = false;
					foreach ( $question_list as $qid => $v ) {
						if ($flat) {
							$question = $v;
							$flat = false;
							break;
						}
						if ($qid == $answer ['question_id']) {
							$flat = true;
						}
					}
				} else {
					// 还没到时间，显示等待界面
					redirect ( U ( 'wait', $param ) );
				}
			} else { // 上次已经回答不对时
				if ($param ['time_left'] > 0) { // 不到时间还不能重新回答
					redirect ( U ( 'fail', $param ) );
					// dump("aaaa");
				} else { // 可重新回答
					$question = $last_question;
				}
			}
		} else {
			// 第一次进入
			reset ( $question_list );
			$question = current ( $question_list );
		}
		
		// 概率进入
		$wait = session ( 'percent_wait_' . $this->mid );
		if ($wait) {
			$time_left = $wait ['wait'] - (NOW_TIME - $wait ['time']);
			if ($time_left > 0) {
				redirect ( U ( 'percent', $param ) );
			} else {
				session ( 'percent_wait_' . $this->mid, null );
			}
		}
		
		$percent = intval ( $question ['percent'] );
		if ($percent < 100) {
			$rand = rand ( 1, 100 );
			if ($percent < $rand) { // 未抢中
				$wait = array (
						'time' => NOW_TIME,
						'wait' => $question ['wait_time'] 
				);
				session ( 'percent_wait_' . $this->mid, $wait );
				
				redirect ( U ( 'percent', $param ) );
			}
		}
		// dump ( $question );
		$extra = parse_config_attr ( $question ['extra'] );
		
		$this->assign ( 'ask', $ask );
		$this->assign ( 'question', $question );
		$this->assign ( 'extra', $extra );
		$this->assign ( 'times', $times );
		
		$this->display ();
	}
	function doAsk() {
		$dao = D ( 'AskAnswer' );
		$ask_id = $param ['ask_id'] = I ( 'get.ask_id' );
		$question_id = I ( 'post.question_id', 0, 'intval' );
		$an = empty ( $_POST ['other_answer'] ) ? I ( 'post.answer' ) : I ( 'post.other_answer' );
		
		$question = D ( 'AskQuestion' )->getQuestionsByAskid ( $ask_id, $question_id );
		if ($question ['type'] == 'radio') {
			$an = array (
					$an 
			);
		}
		
		$data ['is_correct'] = $this->_checkAnswer ( $question, $an );
		$data ['ask_id'] = $ask_id;
		$data ['uid'] = $this->mid;
		$data ['cTime'] = NOW_TIME;
		$data ['answer'] = serialize ( $an );
		$data ['times'] = I ( 'post.times' );
		$data ['token'] = get_token ();
		$data ['question_id'] = $question_id;
		$data ['openid'] = get_openid ();
		
		if ($data ['openid'] == NULL) {
			$data ['openid'] = - 1;
		}
		$id = $dao->hasAnswer ( $this->mid, $question_id, $data ['times'] );
		if ($id) {
			$dao->update ( $id, $data );
		} else {
			$dao->delayAdd ( $data );
		}
		
		if ($data ['is_correct']) {
			$url = $question ['is_last'] ? U ( 'finish', $param ) : U ( 'wait', $param );
			redirect ( $url );
		} else {
			redirect ( U ( 'fail', $param ) );
		}
	}
	// 判断答题是否正确
	function _checkAnswer($question, $answer) {
		if (! is_array ( $answer )) {
			$answer = array (
					$answer 
			);
		}
		
		$answer = array_filter ( $answer );
		$answer = array_map ( 'trim', $answer );
		if (empty ( $answer )) {
			return 0;
		}
		
		$correct = preg_split ( '/[\s,;]+/', $question ['answer'] );
		$correct = array_filter ( $correct );
		$correct = array_map ( 'trim', $correct );
		
		$diff = array_diff ( ( array ) $correct, ( array ) $answer );
		$diff2 = array_diff ( ( array ) $answer, ( array ) $correct );
		return empty ( $diff ) && empty ( $diff2 ) ? 1 : 0;
	}
	function wait($html = 'wait') {
		/*
		 * $ask_id = I ( 'ask_id', 0, 'intval' );
		 * if (! $ask_id) {
		 * $this->error ( '参数非法' );
		 * }
		 *
		 * $ask = D ( 'Ask' )->getAskInfo ( $ask_id );
		 * $this->assign ( 'ask', $ask );
		 *
		 * if ($html == 'percent') {
		 * $wait = session ( 'percent_wait_' . $this->mid );
		 * $time_left = $wait ['wait'] - (NOW_TIME - $wait ['time']);
		 * } elseif (isset ( $_GET ['time_left'] )) {
		 * $time_left = I ( 'get.time_left' );
		 * } else {
		 * $answer = D ( 'AskAnswer' )->myLastAnswer ( $ask_id );
		 * $question = D ( 'AskQuestion' )->getQuestionsByAskid ( $ask_id, $answer ['question_id'] );
		 *
		 * $time_left = $question ['wait_time'] - (NOW_TIME - $answer ['cTime']);
		 * }
		 * $this->assign ( 'time_left', $time_left );
		 *
		 * $this->display ( $html );
		 */
		$this->display ();
	}
	function fail() {
		$this->wait ( 'fail' );
	}
	function percent() {
		$this->wait ( 'percent' );
	}
	function finish() {
		$ask_id = I ( 'ask_id', 0, 'intval' );
		$info = D ( 'Ask' )->getAskInfo ( $ask_id );
		if (! empty ( $info ['card_id'] ) && ! empty ( $info ['appsecre'] )) {
			$sha1 ['timestamp'] = NOW_TIME;
			$sha1 ['appsecre'] = trim ( $info ['appsecre'] );
			$sha1 ['card_id'] = $card_id = trim ( $info ['card_id'] );
			$sha1 ['signature'] = getSHA1 ( $sha1 );
			
			$info ['card_ext'] = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		}
		
		$this->assign ( 'info', $info );
		
		// 增加积分
		// add_credit ( 'ask' );
		
		$this->display ();
	}
	// 第一步：查看缓存配置并设置一个缓存值
	function test_cache() {
		dump ( C ( 'DATA_CACHE_TYPE' ) );
		dump ( C ( 'MEMCACHE_HOST' ) );
		dump ( C ( 'MEMCACHE_PORT' ) );
		
		$key = 'test_cache1';
		$vv = I ( 'val', 'test_cache value' );
		dump ( S ( $key, $vv ) );
		$val = S ( $key );
		dump ( $val );
	}
	// 第二步：读取缓存值，看看第一步的缓存是否设置成功
	function test_cache2() {
		$key = 'test_cache1';
		$val = S ( $key );
		dump ( $val );
		if ($val == 'test_cache value') {
			dump ( '缓存读取成功' );
		} else {
			dump ( '缓存读取失败' );
		}
	}
	function test_cache3() {
		$key = 'test_cache3';
		$time = NOW_TIME;
		$val = S ( $key, $time, 10 );
		dump ( $time );
		dump ( $val );
	}
	function test_cache4() {
		$key = 'test_cache3';
		$val = S ( $key );
		dump ( $val );
		dump ( NOW_TIME - $val );
	}
}
