<?php

namespace Addons\Invite\Model;

use Home\Model\WeixinModel;

/**
 * Invite的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		if (! empty ( $keywordArr ['aim_id'] )) {
			$map ['id'] = $keywordArr ['aim_id'];
			$info = M ( 'invite' )->where ( $map )->find ();
		} else {
			$info = M ( 'invite' )->order ( 'id desc' )->find ();
		}
		
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		$param ['id'] = $info ['id'];
		$url = addons_url ( 'Invite://Wap/receive', $param );
		
		// 组装微信需要的图文数据，格式是固定的
		$articles [0] = array (
				'Title' => $info ['title'],
				'Description' => $info ['intro'],
				'PicUrl' => get_cover_url ( $info ['cover'] ),
				'Url' => $url 
		);
		
		$res = $this->replyNews ( $articles );
	}
}
        	