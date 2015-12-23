<?php

namespace Addons\CardVouchers\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	/*
	function index() {
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['openid'] = '';
		$sha1 ['code'] = '';
		$sha1 ['balance'] = '';
		
		$sha1 ['appsecre'] = '089211161352804511ab0b658be7790f';
		$sha1 ['card_id'] = $card_id = 'pY-EguGiW-3rSt5MHl6c9dygY--c';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		// dump($sha1);
		$this->assign ( 'info', $sha1 );
		$this->display ();
	}
	function index2() {
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['openid'] = get_openid ();
		$sha1 ['code'] = '';
		$sha1 ['balance'] = '';
		
		$sha1 ['appsecre'] = '3f8809558dbfe261c1556b5c7550f369';
		$sha1 ['card_id'] = $card_id = 'pDYw9uNN6GWEuFjq4lCC2AOYYruc';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		// dump($sha1);
		$this->assign ( 'info', $sha1 );
		$this->display ( 'index' );
	}
	function index3() {
		$sha1 ['timestamp'] = NOW_TIME;
		// $sha1 ['openid'] = '';
		// $sha1 ['code'] = '';
		// $sha1 ['balance'] = '';
		
		$sha1 ['appsecre'] = '089211161352804511ab0b658be7790f';
		$sha1 ['card_id'] = $card_id = 'pY-EguES0EhZZTIQnE9KoDZSiFZQ';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		
		$str = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		$this->assign ( 'card_ext', $str );
		$this->assign ( 'info', $sha1 );
		$this->display ( 'index' );
	}
	function index4() {
		$sha1 ['timestamp'] = NOW_TIME;
		
		$sha1 ['appsecre'] = '089211161352804511ab0b658be7790f';
		$sha1 ['card_id'] = 'pY-EguCvSac01XujgRHjWJsjaqHU';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		$sha1 ['openid'] = '';
		$sha1 ['code'] = '';
		// $sha1 ['balance'] = '';
		
		$str = json_encode ( $sha1 );
		
		// $str = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		$this->assign ( 'card_ext', $str );
		$this->assign ( 'info', $sha1 );
		$this->display ( 'index' );
	}
	*/
	function index() {		
		$id = I ( 'id' );
		$info = D ( 'CardVouchers' )->getInfo ( $id );
		$public_info = get_token_appinfo ();
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['appsecre'] = trim($info ['appsecre']);
		$sha1 ['card_id'] = $card_id = trim($info ['card_id']);
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		
		$info ['card_ext'] = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		$this->assign ( 'info', $info );
		$this->assign ('public_info',$public_info);
		
		$this -> display();
	}
}
