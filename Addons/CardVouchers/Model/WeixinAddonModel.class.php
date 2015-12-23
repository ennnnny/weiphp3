<?php
        	
namespace Addons\CardVouchers\Model;
use Home\Model\WeixinModel;
        	
/**
 * CardVouchers的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'CardVouchers' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	