<?php
        	
namespace Addons\Comment\Model;
use Home\Model\WeixinModel;
        	
/**
 * Comment的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Comment' ); // 获取后台插件的配置参数	
		//dump($config);

	}
}
        	