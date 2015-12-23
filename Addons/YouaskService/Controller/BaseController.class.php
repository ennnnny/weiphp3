<?php

namespace Addons\YouaskService\Controller;

use Home\Controller\AddonsController;

class BaseController extends AddonsController{	
	var $config;
	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _CONTROLLER );		
		$action = strtolower ( _ACTION );
		
		$res ['title'] = '客服管理';				
		$res ['url'] = addons_url ( 'YouaskService://YouaskService/lists' );
		$res ['class'] = (($controller == 'youaskservice' || $controller == 'group')&& $action  != 'config') ? 'current' : '';
		$nav [] = $res;
				
		$res ['title'] = '关键词指定客服';
		$res ['url'] = addons_url ( 'YouaskService://KeywordKF/lists' );
		$res ['class'] = $controller == 'keywordkf'? 'current' : '';
		$nav [] = $res;
		
		
		/*$res ['title'] = '聊天记录管理';
		$res ['url'] = addons_url ( 'YouaskService://ChatLog/lists' );
		$res ['class'] = $controller == 'chatlog'? 'current' : '';
		$nav [] = $res;*/
		
		$res ['title'] = '微信客服设置';				
		$res ['url'] = addons_url ( 'YouaskService://YouaskService/config' );
		$res ['class'] = ($controller == 'youaskservice' && $action  == 'config') ? 'current' : '';
		$nav [] = $res;
						
		$this->assign ( 'nav', $nav );
		
		$config = getAddonConfig ( 'YouaskService' );
		$config ['cover_url'] = get_cover_url ( $config ['cover'] );
		$config ['background'] = get_cover_url ( $config ['background'] );
		$this->config = $config;
		$this->assign ( 'config', $config );
				
		// 定义模板常量
		$act = strtolower ( _ACTION );
		$temp = $config ['template_' . $act];
		$act = ucfirst ( $act );		
	}
	
	//获取微信认证
	function getaccess_token(){
        return get_access_token();
	}
	
	function curlGet($url, $method = 'get', $data = '')
    {		
        $ch = curl_init();
        $headers = array('Accept-Charset: utf-8');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }
}
