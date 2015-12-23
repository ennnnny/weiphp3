<?php

namespace Addons\Wecome\Model;

use Home\Model\WeixinModel;

/**
 * Vote模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Wecome' ); // 获取后台插件的配置参数
		if ($dataArr ['Content'] == 'subscribe') {
			$uid = D ( 'Common/Follow' )->init_follow ( $dataArr ['FromUserName'] );
			D ( 'Common/Follow' )->set_subscribe ( $dataArr ['FromUserName'], 1 );
			// 增加积分
			session ( 'mid', $uid );
			
			add_credit ( 'subscribe' );
			
			// 关注公众号获取会员卡号
			//D ( 'Addons://Card/Card' )->init_card_member ( $dataArr ['FromUserName'] );
			
			$has_return = false;
			if (! empty ( $dataArr ['EventKey'] )) {
				$has_return = $this->scan ( $dataArr, $keywordArr, $config );
			}
			if ($has_return)
				return true;
				
				// 其中token和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
			$param ['token'] = get_token ();
			$param ['openid'] = get_openid ();
			
			$sreach = array (
					'[follow]',
					'[website]',
					'[token]',
					'[openid]' 
			);
			$replace = array (
					addons_url ( 'UserCenter://Wap/bind', $param ),
					addons_url ( 'WeiSite://WeiSite/index', $param ),
					$param ['token'],
					$param ['openid'] 
			);
			$config ['description'] = str_replace ( $sreach, $replace, $config ['description'] );
			
			switch ($config ['type']) {
				case '3' :
// 					$articles [0] = array (
// 							'Title' => $config ['title'],
// 							'Description' => $config ['description'],
// 							'PicUrl' => get_cover_url ( $config ['pic_url'] ),
// 							'Url' => str_replace ( $sreach, $replace, $config ['url'] ) 
// 					);
				    $res = D ( 'Common/Custom' )->replyNews ( $uid, $config ['appmsg_id'] );
// 					$res = $this->replyNews ( $articles );
					break;
				case '2' :
					return false;
					break;
				default :
					$res = $this->replyText ( $config ['description'] );
			}
		} elseif ($dataArr ['Content'] == 'scan') {
			$this->scan ( $dataArr, $keywordArr, $config );
		} elseif ($dataArr ['Content'] == 'unsubscribe') {
			// 直接删除用户
			$map1 ['openid'] = $dataArr ['FromUserName'];
			$map1 ['token'] = get_token ();
			$map2 ['uid'] = D ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			M ( 'public_follow' )->where ( $map1 )->delete ();
			M ( 'user' )->where ( $map2 )->delete ();
			M ( 'credit_data' )->where ( $map2 )->delete ();
			session ( 'mid', null );
			// D ( 'Common/Follow' )->set_subscribe ( $dataArr ['FromUserName'], 0 );
			// 增加积分
			add_credit ( 'unsubscribe' );
		} elseif ($dataArr ['Content'] == '获取内测码') {
			$map ['openid'] = $dataArr ['FromUserName'];
			$code = M ( 'invite_code' )->where ( $map )->getField ( 'code' );
			if (! $code) {
				$code = $map ['code'] = substr ( uniqid (), - 5 );
				M ( 'invite_code' )->add ( $map );
			}
			$this->replyText ( '您的内测码是：' . $code . ', 注意：内测码只能使用一次，再次注册时需要重新获取内测码' );
		}
	}
	function scan($dataArr, $keywordArr = array(), $config = array()) {
		$map ['scene_id'] = ltrim ( $dataArr ['EventKey'], 'qrscene_' );
		$map ['token'] = get_token ();
		$qr = M ( 'qr_code' )->where ( $map )->find ();
		if ($qr ['addon'] == 'UserCenter') { // 设置用户分组
			$group = D ( 'Home/AuthGroup' )->move_group ( $GLOBALS ['mid'], $qr ['aim_id'] );
			
			$this->replyText ( '您已加入' . $group ['title'] );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} else if ($qr ['addon'] == 'Shop') {
			$savedata ['openid'] = $map1 ['openid'] = $dataArr ['FromUserName'];
			$map1 ['token'] = get_token ();
			$followId = M ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			
			$savedata ['duid'] = $qr ['aim_id'];
			$savedata ['uid'] = $followId;
			$res1 = M ( 'shop_statistics_follow' )->where ( $map1 )->getField ( 'id' );
			if (! $res1) {
				$savedata ['ctime'] = time ();
				$savedata ['token'] = get_token ();
				M ( 'shop_statistics_follow' )->add ( $savedata );
			}
		} elseif ($qr ['addon'] == 'HelpOpen') {
			$user = getUserInfo ( $qr ['extra_int'] );
			$url = addons_url ( 'HelpOpen://Wap/index', array (
					'invite_uid' => $qr ['extra_int'],
					'id' => $qr ['aim_id'] 
			) );
			$this->replyText ( "关注成功，<a href='{$url}'>请点击这里继续帮{$user[nickname]}领取奖品</a>" );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} elseif ($qr ['addon'] == 'CouponShop') {
			// 门店二维码
			// 触发会员卡图文
			$config = getAddonConfig ( 'Card' ); // 获取后台插件的配置参数
			$articles [0] = array (
					'Title' => '点击进入免费领取微会员哦~',
					'Description' => $config ['title'],
					'PicUrl' => SITE_URL . "/Addons/Card/View/default/Public/cover_pic.png",
					'Url' => addons_url ( 'Card://Wap/index', array (
							'token' => get_token () 
					) ) 
			);
			$res = $this->replyNews ( $articles );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		}
	}
}
