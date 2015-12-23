<?php
return array (
	   'type' => array (
				'title' => '短信服务商:',
				'type' => 'select',
				'options'=>array(
						'1'=>'云之讯',
						'2'=>'云通讯',
				),
				'value' => '1' 
				),
		'accountSid' => array ( 
				'title' => '开发者Account Sid',
				'type' => 'text',
				'value' => ''
		),	
		'authToken' => array ( 
				'title' => '开发者Auth Token',
				'type' => 'text',
				'value' => ''
		),	
		'appId' => array ( 
				'title' => '应用Id',
				'type' => 'text',
				'value' => '',
				'tip' => '在服务商平台上应用里面创建获得'
		),		
		'cardTemplateId' => array (
				'title' => '会员卡手机认证短信模板Id', 
				'type' => 'text',
				'value' => '', 
				'tip' => '在服务商平台上短信模板里面创建获得,模板参数{1}代表验证码 {2}代表验证码有效期'
				),
		'expire' => array (
				'title' => '验证码有效期', 
				'type' => 'text',
				'value' => '', 
				'tip' => '单位为分钟'
				)
);
					