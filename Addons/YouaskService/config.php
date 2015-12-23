<?php
return array (
		'state' => array (
				'title' => '开启微信人工客服:',
				'type' => 'radio',
				'options'=>array(
					'1'=>'开启',
					'0'=>'关闭',
				),
				'value' => '0',
				'tip' => '' 
		),
		'zrg' => array (
				'title' => '转人工客服',
				'type' => 'text',				
				'value' => '人工客服',
				'tip' => '设置用户手动转人工客服关键词' 
		),
		'model' => array (
				'title' => '客服模式:',
				'type' => 'radio',
				'options'=>array(
					'1'=>'多客服',
					'0'=>'系统处理(无效)',
				),
				'value' => '1',
				'tip' => '启用多客服模式只能客服结束会话或者2小时内无互动' 
		),
		'tcrg' => array (
				'title' => '退出人工客服',
				'type' => 'text',				
				'value' => '退出人工客服',
				'tip' => '设置用户手动退出人工客服关键词(系统处理模式有效)' 
		)
		
);
					