<?php
        	
namespace Addons\Game\Model;
use Home\Model\WeixinModel;
        	
/**
 * Game的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Game' ); // 获取后台插件的配置参数	
		//dump($config);

	} 	
}
        	