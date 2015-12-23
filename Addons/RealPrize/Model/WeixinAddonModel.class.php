<?php
        	
namespace Addons\RealPrize\Model;
use Home\Model\WeixinModel;
        	
/**
 * RealPrize的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'RealPrize' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	