<?php
        	
namespace Addons\ConfigureAccount\Model;
use Home\Model\WeixinModel;
        	
/**
 * ConfigureAccount的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'ConfigureAccount' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	