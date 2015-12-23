<?php
        	
namespace Addons\Leaflets\Model;
use Home\Model\WeixinModel;
        	
/**
 * Leaflets的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Leaflets' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	