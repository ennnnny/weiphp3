<?php
return array (
		'title' => array (
				'title' => '公众号名称 :',
				'type' => 'text',
				'value' => 'WeiPHP摇电视',
				'tip' => '不可以为空'
		),	
		'id' => array (
				'title' => '原始ID :',
				'type' => 'text',
				'value' => 'gh_dd85ac50d2dd',
				'tip' => '请正确填写，保存后不能再修改，且无法接收到微信的信息'
		),	
		'account' => array (
				'title' => '微信号 :',
				'type' => 'text',
				'value' => 'weiphp-tv',
				'tip' => ''
		),	
		'type' => array (
				'title' => '公众号类型 :',
				'type' => 'radio',
				'options'=>array(
						'1'=>'普通订阅号',
						'2'=>'认证订阅号/普通服务号',
						'3'=>'认证服务号'
				),
				'value' => '3'
		),	
			
		'logo' => array (
				'title' => '上传LOGO :',
				'type' => 'picture',
				'value' => ''
		),

		'articleurl' => array (
				'title' => '提示关注公众号的文章地址 :',
				'type' => 'text',
				'value' => ''
				
		),
		
);
					