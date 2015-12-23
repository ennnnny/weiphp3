<?php

namespace Addons\PublicBind\Model;

use Home\Model\WeixinModel;

/**
 * PublicBind的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		// addWeixinLog ( $dataArr, 'PublicBindModel' );
		if ($dataArr ['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
			$this->replyText ( 'TESTCOMPONENT_MSG_TYPE_TEXT_callback' );
		} elseif (strpos ( $dataArr ['Content'], 'QUERY_AUTH_CODE' ) !== false) {
			// addWeixinLog ( $dataArr ['Content'], '222' );
			$query_auth_code = str_replace ( 'QUERY_AUTH_CODE:', '', $dataArr ['Content'] );
			
			$info = D ( 'Addons://PublicBind/PublicBind' )->getAuthInfo ( $query_auth_code );
			// addWeixinLog ( $info, '555' );
			$param ['touser'] = $dataArr ['FromUserName'];
			$param ['msgtype'] = 'text';
			$param ['text'] ['content'] = $query_auth_code . '_from_api';
			$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $info ['authorization_info'] ['authorizer_access_token'];
			// addWeixinLog ( $param, $url );
			$res = post_data ( $url, $param );
		} else {
			$this->replyText ( $dataArr ['Event'] . 'from_callback' );
		}
	}
}
        	