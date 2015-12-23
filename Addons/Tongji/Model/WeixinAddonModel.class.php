<?php
        	
namespace Addons\Tongji\Model;
use Home\Model\WeixinModel;
        	
/**
 * Tongji的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Tongji' ); // 获取后台插件的配置参数	
		//dump($config);

	} 
}
        	