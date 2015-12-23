<?php
        	
namespace Addons\Sms\Model;
use Home\Model\WeixinModel;
        	
/**
 * Sms的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Sms' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	