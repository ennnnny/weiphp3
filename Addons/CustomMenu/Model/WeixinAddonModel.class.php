<?php

namespace Addons\CustomMenu\Model;

use Home\Model\WeixinModel;

/**
 * CustomMenu的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'CustomMenu' ); // 获取后台插件的配置参数
			                                           // dump($config);
		if($data['Content']=='view'){
			redirect ( $data ['EventKey'] );
		}
	}
}