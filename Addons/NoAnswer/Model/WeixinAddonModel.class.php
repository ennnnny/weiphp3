<?php

namespace Addons\NoAnswer\Model;

use Home\Model\WeixinModel;

/**
 * NoAnswer的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'NoAnswer' ); // 获取后台插件的配置参数
		                                         
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
			    if ($config['title'] && $config['description']){
			        $articles [0] = array (
			        		'Title' => $config ['title'],
			        		'Description' => $config ['description'],
			        		'PicUrl' => get_cover_url ( $config ['pic_url'] ),
			        		'Url' => str_replace ( $sreach, $replace, $config ['url'] )
			        );
			        $res = $this->replyNews ( $articles );
			    }
				break;
			default :
			    if ($config['description']){
			        $res = $this->replyText ( $config ['description'] );
			    }
		}
	}
}
        	