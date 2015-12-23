<?php
return array(
	'page_size'=>array(
		'title'=>'每页显示数:',
		'type'=>'text',
		'value'=>'20'
	),
	'delete_switch'=>array(
		'title' => '显示删除按钮:',
		'type' => 'radio',
		'options' => array(
                    '0'=>'不显示',
                    '1'=>'显示'
		),
		'value'=>'1',
	),
	'delete_mode'=>array(
		'title' => '删除方式:',
		'type' => 'radio',
		'options' => array(
                    '0'=>'逻辑删除',
                    '1'=>'物理删除'
		),
		'value'=>'0',
                'tip' => '逻辑删除是改变数据状态，物理删除直接删除图片文件和数据'
	),
);