<?php
        	
namespace Addons\WishCard\Model;
use Home\Model\WeixinModel;
        	
/**
 * WishCard的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'WishCard' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	