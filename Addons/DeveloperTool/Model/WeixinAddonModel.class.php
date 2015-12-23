<?php
        	
namespace Addons\DeveloperTool\Model;
use Home\Model\WeixinModel;
        	
/**
 * DeveloperTool的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'DeveloperTool' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	